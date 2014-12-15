<?php
use MPSToolbox\Settings\Form\AllSettingsForm;

/**
 * Class MPSToolbox_Settings_Form_AllSettingsTest
 */
class MPSToolbox_Settings_Form_AllSettingsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Gets the form to be used in testing
     *
     * @return AllSettingsForm
     */
    public function getForm ()
    {
        return new AllSettingsForm(['tonerVendorList' => [
            1 => 'Vendor 1',
            2 => 'Vendor 2',
            3 => 'Vendor 3',
        ]]);

    }

    /**
     * Tests to make sure AllSettingsForm contains the necessary subForms
     */
    public function testInitializedForms ()
    {
        $form = $this->getForm();
        $this->assertContains($form->currentFleetSettingsForm, $form);
        $this->assertContains($form->genericSettingsForm, $form);
        $this->assertContains($form->optimizationSettingsForm, $form);
        $this->assertContains($form->quoteSettingsForm, $form);
        $this->assertContains($form->proposedFleetSettingsForm, $form);
    }

    /**
     * Test to make sure AllSettingsForm has the toner vendor list
     */
    public function testCanGetTonerVendorList ()
    {
        $form = $this->getForm();
        $this->assertNotEmpty($form->getTonerVendorList());
    }
}