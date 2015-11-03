<?php

namespace MPSToolbox\Entities;

/**
 * Class UserEntity
 * @package MPSToolbox\Entities
 *
 * @Entity
 * @Table(name="users")
 */
class UserEntity extends BaseEntity {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="DealerEntity")
     * @JoinColumn(name="dealerId", referencedColumnName="id")
     **/
    private $dealer;

    /** @Column(type="string") */
    private $email;

    /** @Column(type="string") */
    private $password;

    /** @Column(type="string") */
    private $firstname;

    /** @Column(type="string") */
    private $lastname;

    /** @Column(type="integer") */
    private $loginAttempts;

    /** @Column(type="datetime") */
    private $frozenUntil;

    /** @Column(type="boolean") */
    private $locked;

    /** @Column(type="datetime") */
    private $eulaAccepted;

    /** @Column(type="boolean") */
    private $resetPasswordOnNextLogin;

    /** @Column(type="datetime") */
    private $passwordResetRequest;

    /** @Column(type="datetime") */
    private $lastSeen;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDealer()
    {
        return $this->dealer;
    }

    /**
     * @param mixed $dealer
     */
    public function setDealer($dealer)
    {
        $this->dealer = $dealer;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getLoginAttempts()
    {
        return $this->loginAttempts;
    }

    /**
     * @param mixed $loginAttempts
     */
    public function setLoginAttempts($loginAttempts)
    {
        $this->loginAttempts = $loginAttempts;
    }

    /**
     * @return mixed
     */
    public function getFrozenUntil()
    {
        return $this->frozenUntil;
    }

    /**
     * @param mixed $frozenUntil
     */
    public function setFrozenUntil($frozenUntil)
    {
        $this->frozenUntil = $frozenUntil;
    }

    /**
     * @return mixed
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * @param mixed $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * @return mixed
     */
    public function getEulaAccepted()
    {
        return $this->eulaAccepted;
    }

    /**
     * @param mixed $eulaAccepted
     */
    public function setEulaAccepted($eulaAccepted)
    {
        $this->eulaAccepted = $eulaAccepted;
    }

    /**
     * @return mixed
     */
    public function getResetPasswordOnNextLogin()
    {
        return $this->resetPasswordOnNextLogin;
    }

    /**
     * @param mixed $resetPasswordOnNextLogin
     */
    public function setResetPasswordOnNextLogin($resetPasswordOnNextLogin)
    {
        $this->resetPasswordOnNextLogin = $resetPasswordOnNextLogin;
    }

    /**
     * @return mixed
     */
    public function getPasswordResetRequest()
    {
        return $this->passwordResetRequest;
    }

    /**
     * @param mixed $passwordResetRequest
     */
    public function setPasswordResetRequest($passwordResetRequest)
    {
        $this->passwordResetRequest = $passwordResetRequest;
    }

    /**
     * @return mixed
     */
    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    /**
     * @param mixed $lastSeen
     */
    public function setLastSeen($lastSeen)
    {
        $this->lastSeen = $lastSeen;
    }



}