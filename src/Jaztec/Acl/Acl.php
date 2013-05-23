<?php
/**
 * Database driven ACL class.
 * 
 * @author Jasper van Herpt
 * @package Jaztec\Acl 
 */

namespace Jaztec\Acl;
 
use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Cache\Storage\StorageInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class Acl extends ZendAcl
{
    
    /** @var boolean $loaded */
    protected $loaded;

    /**
     * @return bool
     */
    public function isLoaded() {
        return $this->loaded;
    }

    /**
     * Build a new ACL object from the database.
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @return \Jaztec\Acl\Acl
     */
    public function setupAcl(EntityManager $em) {
        $this->insertRoles($this->findRoles($em))
             ->insertResources($this->findResources($em))
             ->insertPrivileges($this->findPrivileges($em));
        
        $this->loaded = true;
        
        return $this;
    }
    
    /**
     * Insert an array of roles into the current ACL object.
     * 
     * @param array $roles
     * @return \Jaztec\Acl\Acl
     */
    protected function insertRoles(array $roles) {
        foreach($roles as $role) {
            if(null === $role->getParent()) {
                $this->addRole($role);
            } else {
                $parents = array();
                $parents[] = $role->getParent()->getRoleId();
                $this->addRole($role,$parents);
            }
        }
        return $this;
    }
    
    /**
     * Inserts an array of resources into the current ACL object.
     * 
     * @param array $resources
     * @return \Jaztec\Acl\Acl
     */
    protected function insertResources(array $resources) {
        foreach($resources as $resource) {
            if(null === $resource->getParent()) {
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
     * @param array $privileges
     * @return \Jaztec\Acl\Acl
     */
    protected function insertPrivileges(array $privileges) {
        foreach($privileges as $privilege) {
            $type = $privilege->getType();
            $this->$type(
                $privilege->getRole(), 
                $privilege->getResource(), 
                $privilege->getPrivilege()
            );
        }
        return $this;
    }
    
    /**
     * Find the roles in the database.
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @return array
     */
    protected function findRoles(EntityManager $em) {
        $sql = 'SELECT ro.* FROM acl_roles ro ORDER BY sort';
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('\Jaztec\Entity\Role', 'ro');
        $roles = $em->createNativeQuery($sql, $rsm)->getResult();
        
        return $roles;
    }
    
    /**
     * Find the resources in the database.
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @return array
     */
    protected function findResources(EntityManager $em) {
        $sql = 'SELECT re.* FROM acl_resources re ORDER BY sort';
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('\Jaztec\Entity\Resource', 're');
        $resources = $em->createNativeQuery($sql, $rsm)->getResult();
        
        return $resources;
    }
    
    /**
     * Find the privileges in the database.
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @return array
     */
    protected function findPrivileges(EntityManager $em) {
        $sql = 'SELECT pr.* FROM acl_privileges pr';
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('\Jaztec\Entity\Privilege', 'pr');
        $privileges = $em->createNativeQuery($sql, $rsm)->getResult();
        
        return $privileges;
    }
}