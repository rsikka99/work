<?php
use MPSToolbox\Settings\Form\QuoteSettingsForm;

/**
 * Class MPSToolbox_Settings_Form_QuoteSettingsTest
 */
class MPSToolbox_Settings_Form_QuoteSettingsTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * Gets the form to be used in testing
     *
     * @return QuoteSettingsForm
     */
    public function getForm ()
    {
        return new QuoteSettingsForm();
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/files/goodData_QuoteSettingsTest.xml");
    }


    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/files/badData_QuoteSettingsTest.xml");
    }

}