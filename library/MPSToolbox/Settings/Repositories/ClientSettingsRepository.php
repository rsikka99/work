<?php

namespace MPSToolbox\Settings\Repositories;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\ClientModel;
use MPSToolbox\Settings\Entities\ClientSettingsEntity as ClientSettingsEntity;
use MPSToolbox\Settings\Entities\FleetSettingsEntity;
use MPSToolbox\Settings\Entities\GenericSettingsEntity;
use MPSToolbox\Settings\Entities\OptimizationSettingsEntity;
use MPSToolbox\Settings\Entities\QuoteSettingsEntity;

/**
 * Class ClientSettingsRepository
 *
 * @package MPSToolbox\Settings\Repositories
 */
class ClientSettingsRepository
{
    /**
     * @param ClientModel $client
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|ClientSettingsRepository|null|static
     */
    public static function findClientSettings ($client)
    {
        $clientSettings = ClientSettingsEntity
            ::with(
                'CurrentFleetSettingsForm',
                'ProposedFleetSettingsForm',
                'GenericSettingsEntity',
                'QuoteSettingsEntity',
                'OptimizationSettingsEntity'
            )
            ->find($client->id);

        if (!$clientSettings instanceof ClientSettingsEntity)
        {
            $clientSettings = self::createNewClientSettings($client);
        }

        return $clientSettings;
    }

    /**
     * Creates new client settings with defaults already set
     *
     * @param ClientModel $client
     *
     * @return ClientSettingsRepository
     */
    protected static function createNewClientSettings ($client)
    {
        $clientSettings           = new ClientSettingsEntity();
        $clientSettings->clientId = $client->id;

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
            $fleetSetting->monochromeTonerVendorRankingSetId = 1;
            $fleetSetting->colorTonerVendorRankingSetId      = 1;
        }

        $currentFleetSetting->defaultMonochromeLaborCostPerPage = 0;
        $currentFleetSetting->defaultMonochromePartsCostPerPage = 0;
        $currentFleetSetting->defaultColorLaborCostPerPage      = 0;
        $currentFleetSetting->defaultColorPartsCostPerPage      = 0;
        $currentFleetSetting->adminCostPerPage                  = 0;

        $currentFleetSetting->useDevicePageCoverages = true;

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
        $optimizationSettings->autoOptimizeFunctionality            = 1;
        $optimizationSettings->blackToColorRatio                    = 30;
        $optimizationSettings->monochromeTonerVendorRankingSetId    = 1;
        $optimizationSettings->colorTonerVendorRankingSetId         = 1;

        $quoteSettings                      = new QuoteSettingsEntity();
        $quoteSettings->defaultDeviceMargin = 15;
        $quoteSettings->defaultPageMargin   = 45;


        $currentFleetSetting->save();
        $proposedFleetSetting->save();
        $genericSettings->save();
        $optimizationSettings->save();
        $quoteSettings->save();


        $clientSettings->currentFleetSettings()->associate($currentFleetSetting);
        $clientSettings->proposedFleetSettings()->associate($proposedFleetSetting);
        $clientSettings->genericSettings()->associate($genericSettings);
        $clientSettings->optimizationSettings()->associate($optimizationSettings);
        $clientSettings->quoteSettings()->associate($quoteSettings);

        $clientSettings->push();

        return $clientSettings;
    }
}