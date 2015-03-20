<?php

namespace JaztecAcl\Entity\Acl;

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
     * @ORM\ManyToOne(targetEntity="JaztecAcl\Entity\Acl\Role", inversedBy="childRoles")
     * @ORM\JoinColumn(name="ParentId", referencedColumnName="Id")
     *
     * @var \JaztecAcl\Entity\Acl\Role
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="JaztecAcl\Entity\Acl\Privilege", mappedBy="role", cascade={"persist", "remove"})
     * @var JaztecAcl\Entity\Acl\Privilege[] | \Doctrine\Common\Collections\ArrayCollection
     */
    protected $privileges;
    
    /**
     * @ORM\OneToMany(targetEntity="JaztecAcl\Entity\Acl\Role", mappedBy="parent", cascade={"persist", "remove"})
     * @var JaztecAcl\Entity\Acl\Role[] | \Doctrine\Common\Collections\ArrayCollection
     */
    protected $childRoles;
    
    /**
     * @param type $name
     * @param \JaztecAcl\Entity\Acl\Role $parent
     * @param int $sort
     */
    public function __construct($name, Role $parent = null, $sort = 0)
    {
        $this->setName($name);
        $this->setParent($parent);
        $this->setSort($sort);
        $this->setPrivileges(new ArrayCollection());
        $this->setChildRoles(new ArrayCollection());
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
     * @return \JaztecAcl\Entity\Acl\Role
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
     * @return \JaztecAcl\Entity\Acl\Role
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
     * @return \JaztecAcl\Entity\Acl\Role
     */
    public function setSort($sort)
    {
        $this->sort = (int) $sort;

        return $this;
    }

    /**
     * @return \JaztecAcl\Entity\Acl\Role
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param  \JaztecAcl\Entity\Acl\Role $parent
     * @return \JaztecAcl\Entity\Acl\Role
     */
    public function setParent(Role $parent = null)
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
     * @return \JaztecAcl\Entity\Acl\Privilege[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPrivileges()
    {
        return $this->privileges;
    }

    /**
     * @return \JaztecAcl\Entity\Acl\Role[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildRoles()
    {
        return $this->childRoles;
    }

    /**
     * @param JaztecAcl\Entity\Acl\Privilege $privileges
     * @return self
     */
    public function setPrivileges($privileges)
    {
        $this->privileges = $privileges;
        return $this;
    }

    /**
     * @param \JaztecAcl\Entity\Acl\Role[] $childRoles
     * @return self
     */
    public function setChildRoles($childRoles)
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
