<?php

class Admin_Form_UserFormTest extends PHPUnit_Framework_TestCase
{
    protected $_createform;
    protected $_editform;
    protected $_usereditform;

    public function setUp ()
    {
        $this->_createform = new Admin_Form_User(Admin_Form_User::MODE_CREATE);
        //$this->_createform->render();
        

        $this->_editform = new Admin_Form_User(Admin_Form_User::MODE_EDIT);
        $this->_usereditform = new Admin_Form_User(Admin_Form_User::MODE_USER_EDIT);
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
    public function goodCreateData ()
    {
        return array (
                array (
                        1, 
                        'lrobert', 
                        'tmtwdev', 
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
     * @dataProvider goodCreateData
     */
    public function testFormAcceptsValidDataInCreateForm ($id, $username, $password, $passwordConfirm, $firstname, $lastname, $email, $loginAttempts, $frozenUntil, $locked)
    {
        $data = array (
                'id' => $id, 
                'username' => $username, 
                'password' => $password, 
                'password_confirm' => $passwordConfirm, 
                'firstname' => $firstname, 
                'lastname' => $lastname, 
                'email' => $email, 
                'loginAttempts' => $loginAttempts, 
                'frozenUntil' => $frozenUntil, 
                'locked' => $locked 
        );
        $this->assertTrue($this->_editform->isValid($data), "User form did not accept good data.");
    }

    /**
     * This function returns an array of good data to put into the form
     */
    public function goodEditData ()
    {
        return array (
                array (
                        1, 
                        'lrobert', 
                        'tmtwdev', 
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
     * @dataProvider goodEditData
     */
    public function testFormAcceptsValidDataInEditForm ($id, $username, $password, $passwordConfirm, $firstname, $lastname, $email, $loginAttempts, $frozenUntil, $locked)
    {
        $data = array (
                'id' => $id, 
                'username' => $username, 
                'password' => $password, 
                'password_confirm' => $passwordConfirm, 
                'firstname' => $firstname, 
                'lastname' => $lastname, 
                'email' => $email, 
                'loginAttempts' => $loginAttempts, 
                'frozenUntil' => $frozenUntil, 
                'locked' => $locked 
        );
        $this->assertTrue($this->_editform->isValid($data), "User form did not accept good data.");
    }

    /**
     * This function returns an array of good data to put into the form
     */
    public function goodUserEditData ()
    {
        return array (
                array (
                        1, 
                        'lrobert', 
                        'tmtwdev', 
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
     * @dataProvider goodUserEditData
     */
    public function testFormAcceptsValidDataInUserEditForm ($id, $username, $password, $passwordConfirm, $firstname, $lastname, $email, $loginAttempts, $frozenUntil, $locked)
    {
        $data = array (
                'id' => $id, 
                'username' => $username, 
                'password' => $password, 
                'password_confirm' => $passwordConfirm, 
                'firstname' => $firstname, 
                'lastname' => $lastname, 
                'email' => $email, 
                'loginAttempts' => $loginAttempts, 
                'frozenUntil' => $frozenUntil, 
                'locked' => $locked 
        );
        $this->assertTrue($this->_usereditform->isValid($data), "User form did not accept good data.");
    }

    /**
     * Provides bad data for tests to use
     */
    public function badCreateData ()
    {
        return array (
                array (
                        0, 
                        'lee', 
                        'tmtwdev', 
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
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert at tangentmtw dot com', 
                        0, 
                        null, 
                        false 
                ) 
        );
    }

    /**
     * @dataProvider badCreateData
     */
    public function testFormRejectsBadDataInCreateForm ($id, $username, $password, $passwordConfirm, $firstname, $lastname, $email, $loginAttempts, $frozenUntil, $locked)
    {
        $data = array (
                'id' => $id, 
                'username' => $username, 
                'password' => $password, 
                'password_confirm' => $passwordConfirm, 
                'firstname' => $firstname, 
                'lastname' => $lastname, 
                'email' => $email, 
                'loginAttempts' => $loginAttempts, 
                'frozenUntil' => $frozenUntil, 
                'locked' => $locked 
        );
        $this->assertFalse($this->_createform->isValid($data), "User form accepted bad data! " . var_export($data, true));
    }

    /**
     * Provides bad data for tests to use
     */
    public function badEditData ()
    {
        return array (
                array (
                        0, 
                        'lee', 
                        'tmtwdev', 
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
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        'I AM A BANANA', 
                        false 
                ), 
                array (
                        7, 
                        'lrobert', 
                        'tmtwdev', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        'ABC', 
                        false 
                ), 
                array (
                        8, 
                        'lrobert', 
                        'tmtwdev', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        '2012-27-02 10:10:10', 
                        false 
                ), 
                array (
                        9, 
                        'lrobert', 
                        'tmtwdev', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        '2012-02-02 26:59:59', 
                        false 
                ), 
                array (
                        10, 
                        'lrobert', 
                        'tmtwdev', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        '2012-02-02 23:60:59', 
                        false 
                ), 
                array (
                        11, 
                        'lrobert', 
                        'tmtwdev', 
                        'tmtwdev', 
                        'Lee', 
                        'Robert', 
                        'lrobert@tangentmtw.com', 
                        0, 
                        '2012-02-02 23:59:61', 
                        false 
                ), 
                array (
                        12, 
                        'lrobert', 
                        'tmtwdev', 
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
     * @dataProvider badEditData
     */
    public function testFormRejectsBadDataInEditForm ($id, $username, $password, $passwordConfirm, $firstname, $lastname, $email, $loginAttempts, $frozenUntil, $locked)
    {
        $data = array (
                'id' => $id, 
                'username' => $username, 
                'password' => $password, 
                'password_confirm' => $passwordConfirm, 
                'firstname' => $firstname, 
                'lastname' => $lastname, 
                'email' => $email, 
                'loginAttempts' => $loginAttempts, 
                'frozenUntil' => $frozenUntil, 
                'locked' => $locked 
        );
        $this->assertFalse($this->_editform->isValid($data), "User form accepted bad data! " . var_export($data, true));
    }

    /**
     * Provides bad data for tests to use
     */
    public function badUserEditData ()
    {
        return array (
                array (
                        0, 
                        'Herman', 
                        'i', 
                        'lrobert@tangentmtw.com' 
                ), 
                array (
                        1, 
                        'i', 
                        'Herman', 
                        'lrobert@tangentmtw.com' 
                ), 
                array (
                        2, 
                        'Lee', 
                        'Robert', 
                        'lrobert' 
                ), 
                array (
                        3, 
                        'Lee', 
                        'Robert', 
                        'lrobert at tangentmtw dot com' 
                ) 
        );
    }

    /**
     * @dataProvider badUserEditData
     */
    public function testFormRejectsBadDataInUserEditForm ($id, $firstname, $lastname, $email)
    {
        $data = array (
                'id' => $id, 
                'firstname' => $firstname, 
                'lastname' => $lastname, 
                'email' => $email 
        );
        $this->assertFalse($this->_usereditform->isValid($data), "User form accepted bad data! " . var_export($data, true));
    }
}

