<?php

namespace JaztecAcl\Entity\Auth;

use ZfcUser\Entity\UserInterface as ZfcUserInterface;
use Doctrine\ORM\Mapping as ORM;
use JaztecBase\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="AclUsers")
 */
class User extends AbstractEntity implements ZfcUserInterface
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
     * @ORM\Column(name="Username", type="string")
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(name="Email", type="string")
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(name="Password", type="string")
     *
     * @var string
     */
    protected $password;

    /**
     * @ORM\Column(name="FirstName", type="string")
     *
     * @var string
     */
    protected $firstName;

    /**
     * @ORM\Column(name="LastName", type="string")
     *
     * @var string
     */
    protected $lastName;

    /**
     * @ORM\Column(name="Active", type="boolean")
     *
     * @var bool
     */
    protected $active;

    /**
     * @ORM\Column(name="DisplayName", type="string")
     *
     * @var string
     */
    protected $displayName;

    /**
     * @ORM\ManyToOne(targetEntity="JaztecAcl\Entity\Acl\Role")
     * @ORM\JoinColumn(name="RoleId", referencedColumnName="Id")
     *
     * @var Role
     */
    protected $role;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Default waarden zetten
        $this->setActive(true)
            ->setFirstName('Gast')
            ->setLastName('')
            ->setRoleID(1);
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
     * @return \JaztecAcl\Entity\Auth\User
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param  string                 $username
     * @return \JaztecAcl\Entity\Auth\User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param  string                 $email
     * @return \JaztecAcl\Entity\Auth\User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param  string                 $password
     * @return \JaztecAcl\Entity\Auth\User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param  string                 $firstName
     * @return \JaztecAcl\Entity\Auth\User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return JaztecAcl\Entity\Acl\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param  \JaztecAcl\Entity\Acl\Role $role
     * @return \JaztecAcl\Entity\Auth\User
     */
    public function setRole(\JaztecAcl\Entity\Acl\Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @param  string                 $lastName
     * @return \JaztecAcl\Entity\Auth\User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param  bool                   $active
     * @return \JaztecAcl\Entity\Auth\User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param  string                 $displayName
     * @return \JaztecAcl\Entity\Auth\User
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @param  int                    $roleID
     * @return \JaztecAcl\Entity\Auth\User
     */
    protected function setRoleID($roleID)
    {
        $this->roleID = (int) $roleID;

        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->active ? 1 : 0;
    }

    /**
     * Set state.
     *
     * @param  int           $state
     * @return UserInterface
     */
    public function setState($state)
    {
        $this->active = (bool) $state;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'          => $this->getId(),
            'role'        => $this->getRole()->getId(),
            'displayName' => $this->getDisplayName(),
            'username'    => $this->getUsername(),
            'email'       => $this->getEmail(),
        );
    }
}
