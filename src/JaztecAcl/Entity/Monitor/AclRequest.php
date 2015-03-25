<?php

namespace JaztecAcl\Entity\Monitor;

use Doctrine\ORM\Mapping as ORM;
use JaztecBase\Entity\AbstractEntity;
use JaztecAcl\Entity\Acl\Role;
use JaztecAcl\Entity\Acl\Resource;

/**
 * Description of AclRequest
 *
 * @author Jasper van Herpt <jasper.v.herpt@gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="AclRequests")
 */
class AclRequest extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="Id", type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="DateTime", type="datetime")
     * @var \DateTime
     */
    protected $dateTime;

    /**
     * @ORM\Column(name="Role", type="string")
     * @var string
     */
    protected $role;

    /**
     * @ORM\Column(name="Resource", type="string")
     * @var string
     */
    protected $resource;

    /**
     * @ORM\Column(name="Privilege", type="string")
     * @var string
     */
    protected $privilege;

    /**
     * @ORM\Column(name="Allowed", type="boolean")
     * @var bool
     */
    protected $allowed;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }

    /**
     * @return bool
     */
    public function getAllowed()
    {
        return $this->allowed;
    }

    /**
     * @param \DateTime $dateTime
     * @return \JaztecAcl\Entity\Monitor\AclRequest
     */
    public function setDateTime(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    /**
     * @param string $role
     * @return \JaztecAcl\Entity\Monitor\AclRequest
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * 
     * @param string $resource
     * @return \JaztecAcl\Entity\Monitor\AclRequest
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * 
     * @param string $privilege
     * @return \JaztecAcl\Entity\Monitor\AclRequest
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;
        return $this;
    }

    /**
     * @param bool $allowed
     * @return \JaztecAcl\Entity\Monitor\AclRequest
     */
    public function setAllowed($allowed)
    {
        $this->allowed = $allowed;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [];
    }
}
