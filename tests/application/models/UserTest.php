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
                        'dnikolopoulos', 
                        'tmtwdev', 
                        'Demetra', 
                        'Nikolopoulos', 
                        'dnikolopoulos@tangentmtw.com', 
                        0, 
                        '2011-12-12 00:00:00', 
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
                        '2011-02-28 13:59:59', 
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
                        '2011-12-12 13:59:59', 
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
                        0, 
                        'lee', 
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
                        'tmt', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        null, 
                        false 
                ), 
                array (
                        2, 
                        'lrobert', 
                        'tmtwdev', 
                        'Herman', 
                        'i', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        null, 
                        false 
                ), 
                array (
                        3, 
                        'lrobert', 
                        'tmtwdev', 
                        'i', 
                        'Herman', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        null, 
                        false 
                ), 
                array (
                        4, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert', 
                        0, 
                        null, 
                        false 
                ), 
                array (
                        5, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert at tangentmtw dot com', 
                        0, 
                        null, 
                        false 
                ), 
                array (
                        6, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        - 1, 
                        null, 
                        false 
                ), 
                array (
                        7, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        'I AM A BANANA', 
                        false 
                ), 
                array (
                        8, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        'ABC', 
                        false 
                ), 
                array (
                        9, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        '2012-27-02 10:10:10', 
                        false 
                ), 
                array (
                        10, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        '2012-02-02 26:59:59', 
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
                        '2012-02-02 23:60:59', 
                        false 
                ), 
                array (
                        12, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        '2012-02-02 23:59:61', 
                        false 
                ), 
                array (
                        13, 
                        'lrobert', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        '2012-02-32 23:59:59', 
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

