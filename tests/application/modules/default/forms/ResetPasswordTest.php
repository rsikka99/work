<?php

/**
 * Class Default_Form_ResetPasswordTest
 */
class Default_Form_ResetPasswordTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Default_Form_ResetPassword
     */
    protected $_form;

    public function setUp ()
    {
        $this->_form = new Default_Form_ResetPassword();
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
                'jimmy99',
                'jimmy99'
            ),
            array(
                'C0Q6030IF22PXIJZHXT77LJL3D8AK7HEKMUFE10ODBWKJWBBWZLQN5WGRMKYM3I1ATE6ANG89UFEKBF9',
                'C0Q6030IF22PXIJZHXT77LJL3D8AK7HEKMUFE10ODBWKJWBBWZLQN5WGRMKYM3I1ATE6ANG89UFEKBF9'
            ),
            array(
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
    public function testFormAcceptsValidData ($password, $passwordConfirm)
    {
        $data = array(
            'password'         => $password,
            'password_confirm' => $passwordConfirm
        );
        $this->assertTrue($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
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
                'joe12345',
                'joe1234'
            ),
            array(
                'jimmy99',
                'joe12345'
            ),
            array(
                'joe',
                'joe'
            ), array(
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
    public function testFormRejectsBadData ($password, $passwordConfirm)
    {
        $data = array(
            'password'         => $password,
            'password_confirm' => $passwordConfirm
        );
        $this->assertFalse($this->_form->isValid($data), implode(' | ', $this->_form->getErrorMessages()));
    }

}