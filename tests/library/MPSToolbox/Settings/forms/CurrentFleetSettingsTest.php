<?php
use MPSToolbox\Settings\Form\CurrentFleetSettingsForm;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;

/**
 * Class MPSToolbox_Settings_Form_CurrentFleetSettingsTest
 */
class MPSToolbox_Settings_Form_CurrentFleetSettingsTest extends Tangent_PHPUnit_Framework_ZendFormTestCase
{
    /**
     * Gets the form to be used in testing
     *
     * @return CurrentFleetSettingsForm
     */
    public function getForm ()
    {
        return new CurrentFleetSettingsForm(['tonerVendorList' => [
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
        return $this->loadFromXmlFile(__DIR__ . "/files/goodData_CurrentFleetSettingsTest.xml");
    }


    /**
     * @return array|mixed
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/files/badData_CurrentFleetSettingsTest.xml");
    }

}