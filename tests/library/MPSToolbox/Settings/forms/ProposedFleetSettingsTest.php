<?php
use MPSToolbox\Settings\Form\ProposedFleetSettingsForm;

/**
 * Class MPSToolbox_Settings_Form_ProposedFleetSettingsTest
 */
class MPSToolbox_Settings_Form_ProposedFleetSettingsTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * Gets the form to be used in testing
     *
     * @return ProposedFleetSettingsForm
     */
    public function getForm ()
    {
        return new ProposedFleetSettingsForm(['tonerVendorList' => [
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
        return $this->loadFromXmlFile(__DIR__ . "/files/goodData_ProposedFleetSettingsTest.xml");
    }


    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/files/badData_ProposedFleetSettingsTest.xml");
    }

}