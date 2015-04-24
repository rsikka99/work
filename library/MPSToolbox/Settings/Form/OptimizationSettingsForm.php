<?php

namespace MPSToolbox\Settings\Form;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingModel;
use MPSToolbox\Settings\Entities\OptimizationSettingsEntity as OptimizationSettingsEntity;

/**
 * Class OptimizationSettingsForm
 *
 * @package MPSToolbox\Settings\Form
 */
class OptimizationSettingsForm extends \Zend_Form
{
    /**
     * @var array
     */
    protected $tonerVendorList;

    /**
     * Initializes the form with all the elements required
     */
    public function init ()
    {
        $this->setMethod('post');

        /**
         * The list of toner vendors that the user is allowed to select
         */
        $tonerVendors = $this->getTonerVendorList();
        /**
         * Optimization Settings
         */
        $this->addElement('text', 'optimizedTargetMonochromeCostPerPage', [
            'label'    => 'Post-Optimized Monochrome CPP',
            'required' => true,
        ]);

        $this->addElement('text', 'optimizedTargetColorCostPerPage', array(
            'label'    => 'Post-Optimized Color CPP',
            'required' => true,
        ));

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

        $this->addElement('text', 'minimumPageCount', [
            'label'       => 'Minimum Monthly Page Count',
            'description' => "The minimum page count a device must be printing in order to be considered for upgrade.",
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
         * Form Actions
         */
        $this->addElement('submit', 'save', [
            'label' => 'Submit',
            'class' => 'btn btn-primary',
        ]);

        $this->addElement('submit', 'cancel', [
            'label' => 'Cancel',
            'class' => 'btn btn-default',
        ]);
    }

    /**
     * @param array|OptimizationSettingsEntity $optimizationSettings
     */
    public function populateOptimizationSettings ($optimizationSettings)
    {
        if ($optimizationSettings instanceof OptimizationSettingsEntity)
        {
            $optimizationSettings = [
                'optimizedTargetMonochromeCostPerPage' => $optimizationSettings->optimizedTargetMonochromeCostPerPage,
                'optimizedTargetColorCostPerPage'      => $optimizationSettings->optimizedTargetColorCostPerPage,
                'costThreshold'                        => $optimizationSettings->costThreshold,
                'lossThreshold'                        => $optimizationSettings->lossThreshold,
                'minimumPageCount'                     => $optimizationSettings->minimumPageCount,
                'autoOptimizeFunctionality'            => $optimizationSettings->autoOptimizeFunctionality,
                'blackToColorRatio'                    => $optimizationSettings->blackToColorRatio,
                'deviceSwapMonochromeRankSetArray'     => $optimizationSettings->getMonochromeRankSet()->getRanksAsArray(),
                'deviceSwapColorRankSetArray'          => $optimizationSettings->getColorRankSet()->getRanksAsArray(),
            ];
        }

        if ($optimizationSettings)
        {
            $this->populate($optimizationSettings);
        }
    }

    /**
     * Returns a populated model
     *
     * @param null $model
     *
     * @return OptimizationSettingsEntity|null
     */
    public function getOptimizationSettings ($model = null)
    {
        if (!$model instanceof OptimizationSettingsEntity)
        {
            $model = new OptimizationSettingsEntity();
        }

        $model->optimizedTargetMonochromeCostPerPage = $this->getValue('optimizedTargetMonochromeCostPerPage');
        $model->optimizedTargetColorCostPerPage      = $this->getValue('optimizedTargetColorCostPerPage');
        $model->costThreshold                        = $this->getValue('costThreshold');
        $model->lossThreshold                        = $this->getValue('lossThreshold');
        $model->minimumPageCount                     = $this->getValue('minimumPageCount');
        $model->autoOptimizeFunctionality            = $this->getValue('autoOptimizeFunctionality');
        $model->blackToColorRatio                    = $this->getValue('blackToColorRatio');

        return $model;
    }

    /**
     * Gets a list of toner manufacturer ranks
     *
     * @return TonerVendorRankingModel[]
     */
    public function getMonochromeRanks ()
    {
        $tonerRanks = [];

        $manufacturerIds = $this->getValue('deviceSwapMonochromeRankSetArray');
        if ($manufacturerIds)
        {
            $i = 0;
            foreach ($manufacturerIds as $manufacturerId)
            {
                $i++;
                $tonerRank                        = new TonerVendorRankingModel();
                $tonerRank->manufacturerId        = $manufacturerId;
                $tonerRank->rank                  = $i;
                $tonerRanks[(int)$manufacturerId] = $tonerRank;
            }
        }

        return $tonerRanks;
    }

    /**
     * Gets a list of toner manufacturer ranks
     *
     * @return TonerVendorRankingModel[]
     */
    public function getColorRanks ()
    {
        $tonerRanks = [];

        $manufacturerIds = $this->getValue('deviceSwapColorRankSetArray');
        if ($manufacturerIds)
        {
            $i = 0;
            foreach ($manufacturerIds as $manufacturerId)
            {
                $i++;
                $tonerRank                        = new TonerVendorRankingModel();
                $tonerRank->manufacturerId        = $manufacturerId;
                $tonerRank->rank                  = $i;
                $tonerRanks[(int)$manufacturerId] = $tonerRank;
            }
        }

        return $tonerRanks;
    }

    /**
     * Overrides the parent to enforce a view script to render the form
     *
     * @return void|\Zend_Form
     */
    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/client-settings-form.phtml']]]);
    }

    /**
     * Getter for toner vendor list
     *
     * @return array
     */
    public function getTonerVendorList ()
    {
        return $this->tonerVendorList;
    }

    /**
     * Setter for toner vendor list
     *
     * @param array $value
     *
     * @return $this
     */
    public function setTonerVendorList ($value)
    {
        $this->tonerVendorList = $value;

        return $this;
    }
}