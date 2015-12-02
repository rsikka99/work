<?php

namespace MPSToolbox\Settings\Service;

use MPSToolbox\Legacy\Entities\DealerEntity;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingModel;
use MPSToolbox\Settings\Entities\DealerSettingsEntity;
use MPSToolbox\Settings\Entities\FleetSettingsEntity;
use MPSToolbox\Settings\Entities\GenericSettingsEntity;
use MPSToolbox\Settings\Entities\OptimizationSettingsEntity;
use MPSToolbox\Settings\Entities\QuoteSettingsEntity;
use MPSToolbox\Settings\Entities\ShopSettingsEntity;
use MPSToolbox\Settings\Form\AllSettingsForm as AllSettingsForm;
use MPSToolbox\Settings\Form\CurrentFleetSettingsForm;
use MPSToolbox\Settings\Form\GenericSettingsForm;
use MPSToolbox\Settings\Form\OptimizationSettingsForm;
use MPSToolbox\Settings\Form\ProposedFleetSettingsForm;
use MPSToolbox\Settings\Form\QuoteSettingsForm as QuoteSettingsForm;
use MPSToolbox\Settings\Form\ShopSettingsForm as ShopSettingsForm;

/**
 * Class DealerSettingsService
 *
 * @package MPSToolbox\Settings\Service
 */
class DealerSettingsService
{
    /**
     * @param AllSettingsForm      $form
     * @param DealerSettingsEntity $dealerSettings
     */
    public function saveAllSettingsForm ($form, $dealerSettings)
    {
        $this->saveCurrentFleetSettingsForm($form->getSubForm('currentFleetSettingsForm'), $dealerSettings);
        $this->saveProposedFleetSettingsForm($form->getSubForm('proposedFleetSettingsForm'), $dealerSettings);
        $this->saveGenericSettingsForm($form->getSubForm('genericSettingsForm'), $dealerSettings);
        $this->saveQuoteSettingsForm($form->getSubForm('quoteSettingsForm'), $dealerSettings);
        $this->saveOptimizationSettingsForm($form->getSubForm('optimizationSettingsForm'), $dealerSettings);

        if ($dealerSettings->isDirty())
        {
            $dealerSettings->push();
        }
    }

    /**
     * @param \Zend_Form|CurrentFleetSettingsForm $form
     * @param DealerSettingsEntity                $dealerSettings
     */
    public function saveCurrentFleetSettingsForm ($form, $dealerSettings)
    {
        if (count($dealerSettings->currentFleetSettings))
        {
            $currentFleetSettings = $form->getCurrentFleetSettings($dealerSettings->currentFleetSettings);

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
            $dealerSettings->currentFleetSettings()->associate($currentFleetSettings);
            $dealerSettings->push();
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
     * @param DealerSettingsEntity                 $dealerSettings
     */
    public function saveProposedFleetSettingsForm ($form, $dealerSettings)
    {
        if (count($dealerSettings->proposedFleetSettings))
        {
            $proposedFleetSettings = $form->getProposedFleetSettings($dealerSettings->proposedFleetSettings);

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
            $dealerSettings->proposedFleetSettings()->associate($proposedFleetSettings);
            $dealerSettings->push();
        }
    }

    /**
     * @param \Zend_Form|GenericSettingsForm $form
     * @param DealerSettingsEntity           $dealerSettings
     */
    public function saveGenericSettingsForm ($form, $dealerSettings)
    {
        if (count($dealerSettings->genericSettings))
        {
            $genericSettings = $form->getGenericSettings($dealerSettings->genericSettings);
            if ($genericSettings->isDirty())
            {
                $genericSettings->save();
            }
        }
        else
        {
            $genericSettings = $form->getGenericSettings();
            $genericSettings->save();
            $dealerSettings->genericSettings()->associate($genericSettings);
            $dealerSettings->push();
        }
    }

    /**
     * @param \Zend_Form|QuoteSettingsForm $form
     * @param DealerSettingsEntity         $dealerSettings
     */
    public function saveQuoteSettingsForm ($form, $dealerSettings)
    {
        if (count($dealerSettings->quoteSettings))
        {
            $quoteSettings = $form->getQuoteSettings($dealerSettings->quoteSettings);
            if ($quoteSettings->isDirty())
            {
                $quoteSettings->save();
            }
        }
        else
        {
            $quoteSettings = $form->getQuoteSettings();
            $quoteSettings->save();
            $dealerSettings->quoteSettings()->associate($quoteSettings);
            $dealerSettings->push();
        }
    }

    /**
     * @param \Zend_Form|OptimizationSettingsForm $form
     * @param DealerSettingsEntity                $dealerSettings
     */
    public function saveOptimizationSettingsForm ($form, $dealerSettings)
    {
        if (count($dealerSettings->optimizationSettings))
        {
            $optimizationSettings = $form->getOptimizationSettings($dealerSettings->optimizationSettings);

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
            $dealerSettings->optimizationSettings()->associate($optimizationSettings);
            $dealerSettings->push();
        }
    }

    /**
     * Gets a populated dealer setting model to test with
     *
     * @param DealerSettingsEntity $dealerSettings
     */
    public function populateDealerSettingsWithDefaults (&$dealerSettings)
    {
        $currentFleetSetting  = new FleetSettingsEntity();
        $proposedFleetSetting = new FleetSettingsEntity();

        foreach ([$currentFleetSetting, $proposedFleetSetting] as $fleetSetting)
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

        $shopSettings                      = new ShopSettingsEntity();
        $shopSettings->shopifyName = '';
        $shopSettings->hardwareMargin = 30;
        $shopSettings->oemTonerMargin = 30;
        $shopSettings->compatibleTonerMargin = 30;
        $shopSettings->rmsUri = '';

        $currentFleetSetting->save();
        $proposedFleetSetting->save();
        $genericSettings->save();
        $optimizationSettings->save();
        $quoteSettings->save();
        $shopSettings->save();

        $dealerSettings->currentFleetSettings()->associate($currentFleetSetting);
        $dealerSettings->proposedFleetSettings()->associate($proposedFleetSetting);
        $dealerSettings->genericSettings()->associate($genericSettings);
        $dealerSettings->optimizationSettings()->associate($optimizationSettings);
        $dealerSettings->quoteSettings()->associate($quoteSettings);
        $dealerSettings->shopSettings()->associate($shopSettings);
    }

    /**
     * Gets the dealer settings model for a dealer id
     *
     * @param int $dealerId
     *
     * @return DealerSettingsEntity
     */
    public function getDealerSettings ($dealerId=null)
    {
        if (!$dealerId) {
            $dealerId = DealerEntity::getDealerId();
        }
        $dealerSettings = DealerSettingsEntity::with(
            'CurrentFleetSettings',
            'ProposedFleetSettings',
            'GenericSettings',
            'QuoteSettings',
            'OptimizationSettings',
            'ShopSettings'
        )->find($dealerId);

        if (!$dealerSettings instanceof DealerSettingsEntity)
        {
            $dealerSettings           = new DealerSettingsEntity();
            $dealerSettings->dealerId = $dealerId;
            $this->populateDealerSettingsWithDefaults($dealerSettings);

            $dealerSettings->push();
        }

        return $dealerSettings;
    }

    /**
     * @param \Zend_Form|ShopSettingsForm $form
     * @param DealerSettingsEntity         $dealerSettings
     */
    public function saveShopSettingsForm ($form, $dealerSettings)
    {
        if (count($dealerSettings->shopSettings))
        {
            $shopSettings = $form->getShopSettings($dealerSettings->shopSettings);
            if ($shopSettings->isDirty())
            {
                $shopSettings->save();
            }
        }
        else
        {
            $shopSettings = $form->getShopSettings();
            $shopSettings->save();
            $dealerSettings->shopSettings()->associate($shopSettings);
            $dealerSettings->push();
        }
    }

}