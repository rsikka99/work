<?php

class Default_Form_LoginFormTest extends PHPUnit_Framework_TestCase
{
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Default_Form_Login();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    public function testCanRunPHPUNIT ()
    {
        $this->assertTrue(true, "This should never fail unless unit testing is broken");
    }

    /**
     * This function returns an array of good data to put into the form
     */
    public function goodData ()
    {
        return array(
            array(
                'lrobert@tangentmtw.com',
                'somepassword'
            ),
            array(
                'swilder@tangentmtw.com',
                'O1as94_adsf#@'
            ),
            array(
                'someuser44@example.com',
                '75647564'
            )
        );
    }

    /**
     * @dataProvider goodData
     */
    public function testFormAcceptsValidData ($username, $password)
    {
        $data = array(
            'email'    => $username,
            'password' => $password
        );
        $this->assertTrue($this->_form->isValid($data), "Login form did not accept good data. {$username} {$password}");
    }

    /**
     * Provides bad data for tests to use
     */
    public function badData ()
    {
        return array(
            array(
                '',
                ''
            ),
            array(
                'lrobert',
                ''
            ),
            array(
                '',
                'somepassword'
            ),
            array(
                "lrobert'; DROP TABLE users; --",
                'somepassword'
            ),
            array(
                'asdf!@#$%^&*(*',
                '�6��'
            ),
            array(
                'goodusername',
                '�6��'
            ),
            array(
                'asdf!@#$%^&*(*',
                'goodpassword'
            ),
            array(
                'lrobert',
                'goodpassword'
            )
        );
    }

    /**
     * @dataProvider badData
     */
    public function testFormRejectsBadData ($username, $password)
    {
        $data = array(
            'username' => $username,
            'password' => $password
        );
        $this->assertFalse($this->_form->isValid($data), "Login form accepted bad data! {$username} {$password}");
    }

}

