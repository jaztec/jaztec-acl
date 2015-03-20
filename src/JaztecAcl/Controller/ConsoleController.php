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
        if ((bool) $request->getParam('help', false) || $request->getParam('h', false)) {
            $mode = 'help';
        } else {
            $mode = $request->getParam('clean-install', false) ? 'clean-install' : false;
            if (!$mode) {
                $mode = $request->getParam('update', false) ? 'update' : false;
            }
            if (!$mode) {
                $mode = 'help';
            }
        }
        /* @var $verbose boolean */
        $verbose = (bool) $request->getParam('verbose', false) || $request->getParam('v', false);

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
            case 'help':
                $result = $this->getHelpOutput();
                break;
        }
        return $result;
    }

    /**
     * Get the help output for console usage of the JaztecAcl 'acl database'
     * console command.
     * 
     * @return string The help output string.
     */
    protected function getHelpOutput()
    {
        /* @var $result string */
        $result = "\n";
        $result .= "Copyright 2014 by Jasper van Herpt <jasper.v.herpt@gmail.com>\n";
        $result .= "\n";
        $result .= "Usage:\t\tacl database [clean-install|update] [--email=] [--help|-h] [--verbose|-v]\n";
        $result .= "Examples:\tacl database clean-install --email=john.doe@example.com --verbose\n";
        $result .= "\t\tacl database update --verbose\n";
        $result .= "\t\tacl database --help\n";
        $result .= "\n";
        $result .= "clean-install: Installs the database schema to the configurated database connection\n";
        $result .= "\t--e-mail\t(required):\tAny e-mail address\n";
        $result .= "\t--verbose|-v\t(optional):\tOutput progress\n";
        $result .= "\n";
        $result .= "update: Update the class data to the existing database schema.\n";
        $result .= "\t--verbose|-v\t(optional):\tOutput progress\n";
        $result .= "\n";
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

        // Rebuild the schema
        if ($verbose) {
            print_r("Drop existing schema if exists.\n");
        }
        $tool->dropSchema($classes);
        if ($verbose) {
            print_r("Rebuild the schema.\n");
        }
        $tool->createSchema($classes);

        /* @var $config array */
        $config = $this->getServiceLocator()->get('Config');
        /* @var $setUp array */
        $setUp = $config['jaztec_acl']['setUp'];

        /* @var $roleSetUp array */
        $roleSetUp = $setUp['roles'];
        /* @var $roles \JaztecAcl\Entity\Acl\Role[] */
        $roles = [];

        // Setup roles.
        foreach ($roleSetUp as $setUpConfig) {
            $role = new \JaztecAcl\Entity\Acl\Role();
            $role->setName($setUpConfig['name'])
                ->setSort($setUpConfig['sort']);

            $this->validateRoleParent($setUpConfig, $roles, $role);

            $em->persist($role);
            $roles[] = $role;
        }
        $em->flush();

        // Add a global user.
        if ($verbose) {
            print_r("Create an administrative user.\n");
        }
        $this->addAdminUser($em, $email);

        return "Database installed\n";
    }

    /**
     * Checks if any role setup configuration includes a parent and if so,
     * retreives the parent from the roles stack and couples it with the
     * provides role.
     * 
     * @param array                     $setUpConfig
     * @param \JaztecAcl\Entity\Acl\Role[]  $roles
     * @param \JaztecAcl\Entity\Acl\Role    $role
     */
    protected function validateRoleParent(array $setUpConfig, $roles, \JaztecAcl\Entity\Acl\Role $role)
    {
        if (array_key_exists('parent', $setUpConfig)) {
            foreach ($roles as $cached) {
                /* @var $cached \JaztecAcl\Entity\Acl\Role */
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

        // Rebuild the schema
        if ($verbose) {
            print_r("Update existing schema.\n");
        }
        $tool->updateSchema($classes, true);

        return "Database updated\n";
    }

    /**
     * Get the metadata of all the Doctrine entities in this module.
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @return array A merged array with the metadata for this module.
     */
    protected function getEntityMetaData(EntityManager $em)
    {
        return [
            $em->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Acl\Privilege'),
            $em->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Monitor\RequestedPrivilege'),
            $em->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Acl\Resource'),
            $em->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Acl\Role'),
            $em->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Auth\User'),
        ];
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
        /* @var $adminRole \JaztecAcl\Entity\Acl\Role */
        $adminRole = $em->getRepository('JaztecAcl\Entity\Acl\Role')->findOneBy(['name'  => 'admin']);
        /* @var $user \JaztecAcl\Entity\Auth\User */
        $user = new \JaztecAcl\Entity\Auth\User();
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
