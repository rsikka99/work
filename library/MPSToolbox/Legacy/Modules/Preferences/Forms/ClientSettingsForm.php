<?php

namespace MPSToolbox\Legacy\Modules\Preferences\Forms;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use Zend_Form;
use Zend_Form_Element_Text;

/**
 * Class ClientSettingsForm
 *
 * @deprecated this form is not used
 * @package MPSToolbox\Legacy\Modules\Preferences\Forms
 */
class ClientSettingsForm extends Zend_Form
{
    public $allowsNull = false;

    /**
     * @param null|array $options
     */
    public function __construct ($options = null)
    {
        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');

        /**
         * The list of toner vendors that the user is allowed to select
         */
        $tonerVendors = TonerVendorManufacturerMapper::getInstance()->fetchAllForDealerDropdown();

        /**
         * Current Fleet Settings
         */
        $this->addElement('text', 'currentTonerMargin', [
            'label'       => 'Pricing Margin on Toners',
            'description' => 'The margin percentage to add onto the current toner cost in the system.',
            'required'    => true,
        ]);

        $this->addElement('checkbox', 'currentUseDevicePageCoverages', [
            'label'       => 'Use Device Page Coverages',
            'description' => 'When checked this will cause page coverages reported by the device to be used, if available.',
            'required'    => true,
        ]);

        $this->addElement('text', 'currentPageCoverageMono', [
            'label'       => 'Default Monochrome Coverage',
            'description' => 'Assumed page coverage for monochrome pages.',
            'required'    => true,
        ]);

        $this->addElement('text', 'currentPageCoverageColor', [
            'label'       => 'Default Color Coverage',
            'description' => 'Assumed page coverage for color pages. This is the sum of the CMYK page coverages.',
            'required'    => true,
        ]);

        $this->addElement('text', 'currentLeasedMonochromeCostPerPage', [
            'label'       => 'Current Leased Monochrome Cost',
            'description' => 'Used as a cost per page assumption for devices marked as leased.',
            'required'    => true,
        ]);

        $this->addElement('text', 'currentLeasedColorCostPerPage', [
            'label'       => 'Current Leased Color Cost',
            'description' => 'Used as a cost per page assumption for devices marked as leased.',
            'required'    => true,
        ]);

        $this->addElement('text', 'currentMpsMonochromeCostPerPage', [
            'label'       => 'Current MPS Monochrome Cost',
            'description' => 'Used as a cost per page assumption for devices marked as managed.',
            'required'    => true,
        ]);

        $this->addElement('text', 'currentMpsColorCostPerPage', [
            'label'       => 'Current MPS Color Cost',
            'description' => 'Used as a cost per page assumption for devices marked as managed.',
            'required'    => true,
        ]);

        $this->addElement('multiselect', 'currentMonochromeRankSetArray', [
            'label'        => 'Monochrome Toner Vendors',
            'description'  => 'Devices will search for toners in order of selection from top to bottom with OEM being always being used last.',
            'class'        => 'tonerMultiselect',
            'required'     => true,
            'multiOptions' => $tonerVendors,
        ]);

        $this->addElement('multiselect', 'currentColorRankSetArray', [
            'label'        => 'Color Toner Vendors',
            'description'  => 'Devices will search for toners in order of selection from top to bottom with OEM being always being used last.',
            'class'        => 'tonerMultiselect',
            'required'     => true,
            'multiOptions' => $tonerVendors,
        ]);

        /**
         * New Fleet Settings
         */
        $this->addElement('checkbox', 'proposedUseDevicePageCoverages', [
            'label'       => 'Use Device Page Coverages',
            'description' => 'When checked this will cause page coverages reported by the device to be used, if available.',
            'required'    => true,
        ]);

        $this->addElement('text', 'proposedPageCoverageMono', [
            'label'       => 'Default Monochrome Coverage',
            'description' => 'Assumed page coverage for monochrome pages.',
            'required'    => true,
        ]);

        $this->addElement('text', 'proposedPageCoverageColor', [
            'label'       => 'Default Color Coverage',
            'description' => 'Assumed page coverage for color pages. This is the sum of the CMYK page coverages.',
            'required'    => true,
        ]);

        $this->addElement('text', 'proposedDefaultAdminCostPerPage', [
            'label'       => 'Admin Cost Per Page',
            'description' => 'You can specify an additional cost per page here. It is applied to all devices.',
            'required'    => true,
        ]);

        $this->addElement('text', 'proposedDefaultLaborCostPerPage', [
            'label'       => 'Labor Cost Per Page',
            'description' => 'The default labor cost per page to apply to devices.',
            'required'    => true,
        ]);

        $this->addElement('text', 'proposedDefaultPartsCostPerPage', [
            'label'       => 'Parts Cost Per Page',
            'description' => 'The default parts cost per page to apply to devices.',
            'required'    => true,
        ]);

        $this->addElement('select', 'proposedLevel', [
            'label'        => 'Pricing Level',
            'multiOptions' => [''=>'Standard', 'level1'=>'Level 1', 'level2'=>'Level 2', 'level3'=>'Level 3', 'level4'=>'Level 4', 'level5'=>'Level 5'],
        ]);

        $this->addElement('multiselect', 'proposedMonochromeRankSetArray', [
            'label'        => 'Monochrome Toner Vendors',
            'description'  => 'Devices will search for toners in order of selection from top to bottom with OEM always used last.',
            'class'        => 'tonerMultiselect',
            'required'     => true,
            'multiOptions' => $tonerVendors,
        ]);

        $this->addElement('multiselect', 'proposedColorRankSetArray', [
            'label'        => 'Color Toner Vendors',
            'description'  => 'Devices will search for toners in order of selection from top to bottom with OEM always used last.',
            'class'        => 'tonerMultiselect',
            'required'     => true,
            'multiOptions' => $tonerVendors,
        ]);


        /**
         * Generic Settings
         */
        $this->addElement('text', 'defaultMonthlyLeasePayment', [
            'label'       => 'Monthly Lease Payment',
            'description' => 'Used on the assessment to calculate the approximate annual leasing cost of devices marked as leased.',
            'required'    => true,
        ]);

        $this->addElement('text', 'defaultPrinterCost', [
            'label'       => 'Default Printer Cost',
            'description' => 'Used on the assessment to calculate the approximate annual cost of device purchases.',
            'required'    => true,
        ]);


        $this->addElement('text', 'defaultEnergyCost', [
            'label'       => 'Energy Cost $/kWh',
            'description' => 'Used to calculate the approximate cost of energy used by devices.',
            'required'    => true,
        ]);


        /**
         * Hardware Quote Default Settings
         */
        $this->addElement('text', 'defaultDeviceMargin', [
            'label'    => 'Default Margin on Devices',
            'required' => true,
        ]);

        $this->addElement('text', 'defaultPageMargin', [
            'label'    => 'Default Margin on Pages',
            'required' => true,
        ]);

        /**
         * Optimization Settings
         */
        $this->addElement('text', 'optimizedTargetMonochromeCostPerPage', [
            'label'    => 'Post-Optimized Monochrome CPP',
            'required' => true,
        ]);

        $this->addElement('text', 'optimizedTargetColorCostPerPage', [
            'label'    => 'Post-Optimized Color CPP',
            'required' => true,
        ]);

        $this->addElement('text', 'costThreshold', [
            'label'       => 'Minimum Device savings',
            'description' => 'Replace all devices where your proposed <strong>Device Swaps</strong> can save you more than this number <strong>per month</strong>.',
            'required'    => true,
        ]);

        $this->addElement('checkbox', 'autoOptimizeFunctionality', [
            'label'       => 'Monochrome to Color Upgrade',
            'description' => 'Automatically upgrade devices based on functionality.',
        ]);

        $this->addElement('text', 'lossThreshold', [
            'label'       => 'Upgrade Loss Threshold',
            'description' => 'Do not upgrade monochrome devices with color devices if they will increase the the monthly cost by more than amount this each month.',
            'required'    => true,
        ]);

        $this->addElement('text', 'blackToColorRatio', [
            'label'       => 'Monochrome to Color Ratio',
            'description' => "Assume new color devices that replace monochrome devices will print this percentage of color pages.",
            'required'    => true,
        ]);

        $this->addElement('multiselect', 'deviceSwapMonochromeRankSetArray', [
            'label'        => 'Monochrome Toner Vendors',
            'description'  => 'Devices will search for toners in order of selection from top to bottom with OEM being always being used last.',
            'class'        => 'tonerMultiselect',
            'multiOptions' => $tonerVendors,
        ]);

        $this->addElement('multiselect', 'deviceSwapColorRankSetArray', [
            'label'        => 'Color Toner Vendors',
            'description'  => 'Devices will search for toners in order of selection from top to bottom with OEM being always being used last.',
            'class'        => 'tonerMultiselect',
            'multiOptions' => $tonerVendors,
        ]);

        /**
         * Healthcheck Settings
         */


        /**
         * Form Actions
         */
        $this->addElement('submit', 'save', [
            'label' => 'Submit',
            'class' => 'btn btn-primary',
        ]);

        $this->addElement('submit', 'cancel', [
            'label' => 'Cancel',
            'class' => 'btn btn-default'
        ]);
    }

    /**
     * Allows the form to allow null values
     */
    public function allowNullValues ()
    {
        /* @var Zend_Form_Element_Text $element */
        foreach ($this->getElements() as $element)
        {
            $element->setRequired(false);
        }

        $this->allowsNull = true;
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/settings/client-settings-form.phtml']]]);
    }
}