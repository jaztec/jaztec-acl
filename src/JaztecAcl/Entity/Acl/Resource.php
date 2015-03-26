<?php

namespace JaztecAcl\Entity\Acl;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\Permissions\Acl\Resource\ResourceInterface as ZendResourceInterface;
use JaztecBase\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="AclResources")
 */
class Resource extends AbstractEntity implements ZendResourceInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(name="Id", type="integer")
     * @ORM\GeneratedValue
     *
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(name="Name", type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="JaztecAcl\Entity\Acl\Resource", inversedBy="childResources")
     * @ORM\JoinColumn(name="ParentId", referencedColumnName="Id")
     *
     * @var \JaztecAcl\Entity\Acl\Resource
     */
    protected $parent;

    /**
     * @ORM\Column(name="Sort", type="integer")
     *
     * @var integer
     */
    protected $sort;

    /**
     * @ORM\OneToMany(targetEntity="JaztecAcl\Entity\Acl\Privilege", mappedBy="resource", cascade={"persist", "remove"})
     * @var \JaztecAcl\Entity\Acl\Privilege[] | \Doctrin\Common\Collections\ArrayCollection
     */
    protected $privileges;

    /**
     * @ORM\OneToMany(targetEntity="JaztecAcl\Entity\Acl\Resource", mappedBy="parent", cascade={"persist", "remove"})
     * @var \JaztecAcl\Entity\Acl\Resource[] | \Doctrin\Common\Collections\ArrayCollection
     */
    protected $childResources;

    /**
     * @param string $name
     * @param \JaztecAcl\Entity\Acl\Resource $parent
     * @param int $sort
     */
    public function __construct($name, Resource $parent = null, $sort = 0)
    {
        $this->setName($name);
        $this->setParent($parent);
        $this->setSort($sort);
        $this->setPrivileges(new ArrayCollection());
        $this->setChildResources(new ArrayCollection());
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Resource
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \JaztecAcl\Entity\Acl\Resource
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \JaztecAcl\Entity\Acl\Resource $parent
     * @return self
     */
    public function setParent(Resource $parent = null)
    {
        $this->parent = $parent;

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
     * @param int $sort
     * @return self
     */
    public function setSort($sort)
    {
        $this->sort = (int) $sort;

        return $this;
    }

    /**
     * @return string
     */
    public function getResourceId()
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
     * @return \JaztecAcl\Entity\Acl\Resource[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildResources()
    {
        return $this->childResources;
    }

    /**
     * @param \JaztecAcl\Entity\Acl\Privilege[]
     * @return self
     */
    public function setPrivileges($privileges)
    {
        $this->privileges = $privileges;
        return $this;
    }

    /**
     * @param \JaztecAcl\Entity\Acl\Resource[]
     * @return self
     */
    public function setChildResources($childResources)
    {
        $this->childResources = $childResources;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id'         => $this->getId(),
            'name'       => $this->getResourceId(),
            'parentId'   => $this->getParent() ? $this->getParent()->getId() : null,
        ];
    }
}
