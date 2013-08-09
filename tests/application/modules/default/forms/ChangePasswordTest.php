<?php

/**
 * Class Default_Form_ChangePasswordTest
 */
class Default_Form_ChangePasswordTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Default_Form_ChangePassword
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Default_Form_ChangePassword();
        parent::setUp();
    }

    public function tearDown ()
    {
        parent::tearDown();
        $this->_form = null;
    }

    /**
     * This function returns an array of good data to put into the form
     *
     * @return array
     */
    public function goodData ()
    {
        return array(
            array(
                'asdfasdf',
                'jimmy99',
                'jimmy99'
            ),
            array(
                'asdfasdf',
                'C0Q6030IF22PXIJZHXT77LJL3D8AK7HEKMUFE10ODBWKJWBBWZLQN5WGRMKYM3I1ATE6ANG89UFEKBF9',
                'C0Q6030IF22PXIJZHXT77LJL3D8AK7HEKMUFE10ODBWKJWBBWZLQN5WGRMKYM3I1ATE6ANG89UFEKBF9'
            ),
            array(
                'asdfasdf',
                'bob678',
                'bob678'
            )
        );
    }

    /**
     * Test the form using valid data
     *
     *
     * @dataProvider goodData
     */
    public function testFormAcceptsValidData ($currentPassword, $password, $passwordConfirm)
    {
        $data = array(
            'current_password' => $currentPassword,
            'password'         => $password,
            'password_confirm' => $passwordConfirm
        );
        $this->assertTrue($this->_form->isValid($data), "Change password form did not accept good data.");
    }

    /**
     *  This function returns an array of bad data to put into the form
     *
     * @return array
     */
    public function badData ()
    {
        return array(
            array(
                'adsf',
                'joe12345',
                'joe12345'
            ),
            array(
                'asdfasdf',
                'jimmy99',
                'joe12345'
            ),
            array(
                'asdfasdf',
                'joe',
                'joe'
            ), array(
                'asdfasdf',
                'C0Q6030IF22PXIJZHXT77LJL3D8AK7HEKMUFE10ODBWKJWBBWZLQN5WGRMKYM3I1ATE6ANG89UFEKBF91',
                'C0Q6030IF22PXIJZHXT77LJL3D8AK7HEKMUFE10ODBWKJWBBWZLQN5WGRMKYM3I1ATE6ANG89UFEKBF91'
            ),
        );
    }

    /**
     * Test the form using bad data
     *
     * @dataProvider badData
     */
    public function testFormRejectsBadData ($currentPassword, $password, $passwordConfirm)
    {
        $data = array(
            'current_password' => $currentPassword,
            'password'         => $password,
            'password_confirm' => $passwordConfirm
        );
        $this->assertFalse($this->_form->isValid($data), "Change password form accepted bad data!");
    }

}