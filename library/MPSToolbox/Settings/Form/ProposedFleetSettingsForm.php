<?php

namespace MPSToolbox\Settings\Form;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingModel;
use MPSToolbox\Settings\Entities\FleetSettingsEntity as FleetSettingsEntity;

/**
 * Class ProposedFleetSettingsForm
 *
 * @package MPSToolbox\Settings\Form
 */
class ProposedFleetSettingsForm extends \My_Form_Form
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
         * New Fleet Settings
         */


        $this->addElement('checkbox', 'proposedUseDevicePageCoverages', [
            'label'       => 'Use Device Page Coverages',
            'description' => 'Use the actual page coverage value reported by the RMS.',
        ]);

        $this->addElement('text_float', 'proposedPageCoverageMono', [
            'label'       => 'Default Monochrome Coverage',
            'description' => 'Apply this coverage to determine the toner costs of all monochrome pages.',
            'required'    => true,
        ]);

        $this->addElement('text_float', 'proposedPageCoverageColor', [
            'label'       => 'Default Color Coverage',
            'description' => 'Apply this coverage to determine the toner costs of all color (CMYK) pages.',
            'required'    => true,
        ]);

        $this->addElement('text_currency', 'proposedDefaultAdminCostPerPage', [
            'label'       => 'Admin CPP',
            'description' => 'You can specify an additional cost per page here. It is applied to all devices.',
            'required'    => true,
        ]);

        $this->addElement('text_currency', 'proposedDefaultMonochromeLaborCostPerPage', [
            'label'       => 'Monochrome Device Labor CPP',
            'description' => 'The default labor cost per page to apply to monochrome devices.',
            'required'    => true,
        ]);

        $this->addElement('text_currency', 'proposedDefaultMonochromePartsCostPerPage', [
            'label'       => 'Monochrome Device Parts CPP',
            'description' => 'The default parts cost per page to apply to monochrome devices.',
            'required'    => true,
        ]);

        $this->addElement('text_currency', 'proposedDefaultColorLaborCostPerPage', [
            'label'       => 'Color Device Labor CPP',
            'description' => 'The default labor cost per page to apply to color devices.',
            'required'    => true,
        ]);

        $this->addElement('text_currency', 'proposedDefaultColorPartsCostPerPage', [
            'label'       => 'Color Device Parts CPP',
            'description' => 'The default parts cost per page to apply to color devices.',
            'required'    => true,
        ]);

        $this->addElement('select', 'proposedLevel', [
            'label'        => 'Pricing Level',
            'multiOptions' => [''=>'Standard', 'level1'=>'Level 1', 'level2'=>'Level 2', 'level3'=>'Level 3', 'level4'=>'Level 4', 'level5'=>'Level 5', 'level6'=>'Level 6', 'level7'=>'Level 7', 'level8'=>'Level 8', 'level9'=>'Level 9'],
        ]);

        $this->addElement('multiselect', 'proposedMonochromeRankSetArray', [
            'label'        => 'Monochrome Toner Vendors',
            'description'  => 'Which supply vendor(s) would you like to use for <strong>monochrome devices</strong>?<br><em>Note: The system always uses OEM as the <strong>last</strong> option. If you leave this blank then it will use <strong>only OEM</strong> supplies.</em>',
            'class'        => 'tonerMultiselect',
            'multiOptions' => $tonerVendors,
        ]);

        $this->addElement('multiselect', 'proposedColorRankSetArray', [
            'label'        => 'Color Toner Vendors',
            'description'  => 'Which supply vendor(s) would you like to use for <strong>color devices</strong>?<br><em>Note: The system always uses OEM as the <strong>last</strong> option. If you leave this blank then it will use <strong>only OEM</strong> supplies.</em>',
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
    public function populateProposedFleetSettings ($fleetSettings)
    {
        if ($fleetSettings instanceof FleetSettingsEntity)
        {
            $fleetSettings = [
                'proposedUseDevicePageCoverages'            => $fleetSettings->useDevicePageCoverages,
                'proposedPageCoverageMono'                  => $fleetSettings->defaultMonochromeCoverage,
                'proposedPageCoverageColor'                 => $fleetSettings->defaultColorCoverage,
                'proposedDefaultAdminCostPerPage'           => $fleetSettings->adminCostPerPage,
                'proposedDefaultMonochromeLaborCostPerPage' => $fleetSettings->defaultMonochromeLaborCostPerPage,
                'proposedDefaultMonochromePartsCostPerPage' => $fleetSettings->defaultMonochromePartsCostPerPage,
                'proposedDefaultColorLaborCostPerPage'      => $fleetSettings->defaultColorLaborCostPerPage,
                'proposedDefaultColorPartsCostPerPage'      => $fleetSettings->defaultColorPartsCostPerPage,
                'proposedMonochromeRankSetArray'            => $fleetSettings->getMonochromeRankSet()->getRanksAsArray(),
                'proposedColorRankSetArray'                 => $fleetSettings->getColorRankSet()->getRanksAsArray(),
                'proposedLevel'                             => $fleetSettings->level,
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
    public function getProposedFleetSettings ($model = null)
    {
        if (!$model instanceof FleetSettingsEntity)
        {
            $model = new FleetSettingsEntity();
        }

        $model->useDevicePageCoverages            = $this->getValue('proposedUseDevicePageCoverages');
        $model->defaultMonochromeCoverage         = $this->getValue('proposedPageCoverageMono');
        $model->defaultColorCoverage              = $this->getValue('proposedPageCoverageColor');
        $model->adminCostPerPage                  = $this->getValue('proposedDefaultAdminCostPerPage');
        $model->defaultMonochromeLaborCostPerPage = $this->getValue('proposedDefaultMonochromeLaborCostPerPage');
        $model->defaultMonochromePartsCostPerPage = $this->getValue('proposedDefaultMonochromePartsCostPerPage');
        $model->defaultColorLaborCostPerPage      = $this->getValue('proposedDefaultColorLaborCostPerPage');
        $model->defaultColorPartsCostPerPage      = $this->getValue('proposedDefaultColorPartsCostPerPage');
        $model->level                             = $this->getValue('proposedLevel');

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

        $manufacturerIds = $this->getValue('proposedMonochromeRankSetArray');
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

        $manufacturerIds = $this->getValue('proposedColorRankSetArray');
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