<?php

namespace JaztecAcl\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Permissions\Acl\Role\RoleInterface as ZendRoleInterface;
use JaztecBase\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="AclRoles")
 */
class Role extends AbstractEntity implements ZendRoleInterface
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
     * @ORM\Column(name="Name", type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(name="Sort", type="integer")
     *
     * @var int
     */
    protected $sort;

    /**
     * @ORM\ManyToOne(targetEntity="JaztecAcl\Entity\Role", inversedBy="childRoles")
     * @ORM\JoinColumn(name="ParentId", referencedColumnName="Id")
     *
     * @var \JaztecAcl\Entity\Role
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="JaztecAcl\Entity\Privilege", mappedBy="role", cascade={"persist","remove"})
     * @var JaztecAcl\Entity\Privilege[] | \Doctrine\Common\Collections\ArrayCollection
     */
    protected $privileges;
    
    /**
     * @ORM\OneToMany(targetEntity="JaztecAcl\Entity\Role", mappedBy="parent", cascade={"persist","remove"})
     * @var JaztecAcl\Entity\Role[] | \Doctrine\Common\Collections\ArrayCollection
     */
    protected $childRoles;
    
    
    public function __construct()
    {
        $this->privileges = new ArrayCollection();
        $this->childRoles = new ArrayCollection();
    }
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  int                    $id
     * @return \JaztecAcl\Entity\Role
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string                 $name
     * @return \JaztecAcl\Entity\Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param  int                    $sort
     * @return \JaztecAcl\Entity\Role
     */
    public function setSort($sort)
    {
        $this->sort = (int) $sort;

        return $this;
    }

    /**
     * @return \JaztecAcl\Entity\Role
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param  \JaztecAcl\Entity\Role $parent
     * @return \JaztecAcl\Entity\Role
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoleId()
    {
        return $this->name;
    }

    /**
     * @return \JaztecAcl\Entity\Privilege[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPrivileges()
    {
        return $this->privileges;
    }

    /**
     * @return \JaztecAcl\Entity\Role[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildRoles()
    {
        return $this->childRoles;
    }

    /**
     * @param JaztecAcl\Entity\Privilege $privileges
     * @return self
     */
    public function setPrivileges(array $privileges)
    {
        $this->privileges = $privileges;
        return $this;
    }

    /**
     * @param \JaztecAcl\Entity\Role[] $childRoles
     * @return self
     */
    public function setChildRoles(array $childRoles)
    {
        $this->childRoles = $childRoles;
        return $this;
    }
        
    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'       => $this->getId(),
            'name'     => $this->getRoleId(),
            'parentId' => $this->getParent() ?: $this->getParent()->getId()
        );
    }
}
