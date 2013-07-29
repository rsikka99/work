<?php

class Application_Model_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Application_Model_User
     */
    protected $_user;

    public function setUp ()
    {
        $this->_user = new Application_Model_User();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_user = null;
    }

    public function testModelIsEmptyAtConstruct ()
    {
        $data     = array(
            'id'                       => null,
            'password'                 => null,
            'firstname'                => null,
            'lastname'                 => null,
            'email'                    => null,
            'frozenUntil'              => null,
            'loginAttempts'            => 0,
            'resetPasswordOnNextLogin' => 0,
            'eulaAccepted'             => null,
            'locked'                   => 0,
            'dealerId'                 => null
        );
        $userData = $this->_user->toArray();
        $this->assertSame($data, $userData);
    }
}

