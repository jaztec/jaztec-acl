<?php

namespace JaztecAcl\Entity;

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
     * @ORM\ManyToOne(targetEntity="JaztecAcl\Entity\Resource", inversedBy="childResources")
     * @ORM\JoinColumn(name="ParentId", referencedColumnName="Id")
     *
     * @var \JaztecAcl\Entity\Resource
     */
    protected $parent;

    /**
     * @ORM\Column(name="Sort", type="integer")
     *
     * @var integer
     */
    protected $sort;

    /**
     * @ORM\OneToMany(targetEntity="JaztecAcl\Entity\Privilege", mappedBy="resource", cascade={"persist", "remove"})
     * @var \JaztecAcl\Entity\Privilege[] | \Doctrin\Common\Collections\ArrayCollection
     */
    protected $privileges;
    
    /**
     * @ORM\OneToMany(targetEntity="JaztecAcl\Entity\Resource", mappedBy="parent", cascade={"persist", "remove"})
     * @var \JaztecAcl\Entity\Resource[] | \Doctrin\Common\Collections\ArrayCollection
     */
    protected $childResources;
    
    public function __construct()
    {
        $this->privileges = new ArrayCollection();
        $this->childResources = new ArrayCollection();
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
     * @return \JaztecAcl\Entity\Resource
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \JaztecAcl\Entity\Resource $parent
     * @return self
     */
    public function setParent($parent)
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
     * @return \JaztecAcl\Entity\Privilege[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPrivileges()
    {
        return $this->privileges;
    }

    /**
     * @return \JaztecAcl\Entity\Resource[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildResources()
    {
        return $this->childResources;
    }

    /**
     * @param \JaztecAcl\Entity\Privilege[]
     * @return self
     */
    public function setPrivileges(array $privileges)
    {
        $this->privileges = $privileges;
        return $this;
    }

    /**
     * @param \JaztecAcl\Entity\Resource[]
     * @return self
     */
    public function setChildResources(array $childResources)
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
            'resourceId' => $this->getId(),
            'name'       => $this->getResourceId(),
            'parentId'   => $this->getParent() ?: $this->getParent()->getId(),
        ];
    }
}
