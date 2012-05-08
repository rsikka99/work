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
        $this->assertSame(0, $this->_user->getId(), "Id value is not the correct default value");
        $this->assertSame("", $this->_user->getUsername(), "Username value is not the correct default value");
        $this->assertSame("", $this->_user->getPassword(), "Password value is not the correct default value");
        $this->assertSame("", $this->_user->getFirstname(), "Firstname value is not the correct default value");
        $this->assertSame("", $this->_user->getLastname(), "Lastname value is not the correct default value");
        $this->assertSame("", $this->_user->getEmail(), "Email value is not the correct default value");
        $this->assertSame(0, $this->_user->getLoginAttempts(), "LoginAttempts value is not the correct default value");
        $this->assertNull($this->_user->getFrozenUntil(), "FrozenUntil value is not null");
        $this->assertSame(0, $this->_user->getLocked(), "Locked value is not the correct default value");
    }

    /**
     * This function returns an array of good data to put into the form
     */
    public function goodData ()
    {
        return array (
                array (
                        1, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        null, 
                        false 
                ), 
                array (
                        297234879, 
                        'jdoe', 
                        '1337prog', 
                        'John', 
                        'Doe', 
                        'jdoe@pl.pl', 
                        55, 
                        '2012-10-12 12:30:00', 
                        true 
                ), 
                array (
                        1, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        null, 
                        false 
                ), 
                array (
                        1, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        null, 
                        false 
                ), 
                array (
                        1, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        null, 
                        false 
                ) 
        );
    }

    /**
     * @dataProvider goodData
     */
    public function testModelAcceptsValidData ($id, $username, $password, $firstname, $lastname, $email, $loginAttempts, $frozenUntil, $locked)
    {
        $data = array (
                'id' => $id, 
                'username' => $username, 
                'password' => $password, 
                'firstname' => $firstname, 
                'lastname' => $lastname, 
                'email' => $email, 
                'loginAttempts' => $loginAttempts, 
                'frozenUntil' => $frozenUntil, 
                'locked' => $locked 
        );
        try
        {
            $this->_user->populate($data);
        }
        catch ( Exception $e )
        {
            $this->fail('Unexpected exception should not be triggered. ' . $e->getMessage());
        }
    }

    /**
     * Provides bad data for tests to use
     */
    public function badData ()
    {
        return array (
                array (
                        1, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        null, 
                        false 
                ) 
        );
    }

    /**
     * @dataProvider badData
     */
    public function testModelRejectsBadData ($id, $username, $password, $firstname, $lastname, $email, $loginAttempts, $frozenUntil, $locked)
    {
        
        $data = array (
                'id' => $id, 
                'username' => $username, 
                'password' => $password, 
                'firstname' => $firstname, 
                'lastname' => $lastname, 
                'email' => $email, 
                'loginAttempts' => $loginAttempts, 
                'frozenUntil' => $frozenUntil, 
                'locked' => $locked 
        );
        try
        {
            $this->_user->populate($data);
        }
        catch ( Exception $e )
        {
            return;
        }
        $this->fail('An exception should have been triggered by bad data');
    }

}

