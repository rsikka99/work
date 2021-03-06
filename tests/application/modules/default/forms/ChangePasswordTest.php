<?php
use MPSToolbox\Legacy\Modules\DDefault\Forms\ChangePasswordForm;

/**
 * Class Default_Form_ChangePasswordTest
 */
class Default_Form_ChangePasswordTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{

    public function getForm ()
    {
        return new ChangePasswordForm();
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_ChangePasswordTest.xml");
    }

    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_ChangePasswordTest.xml");
    }
}