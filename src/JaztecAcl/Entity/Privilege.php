<?php

namespace JaztecAcl\Entity;

use Doctrine\ORM\Mapping as ORM;
use JaztecBase\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="acl_privileges")
 */
class Privilege extends AbstractEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $privilege;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role", referencedColumnName="id")
     *
     * @var \JaztecAcl\Entity\Role
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="Resource")
     * @ORM\JoinColumn(name="resource", referencedColumnName="id")
     *
     * @var \JaztecAcl\Entity\Role
     */
    protected $resource;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param  string                      $type
     * @return \JaztecAcl\Entity\Privilege
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * @param  string                      $privilege
     * @return \JaztecAcl\Entity\Privilege
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;

        return $this;
    }

    /**
     * @return \JaztecAcl\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param  \JaztecAcl\Entity\Role      $role
     * @return \JaztecAcl\Entity\Privilege
     */
    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return \JaztecAcl\Entity\Privilege
     */
    public function clearRole()
    {
        $this->role = null;

        return $this;
    }

    /**
     * @return \JaztecAcl\Entity\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param  \JaztecAcl\Entity\Resource  $resource
     * @return \JaztecAcl\Entity\Privilege
     */
    public function setResource(Resource $resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return \JaztecAcl\Entity\Privilege
     */
    public function clearResource()
    {
        $this->resource = null;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'PrivilegeID'  => $this->getId(),
            'Type'         => $this->getType(),
            'Privilege'    => $this->getPrivilege(),
            'Resource'     => (null === $this->getResource()) ? null : $this->getResource()->getId(),
            'Role'         => (null === $this->getRole()) ? null : $this->getRole()->getId(),
            'ResourceName' => (null === $this->getResource()) ? null : $this->getResource()->getName(),
            'RoleName'     => (null === $this->getRole()) ? null : $this->getRole()->getName(),
        );
    }
}
