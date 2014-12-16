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
class CurrentFleetSettingsForm extends \Zend_Form
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
        $this->addElement('checkbox', 'currentUseDevicePageCoverages', array(
            'label'       => 'Use Device Page Coverages',
            'description' => 'Use the actual page coverage value reported by the RMS.',
        ));

        $this->addElement('text', 'currentPageCoverageMono', array(
            'label'       => 'Default Monochrome Coverage',
            'description' => 'Apply this coverage to determine the toner costs of all monochrome pages.',
            'required'    => true,
        ));

        $this->addElement('text', 'currentPageCoverageColor', array(
            'label'       => 'Default Color Coverage',
            'description' => 'Apply this coverage to determine the toner costs of all color (CMYK) pages.',
            'required'    => true,
        ));

        $this->addElement('multiselect', 'currentMonochromeRankSetArray', array(
            'label'        => 'Monochrome Toner Vendors',
            'description'  => 'Which supply vendor(s) is the customer currently using for <strong>monochrome devices</strong>?<br><em>Note: The system always uses OEM as the <strong>last</strong> option. If you leave this blank then it will use <strong>only OEM</strong> supplies.</em>',
            'class'        => 'tonerMultiselect',
            'multiOptions' => $tonerVendors,
        ));

        $this->addElement('multiselect', 'currentColorRankSetArray', array(
            'label'        => 'Color Toner Vendors',
            'description'  => 'Which supply vendor(s) is the customer currently using for <strong>color devices</strong>?<br><em>Note: The system always uses OEM as the <strong>last</strong> option. If you leave this blank then it will use <strong>only OEM</strong> supplies.</em>',
            'class'        => 'tonerMultiselect',
            'multiOptions' => $tonerVendors,
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
     * @param array|FleetSettingsEntity $fleetSettings
     */
    public function populateCurrentFleetSettings ($fleetSettings)
    {
        if ($fleetSettings instanceof FleetSettingsEntity)
        {
            $fleetSettings = array(
                'currentUseDevicePageCoverages' => $fleetSettings->useDevicePageCoverages,
                'currentPageCoverageMono'       => $fleetSettings->defaultMonochromeCoverage,
                'currentPageCoverageColor'      => $fleetSettings->defaultColorCoverage,
                'currentMonochromeRankSetArray' => $fleetSettings->getMonochromeRankSet()->getRanksAsArray(),
                'currentColorRankSetArray'      => $fleetSettings->getColorRankSet()->getRanksAsArray(),
            );
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

        return $model;
    }

    /**
     * Gets a list of toner manufacturer ranks
     *
     * @return TonerVendorRankingModel[]
     */
    public function getMonochromeRanks ()
    {
        $tonerRanks = array();

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
        $tonerRanks = array();

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