<?php
use MPSToolbox\Settings\Form\GenericSettingsForm;

/**
 * Class MPSToolbox_Settings_Form_GenericSettingsTest
 */
class MPSToolbox_Settings_Form_GenericSettingsTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * Gets the form to be used in testing
     *
     * @return GenericSettingsForm
     */
    public function getForm ()
    {
        return new GenericSettingsForm();
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/files/goodData_GenericSettingsTest.xml");
    }


    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/files/badData_GenericSettingsTest.xml");
    }

}