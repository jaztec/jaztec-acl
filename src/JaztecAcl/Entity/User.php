<?php

namespace JaztecAcl\Entity;

use ZfcUser\Entity\UserInterface as ZfcUserInterface;
use Doctrine\ORM\Mapping as ORM;
use JaztecBase\Entity\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends Entity implements ZfcUserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $userID;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $password;

    /**
     * @ORM\Column(name="firstname", type="string")
     *
     * @var string
     */
    protected $firstName;

    /**
     * @ORM\Column(name="lastname", type="string")
     *
     * @var string
     */
    protected $lastName;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $active;

    /**
     * @ORM\Column(name="display_name", type="string")
     *
     * @var string
     */
    protected $displayName;

    /**
     * @ORM\Column(name="role", type="integer")
     *
     * @var int
     */
    protected $roleID;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role", referencedColumnName="id")
     *
     * @var Role
     */
    protected $role;

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
        return $this->userID;
    }

    /**
     * @param  int                    $id
     * @return \JaztecAcl\Entity\User
     */
    public function setId($id)
    {
        $this->userID = (int) $id;

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
     * @return \JaztecAcl\Entity\User
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
     * @return \JaztecAcl\Entity\User
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
     * @return \JaztecAcl\Entity\User
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
     * @return \JaztecAcl\Entity\User
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
     * @return JaztecAcl\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param  \JaztecAcl\Entity\Role $role
     * @return \JaztecAcl\Entity\User
     */
    public function setRole(\JaztecAcl\Entity\Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @param  string                 $lastName
     * @return \JaztecAcl\Entity\User
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
     * @return \JaztecAcl\Entity\User
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
     * @return \JaztecAcl\Entity\User
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @param  int                    $roleID
     * @return \JaztecAcl\Entity\User
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
        return (int) $this->active;
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
    public function serialize()
    {
        return array(
            'UserID' => $this->getId(),
            'Role' => $this->getRole()->getId(),
            'DisplayName' => $this->getDisplayName(),
            'Username' => $this->getUsername(),
            'Email' => $this->getEmail(),
        );
    }

}
