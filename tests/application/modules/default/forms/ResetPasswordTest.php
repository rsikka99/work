<?php

/**
 * Class Default_Form_ResetPasswordTest
 */
class Default_Form_ResetPasswordTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * Gets the form to use in the test
     *
     * @return Default_Form_ResetPassword
     */
    public function getForm ()
    {
        return new Default_Form_ResetPassword();
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_ResetPasswordTest.xml");

    }

    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_ResetPasswordTest.xml");

    }

}