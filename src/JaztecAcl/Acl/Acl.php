<?php

/**
 * Database driven ACL class.
 *
 * @author Jasper van Herpt
 * @package JaztecAcl\Acl
 */

namespace JaztecAcl\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl;
use JaztecAcl\Entity\Acl\Resource as ResourceEntity;
use JaztecBase\ORM\EntityManagerAwareInterface;
use JaztecBase\ORM\EntityManagerAwareTrait;

class Acl extends ZendAcl implements 
    EntityManagerAwareInterface
{

    use EntityManagerAwareTrait;
    
    /** @var boolean */
    protected $loaded;

    /**
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded ? : false;
    }

    /**
     * Build a new ACL object from the database.
     *
     * @return \JaztecAcl\Acl\Acl
     */
    public function setupAcl()
    {
        $this->insertRoles($this->findRoles())
            ->insertResources($this->findResources())
            ->insertPrivileges($this->findPrivileges());

        $this->loaded = true;

        return $this;
    }

    /**
     * Insert an array of roles into the current ACL object.
     *
     * @param  array              $roles
     * @return \JaztecAcl\Acl\Acl
     */
    protected function insertRoles(array $roles)
    {
        foreach ($roles as $role) {
            if (null === $role->getParent()) {
                $this->addRole($role);
            } else {
                $parents   = [];
                $parents[] = $role->getParent()->getRoleId();
                $this->addRole($role, $parents);
            }
        }

        return $this;
    }

    /**
     * Inserts an array of resources into the current ACL object.
     *
     * @param  array              $resources
     * @return \JaztecAcl\Acl\Acl
     */
    protected function insertResources(array $resources)
    {
        foreach ($resources as $resource) {
            if (null === $resource->getParent()) {
                $this->addResource($resource);
            } else {
                $parent = $resource->getParent()->getResourceId();
                $this->addResource($resource, $parent);
            }
        }

        return $this;
    }

    /**
     * Setup the privileges.
     *
     * @param  array              $privileges
     * @return \JaztecAcl\Acl\Acl
     */
    protected function insertPrivileges(array $privileges)
    {
        foreach ($privileges as $privilege) {
            $this->{$privilege->getType()}(
                $privilege->getRole(), $privilege->getResource(), $privilege->getPrivilege()
            );
        }

        return $this;
    }

    /**
     * Find the roles in the database.
     *
     * @return array
     */
    protected function findRoles()
    {
        $roles = $this->getEntityManager()->getRepository('JaztecAcl\Entity\Acl\Role')->findBy(
            [],
            ['sort' => 'ASC']
        );

        return $roles;
    }

    /**
     * Find the resources in the database.
     *
     * @return array
     */
    protected function findResources()
    {
        $resources = $this->getEntityManager()->getRepository('JaztecAcl\Entity\Acl\Resource')->findBy(
            [],
            ['sort' => 'ASC']
        );

        return $resources;
    }

    /**
     * Find the privileges in the database.
     *
     * @return array
     */
    protected function findPrivileges()
    {
        $privileges = $this->getEntityManager()->getRepository('JaztecAcl\Entity\Acl\Privilege')->findAll();

        return $privileges;
    }

    /**
     * Create a new resource in the ACL structure
     * @param  string                            $newResource
     * @param  string|\JaztecAcl\Entity\Acl\Resource $baseResource
     * @return \JaztecAcl\Entity\Acl\Resource
     */
    public function createResource($newResource, $baseResource)
    {
        $em = $this->getEntityManager();
        // Check if the base resource exists, otherwise create it.
        if (!$baseResource instanceof ResourceEntity &&
            !is_string($baseResource)) {
            throw new \Exception('Base resource is not a valid ACL resource, ' . get_class($baseResource) . ' given.');
        } elseif (!$baseResource instanceof \ResourceEntity) {
            $baseName     = $baseResource;
            $baseResource = $em->getRepository('JaztecAcl\Entity\Acl\Resource')->findOneBy(['name' => $baseName]);
            if (!$baseResource instanceof ResourceEntity) {
                $baseResource = new \JaztecAcl\Entity\Acl\Resource($baseName);
                $baseResource->setSort(0);
                $em->persist($baseResource);
                $this->addResource($baseResource->getResourceId());
            }
        }
        // Checking the new resource on validity
        if (!is_string($newResource)) {
            throw new \Exception('The new resource is not a valid string');
        }

        // Create the new (unknown) resource and add it to the ACL.
        $resource = new \JaztecAcl\Entity\Acl\Resource($newResource, $baseResource, $baseResource->getSort() + 1);
        $em->persist($resource);

        $em->flush();

        $this->addResource($resource, $resource->getParent());

        return $resource;
    }

    /**
     * Checks and adds the privilege request and the resource to the request
     * storage if it doesn't exist.
     * 
     * @param   string                          $privilege
     * @param   string                          $resource
     * @return  bool
     */
    public function checkPrivilegeRequest($privilege, $resource)
    {
        $em = $this->getEntityManager();
        $privilege = trim($privilege);
        $resource = trim($resource);
        // Check the input values.
        if ($resource === '' || $privilege === '') {
            return false;
        }
        // Try to find the privilege request in the database.
        $requestedPrivilege = $em->getRepository('JaztecAcl\Entity\Monitor\RequestedPrivilege')->findOneBy([
            'privilege' => $privilege,
            'resource'  => $resource,
        ]);
        if ($requestedPrivilege instanceof \JaztecAcl\Entity\Monitor\RequestedPrivilege) {
            return true;
        }
        // Create the privilege request.
        $newRequestedPrivilege = new \JaztecAcl\Entity\Monitor\RequestedPrivilege();
        $newRequestedPrivilege
            ->setPrivilege($privilege)
            ->setResource($resource);
        $em->persist($newRequestedPrivilege);
        $em->flush();
        return true;
    }

    /**
     * Set up the object for serialization
     * @return array
     */
    public function __sleep()
    {
        return ['resources', 'roleRegistry', 'rules'];
    }
}
