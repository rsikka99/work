<?php
use MPSToolbox\Legacy\Modules\DDefault\Forms\ResetPasswordForm;

/**
 * Class Default_Form_ResetPasswordTest
 */
class Default_Form_ResetPasswordTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * Gets the form to use in the test
     *
     * @return ResetPasswordForm
     */
    public function getForm ()
    {
        return new ResetPasswordForm();
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