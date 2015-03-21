<?php

namespace JaztecAcl\Service;

use JaztecBase\Mapper\AbstractDoctrineMapper;
use JaztecBase\ORM\EntityManagerAwareInterface;
use JaztecBase\ORM\EntityManagerAwareTrait;
use JaztecAcl\Entity\Acl\Role;
use JaztecAcl\Entity\Auth\User;
use Zend\Crypt\Password\Bcrypt;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Class InstallationService
 *
 * @author Jasper van Herpt <jasper.v.herpt@gmail.com>
 */
class InstallationService extends AbstractDoctrineMapper implements
    EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;
    
    /**
     * Install the database.
     * 
     * @param string                        $email
     * @param bool                          $verbose
     */
    public function installDatabase($email, $verbose)
    {
        $em = $this->getEntityManager();
        $tool = new SchemaTool($em);
        if ($verbose) {
            print_r("Installing database.\n");
        }
        $classes = $this->getEntityMetaData($em);

        // Rebuild the schema
        if ($verbose) {
            print_r("Drop existing schema if exists.\n");
        }
        $tool->dropSchema($classes);
        if ($verbose) {
            print_r("Rebuild the schema.\nRunning:\n");
            foreach ($tool->getCreateSchemaSql($classes) as $statement) {
                print_r($statement . "\n");
            }
        }
        $tool->createSchema($classes);

        $config = $this->getServiceLocator()->get('Config');
        $setUp = $config['jaztec_acl']['setUp'];

        $roleSetUp = $setUp['roles'];
        /* @var \JaztecAcl\Entity\Acl\Role[] $roles */
        $roles = [];

        // Setup roles.
        foreach ($roleSetUp as $setUpConfig) {
            $role = new Role($setUpConfig['name'], null, $setUpConfig['sort']);

            $this->validateRoleParent($setUpConfig, $roles, $role);

            $em->persist($role);
            $roles[] = $role;
        }
        $em->flush();

        // Add a global user.
        if ($verbose) {
            print_r("Create an administrative user with email '{$email}'.\n");
        }
        $this->addAdminUser($email);

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
    protected function validateRoleParent(array $setUpConfig, $roles, Role $role)
    {
        if (array_key_exists('parent', $setUpConfig)) {
            foreach ($roles as $cached) {
                if ($cached->getName() === $setUpConfig['parent']) {
                    $role->setParent($cached);
                }
            }
        }
    }

    /**
     * Update the database.
     * 
     * @param bool $verbose
     */
    public function updateDatabase($verbose)
    {
        $em = $this->getEntityManager();
        $tool = new SchemaTool($em);
        if ($verbose) {
            print_r("Updating database.\n");
        }
        $classes = $this->getEntityMetaData($em);

        // Rebuild the schema
        if ($verbose) {
            print_r("Update existing schema.\nRunning:\n");
            foreach ($tool->getUpdateSchemaSql($classes) as $statement) {
                print_r($statement . "\n");
            }
        }
        $tool->updateSchema($classes, true);

        return "Database updated\n";
    }

    /**
     * Get the metadata of all the Doctrine entities in this module.
     * 
     * @return array A merged array with the metadata for this module.
     */
    protected function getEntityMetaData()
    {
        return [
            $this->getEntityManager()->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Acl\Privilege'),
            $this->getEntityManager()->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Monitor\RequestedPrivilege'),
            $this->getEntityManager()->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Acl\Resource'),
            $this->getEntityManager()->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Acl\Role'),
            $this->getEntityManager()->getMetadataFactory()->getMetadataFor('\JaztecAcl\Entity\Auth\User'),
        ];
    }

    /**
     * Add an administration user to the database.
     * 
     * @param string                        $email
     */
    protected function addAdminUser($email)
    {
        $options = $this->getServiceLocator()->get('zfcuser_module_options');
        $cost = $options->getPasswordCost();
        $crypt = new Bcrypt();
        $crypt->setCost($cost);
        $adminRole = $this->getEntityManager()->getRepository('JaztecAcl\Entity\Acl\Role')->findOneBy(['name'  => 'admin']);
        $user = new User();
        $user->setUsername('admin')
            ->setPassword($crypt->create('admin'))
            ->setEmail($email)
            ->setRole($adminRole)
            ->setFirstName('Admin')
            ->setLastName('User')
            ->setDisplayName('Administration user')
            ->setActive(true)
            ->setState(true);
            ;
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush($user);
    }
}
