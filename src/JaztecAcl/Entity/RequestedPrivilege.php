<?php

namespace JaztecAcl\Entity;

use Doctrine\ORM\Mapping as ORM;
use JaztecBase\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="acl_requested_privileges")
 */
class RequestedPrivilege extends AbstractEntity
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
    protected $privilege;

    /**
     * @ORM\Column(type="string")
     * 
     * @var string
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
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * @param  string                      $privilege
     * @return \JaztecAcl\Entity\RequestedPrivilege
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;

        return $this;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param  string  $resource
     * @return \JaztecAcl\Entity\RequestedPrivilege
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'           => $this->getId(),
            'Privilege'    => $this->getPrivilege(),
            'Resource'     => $this->getResource(),
        );
    }
}
