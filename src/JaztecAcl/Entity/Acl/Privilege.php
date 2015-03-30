<?php

namespace JaztecAcl\Entity\Acl;

use Doctrine\ORM\Mapping as ORM;
use JaztecBase\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="AclPrivileges")
 */
class Privilege extends AbstractEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(name="Id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="Type", type="string")
     *
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(name="Privilege", type="string", nullable=true)
     *
     * @var string
     */
    protected $privilege;

    /**
     * @ORM\ManyToOne(targetEntity="JaztecAcl\Entity\Acl\Role", inversedBy="privileges")
     * @ORM\JoinColumn(name="RoleId", referencedColumnName="Id")
     *
     * @var \JaztecAcl\Entity\Acl\Role
     */
    protected $role;

    /**
     * @ORM\ManyToOne(targetEntity="JaztecAcl\Entity\Acl\Resource", inversedBy="privileges")
     * @ORM\JoinColumn(name="ResourceId", referencedColumnName="Id")
     *
     * @var \JaztecAcl\Entity\Acl\Role
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
     * @param  string $type
     * @return self
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
     * @param string $privilege
     * @return self
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;

        return $this;
    }

    /**
     * @return \JaztecAcl\Entity\Acl\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param  \JaztecAcl\Entity\Acl\Role      $role
     * @return \JaztecAcl\Entity\Acl\Privilege
     */
    public function setRole(Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return \JaztecAcl\Entity\Acl\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param  \JaztecAcl\Entity\Acl\Resource  $resource
     * @return \JaztecAcl\Entity\Acl\Privilege
     */
    public function setResource(Resource $resource = null)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id'           => $this->getId(),
            'type'         => $this->getType(),
            'privilege'    => $this->getPrivilege(),
            'resourceId'   => $this->getResource() ? $this->getResource()->getId() : null,
            'roleId'       => $this->getRole() ? $this->getRole()->getId() : null,
            'resourceName' => $this->getResource() ? $this->getResource()->getName() : null,
            'roleName'     => $this->getRole() ? $this->getRole()->getName() : null,
        ];
    }
}
