<?php

namespace JaztecAcl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;
use Zend\Stdlib\ArrayUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

class ConsoleController extends AbstractActionController
{

    /** @var \Doctrine\ORM\EntityManager $em */
    protected $em;

    /**
     * Will return a blank page by default.
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * Will check and update the entire database structure.
     * 
     * @throws \Zend\Console\Exception\RuntimeException
     * @return string Program output.
     */
    public function updateDatabaseAction()
    {
        // Check if a console is used.
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \Zend\Console\Exception\RuntimeException('You can only access this function through a console');
        }
        // Gather the variables.
        /* @var $mode string */
        $mode = $request->getParam('mode', 'update');
        /* @var $verbose boolean */
        $verbose = $request->getParam('verbose', false) || $request->getParam('v', false) ? true : false;

        /* @var $result string */
        $result = '';
        if ($verbose) {
            print_r('Processing mode: ');
            print_r($mode . "\n");
        }
        // Test the mode.
        switch ($mode) {
            case 'clean-install':
                $result = $this->installDatabase($this->getEntityManager(), $verbose);
                break;
            case 'update':
                $result = $this->updateDatabase($this->getEntityManager(), $verbose);
                break;
        }
        return $result;
    }

    /**
     * Install the database.
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param bool $verbose
     */
    protected function installDatabase(EntityManager $em, $verbose)
    {
        $tool = new SchemaTool($em);
        /* @var $tool \Doctrine\ORM\Tools\SchemaTool */
        if ($verbose) {
            print_r("Installing database.\n");
        }
        /* @var $classes array */
        $classes = $this->getEntityMetaData($em);

        // Rebuild the scheme
        if ($verbose) {
            print_r("Drop existing scheme if exists.\n");
        }
        $tool->dropSchema($classes);
        if ($verbose) {
            print_r("Rebuild the scheme.\n");
        }
        $tool->createSchema($classes);

        return "Database installed\n";
    }

    /**
     * Update the database.
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param bool $verbose
     */
    protected function updateDatabase(EntityManager $em, $verbose)
    {
        $tool = new SchemaTool($em);
        /* @var $tool \Doctrine\ORM\Tools\SchemaTool */
        if ($verbose) {
            print_r("Updating database.\n");
        }
        /* @var $classes array */
        $classes = $this->getEntityMetaData($em);

        // Rebuild the scheme
        if ($verbose) {
            print_r("Update existing scheme.\n");
        }
        $tool->updateSchema($classes, true);

        return "Database updated\n";
    }

    /**
     * Get 
     * @param \Doctrine\ORM\EntityManager $em
     * @return array A merged array with the metadata for this module.
     */
    protected function getEntityMetaData(EntityManager $em)
    {
        /* @var $classes array */
        $classes = array(
            $em->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Privilege'),
            $em->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\RequestedPrivilege'),
            $em->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Resource'),
            $em->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Role'),
            $em->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\User'),
        );
        return $classes;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }

        return $this->em;
    }
}
