<?php
class Proposalgen_Model_UnknownDeviceInstance extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $reportId;

    /**
     * @var int
     */
    public $uploadDataCollectorRowId;

    /**
     * @var int
     */
    public $printerModelId;

    /**
     * @var string
     */
    public $mpsMonitorStartDate;

    /**
     * @var string
     */
    public $mpsMonitorEndDate;

    /**
     * @var string
     */
    public $mpsDiscoveryDate;

    /**
     * @var string
     */
    public $installDate;

    /**
     * @var string
     */
    public $deviceManufacturer;

    /**
     * @var string
     */
    public $printerModel;

    /**
     * @var string
     */
    public $printerSerialNumber;

    /**
     * @var int
     */
    public $tonerConfigId;

    /**
     * @var bool
     */
    public $isCopier;

    /**
     * @var bool
     */
    public $isFax;

    /**
     * @var bool
     */
    public $isDuplex;

    /**
     * @var bool
     */
    public $isScanner;

    /**
     * @var int
     */
    public $wattsPowerNormal;

    /**
     * @var int
     */
    public $wattsPowerIdle;

    /**
     * @var float
     */
    public $cost;

    /**
     * @var string
     */
    public $launchDate;

    /**
     * @var string
     */
    public $dateCreated;

    /**
     * @var string
     */
    public $blackTonerSku;

    /**
     * @var float
     */
    public $blackTonerPrice;

    /**
     * @var int
     */
    public $blackTonerYield;

    /**
     * @var string
     */
    public $cyanTonerSku;

    /**
     * @var float
     */
    public $cyanTonerPrice;

    /**
     * @var int
     */
    public $cyanTonerYield;

    /**
     * @var string
     */
    public $magentaTonerSku;

    /**
     * @var float
     */
    public $magentaTonerPrice;

    /**
     * @var int
     */
    public $magentaTonerYield;

    /**
     * @var string
     */
    public $yellowTonerSku;

    /**
     * @var float
     */
    public $yellowTonerPrice;

    /**
     * @var int
     */
    public $yellowTonerYield;

    /**
     * @var string
     */
    public $threeColorTonerSku;

    /**
     * @var float
     */
    public $threeColorTonerPrice;

    /**
     * @var int
     */
    public $threeColorTonerYield;

    /**
     * @var string
     */
    public $fourColorTonerSku;

    /**
     * @var float
     */
    public $fourColorTonerPrice;

    /**
     * @var int
     */
    public $fourColorTonerYield;

    /**
     * @var string
     */
    public $blackCompSku;

    /**
     * @var float
     */
    public $blackCompPrice;

    /**
     * @var int
     */
    public $blackCompYield;

    /**
     * @var string
     */
    public $cyanCompSku;

    /**
     * @var float
     */
    public $cyanCompPrice;

    /**
     * @var int
     */
    public $cyanCompYield;

    /**
     * @var string
     */
    public $magentaCompSku;

    /**
     * @var float
     */
    public $magentaCompPrice;

    /**
     * @var int
     */
    public $magentaCompYield;

    /**
     * @var string
     */
    public $yellowCompSku;

    /**
     * @var float
     */
    public $yellowCompPrice;

    /**
     * @var int
     */
    public $yellowCompYield;

    /**
     * @var string
     */
    public $threeColorCompSku;

    /**
     * @var float
     */
    public $threeColorCompPrice;

    /**
     * @var int
     */
    public $threeColorCompYield;

    /**
     * @var string
     */
    public $fourColorCompSku;

    /**
     * @var float
     */
    public $fourColorCompPrice;

    /**
     * @var int
     */
    public $fourColorCompYield;

    /**
     * @var int
     */
    public $startMeterLife;

    /**
     * @var int
     */
    public $endMeterLife;

    /**
     * @var int
     */
    public $startMeterBlack;

    /**
     * @var int
     */
    public $endMeterBlack;

    /**
     * @var int
     */
    public $startMeterColor;

    /**
     * @var int
     */
    public $endMeterColor;

    /**
     * @var int
     */
    public $startMeterPrintBlack;

    /**
     * @var int
     */
    public $endMeterPrintBlack;

    /**
     * @var int
     */
    public $startMeterPrintColor;

    /**
     * @var int
     */
    public $endMeterPrintColor;

    /**
     * @var int
     */
    public $startMeterCopyBlack;

    /**
     * @var int
     */
    public $endMeterCopyBlack;

    /**
     * @var int
     */
    public $startMeterCopyColor;

    /**
     * @var int
     */
    public $endMeterCopyColor;

    /**
     * @var int
     */
    public $startMeterFax;

    /**
     * @var int
     */
    public $endMeterFax;

    /**
     * @var int
     */
    public $startMeterScan;

    /**
     * @var int
     */
    public $endMeterScan;

    /**
     * @var bool
     */
    public $jitSuppliesSupported;

    /**
     * @var bool
     */
    public $isExcluded;

    /**
     * @var bool
     */
    public $isLeased;

    /**
     * @var string
     */
    public $ipAddress;

    /**
     * @var int
     */
    public $dutyCycle;

    /**
     * @var int
     */
    public $ppmBlack;

    /**
     * @var int
     */
    public $ppmColor;

    /**
     * @var float
     */
    public $serviceCostPerPage;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->reportId) && !is_null($params->reportId))
        {
            $this->reportId = $params->reportId;
        }

        if (isset($params->uploadDataCollectorRowId) && !is_null($params->uploadDataCollectorRowId))
        {
            $this->uploadDataCollectorRowId = $params->uploadDataCollectorRowId;
        }

        if (isset($params->printerModelId) && !is_null($params->printerModelId))
        {
            $this->printerModelId = $params->printerModelId;
        }

        if (isset($params->mpsMonitorStartDate) && !is_null($params->mpsMonitorStartDate))
        {
            $this->mpsMonitorStartDate = $params->mpsMonitorStartDate;
        }

        if (isset($params->mpsMonitorEndDate) && !is_null($params->mpsMonitorEndDate))
        {
            $this->mpsMonitorEndDate = $params->mpsMonitorEndDate;
        }

        if (isset($params->mpsDiscoveryDate) && !is_null($params->mpsDiscoveryDate))
        {
            $this->mpsDiscoveryDate = $params->mpsDiscoveryDate;
        }

        if (isset($params->installDate) && !is_null($params->installDate))
        {
            $this->installDate = $params->installDate;
        }

        if (isset($params->deviceManufacturer) && !is_null($params->deviceManufacturer))
        {
            $this->deviceManufacturer = $params->deviceManufacturer;
        }

        if (isset($params->printerModel) && !is_null($params->printerModel))
        {
            $this->printerModel = $params->printerModel;
        }

        if (isset($params->printerSerialNumber) && !is_null($params->printerSerialNumber))
        {
            $this->printerSerialNumber = $params->printerSerialNumber;
        }

        if (isset($params->tonerConfigId) && !is_null($params->tonerConfigId))
        {
            $this->tonerConfigId = $params->tonerConfigId;
        }

        if (isset($params->isCopier) && !is_null($params->isCopier))
        {
            $this->isCopier = $params->isCopier;
        }

        if (isset($params->isFax) && !is_null($params->isFax))
        {
            $this->isFax = $params->isFax;
        }

        if (isset($params->isDuplex) && !is_null($params->isDuplex))
        {
            $this->isDuplex = $params->isDuplex;
        }

        if (isset($params->isScanner) && !is_null($params->isScanner))
        {
            $this->isScanner = $params->isScanner;
        }

        if (isset($params->wattsPowerNormal) && !is_null($params->wattsPowerNormal))
        {
            $this->wattsPowerNormal = $params->wattsPowerNormal;
        }

        if (isset($params->wattsPowerIdle) && !is_null($params->wattsPowerIdle))
        {
            $this->wattsPowerIdle = $params->wattsPowerIdle;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->launchDate) && !is_null($params->launchDate))
        {
            $this->launchDate = $params->launchDate;
        }

        if (isset($params->dateCreated) && !is_null($params->dateCreated))
        {
            $this->dateCreated = $params->dateCreated;
        }

        if (isset($params->blackTonerSku) && !is_null($params->blackTonerSku))
        {
            $this->blackTonerSku = $params->blackTonerSku;
        }

        if (isset($params->blackTonerPrice) && !is_null($params->blackTonerPrice))
        {
            $this->blackTonerPrice = $params->blackTonerPrice;
        }

        if (isset($params->blackTonerYield) && !is_null($params->blackTonerYield))
        {
            $this->blackTonerYield = $params->blackTonerYield;
        }

        if (isset($params->cyanTonerSku) && !is_null($params->cyanTonerSku))
        {
            $this->cyanTonerSku = $params->cyanTonerSku;
        }

        if (isset($params->cyanTonerPrice) && !is_null($params->cyanTonerPrice))
        {
            $this->cyanTonerPrice = $params->cyanTonerPrice;
        }

        if (isset($params->cyanTonerYield) && !is_null($params->cyanTonerYield))
        {
            $this->cyanTonerYield = $params->cyanTonerYield;
        }

        if (isset($params->magentaTonerSku) && !is_null($params->magentaTonerSku))
        {
            $this->magentaTonerSku = $params->magentaTonerSku;
        }

        if (isset($params->magentaTonerPrice) && !is_null($params->magentaTonerPrice))
        {
            $this->magentaTonerPrice = $params->magentaTonerPrice;
        }

        if (isset($params->magentaTonerYield) && !is_null($params->magentaTonerYield))
        {
            $this->magentaTonerYield = $params->magentaTonerYield;
        }

        if (isset($params->yellowTonerSku) && !is_null($params->yellowTonerSku))
        {
            $this->yellowTonerSku = $params->yellowTonerSku;
        }

        if (isset($params->yellowTonerPrice) && !is_null($params->yellowTonerPrice))
        {
            $this->yellowTonerPrice = $params->yellowTonerPrice;
        }

        if (isset($params->yellowTonerYield) && !is_null($params->yellowTonerYield))
        {
            $this->yellowTonerYield = $params->yellowTonerYield;
        }

        if (isset($params->threeColorTonerSku) && !is_null($params->threeColorTonerSku))
        {
            $this->threeColorTonerSku = $params->threeColorTonerSku;
        }

        if (isset($params->threeColorTonerPrice) && !is_null($params->threeColorTonerPrice))
        {
            $this->threeColorTonerPrice = $params->threeColorTonerPrice;
        }

        if (isset($params->threeColorTonerYield) && !is_null($params->threeColorTonerYield))
        {
            $this->threeColorTonerYield = $params->threeColorTonerYield;
        }

        if (isset($params->fourColorTonerSku) && !is_null($params->fourColorTonerSku))
        {
            $this->fourColorTonerSku = $params->fourColorTonerSku;
        }

        if (isset($params->fourColorTonerPrice) && !is_null($params->fourColorTonerPrice))
        {
            $this->fourColorTonerPrice = $params->fourColorTonerPrice;
        }

        if (isset($params->fourColorTonerYield) && !is_null($params->fourColorTonerYield))
        {
            $this->fourColorTonerYield = $params->fourColorTonerYield;
        }

        if (isset($params->blackCompSku) && !is_null($params->blackCompSku))
        {
            $this->blackCompSku = $params->blackCompSku;
        }

        if (isset($params->blackCompPrice) && !is_null($params->blackCompPrice))
        {
            $this->blackCompPrice = $params->blackCompPrice;
        }

        if (isset($params->blackCompYield) && !is_null($params->blackCompYield))
        {
            $this->blackCompYield = $params->blackCompYield;
        }

        if (isset($params->cyanCompSku) && !is_null($params->cyanCompSku))
        {
            $this->cyanCompSku = $params->cyanCompSku;
        }

        if (isset($params->cyanCompPrice) && !is_null($params->cyanCompPrice))
        {
            $this->cyanCompPrice = $params->cyanCompPrice;
        }

        if (isset($params->cyanCompYield) && !is_null($params->cyanCompYield))
        {
            $this->cyanCompYield = $params->cyanCompYield;
        }

        if (isset($params->magentaCompSku) && !is_null($params->magentaCompSku))
        {
            $this->magentaCompSku = $params->magentaCompSku;
        }

        if (isset($params->magentaCompPrice) && !is_null($params->magentaCompPrice))
        {
            $this->magentaCompPrice = $params->magentaCompPrice;
        }

        if (isset($params->magentaCompYield) && !is_null($params->magentaCompYield))
        {
            $this->magentaCompYield = $params->magentaCompYield;
        }

        if (isset($params->yellowCompSku) && !is_null($params->yellowCompSku))
        {
            $this->yellowCompSku = $params->yellowCompSku;
        }

        if (isset($params->yellowCompPrice) && !is_null($params->yellowCompPrice))
        {
            $this->yellowCompPrice = $params->yellowCompPrice;
        }

        if (isset($params->yellowCompYield) && !is_null($params->yellowCompYield))
        {
            $this->yellowCompYield = $params->yellowCompYield;
        }

        if (isset($params->threeColorCompSku) && !is_null($params->threeColorCompSku))
        {
            $this->threeColorCompSku = $params->threeColorCompSku;
        }

        if (isset($params->threeColorCompPrice) && !is_null($params->threeColorCompPrice))
        {
            $this->threeColorCompPrice = $params->threeColorCompPrice;
        }

        if (isset($params->threeColorCompYield) && !is_null($params->threeColorCompYield))
        {
            $this->threeColorCompYield = $params->threeColorCompYield;
        }

        if (isset($params->fourColorCompSku) && !is_null($params->fourColorCompSku))
        {
            $this->fourColorCompSku = $params->fourColorCompSku;
        }

        if (isset($params->fourColorCompPrice) && !is_null($params->fourColorCompPrice))
        {
            $this->fourColorCompPrice = $params->fourColorCompPrice;
        }

        if (isset($params->fourColorCompYield) && !is_null($params->fourColorCompYield))
        {
            $this->fourColorCompYield = $params->fourColorCompYield;
        }

        if (isset($params->startMeterLife) && !is_null($params->startMeterLife))
        {
            $this->startMeterLife = $params->startMeterLife;
        }

        if (isset($params->endMeterLife) && !is_null($params->endMeterLife))
        {
            $this->endMeterLife = $params->endMeterLife;
        }

        if (isset($params->startMeterBlack) && !is_null($params->startMeterBlack))
        {
            $this->startMeterBlack = $params->startMeterBlack;
        }

        if (isset($params->endMeterBlack) && !is_null($params->endMeterBlack))
        {
            $this->endMeterBlack = $params->endMeterBlack;
        }

        if (isset($params->startMeterColor) && !is_null($params->startMeterColor))
        {
            $this->startMeterColor = $params->startMeterColor;
        }

        if (isset($params->endMeterColor) && !is_null($params->endMeterColor))
        {
            $this->endMeterColor = $params->endMeterColor;
        }

        if (isset($params->startMeterPrintBlack) && !is_null($params->startMeterPrintBlack))
        {
            $this->startMeterPrintBlack = $params->startMeterPrintBlack;
        }

        if (isset($params->endMeterPrintBlack) && !is_null($params->endMeterPrintBlack))
        {
            $this->endMeterPrintBlack = $params->endMeterPrintBlack;
        }

        if (isset($params->startMeterPrintColor) && !is_null($params->startMeterPrintColor))
        {
            $this->startMeterPrintColor = $params->startMeterPrintColor;
        }

        if (isset($params->endMeterPrintColor) && !is_null($params->endMeterPrintColor))
        {
            $this->endMeterPrintColor = $params->endMeterPrintColor;
        }

        if (isset($params->startMeterCopyBlack) && !is_null($params->startMeterCopyBlack))
        {
            $this->startMeterCopyBlack = $params->startMeterCopyBlack;
        }

        if (isset($params->endMeterCopyBlack) && !is_null($params->endMeterCopyBlack))
        {
            $this->endMeterCopyBlack = $params->endMeterCopyBlack;
        }

        if (isset($params->startMeterCopyColor) && !is_null($params->startMeterCopyColor))
        {
            $this->startMeterCopyColor = $params->startMeterCopyColor;
        }

        if (isset($params->endMeterCopyColor) && !is_null($params->endMeterCopyColor))
        {
            $this->endMeterCopyColor = $params->endMeterCopyColor;
        }

        if (isset($params->startMeterFax) && !is_null($params->startMeterFax))
        {
            $this->startMeterFax = $params->startMeterFax;
        }

        if (isset($params->endMeterFax) && !is_null($params->endMeterFax))
        {
            $this->endMeterFax = $params->endMeterFax;
        }

        if (isset($params->startMeterScan) && !is_null($params->startMeterScan))
        {
            $this->startMeterScan = $params->startMeterScan;
        }

        if (isset($params->endMeterScan) && !is_null($params->endMeterScan))
        {
            $this->endMeterScan = $params->endMeterScan;
        }

        if (isset($params->jitSuppliesSupported) && !is_null($params->jitSuppliesSupported))
        {
            $this->jitSuppliesSupported = $params->jitSuppliesSupported;
        }

        if (isset($params->isExcluded) && !is_null($params->isExcluded))
        {
            $this->isExcluded = $params->isExcluded;
        }

        if (isset($params->isLeased) && !is_null($params->isLeased))
        {
            $this->isLeased = $params->isLeased;
        }

        if (isset($params->ipAddress) && !is_null($params->ipAddress))
        {
            $this->ipAddress = $params->ipAddress;
        }

        if (isset($params->dutyCycle) && !is_null($params->dutyCycle))
        {
            $this->dutyCycle = $params->dutyCycle;
        }

        if (isset($params->ppmBlack) && !is_null($params->ppmBlack))
        {
            $this->ppmBlack = $params->ppmBlack;
        }

        if (isset($params->ppmColor) && !is_null($params->ppmColor))
        {
            $this->ppmColor = $params->ppmColor;
        }

        if (isset($params->serviceCostPerPage) && !is_null($params->serviceCostPerPage))
        {
            $this->serviceCostPerPage = $params->serviceCostPerPage;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                       => $this->id,
            "userId"                   => $this->userId,
            "reportId"                 => $this->reportId,
            "uploadDataCollectorRowId" => $this->uploadDataCollectorRowId,
            "printerModelId"           => $this->printerModelId,
            "mpsMonitorStartDate"      => $this->mpsMonitorStartDate,
            "mpsMonitorEndDate"        => $this->mpsMonitorEndDate,
            "mpsDiscoveryDate"         => $this->mpsDiscoveryDate,
            "installDate"              => $this->installDate,
            "deviceManufacturer"       => $this->deviceManufacturer,
            "printerModel"             => $this->printerModel,
            "printerSerialNumber"      => $this->printerSerialNumber,
            "tonerConfigId"            => $this->tonerConfigId,
            "isCopier"                 => $this->isCopier,
            "isFax"                    => $this->isFax,
            "isDuplex"                 => $this->isDuplex,
            "isScanner"                => $this->isScanner,
            "wattsPowerNormal"         => $this->wattsPowerNormal,
            "wattsPowerIdle"           => $this->wattsPowerIdle,
            "cost"                     => $this->cost,
            "launchDate"               => $this->launchDate,
            "dateCreated"              => $this->dateCreated,
            "blackTonerSku"            => $this->blackTonerSku,
            "blackTonerPrice"          => $this->blackTonerPrice,
            "blackTonerYield"          => $this->blackTonerYield,
            "cyanTonerSku"             => $this->cyanTonerSku,
            "cyanTonerPrice"           => $this->cyanTonerPrice,
            "cyanTonerYield"           => $this->cyanTonerYield,
            "magentaTonerSku"          => $this->magentaTonerSku,
            "magentaTonerPrice"        => $this->magentaTonerPrice,
            "magentaTonerYield"        => $this->magentaTonerYield,
            "yellowTonerSku"           => $this->yellowTonerSku,
            "yellowTonerPrice"         => $this->yellowTonerPrice,
            "yellowTonerYield"         => $this->yellowTonerYield,
            "threeColorTonerSku"       => $this->threeColorTonerSku,
            "threeColorTonerPrice"     => $this->threeColorTonerPrice,
            "threeColorTonerYield"     => $this->threeColorTonerYield,
            "fourColorTonerSku"        => $this->fourColorTonerSku,
            "fourColorTonerPrice"      => $this->fourColorTonerPrice,
            "fourColorTonerYield"      => $this->fourColorTonerYield,
            "blackCompSku"             => $this->blackCompSku,
            "blackCompPrice"           => $this->blackCompPrice,
            "blackCompYield"           => $this->blackCompYield,
            "cyanCompSku"              => $this->cyanCompSku,
            "cyanCompPrice"            => $this->cyanCompPrice,
            "cyanCompYield"            => $this->cyanCompYield,
            "magentaCompSku"           => $this->magentaCompSku,
            "magentaCompPrice"         => $this->magentaCompPrice,
            "magentaCompYield"         => $this->magentaCompYield,
            "yellowCompSku"            => $this->yellowCompSku,
            "yellowCompPrice"          => $this->yellowCompPrice,
            "yellowCompYield"          => $this->yellowCompYield,
            "threeColorCompSku"        => $this->threeColorCompSku,
            "threeColorCompPrice"      => $this->threeColorCompPrice,
            "threeColorCompYield"      => $this->threeColorCompYield,
            "fourColorCompSku"         => $this->fourColorCompSku,
            "fourColorCompPrice"       => $this->fourColorCompPrice,
            "fourColorCompYield"       => $this->fourColorCompYield,
            "startMeterLife"           => $this->startMeterLife,
            "endMeterLife"             => $this->endMeterLife,
            "startMeterBlack"          => $this->startMeterBlack,
            "endMeterBlack"            => $this->endMeterBlack,
            "startMeterColor"          => $this->startMeterColor,
            "endMeterColor"            => $this->endMeterColor,
            "startMeterPrintBlack"     => $this->startMeterPrintBlack,
            "endMeterPrintBlack"       => $this->endMeterPrintBlack,
            "startMeterPrintColor"     => $this->startMeterPrintColor,
            "endMeterPrintColor"       => $this->endMeterPrintColor,
            "startMeterCopyBlack"      => $this->startMeterCopyBlack,
            "endMeterCopyBlack"        => $this->endMeterCopyBlack,
            "startMeterCopyColor"      => $this->startMeterCopyColor,
            "endMeterCopyColor"        => $this->endMeterCopyColor,
            "startMeterFax"            => $this->startMeterFax,
            "endMeterFax"              => $this->endMeterFax,
            "startMeterScan"           => $this->startMeterScan,
            "endMeterScan"             => $this->endMeterScan,
            "jitSuppliesSupported"     => $this->jitSuppliesSupported,
            "isExcluded"               => $this->isExcluded,
            "isLeased"                 => $this->isLeased,
            "ipAddress"                => $this->ipAddress,
            "dutyCycle"                => $this->dutyCycle,
            "ppmBlack"                 => $this->ppmBlack,
            "ppmColor"                 => $this->ppmColor,
            "serviceCostPerPage"       => $this->serviceCostPerPage,
        );
    }
}