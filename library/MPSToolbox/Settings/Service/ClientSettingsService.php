<?php

namespace MPSToolbox\Settings\Service;

use MPSToolbox\Legacy\Entities\UserEntity;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingModel;
use MPSToolbox\Settings\Entities\ClientSettingsEntity;
use MPSToolbox\Settings\Entities\DealerSettingsEntity;
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
 * Class ClientSettingsService
 *
 * @package MPSToolbox\Settings\Service
 */
class ClientSettingsService
{
    /**
     * @param AllSettingsForm      $form
     * @param ClientSettingsEntity $clientSettings
     */
    public function saveAllSettingsForm ($form, $clientSettings)
    {
        $this->saveCurrentFleetSettingsForm($form->getSubForm('currentFleetSettingsForm'), $clientSettings);
        $this->saveProposedFleetSettingsForm($form->getSubForm('proposedFleetSettingsForm'), $clientSettings);
        $this->saveGenericSettingsForm($form->getSubForm('genericSettingsForm'), $clientSettings);
        $this->saveQuoteSettingsForm($form->getSubForm('quoteSettingsForm'), $clientSettings);
        $this->saveOptimizationSettingsForm($form->getSubForm('optimizationSettingsForm'), $clientSettings);

        if ($clientSettings->isDirty())
        {
            $clientSettings->push();
        }
    }

    /**
     * @param \Zend_Form|CurrentFleetSettingsForm $form
     * @param ClientSettingsEntity                $clientSettings
     */
    public function saveCurrentFleetSettingsForm ($form, $clientSettings)
    {
        if (count($clientSettings->currentFleetSettings))
        {
            $currentFleetSettings = $form->getCurrentFleetSettings($clientSettings->currentFleetSettings);

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
            $clientSettings->currentFleetSettings()->associate($currentFleetSettings);
            $clientSettings->push();
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
     * @param ClientSettingsEntity                 $clientSettings
     */
    public function saveProposedFleetSettingsForm ($form, $clientSettings)
    {
        if (count($clientSettings->proposedFleetSettings))
        {
            $proposedFleetSettings = $form->getProposedFleetSettings($clientSettings->proposedFleetSettings);

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
            $clientSettings->proposedFleetSettings()->associate($proposedFleetSettings);
            $clientSettings->push();
        }
    }

    /**
     * @param \Zend_Form|GenericSettingsForm $form
     * @param ClientSettingsEntity           $clientSettings
     */
    public function saveGenericSettingsForm ($form, $clientSettings)
    {
        if (count($clientSettings->genericSettings))
        {
            $genericSettings = $form->getGenericSettings($clientSettings->genericSettings);
            if ($genericSettings->isDirty())
            {
                $genericSettings->save();
            }
        }
        else
        {
            $genericSettings = $form->getGenericSettings();
            $genericSettings->save();
            $clientSettings->genericSettings()->associate($genericSettings);
            $clientSettings->push();
        }
    }

    /**
     * @param \Zend_Form|QuoteSettingsForm $form
     * @param ClientSettingsEntity         $clientSettings
     */
    public function saveQuoteSettingsForm ($form, $clientSettings)
    {
        if (count($clientSettings->quoteSettings))
        {
            $quoteSettings = $form->getQuoteSettings($clientSettings->quoteSettings);
            if ($quoteSettings->isDirty())
            {
                $quoteSettings->save();
            }
        }
        else
        {
            $quoteSettings = $form->getQuoteSettings();
            $quoteSettings->save();
            $clientSettings->quoteSettings()->associate($quoteSettings);
            $clientSettings->push();
        }
    }

    /**
     * @param \Zend_Form|OptimizationSettingsForm $form
     * @param ClientSettingsEntity                $clientSettings
     */
    public function saveOptimizationSettingsForm ($form, $clientSettings)
    {
        if (count($clientSettings->optimizationSettings))
        {
            $optimizationSettings = $form->getOptimizationSettings($clientSettings->optimizationSettings);

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
            $clientSettings->optimizationSettings()->associate($optimizationSettings);
            $clientSettings->push();
        }
    }

    /**
     * Gets the client settings model for a client id
     *
     * @param int $clientId
     * @param int $dealerId
     *
     * @return ClientSettingsEntity
     */
    public function getClientSettings ($clientId, $dealerId)
    {
        $clientSettings = ClientSettingsEntity::with(
            'CurrentFleetSettings',
            'ProposedFleetSettings',
            'GenericSettings',
            'QuoteSettings',
            'OptimizationSettings'
        )->find($clientId);

        if (!$clientSettings instanceof ClientSettingsEntity)
        {
            $dealerSettingsService = new DealerSettingsService();
            $dealerSettings        = $dealerSettingsService->getDealerSettings($dealerId);

            // Create new entity
            $clientSettings           = new ClientSettingsEntity();
            $clientSettings->clientId = $clientId;

            // Clone from Dealer Settings
            $currentFleetSettings  = $dealerSettings->currentFleetSettings->replicate();
            $proposedFleetSettings = $dealerSettings->proposedFleetSettings->replicate();
            $genericSettings       = $dealerSettings->genericSettings->replicate();
            $optimizationSettings  = $dealerSettings->optimizationSettings->replicate();
            $quoteSettings         = $dealerSettings->quoteSettings->replicate();

            // Save clones
            $currentFleetSettings->save();
            $proposedFleetSettings->save();
            $genericSettings->save();
            $optimizationSettings->save();
            $quoteSettings->save();

            // Associate
            $clientSettings->currentFleetSettings()->associate($currentFleetSettings);
            $clientSettings->proposedFleetSettings()->associate($proposedFleetSettings);
            $clientSettings->genericSettings()->associate($genericSettings);
            $clientSettings->optimizationSettings()->associate($optimizationSettings);
            $clientSettings->quoteSettings()->associate($quoteSettings);

            // Save client settings
            $clientSettings->push();
        }

        return $clientSettings;
    }
}