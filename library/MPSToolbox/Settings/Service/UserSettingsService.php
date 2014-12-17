<?php

namespace MPSToolbox\Settings\Service;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingModel;
use MPSToolbox\Settings\Entities\UserSettingsEntity;
use MPSToolbox\Settings\Entities\FleetSettingsEntity;
use MPSToolbox\Settings\Entities\GenericSettingsEntity;
use MPSToolbox\Settings\Entities\OptimizationSettingsEntity;
use MPSToolbox\Settings\Entities\QuoteSettingsEntity;
use MPSToolbox\Settings\Form\AllSettingsForm as AllSettingsForm;
use MPSToolbox\Settings\Form\CurrentFleetSettingsForm;
use MPSToolbox\Settings\Form\GenericSettingsForm;
use MPSToolbox\Settings\Form\OptimizationSettingsForm;
use MPSToolbox\Settings\Form\ProposedFleetSettingsForm;
use MPSToolbox\Settings\Form\QuoteSettingsForm as QuoteSettingsForm;

/**
 * Class UserSettingsService
 *
 * @package MPSToolbox\Settings\Service
 */
class UserSettingsService
{
    /**
     * @param AllSettingsForm    $form
     * @param UserSettingsEntity $userSettings
     */
    public function saveAllSettingsForm ($form, $userSettings)
    {
        $this->saveCurrentFleetSettingsForm($form->getSubForm('currentFleetSettingsForm'), $userSettings);
        $this->saveProposedFleetSettingsForm($form->getSubForm('proposedFleetSettingsForm'), $userSettings);
        $this->saveGenericSettingsForm($form->getSubForm('genericSettingsForm'), $userSettings);
        $this->saveQuoteSettingsForm($form->getSubForm('quoteSettingsForm'), $userSettings);
        $this->saveOptimizationSettingsForm($form->getSubForm('optimizationSettingsForm'), $userSettings);

        if ($userSettings->isDirty())
        {
            $userSettings->push();
        }
    }

    /**
     * @param \Zend_Form|CurrentFleetSettingsForm $form
     * @param UserSettingsEntity                  $userSettings
     */
    public function saveCurrentFleetSettingsForm ($form, $userSettings)
    {
        if (count($userSettings->currentFleetSettings))
        {
            $currentFleetSettings = $form->getCurrentFleetSettings($userSettings->currentFleetSettings);

            $newMonochromeTonerRanks     = $form->getMonochromeRanks();
            $currentMonochromeTonerRanks = $currentFleetSettings->getMonochromeRankSet()->getRankings();
            $this->saveTonerRankChanges($currentMonochromeTonerRanks, $newMonochromeTonerRanks, $currentFleetSettings->getMonochromeRankSet()->id);

            $newColorTonerRanks     = $form->getColorRanks();
            $currentColorTonerRanks = $currentFleetSettings->getColorRankSet()->getRankings();
            $this->saveTonerRankChanges($currentColorTonerRanks, $newColorTonerRanks, $currentFleetSettings->getColorRankSet()->id);

            if ($currentFleetSettings->isDirty())
            {
                $currentFleetSettings->save();
            }
        }
        else
        {
            $currentFleetSettings = $form->getCurrentFleetSettings();
            $currentFleetSettings->save();
            $userSettings->currentFleetSettings()->associate($currentFleetSettings);
            $userSettings->push();
        }

    }

    /**
     * Handles comparing and updating a toner rank set
     *
     * @param TonerVendorRankingModel[] $current
     * @param TonerVendorRankingModel[] $new
     * @param int                       $tonerRankSetId
     */
    public function saveTonerRankChanges ($current, $new, $tonerRankSetId)
    {
        $hasChanged = count($current) !== count($new);

        if (!$hasChanged)
        {
            foreach ($current as $tonerRankingModel)
            {
                if (!isset($new[(int)$tonerRankingModel->manufacturerId]) || (int)$new[(int)$tonerRankingModel->manufacturerId]->rank !== (int)$tonerRankingModel->rank)
                {
                    $hasChanged = true;
                    break;
                }
            }
        }

        if ($hasChanged)
        {
            // Delete all the ranks
            foreach ($current as $tonerRankingModel)
            {
                TonerVendorRankingMapper::getInstance()->delete($tonerRankingModel);
            }

            foreach ($new as $tonerRankingModel)
            {
                $tonerRankingModel->tonerVendorRankingSetId = $tonerRankSetId;
                TonerVendorRankingMapper::getInstance()->insert($tonerRankingModel);
            }
        }
    }

    /**
     * @param \Zend_Form|ProposedFleetSettingsForm $form
     * @param UserSettingsEntity                   $userSettings
     */
    public function saveProposedFleetSettingsForm ($form, $userSettings)
    {
        if (count($userSettings->proposedFleetSettings))
        {
            $proposedFleetSettings = $form->getProposedFleetSettings($userSettings->proposedFleetSettings);

            $newMonochromeTonerRanks     = $form->getMonochromeRanks();
            $currentMonochromeTonerRanks = $proposedFleetSettings->getMonochromeRankSet()->getRankings();
            $this->saveTonerRankChanges($currentMonochromeTonerRanks, $newMonochromeTonerRanks, $proposedFleetSettings->getMonochromeRankSet()->id);

            $newColorTonerRanks     = $form->getColorRanks();
            $currentColorTonerRanks = $proposedFleetSettings->getColorRankSet()->getRankings();
            $this->saveTonerRankChanges($currentColorTonerRanks, $newColorTonerRanks, $proposedFleetSettings->getColorRankSet()->id);


            if ($proposedFleetSettings->isDirty())
            {
                $proposedFleetSettings->save();
            }
        }
        else
        {
            $proposedFleetSettings = $form->getProposedFleetSettings();
            $proposedFleetSettings->save();
            $userSettings->proposedFleetSettings()->associate($proposedFleetSettings);
            $userSettings->push();
        }
    }

    /**
     * @param \Zend_Form|GenericSettingsForm $form
     * @param UserSettingsEntity             $userSettings
     */
    public function saveGenericSettingsForm ($form, $userSettings)
    {
        if (count($userSettings->genericSettings))
        {
            $genericSettings = $form->getGenericSettings($userSettings->genericSettings);
            if ($genericSettings->isDirty())
            {
                $genericSettings->save();
            }
        }
        else
        {
            $genericSettings = $form->getGenericSettings();
            $genericSettings->save();
            $userSettings->genericSettings()->associate($genericSettings);
            $userSettings->push();
        }
    }

    /**
     * @param \Zend_Form|QuoteSettingsForm $form
     * @param UserSettingsEntity           $userSettings
     */
    public function saveQuoteSettingsForm ($form, $userSettings)
    {
        if (count($userSettings->quoteSettings))
        {
            $quoteSettings = $form->getQuoteSettings($userSettings->quoteSettings);
            if ($quoteSettings->isDirty())
            {
                $quoteSettings->save();
            }
        }
        else
        {
            $quoteSettings = $form->getQuoteSettings();
            $quoteSettings->save();
            $userSettings->quoteSettings()->associate($quoteSettings);
            $userSettings->push();
        }
    }

    /**
     * @param \Zend_Form|OptimizationSettingsForm $form
     * @param UserSettingsEntity                  $userSettings
     */
    public function saveOptimizationSettingsForm ($form, $userSettings)
    {
        if (count($userSettings->optimizationSettings))
        {
            $optimizationSettings = $form->getOptimizationSettings($userSettings->optimizationSettings);

            $newMonochromeTonerRanks     = $form->getMonochromeRanks();
            $currentMonochromeTonerRanks = $optimizationSettings->getMonochromeRankSet()->getRankings();
            $this->saveTonerRankChanges($currentMonochromeTonerRanks, $newMonochromeTonerRanks, $optimizationSettings->getMonochromeRankSet()->id);

            $newColorTonerRanks     = $form->getColorRanks();
            $currentColorTonerRanks = $optimizationSettings->getColorRankSet()->getRankings();
            $this->saveTonerRankChanges($currentColorTonerRanks, $newColorTonerRanks, $optimizationSettings->getColorRankSet()->id);


            if ($optimizationSettings->isDirty())
            {
                $optimizationSettings->save();
            }
        }
        else
        {
            $optimizationSettings = $form->getOptimizationSettings();
            $optimizationSettings->save();
            $userSettings->optimizationSettings()->associate($optimizationSettings);
            $userSettings->push();
        }
    }

    /**
     * Gets a populated user setting model to test with
     *
     * @param UserSettingsEntity $userSettings
     */
    public function populateUserSettingsWithDefaults (&$userSettings)
    {
        $currentFleetSetting  = new FleetSettingsEntity();
        $proposedFleetSetting = new FleetSettingsEntity();

        foreach (array($currentFleetSetting, $proposedFleetSetting) as $fleetSetting)
        {
            /* @var $fleetSetting FleetSettingsEntity */
            $fleetSetting->useDevicePageCoverages            = false;
            $fleetSetting->defaultMonochromeCoverage         = 6;
            $fleetSetting->defaultColorCoverage              = 24;
            $fleetSetting->adminCostPerPage                  = 0.0005;
            $fleetSetting->defaultMonochromeLaborCostPerPage = 0.0015;
            $fleetSetting->defaultMonochromePartsCostPerPage = 0.0015;
            $fleetSetting->defaultColorLaborCostPerPage      = 0.0015;
            $fleetSetting->defaultColorPartsCostPerPage      = 0.0015;
            $fleetSetting->getMonochromeRankSet();
            $fleetSetting->getColorRankSet();
        }

        $currentFleetSetting->defaultMonochromeLaborCostPerPage = 0;
        $currentFleetSetting->defaultMonochromePartsCostPerPage = 0;
        $currentFleetSetting->defaultColorLaborCostPerPage      = 0;
        $currentFleetSetting->defaultColorPartsCostPerPage      = 0;
        $currentFleetSetting->adminCostPerPage                  = 0;
        $currentFleetSetting->useDevicePageCoverages            = true;

        $genericSettings                              = new GenericSettingsEntity();
        $genericSettings->defaultEnergyCost           = 0.11;
        $genericSettings->defaultMonthlyLeasePayment  = 250;
        $genericSettings->defaultPrinterCost          = 1000;
        $genericSettings->leasedMonochromeCostPerPage = 0.04;
        $genericSettings->leasedColorCostPerPage      = 0.12;
        $genericSettings->mpsMonochromeCostPerPage    = 0.02;
        $genericSettings->mpsColorCostPerPage         = 0.10;
        $genericSettings->targetMonochromeCostPerPage = 0.02;
        $genericSettings->targetColorCostPerPage      = 0.10;
        $genericSettings->tonerPricingMargin          = 28;

        $optimizationSettings                                       = new OptimizationSettingsEntity();
        $optimizationSettings->optimizedTargetMonochromeCostPerPage = 0.012;
        $optimizationSettings->optimizedTargetColorCostPerPage      = 0.090;
        $optimizationSettings->costThreshold                        = 20.00;
        $optimizationSettings->lossThreshold                        = 50.00;
        $optimizationSettings->autoOptimizeFunctionality            = 0;
        $optimizationSettings->blackToColorRatio                    = 30;
        $optimizationSettings->getMonochromeRankSet();
        $optimizationSettings->getColorRankSet();

        $quoteSettings                      = new QuoteSettingsEntity();
        $quoteSettings->defaultDeviceMargin = 15;
        $quoteSettings->defaultPageMargin   = 45;

        $currentFleetSetting->save();
        $proposedFleetSetting->save();
        $genericSettings->save();
        $optimizationSettings->save();
        $quoteSettings->save();

        $userSettings->currentFleetSettings()->associate($currentFleetSetting);
        $userSettings->proposedFleetSettings()->associate($proposedFleetSetting);
        $userSettings->genericSettings()->associate($genericSettings);
        $userSettings->optimizationSettings()->associate($optimizationSettings);
        $userSettings->quoteSettings()->associate($quoteSettings);
    }

    /**
     * Gets the user settings model for a user id
     *
     * @param int $userId
     *
     * @return UserSettingsEntity
     */
    public function getUserSettings ($userId)
    {
        $userSettings = UserSettingsEntity::with(
            'CurrentFleetSettings',
            'ProposedFleetSettings',
            'GenericSettings',
            'QuoteSettings',
            'OptimizationSettings'
        )->find($userId);

        if (!$userSettings instanceof UserSettingsEntity)
        {
            $userSettings         = new UserSettingsEntity();
            $userSettings->userId = $userId;
            $this->populateUserSettingsWithDefaults($userSettings);

            $userSettings->push();
        }

        return $userSettings;
    }
}