<?php

use MPSToolbox\Settings\Form\OptimizationSettingsForm;

/**
 * Class MPSToolbox_Settings_Form_OptimizationSettingsTest
 */
class MPSToolbox_Settings_Form_OptimizationSettingsTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * Gets the form to be used in testing
     *
     * @return OptimizationSettingsForm
     */
    public function getForm ()
    {
        return new OptimizationSettingsForm(['tonerVendorList' => [
            1 => 'Vendor 1',
            2 => 'Vendor 2',
            3 => 'Vendor 3',
        ]]);
    }

    /**
     * @return array|mixed
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/files/goodData_OptimizationSettingsTest.xml");
    }


    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/files/badData_OptimizationSettingsTest.xml");
    }

}