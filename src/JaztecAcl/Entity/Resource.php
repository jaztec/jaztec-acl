<?php

namespace JaztecAcl\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Permissions\Acl\Resource\ResourceInterface as ZendResourceInterface;
use JaztecBase\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="acl_resources")
 */
class Resource extends AbstractEntity implements ZendResourceInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Resource")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     *
     * @var \JaztecAcl\Entity\Resource
     */
    protected $parent;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $sort;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  int      $id
     * @return Resource
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
     * @param  string   $name
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
     * @param  \JaztecAcl\Entity\Resource $parent
     * @return Resource
     */
    public function setParent(\JaztecAcl\Entity\Resource $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Resource
     */
    public function clearParent()
    {
        $this->parent = null;

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
     * @param  int      $sort
     * @return Resource
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
     * @return array
     */
    public function toArray()
    {
        return array(
            'ResourceID' => $this->getId(),
            'Name'       => $this->getResourceId(),
            'ParentID'   => $this->getParent() === null ? null : $this->getParent()->getId(),
            'Parent'     => $this->getParent() === null ? null : $this->getParent()->getResourceId(),
        );
    }
}
