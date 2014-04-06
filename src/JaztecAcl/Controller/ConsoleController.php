<?php

namespace JaztecAcl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;
use Zend\Crypt\Password\Bcrypt;
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
                $email = $request->getParam('email', null);
                if (!$email) {
                    throw new \Zend\Console\Exception\RuntimeException('No e-mail parameter was received.');
                }
                $result = $this->installDatabase($this->getEntityManager(), $email, $verbose);
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
     * @param \Doctrine\ORM\EntityManager   $em
     * @param string                        $email
     * @param bool                          $verbose
     */
    protected function installDatabase(EntityManager $em, $email, $verbose)
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

        /* @var $config array */
        $config = $this->getServiceLocator()->get('Config');
        /* @var $setUp array */
        $setUp = $config['jaztec_acl']['setUp'];

        /* @var $roleSetUp array */
        $roleSetUp = $setUp['roles'];
        /* @var $roles \JaztecAcl\Entity\Role[] */
        $roles = array();

        // SetUp roles.
        foreach ($roleSetUp as $setUpConfig) {
            $role = new \JaztecAcl\Entity\Role();
            $role->setName($setUpConfig['name'])
                ->setSort($setUpConfig['sort']);

            $this->validateRoleParent($setUpConfig, $roles, $role);

            $em->persist($role);
            $roles[] = $role;
        }
        $em->flush();

        // Add a global role.
        if ($verbose) {
            print_r("Create an administrative user.\n");
        }
        $this->addAdminUser($em, $email);

        return "Database installed\n";
    }

    /**
     * 
     * @param array                     $setUpConfig
     * @param \JaztecAcl\Entity\Role[]  $roles
     * @param \JaztecAcl\Entity\Role    $role
     */
    protected function validateRoleParent(array $setUpConfig, $roles, \JaztecAcl\Entity\Role $role)
    {
        if (array_key_exists('parent', $setUpConfig)) {
            foreach ($roles as $cached) {
                /* @var $cached \JaztecAcl\Entity\Role */
                if ($cached->getName() === $setUpConfig['parent']) {
                    $role->setParent($cached);
                }
            }
        }
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
     * Add an administration user to the database.
     * 
     * @param \Doctrine\ORM\EntityManager   $em
     * @param string                        $email
     */
    protected function addAdminUser(EntityManager $em, $email)
    {
        /* @var $options \ZfcUser\Options\UserServiceOptionsInterface */
        $options = $this->getServiceLocator()->get('zfcuser_module_options');
        /* @var $cost int */
        $cost = $options->getPasswordCost();
        /* @var $bcrypt \Zend\Crypt\Password\Bcrypt */
        $crypt = new Bcrypt();
        $crypt->setCost($cost);
        /* @var $adminRole \JaztecAcl\Entity\Role */
        $adminRole = $em->getRepository('JaztecAcl\Entity\Role')->findOneBy(
            array(
                'name'  => 'admin',
            )
        );
        /* @var $user \JaztecAcl\Entity\User */
        $user = new \JaztecAcl\Entity\User();
        $user->setUsername('admin')
            ->setPassword($crypt->create('admin'))
            ->setEmail($email)
            ->setRole($adminRole)
            ->setFirstName('Admin')
            ->setLastName('User')
            ->setDisplayName('Administration user')
            ->setActive(true)
            ->setState(true);
        $em->persist($user);
        $em->flush();
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
