<?php
/**
 * Class Proposalgen_Service_Rms_Upload_Line
 */
class Proposalgen_Service_Rms_Upload_Line extends My_Model_Abstract
{
    const METER_IS_VALID       = 0;
    const METER_IS_NOT_PRESENT = 1;
    const METER_IS_INVALID     = 2;

    /**
     * The minimum number of days that we need to monitor a device before we can get a decent average page count
     *
     * @var int
     */
    const MINIMUM_MONITOR_INTERVAL_DAYS = 4;

    /**
     * The maximum age for the introduction date
     *
     * @var int
     */
    const MAXIMUM_DEVICE_AGE_YEARS = 5;

    /**
     * The format needed to change a DateTime object into a MySQL compatible time
     *
     * @var string
     */
    const DATETIME_TO_MYSQL_DATE_FORMAT = "Y-m-d H:i:s";

    /**
     * @var bool
     */
    public $isManaged;

    /**
     * @var string|int
     */
    public $rmsDeviceId;

    /**
     * @var int
     */
    public $rmsModelId;

    /**
     * @var string
     */
    public $assetId;

    /**
     * @var string
     */
    public $monitorStartDate;

    /**
     * @var string
     */
    public $monitorEndDate;

    /**
     * @var string
     */
    public $adoptionDate;

    /**
     * @var float
     */
    public $cost;

    /**
     * @var string
     */
    public $discoveryDate;

    /**
     * @var string
     */
    public $launchDate;

    /**
     * @var string
     */
    public $ipAddress;

    /**
     * @var bool
     */
    public $isColor;

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
    public $isA3;

    /**
     * @var bool
     */
    public $isDuplex;

    /**
     * @var string
     */
    public $manufacturer;

    /**
     * @var string
     */
    public $rawDeviceName;

    /**
     * @var string
     */
    public $modelName;

    /**
     * @var int
     */
    public $ppmBlack;

    /**
     * @var int
     */
    public $ppmColor;

    /**
     * @var string
     */
    public $serialNumber;

    /**
     * @var int
     */
    public $wattsOperating;

    /**
     * @var int
     */
    public $wattsIdle;

    /**
     * @var string
     */
    public $blackTonerSku;

    /**
     * @var int
     */
    public $blackTonerYield;

    /**
     * @var float
     */
    public $blackTonerCost;

    /**
     * @var string
     */
    public $cyanTonerSku;

    /**
     * @var int
     */
    public $cyanTonerYield;

    /**
     * @var float
     */
    public $cyanTonerCost;

    /**
     * @var string
     */
    public $magentaTonerSku;

    /**
     * @var int
     */
    public $magentaTonerYield;

    /**
     * @var float
     */
    public $magentaTonerCost;

    /**
     * @var string
     */
    public $yellowTonerSku;

    /**
     * @var int
     */
    public $yellowTonerYield;

    /**
     * @var float
     */
    public $yellowTonerCost;

    /**
     * @var string
     */
    public $threeColorTonerSku;

    /**
     * @var int
     */
    public $threeColorTonerYield;

    /**
     * @var float
     */
    public $threeColorTonerCost;

    /**
     * @var string
     */
    public $fourColorTonerSku;

    /**
     * @var int
     */
    public $fourColorTonerYield;

    /**
     * @var float
     */
    public $fourColorTonerCost;

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
    public $startMeterLife;

    /**
     * @var int
     */
    public $endMeterLife;

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
    public $startMeterScan;

    /**
     * @var int
     */
    public $endMeterScan;

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
    public $startMeterPrintA3Black;

    /**
     * @var int
     */
    public $endMeterPrintA3Black;

    /**
     * @var int
     */
    public $startMeterPrintA3Color;

    /**
     * @var int
     */
    public $endMeterPrintA3Color;

    /**
     * @var bool
     */
    public $reportsTonerLevels;

    /**
     * @var int
     */
    public $tonerLevelBlack;

    /**
     * @var int
     */
    public $tonerLevelCyan;

    /**
     * @var int
     */
    public $tonerLevelMagenta;

    /**
     * @var int
     */
    public $tonerLevelYellow;

    /**
     * @var bool
     */
    public $isValid;

    /**
     * @var string
     */
    public $validationErrorMessage;

    /**
     * @var bool
     */
    public $hasCompleteInformation;

    /**
     * @var int
     */
    public $csvLineNumber;

    /**
     * @var int
     */
    public $tonerConfigId;

    /**
     * @var float
     */
    public $pageCoverageMonochrome;

    /**
     * @var float
     */
    public $pageCoverageCyan;
    /**
     * @var float
     */
    public $pageCoverageMagenta;
    /**
     * @var float
     */
    public $pageCoverageYellow;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->rmsModelId) && !is_null($params->rmsModelId))
        {
            $this->rmsModelId = $params->rmsModelId;
        }

        if (isset($params->assetId) && !is_null($params->assetId))
        {
            $this->assetId = $params->assetId;
        }

        if (isset($params->monitorStartDate) && !is_null($params->monitorStartDate))
        {
            $this->monitorStartDate = $params->monitorStartDate;
        }

        if (isset($params->monitorEndDate) && !is_null($params->monitorEndDate))
        {
            $this->monitorEndDate = $params->monitorEndDate;
        }

        if (isset($params->adoptionDate) && !is_null($params->adoptionDate))
        {
            $this->adoptionDate = $params->adoptionDate;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->discoveryDate) && !is_null($params->discoveryDate))
        {
            $this->discoveryDate = $params->discoveryDate;
        }

        if (isset($params->launchDate) && !is_null($params->launchDate))
        {
            $this->launchDate = $params->launchDate;
        }

        if (isset($params->ipAddress) && !is_null($params->ipAddress))
        {
            $this->ipAddress = $params->ipAddress;
        }

        if (isset($params->isColor) && !is_null($params->isColor))
        {
            $this->isColor = $params->isColor;
        }

        if (isset($params->isCopier) && !is_null($params->isCopier))
        {
            $this->isCopier = $params->isCopier;
        }

        if (isset($params->isFax) && !is_null($params->isFax))
        {
            $this->isFax = $params->isFax;
        }

        if (isset($params->isA3) && !is_null($params->isA3))
        {
            $this->isA3 = $params->isA3;
        }

        if (isset($params->isDuplex) && !is_null($params->isDuplex))
        {
            $this->isDuplex = $params->isDuplex;
        }

        if (isset($params->manufacturer) && !is_null($params->manufacturer))
        {
            $this->manufacturer = $params->manufacturer;
        }

        if (isset($params->rawDeviceName) && !is_null($params->rawDeviceName))
        {
            $this->rawDeviceName = $params->rawDeviceName;
        }

        if (isset($params->modelName) && !is_null($params->modelName))
        {
            $this->modelName = $params->modelName;
        }

        if (isset($params->ppmBlack) && !is_null($params->ppmBlack))
        {
            $this->ppmBlack = $params->ppmBlack;
        }

        if (isset($params->ppmColor) && !is_null($params->ppmColor))
        {
            $this->ppmColor = $params->ppmColor;
        }

        if (isset($params->serialNumber) && !is_null($params->serialNumber))
        {
            $this->serialNumber = $params->serialNumber;
        }

        if (isset($params->wattsOperating) && !is_null($params->wattsOperating))
        {
            $this->wattsOperating = $params->wattsOperating;
        }

        if (isset($params->wattsIdle) && !is_null($params->wattsIdle))
        {
            $this->wattsIdle = $params->wattsIdle;
        }

        if (isset($params->blackTonerSku) && !is_null($params->blackTonerSku))
        {
            $this->blackTonerSku = $params->blackTonerSku;
        }

        if (isset($params->blackTonerYield) && !is_null($params->blackTonerYield))
        {
            $this->blackTonerYield = $params->blackTonerYield;
        }

        if (isset($params->blackTonerCost) && !is_null($params->blackTonerCost))
        {
            $this->blackTonerCost = $params->blackTonerCost;
        }

        if (isset($params->cyanTonerSku) && !is_null($params->cyanTonerSku))
        {
            $this->cyanTonerSku = $params->cyanTonerSku;
        }

        if (isset($params->cyanTonerYield) && !is_null($params->cyanTonerYield))
        {
            $this->cyanTonerYield = $params->cyanTonerYield;
        }

        if (isset($params->cyanTonerCost) && !is_null($params->cyanTonerCost))
        {
            $this->cyanTonerCost = $params->cyanTonerCost;
        }

        if (isset($params->magentaTonerSku) && !is_null($params->magentaTonerSku))
        {
            $this->magentaTonerSku = $params->magentaTonerSku;
        }

        if (isset($params->magentaTonerYield) && !is_null($params->magentaTonerYield))
        {
            $this->magentaTonerYield = $params->magentaTonerYield;
        }

        if (isset($params->magentaTonerCost) && !is_null($params->magentaTonerCost))
        {
            $this->magentaTonerCost = $params->magentaTonerCost;
        }

        if (isset($params->yellowTonerSku) && !is_null($params->yellowTonerSku))
        {
            $this->yellowTonerSku = $params->yellowTonerSku;
        }

        if (isset($params->yellowTonerYield) && !is_null($params->yellowTonerYield))
        {
            $this->yellowTonerYield = $params->yellowTonerYield;
        }

        if (isset($params->yellowTonerCost) && !is_null($params->yellowTonerCost))
        {
            $this->yellowTonerCost = $params->yellowTonerCost;
        }

        if (isset($params->threeColorTonerSku) && !is_null($params->threeColorTonerSku))
        {
            $this->threeColorTonerSku = $params->threeColorTonerSku;
        }

        if (isset($params->threeColorTonerYield) && !is_null($params->threeColorTonerYield))
        {
            $this->threeColorTonerYield = $params->threeColorTonerYield;
        }

        if (isset($params->threeColorTonerCost) && !is_null($params->threeColorTonerCost))
        {
            $this->threeColorTonerCost = $params->threeColorTonerCost;
        }

        if (isset($params->fourColorTonerSku) && !is_null($params->fourColorTonerSku))
        {
            $this->fourColorTonerSku = $params->fourColorTonerSku;
        }

        if (isset($params->fourColorTonerYield) && !is_null($params->fourColorTonerYield))
        {
            $this->fourColorTonerYield = $params->fourColorTonerYield;
        }

        if (isset($params->fourColorTonerCost) && !is_null($params->fourColorTonerCost))
        {
            $this->fourColorTonerCost = $params->fourColorTonerCost;
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

        if (isset($params->startMeterLife) && !is_null($params->startMeterLife))
        {
            $this->startMeterLife = $params->startMeterLife;
        }

        if (isset($params->endMeterLife) && !is_null($params->endMeterLife))
        {
            $this->endMeterLife = $params->endMeterLife;
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

        if (isset($params->startMeterScan) && !is_null($params->startMeterScan))
        {
            $this->startMeterScan = $params->startMeterScan;
        }

        if (isset($params->endMeterScan) && !is_null($params->endMeterScan))
        {
            $this->endMeterScan = $params->endMeterScan;
        }

        if (isset($params->startMeterFax) && !is_null($params->startMeterFax))
        {
            $this->startMeterFax = $params->startMeterFax;
        }

        if (isset($params->endMeterFax) && !is_null($params->endMeterFax))
        {
            $this->endMeterFax = $params->endMeterFax;
        }

        if (isset($params->startMeterPrintA3Black) && !is_null($params->startMeterPrintA3Black))
        {
            $this->startMeterPrintA3Black = $params->startMeterPrintA3Black;
        }

        if (isset($params->endMeterPrintA3Black) && !is_null($params->endMeterPrintA3Black))
        {
            $this->endMeterPrintA3Black = $params->endMeterPrintA3Black;
        }

        if (isset($params->startMeterPrintA3Color) && !is_null($params->startMeterPrintA3Color))
        {
            $this->startMeterPrintA3Color = $params->startMeterPrintA3Color;
        }

        if (isset($params->endMeterPrintA3Color) && !is_null($params->endMeterPrintA3Color))
        {
            $this->endMeterPrintA3Color = $params->endMeterPrintA3Color;
        }

        if (isset($params->reportsTonerLevels) && !is_null($params->reportsTonerLevels))
        {
            $this->reportsTonerLevels = $params->reportsTonerLevels;
        }

        if (isset($params->tonerLevelBlack) && !is_null($params->tonerLevelBlack))
        {
            $this->tonerLevelBlack = $params->tonerLevelBlack;
        }

        if (isset($params->tonerLevelCyan) && !is_null($params->tonerLevelCyan))
        {
            $this->tonerLevelCyan = $params->tonerLevelCyan;
        }

        if (isset($params->tonerLevelMagenta) && !is_null($params->tonerLevelMagenta))
        {
            $this->tonerLevelMagenta = $params->tonerLevelMagenta;
        }

        if (isset($params->tonerLevelYellow) && !is_null($params->tonerLevelYellow))
        {
            $this->tonerLevelYellow = $params->tonerLevelYellow;
        }

        if (isset($params->isValid) && !is_null($params->isValid))
        {
            $this->isValid = $params->isValid;
        }

        if (isset($params->validationErrorMessage) && !is_null($params->validationErrorMessage))
        {
            $this->validationErrorMessage = $params->validationErrorMessage;
        }

        if (isset($params->hasCompleteInformation) && !is_null($params->hasCompleteInformation))
        {
            $this->hasCompleteInformation = $params->hasCompleteInformation;
        }

        if (isset($params->csvLineNumber) && !is_null($params->csvLineNumber))
        {
            $this->csvLineNumber = $params->csvLineNumber;
        }

        if (isset($params->tonerConfigId) && !is_null($params->tonerConfigId))
        {
            $this->tonerConfigId = $params->tonerConfigId;
        }

        if (isset($params->pageCoverageMonochrome) && !is_null($params->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $params->pageCoverageMonochrome;
        }

        if (isset($params->pageCoverageCyan) && !is_null($params->pageCoverageCyan))
        {
            $this->pageCoverageCyan = $params->pageCoverageCyan;
        }

        if (isset($params->pageCoverageMagenta) && !is_null($params->pageCoverageMagenta))
        {
            $this->pageCoverageMagenta = $params->pageCoverageMagenta;
        }

        if (isset($params->pageCoverageYellow) && !is_null($params->pageCoverageYellow))
        {
            $this->pageCoverageYellow = $params->pageCoverageYellow;
        }

        if (isset($params->rmsDeviceId) && !is_null($params->rmsDeviceId))
        {
            $this->rmsDeviceId = $params->rmsDeviceId;
        }

        if (isset($params->rmsDeviceId) && !is_null($params->rmsDeviceId))
        {
            $this->rmsDeviceId = $params->rmsDeviceId;
        }

        if (isset($params->isManaged) && !is_null($params->isManaged))
        {
            $this->isManaged = $params->isManaged;

        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "rmsModelId"             => $this->rmsModelId,
            "assetId"                => $this->assetId,
            "monitorStartDate"       => $this->monitorStartDate,
            "monitorEndDate"         => $this->monitorEndDate,
            "adoptionDate"           => $this->adoptionDate,
            "cost"                   => $this->cost,
            "discoveryDate"          => $this->discoveryDate,
            "launchDate"             => $this->launchDate,
            "ipAddress"              => $this->ipAddress,
            "isColor"                => $this->isColor,
            "isCopier"               => $this->isCopier,
            "isFax"                  => $this->isFax,
            "isA3"                   => $this->isA3,
            "isDuplex"               => $this->isDuplex,
            "isManaged"              => $this->isManaged,
            "manufacturer"           => $this->manufacturer,
            "rawDeviceName"          => $this->rawDeviceName,
            "modelName"              => $this->modelName,
            "ppmBlack"               => $this->ppmBlack,
            "ppmColor"               => $this->ppmColor,
            "serialNumber"           => $this->serialNumber,
            "wattsOperating"         => $this->wattsOperating,
            "wattsIdle"              => $this->wattsIdle,
            "blackTonerSku"          => $this->blackTonerSku,
            "blackTonerYield"        => $this->blackTonerYield,
            "blackTonerCost"         => $this->blackTonerCost,
            "cyanTonerSku"           => $this->cyanTonerSku,
            "cyanTonerYield"         => $this->cyanTonerYield,
            "cyanTonerCost"          => $this->cyanTonerCost,
            "magentaTonerSku"        => $this->magentaTonerSku,
            "magentaTonerYield"      => $this->magentaTonerYield,
            "magentaTonerCost"       => $this->magentaTonerCost,
            "yellowTonerSku"         => $this->yellowTonerSku,
            "yellowTonerYield"       => $this->yellowTonerYield,
            "yellowTonerCost"        => $this->yellowTonerCost,
            "threeColorTonerSku"     => $this->threeColorTonerSku,
            "threeColorTonerYield"   => $this->threeColorTonerYield,
            "threeColorTonerCost"    => $this->threeColorTonerCost,
            "fourColorTonerSku"      => $this->fourColorTonerSku,
            "fourColorTonerYield"    => $this->fourColorTonerYield,
            "fourColorTonerCost"     => $this->fourColorTonerCost,
            "startMeterBlack"        => $this->startMeterBlack,
            "endMeterBlack"          => $this->endMeterBlack,
            "startMeterColor"        => $this->startMeterColor,
            "endMeterColor"          => $this->endMeterColor,
            "startMeterLife"         => $this->startMeterLife,
            "endMeterLife"           => $this->endMeterLife,
            "startMeterPrintBlack"   => $this->startMeterPrintBlack,
            "endMeterPrintBlack"     => $this->endMeterPrintBlack,
            "startMeterPrintColor"   => $this->startMeterPrintColor,
            "endMeterPrintColor"     => $this->endMeterPrintColor,
            "startMeterCopyBlack"    => $this->startMeterCopyBlack,
            "endMeterCopyBlack"      => $this->endMeterCopyBlack,
            "startMeterCopyColor"    => $this->startMeterCopyColor,
            "endMeterCopyColor"      => $this->endMeterCopyColor,
            "startMeterScan"         => $this->startMeterScan,
            "endMeterScan"           => $this->endMeterScan,
            "startMeterFax"          => $this->startMeterFax,
            "endMeterFax"            => $this->endMeterFax,
            "reportsTonerLevels"     => $this->reportsTonerLevels,
            "tonerLevelBlack"        => $this->tonerLevelBlack,
            "tonerLevelCyan"         => $this->tonerLevelCyan,
            "tonerLevelMagenta"      => $this->tonerLevelMagenta,
            "tonerLevelYellow"       => $this->tonerLevelYellow,
            "isValid"                => $this->isValid,
            "validationErrorMessage" => $this->validationErrorMessage,
            "hasCompleteInformation" => $this->hasCompleteInformation,
            "csvLineNumber"          => $this->csvLineNumber,
            "tonerConfigId"          => $this->tonerConfigId,
            "pageCoverageMonochrome" => $this->pageCoverageMonochrome,
            "pageCoverageCyan"       => $this->pageCoverageCyan,
            "pageCoverageMagenta"    => $this->pageCoverageMagenta,
            "pageCoverageYellow"     => $this->pageCoverageYellow,
        );
    }

    /**
     * @param $incomingDateFormat
     *
     * @return bool|string
     */
    public function isValid ($incomingDateFormat)
    {
        // Settings
        $minimumDeviceIntroductionDate = new DateTime("-" . self::MAXIMUM_DEVICE_AGE_YEARS . " years");

        // Validate that certain fields are present
        if (empty($this->modelName))
        {
            return "Device does not have a model name";
        }
        if (empty($this->manufacturer))
        {
            return "Device does not have a manufacturer";
        }
        if (empty($this->monitorStartDate))
        {
            return "Device does not have a start date";
        }
        if (empty($this->monitorEndDate))
        {
            return "Device does not have a end date";
        }

        /*
         * Device name sanitization
         */
        $manufacturer = $this->manufacturer;
        $deviceName   = $this->modelName;

        // Remove the manufacturer name from the model name
        $deviceName = str_ireplace($manufacturer . ' ', '', $deviceName);

        // If the manufacturer is Hewlett-Packard we also need to strip away HP
        if (strcasecmp($manufacturer, 'hewlett-packard') === 0)
        {
            $deviceName = str_ireplace('hp ', '', $deviceName);
        }

        // Correct the device name
        $deviceName = ucwords(trim($deviceName));

        // Convert HP into it's full name
        if ($manufacturer == "hp")
        {
            $manufacturer = "hewlett-packard";
        }

        $this->modelName    = $deviceName;
        $this->manufacturer = ucwords($manufacturer);

        // Check the meter columns
        $checkMetersValidation = $this->validateMeters();
        if ($checkMetersValidation !== true)
        {
            return $checkMetersValidation;
        }

        // Turn all the dates into objects
        $monitorStartDate = (empty($this->monitorStartDate)) ? null : $this->_getDateTime($incomingDateFormat, $this->monitorStartDate);
        $monitorEndDate   = (empty($this->monitorEndDate)) ? null : $this->_getDateTime($incomingDateFormat, $this->monitorEndDate);
        $discoveryDate    = (empty($this->discoveryDate)) ? null : $this->_getDateTime($incomingDateFormat, $this->discoveryDate);
        $introductionDate = (empty($this->launchDate)) ? null : $this->_getDateTime($incomingDateFormat, $this->launchDate);
        $adoptionDate     = (empty($this->adoptionDate)) ? null : $this->_getDateTime($incomingDateFormat, $this->adoptionDate);

        if (!$monitorStartDate)
        {
            return "Invalid Monitor Start Date";
        }

        if (!$monitorEndDate)
        {
            return "Invalid Monitor End Date";
        }

        // If the discovery date is after the start date, use the discovery date
        if ($discoveryDate instanceof DateTime)
        {
            $dateDiff = $discoveryDate->diff($monitorStartDate);
            if ($dateDiff->invert == true)
            {
                // Set the monitor start date to the discovery date
                $monitorStartDate = $discoveryDate;
            }
        }


        // Figure out how long we've been monitoring this device
        $monitoringInterval = $monitorStartDate->diff($monitorEndDate);

        // Monitoring should not be inverted (means start date occurred after end date)
        if ($monitoringInterval->invert || $monitoringInterval->days < self::MINIMUM_MONITOR_INTERVAL_DAYS)
        {
            return "Device was monitored for less than " . self::MINIMUM_MONITOR_INTERVAL_DAYS . " days.";
        }

        // Check to make sure our start date is not more than 5 years old
        if ($minimumDeviceIntroductionDate->diff($monitorStartDate)->invert == 1)
        {
            return "Start date is greater than " . self::MAXIMUM_DEVICE_AGE_YEARS . " years old.";
        }


        // Convert all the dates back to MySQL dates
        $this->monitorStartDate = (!$monitorStartDate) ? null : $monitorStartDate->format(self::DATETIME_TO_MYSQL_DATE_FORMAT);
        $this->monitorEndDate   = (!$monitorEndDate) ? null : $monitorEndDate->format(self::DATETIME_TO_MYSQL_DATE_FORMAT);
        $this->discoveryDate    = (!$discoveryDate) ? null : $discoveryDate->format(self::DATETIME_TO_MYSQL_DATE_FORMAT);
        $this->launchDate       = (!$introductionDate) ? null : $introductionDate->format(self::DATETIME_TO_MYSQL_DATE_FORMAT);
        $this->adoptionDate     = (!$adoptionDate) ? null : $adoptionDate->format(self::DATETIME_TO_MYSQL_DATE_FORMAT);

        /**
         * Figure out if the device is a3
         */
        if ($this->isA3 == false)
        {
            if ($this->tonerConfigId === Proposalgen_Model_TonerConfig::BLACK_ONLY)
            {
                if ($this->endMeterPrintA3Black > 0)
                {
                    $this->isA3 = true;
                }
            }
            else if ($this->endMeterPrintA3Color > 0)
            {
                $this->isA3 = true;
            }
        }


        /*
         * Figure out if the device reports toner levels
         */
        $this->reportsTonerLevels = false;

        if ($this->tonerConfigId === Proposalgen_Model_TonerConfig::BLACK_ONLY)
        {
            /*
             * Monochrome device JIT
             */
            if ($this->validateTonerLevel($this->tonerLevelBlack))
            {
                $this->reportsTonerLevels = true;
            }
        }
        else
        {
            /*
             * Color Device JIT
             */
            // At least one level must return a percentage or number
            if ($this->validateTonerLevel($this->tonerLevelBlack) || $this->validateTonerLevel($this->tonerLevelCyan) || $this->validateTonerLevel($this->tonerLevelMagenta) || $this->validateTonerLevel($this->tonerLevelYellow))
            {
                // All toner levels must have a percentage/number/low/ok
                if ($this->validateTonerLevel($this->tonerLevelBlack, true) && $this->validateTonerLevel($this->tonerLevelCyan, true) & $this->validateTonerLevel($this->tonerLevelMagenta, true) && $this->validateTonerLevel($this->tonerLevelYellow, true))
                {
                    $this->reportsTonerLevels = true;
                }
            }
        }

        return true;
    }

    /**
     * Attempts to get a datetime from an incoming string
     *
     * @param $formats
     * @param $dateTimeString
     *
     * @return \DateTime|null
     */
    protected function _getDateTime ($formats, $dateTimeString)
    {
        $date = null;
        // Convert to array if it's not already
        if (!is_array($formats))
        {
            $formats = array($formats);
        }

        foreach ($formats as $format)
        {
            $date = DateTime::createFromFormat($format, $dateTimeString);
            if ($date)
            {
                break;
            }
        }

        return $date;
    }

    /**
     * Validates a toner level.
     *
     * @param      $tonerLevel
     * @param bool $acceptLowAndOk
     *
     * @return bool
     */
    public function validateTonerLevel ($tonerLevel, $acceptLowAndOk = false)
    {
        // It's a percentage, this is valid
        if (strpos($tonerLevel, '%'))
        {
            return true;
        }

        // If it's a number and between 0 and 100 inclusively we can consider it valid
        $intValidator = new Zend_Validate_Int();
        if ($intValidator->isValid($tonerLevel) && $tonerLevel >= 0 && $tonerLevel <= 100)
        {
            return true;
        }

        /*
         * This is only good when we have a percentage/number somewhere else
         */
        if ($acceptLowAndOk)
        {
            if ($tonerLevel == 'LOW' || $tonerLevel == 'OK')
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Validates the meters
     *
     * @return boolean True if the meters are ok or returns an error message
     */
    public function validateMeters ()
    {
        /*
         * The start meter of any meter should never be greater than the end meter.
         */
        $blackMeterStatus      = $this->_validateMeter($this->startMeterBlack, $this->endMeterBlack);
        $colorMeterStatus      = $this->_validateMeter($this->startMeterColor, $this->endMeterColor);
        $lifeMeterStatus       = $this->_validateMeter($this->startMeterLife, $this->endMeterLife);
        $printBlackMeterStatus = $this->_validateMeter($this->startMeterPrintBlack, $this->endMeterPrintBlack);
        $printColorMeterStatus = $this->_validateMeter($this->startMeterPrintColor, $this->endMeterPrintColor);
        $copyBlackMeterStatus  = $this->_validateMeter($this->startMeterCopyBlack, $this->endMeterCopyBlack);
        $copyColorMeterStatus  = $this->_validateMeter($this->startMeterCopyColor, $this->endMeterCopyColor);
        $scanMeterStatus       = $this->_validateMeter($this->startMeterScan, $this->endMeterScan);
        $faxMeterStatus        = $this->_validateMeter($this->startMeterFax, $this->endMeterFax);

        /**
         * While we're here, lets do some toner configuration guessing
         */
        if ($colorMeterStatus === self::METER_IS_VALID)
        {
            // What type of color are we?
            if ($this->fourColorTonerCost !== null || $this->fourColorTonerSku !== null || $this->fourColorTonerSku !== null)
            {
                $this->tonerConfigId = Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED;
            }
            else if ($this->threeColorTonerCost !== null || $this->threeColorTonerSku !== null || $this->threeColorTonerSku !== null)
            {
                $this->tonerConfigId = Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED;
            }
            else
            {
                $this->tonerConfigId = Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED;
            }
        }
        else
        {
            // Set configuration to black only
            $this->tonerConfigId = Proposalgen_Model_TonerConfig::BLACK_ONLY;
        }

        /**
         * Process && Normalize meters
         */
        // If we are missing a black meter and are missing a color/life meter then we cannot proceed
        if ($blackMeterStatus !== self::METER_IS_VALID && $lifeMeterStatus !== self::METER_IS_VALID)
        {
            return "Invalid black meter";
        }

        if ($blackMeterStatus !== self::METER_IS_VALID)
        {
            /*
             * If we get here it means that we have an invalid black meter, but valid color and life meter. We can now calculate our black meter
             */
            if ($colorMeterStatus === self::METER_IS_VALID)
            {
                $this->startMeterBlack = $this->startMeterLife - $this->startMeterColor;
                $this->endMeterBlack   = $this->endMeterLife - $this->endMeterColor;
            }
            else
            {
                $this->startMeterBlack = $this->startMeterLife;
                $this->endMeterBlack   = $this->endMeterLife;
            }
        }

        // Color meter
        if ($colorMeterStatus === self::METER_IS_INVALID)
        {
            if ($lifeMeterStatus === self::METER_IS_VALID)
            {
                // Derive Color from Life - Black Meter only if it's > 0
                if ($this->startMeterLife !== $this->startMeterBlack && $this->endMeterLife !== $this->endMeterBlack)
                {
                    $this->startMeterColor = $this->startMeterLife - $this->startMeterBlack;
                    $this->endMeterColor   = $this->endMeterLife - $this->endMeterBlack;
                }
            }

            return "Invalid color meter";
        }

        // Life meter
        if ($lifeMeterStatus === self::METER_IS_INVALID)
        {
            $this->startMeterLife = $this->startMeterBlack;
            $this->endMeterLife   = $this->endMeterBlack;

            // If we have a color meter, add that to our life count
            if ($colorMeterStatus === self::METER_IS_VALID)
            {
                $this->startMeterLife = $this->startMeterLife + $this->startMeterColor;
                $this->endMeterLife   = $this->endMeterLife + $this->endMeterColor;
            }
        }


        // Print Black meter
        if ($printBlackMeterStatus === self::METER_IS_INVALID)
        {
            /*
             * Right now we don't really care about this meter. Lets set it to null when invalid
             */
            $this->startMeterPrintBlack = null;
            $this->endMeterPrintBlack   = null;
            // return "Invalid print black meter";
        }

        // Print Color  meter
        if ($printColorMeterStatus === self::METER_IS_INVALID)
        {
            /*
             * Right now we don't really care about this meter. Lets set it to null when invalid
             */
            $this->startMeterPrintColor = null;
            $this->endMeterPrintColor   = null;
            // return "Invalid print color meter";
        }

        // Copy Black meter
        if ($copyBlackMeterStatus === self::METER_IS_INVALID)
        {
            /*
             * Right now we don't really care about this meter. Lets set it to null when invalid
             */
            $this->startMeterCopyBlack = null;
            $this->endMeterCopyBlack   = null;
            // return "Invalid copy black meter";
        }

        // Copy Color meter
        if ($copyColorMeterStatus === self::METER_IS_INVALID)
        {
            /*
             * Right now we don't really care about this meter. Lets set it to null when invalid
             */
            $this->startMeterCopyColor = null;
            $this->endMeterCopyColor   = null;
            // return "Invalid copy color meter";
        }

        // Scan meter
        if ($scanMeterStatus === self::METER_IS_INVALID)
        {
            /*
             * Right now we don't really care about this meter. Lets set it to null when invalid
             */
            $this->startMeterScan = null;
            $this->endMeterScan   = null;
            // return "Invalid scan meter";
        }

        // Fax meter
        if ($faxMeterStatus === self::METER_IS_INVALID)
        {
            /*
             * Right now we don't really care about this meter. Lets set it to null when invalid
             */
            $this->startMeterFax = null;
            $this->endMeterFax   = null;
            // return "Invalid fax meter";
        }

        return true;
    }

    /**
     * Validates a single meter
     *
     * @param int $startMeter The start meter reading
     * @param int $endMeter   The end meter reading
     *
     * @return int
     */
    protected function _validateMeter (&$startMeter, &$endMeter)
    {
        $returnCode = self::METER_IS_VALID;

        /*
         * Because of our previous logic setting the start meter 0 if end meter is present,
         * we can assume that if either meter is set to null that the meter is not present
         */
        if ($startMeter === null || $endMeter === null)
        {
            $returnCode = self::METER_IS_NOT_PRESENT;
        }
        else
        {
            /**
             * If either meter is less than 0 it's invalid
             */
            if ($startMeter < 0 || $endMeter < 0)
            {
                $returnCode = self::METER_IS_INVALID;

            }
            else if ($startMeter >= 0 || $endMeter >= 0)
            {
                /*
                 * Meter Has Data -
                 * Start Meter should never be higher than the end meter but it is allowed to be the same as the end meter
                 */
                if ($startMeter > $endMeter)
                {
                    $returnCode = self::METER_IS_INVALID;
                }
            }
            else
            {
                $returnCode = self::METER_IS_NOT_PRESENT;
            }
        }

        /**
         * Null out our values if we don't have a valid meter. We don't want invalid data to be saved.
         */
        if ($returnCode !== self::METER_IS_VALID)
        {
            $startMeter = null;
            $endMeter   = null;
        }


        return $returnCode;
    }
}