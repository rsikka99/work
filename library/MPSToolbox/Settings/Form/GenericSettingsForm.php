<?php

namespace MPSToolbox\Settings\Form;

use MPSToolbox\Settings\Entities\GenericSettingsEntity;

/**
 * Class GenericSettingsForm
 *
 * @package MPSToolbox\Settings\Form
 */
class GenericSettingsForm extends \Zend_Form
{
    /**
     * Initializes the form with all the elements required
     */
    public function init ()
    {
        $this->setMethod('post');

        /**
         * Generic Settings
         */
        $this->addElement('text', 'defaultEnergyCost', array(
            'label'       => 'Energy Cost $/kWh',
            'description' => 'Used to calculate the approximate cost of energy used by devices.',
            'required'    => true,
        ));

        $this->addElement('text', 'defaultMonthlyLeasePayment', array(
            'label'       => 'Monthly Lease Payment',
            'description' => 'Used on the assessment to calculate the approximate annual leasing cost of devices marked as leased.',
            'required'    => true,
        ));

        $this->addElement('text', 'defaultPrinterCost', array(
            'label'       => 'Default Printer Cost',
            'description' => 'Used on the assessment to calculate the approximate annual cost of device purchases.',
            'required'    => true,
        ));

        $this->addElement('text', 'leasedMonochromeCostPerPage', array(
            'label'       => 'Leased Monochrome CPP',
            'description' => 'Any monochrome devices marked as <strong>leased</strong> will use this cost per page instead.',
            'required'    => true,
        ));

        $this->addElement('text', 'leasedColorCostPerPage', array(
            'label'       => 'Leased Color CPP',
            'description' => 'Any color devices marked as <strong>leased</strong> will use this cost per page instead.',
            'required'    => true,
        ));

        $this->addElement('text', 'mpsMonochromeCostPerPage', array(
            'label'       => 'Existing MPS Monochrome CPP',
            'description' => 'Any monochrome devices marked as <strong>managed</strong> will use this cost per page instead.',
            'required'    => true,
        ));

        $this->addElement('text', 'mpsColorCostPerPage', array(
            'label'       => 'Existing MPS Color CPP',
            'description' => 'Any color devices marked as <strong>managed</strong> will use this cost per page instead.',
            'required'    => true,
        ));

        $this->addElement('text', 'tonerMargin', array(
            'label'       => 'Pricing Margin on Toners',
            'description' => 'The margin percentage to add onto the current toner cost in the system.',
        ));

        $this->addElement('text', 'targetMonochromeCostPerPage', array(
            'label'       => 'Pre-Optimized Monochrome CPP',
            'description' => 'The cost per page you\'d like to charge the customer.',
            'required'    => true,
        ));

        $this->addElement('text', 'targetColorCostPerPage', array(
            'label'       => 'Pre-Optimized Color CPP',
            'description' => 'The cost per page you\'d like to charge the customer.',
            'required'    => true,
        ));

        /**
         * Form Actions
         */
        $this->addElement('submit', 'save', array(
            'label' => 'Submit',
            'class' => 'btn btn-primary',
        ));

        $this->addElement('submit', 'cancel', array(
            'label' => 'Cancel',
            'class' => 'btn btn-default'
        ));
    }

    /**
     * @param array|GenericSettingsEntity $genericSettings
     */
    public function populateGenericSettings ($genericSettings)
    {
        if ($genericSettings instanceof GenericSettingsEntity)
        {
            $genericSettings = array(
                'defaultMonthlyLeasePayment'  => $genericSettings->defaultMonthlyLeasePayment,
                'defaultPrinterCost'          => $genericSettings->defaultPrinterCost,
                'defaultEnergyCost'           => $genericSettings->defaultEnergyCost,
                'leasedMonochromeCostPerPage' => $genericSettings->leasedMonochromeCostPerPage,
                'leasedColorCostPerPage'      => $genericSettings->leasedColorCostPerPage,
                'mpsMonochromeCostPerPage'    => $genericSettings->mpsMonochromeCostPerPage,
                'mpsColorCostPerPage'         => $genericSettings->mpsColorCostPerPage,
                'tonerMargin'                 => $genericSettings->tonerPricingMargin,
                'targetMonochromeCostPerPage' => $genericSettings->targetMonochromeCostPerPage,
                'targetColorCostPerPage'      => $genericSettings->targetColorCostPerPage,
            );
        }

        if ($genericSettings)
        {
            $this->populate($genericSettings);
        }
    }

    /**
     * Returns a populated model
     *
     * @param null $model
     *
     * @return GenericSettingsEntity|null
     */
    public function getGenericSettings ($model = null)
    {
        if (!$model instanceof GenericSettingsEntity)
        {
            $model = new GenericSettingsEntity();
        }

        $model->defaultMonthlyLeasePayment  = $this->getValue('defaultMonthlyLeasePayment');
        $model->defaultPrinterCost          = $this->getValue('defaultPrinterCost');
        $model->defaultEnergyCost           = $this->getValue('defaultEnergyCost');
        $model->leasedMonochromeCostPerPage = $this->getValue('leasedMonochromeCostPerPage');
        $model->leasedColorCostPerPage      = $this->getValue('leasedColorCostPerPage');
        $model->mpsMonochromeCostPerPage    = $this->getValue('mpsMonochromeCostPerPage');
        $model->mpsColorCostPerPage         = $this->getValue('mpsColorCostPerPage');
        $model->tonerPricingMargin          = $this->getValue('tonerMargin');
        $model->targetMonochromeCostPerPage = $this->getValue('targetMonochromeCostPerPage');
        $model->targetColorCostPerPage      = $this->getValue('targetColorCostPerPage');

        return $model;
    }

    /**
     * Overrides the parent to enforce a view script to render the form
     *
     * @return void|\Zend_Form
     */
    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/client-settings-form.phtml'
                )
            )
        ));
    }
}