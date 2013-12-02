<?php

namespace JaztecAcl\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Permissions\Acl\Role\RoleInterface as ZendRoleInterface;
use JaztecBase\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="acl_roles")
 */
class Role extends AbstractEntity implements ZendRoleInterface
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
    protected $name;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $sort;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     *
     * @var \JaztecAcl\Entity\Role
     */
    protected $parent;

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
    public function setParent(\JaztecAcl\Entity\Role $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return \JaztecAcl\Entity\Role
     */
    public function clearParent()
    {
        $this->parent = null;

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
     * @return array
     */
    public function toArray()
    {
        return array(
            'RoleID'   => $this->getId(),
            'Name'     => $this->getRoleId(),
            'ParentID' => $this->getParent() === null ? null : $this->getParent()->getId(),
            'Parent'   => $this->getParent() === null ? null : $this->getParent()->getRoleId(),
        );
    }

}
