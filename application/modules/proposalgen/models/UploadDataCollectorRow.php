<?php
class Proposalgen_Model_UploadDataCollectorRow extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $uploadDataCollectorId;

    /**
     * @var int
     */
    public $reportId;

    /**
     * @var int
     */
    public $devicesPfId;

    /**
     * @var int
     */
    public $startDate;

    /**
     * @var int
     */
    public $endDate;

    /**
     * @var int
     */
    public $printerModelid;

    /**
     * @var int
     */
    public $ipAddress;

    /**
     * @var int
     */
    public $serialNumber;

    /**
     * @var int
     */
    public $modelName;

    /**
     * @var int
     */
    public $manufacturer;

    /**
     * @var int
     */
    public $isColor;

    /**
     * @var int
     */
    public $isCopier;

    /**
     * @var int
     */
    public $isScanner;

    /**
     * @var int
     */
    public $isFax;

    /**
     * @var int
     */
    public $ppmBlack;

    /**
     * @var int
     */
    public $ppmColor;

    /**
     * @var int
     */
    public $dateIntroduction;

    /**
     * @var int
     */
    public $dateAdoption;

    /**
     * @var int
     */
    public $discoveryDate;

    /**
     * @var int
     */
    public $blackProdCodeOem;

    /**
     * @var int
     */
    public $blackYield;

    /**
     * @var int
     */
    public $blackProdCostOem;

    /**
     * @var int
     */
    public $cyanProdCodeOem;

    /**
     * @var int
     */
    public $cyanYield;

    /**
     * @var int
     */
    public $cyanProdCostOem;

    /**
     * @var int
     */
    public $magentaProdCodeOem;

    /**
     * @var int
     */
    public $magentaYield;

    /**
     * @var int
     */
    public $magentaProdCostOem;

    /**
     * @var int
     */
    public $yellowProdCodeOem;

    /**
     * @var int
     */
    public $yellowYield;

    /**
     * @var int
     */
    public $yellowProdCostOem;

    /**
     * @var int
     */
    public $wattsPowerNormal;

    /**
     * @var int
     */
    public $wattsPowerIdle;

    /**
     * @var int
     */
    public $dutyCycle;

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
    public $invalidData;

    /**
     * @var int
     */
    public $isExcluded;

    // Extra Fields
    /**
     * @var string
     */
    protected $_errorMessage;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->uploadDataCollectorId) && !is_null($params->uploadDataCollectorId))
        {
            $this->uploadDataCollectorId = $params->uploadDataCollectorId;
        }

        if (isset($params->reportId) && !is_null($params->reportId))
        {
            $this->reportId = $params->reportId;
        }

        if (isset($params->devicesPfId) && !is_null($params->devicesPfId))
        {
            $this->devicesPfId = $params->devicesPfId;
        }

        if (isset($params->startDate) && !is_null($params->startDate))
        {
            $this->startDate = $params->startDate;
        }

        if (isset($params->endDate) && !is_null($params->endDate))
        {
            $this->endDate = $params->endDate;
        }

        if (isset($params->printerModelid) && !is_null($params->printerModelid))
        {
            $this->printerModelid = $params->printerModelid;
        }

        if (isset($params->ipAddress) && !is_null($params->ipAddress))
        {
            $this->ipAddress = $params->ipAddress;
        }

        if (isset($params->serialNumber) && !is_null($params->serialNumber))
        {
            $this->serialNumber = $params->serialNumber;
        }

        if (isset($params->modelName) && !is_null($params->modelName))
        {
            $this->modelName = $params->modelName;
        }

        if (isset($params->manufacturer) && !is_null($params->manufacturer))
        {
            $this->manufacturer = $params->manufacturer;
        }

        if (isset($params->isColor) && !is_null($params->isColor))
        {
            $this->isColor = $params->isColor;
        }

        if (isset($params->isCopier) && !is_null($params->isCopier))
        {
            $this->isCopier = $params->isCopier;
        }

        if (isset($params->isScanner) && !is_null($params->isScanner))
        {
            $this->isScanner = $params->isScanner;
        }

        if (isset($params->isFax) && !is_null($params->isFax))
        {
            $this->isFax = $params->isFax;
        }

        if (isset($params->ppmBlack) && !is_null($params->ppmBlack))
        {
            $this->ppmBlack = $params->ppmBlack;
        }

        if (isset($params->ppmColor) && !is_null($params->ppmColor))
        {
            $this->ppmColor = $params->ppmColor;
        }

        if (isset($params->dateIntroduction) && !is_null($params->dateIntroduction))
        {
            $this->dateIntroduction = $params->dateIntroduction;
        }

        if (isset($params->dateAdoption) && !is_null($params->dateAdoption))
        {
            $this->dateAdoption = $params->dateAdoption;
        }

        if (isset($params->discoveryDate) && !is_null($params->discoveryDate))
        {
            $this->discoveryDate = $params->discoveryDate;
        }

        if (isset($params->blackProdCodeOem) && !is_null($params->blackProdCodeOem))
        {
            $this->blackProdCodeOem = $params->blackProdCodeOem;
        }

        if (isset($params->blackYield) && !is_null($params->blackYield))
        {
            $this->blackYield = $params->blackYield;
        }

        if (isset($params->blackProdCostOem) && !is_null($params->blackProdCostOem))
        {
            $this->blackProdCostOem = $params->blackProdCostOem;
        }

        if (isset($params->cyanProdCodeOem) && !is_null($params->cyanProdCodeOem))
        {
            $this->cyanProdCodeOem = $params->cyanProdCodeOem;
        }

        if (isset($params->cyanYield) && !is_null($params->cyanYield))
        {
            $this->cyanYield = $params->cyanYield;
        }

        if (isset($params->cyanProdCostOem) && !is_null($params->cyanProdCostOem))
        {
            $this->cyanProdCostOem = $params->cyanProdCostOem;
        }

        if (isset($params->magentaProdCodeOem) && !is_null($params->magentaProdCodeOem))
        {
            $this->magentaProdCodeOem = $params->magentaProdCodeOem;
        }

        if (isset($params->magentaYield) && !is_null($params->magentaYield))
        {
            $this->magentaYield = $params->magentaYield;
        }

        if (isset($params->magentaProdCostOem) && !is_null($params->magentaProdCostOem))
        {
            $this->magentaProdCostOem = $params->magentaProdCostOem;
        }

        if (isset($params->yellowProdCodeOem) && !is_null($params->yellowProdCodeOem))
        {
            $this->yellowProdCodeOem = $params->yellowProdCodeOem;
        }

        if (isset($params->yellowYield) && !is_null($params->yellowYield))
        {
            $this->yellowYield = $params->yellowYield;
        }

        if (isset($params->yellowProdCostOem) && !is_null($params->yellowProdCostOem))
        {
            $this->yellowProdCostOem = $params->yellowProdCostOem;
        }

        if (isset($params->wattsPowerNormal) && !is_null($params->wattsPowerNormal))
        {
            $this->wattsPowerNormal = $params->wattsPowerNormal;
        }

        if (isset($params->wattsPowerIdle) && !is_null($params->wattsPowerIdle))
        {
            $this->wattsPowerIdle = $params->wattsPowerIdle;
        }

        if (isset($params->dutyCycle) && !is_null($params->dutyCycle))
        {
            $this->dutyCycle = $params->dutyCycle;
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

        if (isset($params->invalidData) && !is_null($params->invalidData))
        {
            $this->invalidData = $params->invalidData;
        }

        if (isset($params->isExcluded) && !is_null($params->isExcluded))
        {
            $this->isExcluded = $params->isExcluded;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "uploadDataCollectorId" => $this->uploadDataCollectorId,
            "reportId"              => $this->reportId,
            "devicesPfId"           => $this->devicesPfId,
            "startDate"             => $this->startDate,
            "endDate"               => $this->endDate,
            "printerModelid"        => $this->printerModelid,
            "ipAddress"             => $this->ipAddress,
            "serialNumber"          => $this->serialNumber,
            "modelName"             => $this->modelName,
            "manufacturer"          => $this->manufacturer,
            "isColor"               => $this->isColor,
            "isCopier"              => $this->isCopier,
            "isScanner"             => $this->isScanner,
            "isFax"                 => $this->isFax,
            "ppmBlack"              => $this->ppmBlack,
            "ppmColor"              => $this->ppmColor,
            "dateIntroduction"      => $this->dateIntroduction,
            "dateAdoption"          => $this->dateAdoption,
            "discoveryDate"         => $this->discoveryDate,
            "blackProdCodeOem"      => $this->blackProdCodeOem,
            "blackYield"            => $this->blackYield,
            "blackProdCostOem"      => $this->blackProdCostOem,
            "cyanProdCodeOem"       => $this->cyanProdCodeOem,
            "cyanYield"             => $this->cyanYield,
            "cyanProdCostOem"       => $this->cyanProdCostOem,
            "magentaProdCodeOem"    => $this->magentaProdCodeOem,
            "magentaYield"          => $this->magentaYield,
            "magentaProdCostOem"    => $this->magentaProdCostOem,
            "yellowProdCodeOem"     => $this->yellowProdCodeOem,
            "yellowYield"           => $this->yellowYield,
            "yellowProdCostOem"     => $this->yellowProdCostOem,
            "wattsPowerNormal"      => $this->wattsPowerNormal,
            "wattsPowerIdle"        => $this->wattsPowerIdle,
            "dutyCycle"             => $this->dutyCycle,
            "startMeterLife"        => $this->startMeterLife,
            "endMeterLife"          => $this->endMeterLife,
            "startMeterBlack"       => $this->startMeterBlack,
            "endMeterBlack"         => $this->endMeterBlack,
            "startMeterColor"       => $this->startMeterColor,
            "endMeterColor"         => $this->endMeterColor,
            "startMeterPrintBlack"  => $this->startMeterPrintBlack,
            "endMeterPrintBlack"    => $this->endMeterPrintBlack,
            "startMeterPrintColor"  => $this->startMeterPrintColor,
            "endMeterPrintColor"    => $this->endMeterPrintColor,
            "startMeterCopyBlack"   => $this->startMeterCopyBlack,
            "endMeterCopyBlack"     => $this->endMeterCopyBlack,
            "startMeterCopyColor"   => $this->startMeterCopyColor,
            "endMeterCopyColor"     => $this->endMeterCopyColor,
            "startMeterScan"        => $this->startMeterScan,
            "endMeterScan"          => $this->endMeterScan,
            "startMeterFax"         => $this->startMeterFax,
            "endMeterFax"           => $this->endMeterFax,
            "invalidData"           => $this->invalidData,
            "isExcluded"            => $this->isExcluded,
        );
    }


    /**
     * Validates the information set in the model (assumed to be freshly populated from a CSV file.) The return value is
     * to be used with the
     *
     * @return boolean
     */
    public function IsValid ()
    {
        // Variables
        $minDeviceAgeInDays = 4;

        if (!$this->modelName)
        {
            return false;
        }

        if (!$this->manufacturer)
        {
            return false;
        }

        // Check Meters
        if (!$this->validateMeters())
        {
            return false;
        }

        // Device Age
        $startDate     = new Zend_Date($this->startDate);
        $endDate       = new Zend_Date($this->endDate);
        $discoveryDate = new Zend_Date($this->discoveryDate);

        $interval1 = $startDate->diff($endDate);
        $interval2 = $discoveryDate->diff($endDate);

        $deviceAge = $interval1;

        // Use the smallest age that we have available
        if ($interval1->days > $interval2->days && !$interval2->invert)
        {
            $deviceAge = $interval2;
        }

        if ($deviceAge->invert || $deviceAge->days < $minDeviceAgeInDays)
        {
            return false;
        }

        // If we get here, all is valid.
        return true;
    }

    /**
     * Validates all the meter values
     *
     * @return boolean
     */
    protected function validateMeters ()
    {
        // Get all the meters ready
        $StartMeter ["Black"]      = $this->startMeterBlack;
        $StartMeter ["Color"]      = $this->startMeterColor;
        $StartMeter ["Life"]       = $this->startMeterLife;
        $StartMeter ["PrintBlack"] = $this->startMeterPrintBlack;
        $StartMeter ["PrintColor"] = $this->startMeterPrintColor;
        $StartMeter ["CopyBlack"]  = $this->startMeterCopyBlack;
        $StartMeter ["CopyColor"]  = $this->startMeterCopyColor;
        $StartMeter ["Fax"]        = $this->startMeterFax;
        $StartMeter ["Scan"]       = $this->startMeterScan;

        $EndMeter ["Black"]      = $this->endMeterBlack;
        $EndMeter ["Color"]      = $this->endMeterColor;
        $EndMeter ["Life"]       = $this->endMeterLife;
        $EndMeter ["PrintBlack"] = $this->endMeterPrintBlack;
        $EndMeter ["PrintColor"] = $this->endMeterPrintColor;
        $EndMeter ["CopyBlack"]  = $this->endMeterCopyBlack;
        $EndMeter ["CopyColor"]  = $this->endMeterCopyColor;
        $EndMeter ["Fax"]        = $this->endMeterFax;
        $EndMeter ["Scan"]       = $this->endMeterScan;

        // If end meter black is empty, but has startMeterLife and startMeterColor then allow it
        if (empty($EndMeter ["Black"]) && (empty($StartMeter ["Life"]) || empty($StartMeter ["Color"])))
        {
            return false;
        }

        // Make sure that the end meter is greater than or equal to the end meter and that both meters are >= 0
        foreach ($StartMeter as $meterType => $startValue)
        {
            if ($StartMeter < 0 || $EndMeter [$meterType] < 0 || $StartMeter > $EndMeter [$meterType])
            {
                return false;
            }
        }

        // If we get here, all our meters were valid
        return true;
    }


    /**
     * The error message if any
     *
     * @return null|string
     */
    public function getErrorMessage ()
    {
        if (!isset($this->_errorMessage))
        {
            $this->_errorMessage = null;
        }

        return $this->_errorMessage;
    }

    /**
     * Sets the error message
     *
     * @param string $ErrorMessage
     *
     * @return Proposalgen_Model_UploadDataCollectorRow
     */
    public function setErrorMessage ($ErrorMessage)
    {
        $this->_errorMessage = $ErrorMessage;

        return $this;
    }
}