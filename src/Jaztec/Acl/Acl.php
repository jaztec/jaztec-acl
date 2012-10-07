<?php
/**
 * Bestand voor ACL klasse
 * 
 * @author Jasper van Herpt
 * @package Jaztec\Acl 
 */

namespace Jaztec\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Query\ResultSetMappingBuilder,
    Jaztec\Entity\Role,
    Jaztec\Entity\Resource,
    Jaztec\Entity\Privilege;

class Acl extends ZendAcl {
    
    /** @var \Doctrine\ORM\EntityManager */
    protected $em;
    
    /** @var boolean $loaded */
    protected $loaded;
    
    /**
     * @param EntityManager $em 
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    /**
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->em;
    }
    
    /**
     * @return bool
     */
    public function isLoaded() {
        return $this->loaded;
    }

    public function setupAcl() {
        $this->insertRoles($this->findRoles())
             ->insertResources($this->findResources())
             ->insertPrivileges($this->findPrivileges());
        
        $this->loaded = true;
        
        return $this;
    }
    
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
    
    protected function findRoles() {
        $em = $this->em;
        
        $sql = 'SELECT ro.* FROM acl_roles ro ORDER BY sort';
        
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('\Jaztec\Entity\Role', 'ro');
        
        $roles = array();
        
        $roles = $em->createNativeQuery($sql, $rsm)->getResult();
        
        return $roles;
    }
    
    protected function findResources() {
        $em = $this->em;
        
        $sql = 'SELECT re.* FROM acl_resources re ORDER BY sort';
        
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('\Jaztec\Entity\Resource', 're');
        
        $resources = array();
        
        $resources = $em->createNativeQuery($sql, $rsm)->getResult();
        
        // Ook de geconfigureerde resources inladen
        
        
        return $resources;
    }
    
    protected function findPrivileges() {
        $em = $this->em;
        
        $sql = 'SELECT pr.* FROM acl_privileges pr';
        
        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('\Jaztec\Entity\Privilege', 'pr');
        
        $privileges = array();
        
        $privileges = $em->createNativeQuery($sql, $rsm)->getResult();
        
        return $privileges;
    }
}