<?php

namespace MPSToolbox\Settings\Form;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingModel;
use MPSToolbox\Settings\Entities\FleetSettingsEntity as FleetSettingsEntity;

/**
 * Class CurrentFleetSettingsForm
 *
 * @package MPSToolbox\Settings\Form
 */
class CurrentFleetSettingsForm extends \My_Form_Form
{
    /**
     * @var array
     */
    protected $tonerVendorList;

    /**
     * @var int
     */
    protected $dealerId;

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
         * Current Fleet Settings
         */
        $this->addElement('checkbox', 'currentUseDevicePageCoverages', [
            'label'       => 'Use Device Page Coverages',
            'description' => 'Use the actual page coverage value reported by the RMS.',
        ]);

        $this->addElement('text_float', 'currentPageCoverageMono', [
            'label'       => 'Default Monochrome Coverage',
            'description' => 'Apply this coverage to determine the toner costs of all monochrome pages.',
            'required'    => true,
        ]);

        $this->addElement('text_float', 'currentPageCoverageColor', [
            'label'       => 'Default Color Coverage',
            'description' => 'Apply this coverage to determine the toner costs of all color (CMYK) pages.',
            'required'    => true,
        ]);

        $this->addElement('select', 'currentLevel', [
            'label'        => 'Pricing Level',
            'multiOptions' => [''=>'Standard', 'level1'=>'Level 1', 'level2'=>'Level 2', 'level3'=>'Level 3', 'level4'=>'Level 4', 'level5'=>'Level 5'],
        ]);

        $this->addElement('multiselect', 'currentMonochromeRankSetArray', [
            'label'        => 'Monochrome Toner Vendors',
            'description'  => 'Which supply vendor(s) is the customer currently using for <strong>monochrome devices</strong>?<br><em>Note: The system always uses OEM as the <strong>last</strong> option. If you leave this blank then it will use <strong>only OEM</strong> supplies.</em>',
            'class'        => 'tonerMultiselect',
            'multiOptions' => $tonerVendors,
        ]);

        $this->addElement('multiselect', 'currentColorRankSetArray', [
            'label'        => 'Color Toner Vendors',
            'description'  => 'Which supply vendor(s) is the customer currently using for <strong>color devices</strong>?<br><em>Note: The system always uses OEM as the <strong>last</strong> option. If you leave this blank then it will use <strong>only OEM</strong> supplies.</em>',
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
     * @param array|FleetSettingsEntity $fleetSettings
     */
    public function populateCurrentFleetSettings ($fleetSettings)
    {
        if ($fleetSettings instanceof FleetSettingsEntity)
        {
            $fleetSettings = [
                'currentUseDevicePageCoverages' => $fleetSettings->useDevicePageCoverages,
                'currentPageCoverageMono'       => $fleetSettings->defaultMonochromeCoverage,
                'currentPageCoverageColor'      => $fleetSettings->defaultColorCoverage,
                'currentMonochromeRankSetArray' => $fleetSettings->getMonochromeRankSet()->getRanksAsArray(),
                'currentColorRankSetArray'      => $fleetSettings->getColorRankSet()->getRanksAsArray(),
                'currentLevel'                  => $fleetSettings->level,
            ];
        }

        if ($fleetSettings)
        {
            $this->populate($fleetSettings);
        }
    }

    /**
     * Returns a populated model
     *
     * @param null $model
     *
     * @return FleetSettingsEntity|null
     */
    public function getCurrentFleetSettings ($model = null)
    {
        if (!$model instanceof FleetSettingsEntity)
        {
            $model = new FleetSettingsEntity();
        }

        $model->useDevicePageCoverages    = $this->getValue('currentUseDevicePageCoverages');
        $model->defaultMonochromeCoverage = $this->getValue('currentPageCoverageMono');
        $model->defaultColorCoverage      = $this->getValue('currentPageCoverageColor');
        $model->level                     = $this->getValue('currentLevel');

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

        $manufacturerIds = $this->getValue('currentMonochromeRankSetArray');
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

        $manufacturerIds = $this->getValue('currentColorRankSetArray');
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