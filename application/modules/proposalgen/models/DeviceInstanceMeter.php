<?php

/**
 * Class Proposalgen_Model_DeviceInstanceMeter
 */
class Proposalgen_Model_DeviceInstanceMeter extends My_Model_Abstract
{
    const DAYS_IN_MONTH = 30.4;

    // Database Fields
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $deviceInstanceId;

    /**
     * @var string
     */
    public $monitorStartDate;

    /**
     * @var string
     */
    public $monitorEndDate;

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
     * @var int
     */
    public $startMeterLife;

    /**
     * @var int
     */
    public $endMeterLife;

    /**
     * @var Proposalgen_Model_PageCounts
     */
    protected $_pageCounts;

    // Extra Fields
    /**
     * When set to true it means it did not come from the database
     *
     * @var boolean
     */
    public $generatedBySystem = false;

    /**
     * @var DateTime
     */
    protected $_mpsMonitorInterval;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_blackPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_colorPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_combinedPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printBlackPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printColorPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printCombinedPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_copyBlackPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_copyColorPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_copyCombinedPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_faxPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_scanPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printA3BlackPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printA3ColorPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_printA3CombinedPageCount;

    /**
     * @var Proposalgen_Model_PageCount
     */
    protected $_lifePageCount;

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

        if (isset($params->deviceInstanceId) && !is_null($params->deviceInstanceId))
        {
            $this->deviceInstanceId = $params->deviceInstanceId;
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

        if (isset($params->startMeterLife) && !is_null($params->startMeterLife))
        {
            $this->startMeterLife = $params->startMeterLife;
        }

        if (isset($params->endMeterLife) && !is_null($params->endMeterLife))
        {
            $this->endMeterLife = $params->endMeterLife;
        }

        if (isset($params->monitorStartDate) && !is_null($params->monitorStartDate))
        {
            $this->monitorStartDate = $params->monitorStartDate;
        }

        if (isset($params->monitorEndDate) && !is_null($params->monitorEndDate))
        {
            $this->monitorEndDate = $params->monitorEndDate;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                     => $this->id,
            "deviceInstanceId"       => $this->deviceInstanceId,
            "startMeterBlack"        => $this->startMeterBlack,
            "endMeterBlack"          => $this->endMeterBlack,
            "startMeterColor"        => $this->startMeterColor,
            "endMeterColor"          => $this->endMeterColor,
            "startMeterPrintBlack"   => $this->startMeterPrintBlack,
            "endMeterPrintBlack"     => $this->endMeterPrintBlack,
            "startMeterPrintColor"   => $this->startMeterPrintColor,
            "endMeterPrintColor"     => $this->endMeterPrintColor,
            "startMeterCopyBlack"    => $this->startMeterCopyBlack,
            "endMeterCopyBlack"      => $this->endMeterCopyBlack,
            "startMeterCopyColor"    => $this->startMeterCopyColor,
            "endMeterCopyColor"      => $this->endMeterCopyColor,
            "startMeterFax"          => $this->startMeterFax,
            "endMeterFax"            => $this->endMeterFax,
            "startMeterScan"         => $this->startMeterScan,
            "endMeterScan"           => $this->endMeterScan,
            "startMeterPrintA3Black" => $this->startMeterPrintA3Black,
            "endMeterPrintA3Black"   => $this->endMeterPrintA3Black,
            "startMeterPrintA3Color" => $this->startMeterPrintA3Color,
            "endMeterPrintA3Color"   => $this->endMeterPrintA3Color,
            "startMeterLife"         => $this->startMeterLife,
            "endMeterLife"           => $this->endMeterLife,
            "monitorStartDate"       => $this->monitorStartDate,
            "monitorEndDate"         => $this->monitorEndDate,
        );
    }

    /**
     * Gets the DateInterval for how many days the meters were monitored for
     *
     * @return DateInterval
     */
    public function calculateMpsMonitorInterval ()
    {
        if (!isset($this->_mpsMonitorInterval))
        {
            $startDate                 = new DateTime($this->monitorStartDate);
            $endDate                   = new DateTime($this->monitorEndDate);
            $this->_mpsMonitorInterval = $startDate->diff($endDate);
        }

        return $this->_mpsMonitorInterval;
    }

    /**
     * Calculates the average daily page count over a date interval
     *
     * @param $startMeter
     * @param $endMeter
     *
     * @return float
     */
    public function calculateAverageDailyPageCount ($startMeter, $endMeter)
    {
        $pageVolume   = $endMeter - $startMeter;
        $dateInterval = $this->calculateMpsMonitorInterval();

        if ($pageVolume > 0 && $dateInterval->days > 0)
        {
            $pageVolume = $pageVolume / $dateInterval->days;
        }
        else
        {
            $pageVolume = 0;
        }

        return $pageVolume;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public function getBlackPageCount ()
    {
        if (!isset($this->_blackPageCount))
        {
            $this->_blackPageCount = new Proposalgen_Model_PageCount();
            $this->_blackPageCount->setDaily($this->calculateAverageDailyPageCount($this->startMeterBlack, $this->endMeterBlack));
        }

        return $this->_blackPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getColorPageCount ()
    {
        if (!isset($this->_colorPageCount))
        {
            $this->_colorPageCount = new Proposalgen_Model_PageCount();
            $this->_colorPageCount->setDaily($this->calculateAverageDailyPageCount($this->startMeterColor, $this->endMeterColor));
        }

        return $this->_colorPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getCombinedPageCount ()
    {
        if (!isset($this->_combinedPageCount))
        {
            $this->_combinedPageCount = new Proposalgen_Model_PageCount();
            $this->_combinedPageCount->add($this->getBlackPageCount());
            $this->_combinedPageCount->add($this->getColorPageCount());
        }

        return $this->_combinedPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public function getPrintBlackPageCount ()
    {
        if (!isset($this->_printBlackPageCount))
        {
            $this->_printBlackPageCount = new Proposalgen_Model_PageCount();
            $this->_printBlackPageCount->setDaily($this->calculateAverageDailyPageCount($this->startMeterPrintBlack, $this->endMeterPrintBlack));
        }

        return $this->_printBlackPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getPrintColorPageCount ()
    {
        if (!isset($this->_printColorPageCount))
        {
            $this->_printColorPageCount = new Proposalgen_Model_PageCount();
            $this->_printColorPageCount->setDaily($this->calculateAverageDailyPageCount($this->startMeterPrintColor, $this->endMeterPrintColor));
        }

        return $this->_printColorPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getPrintCombinedPageCount ()
    {
        if (!isset($this->_printCombinedPageCount))
        {
            $this->_printCombinedPageCount = new Proposalgen_Model_PageCount();
            $this->_printCombinedPageCount->add($this->getPrintBlackPageCount());
            $this->_printCombinedPageCount->add($this->getPrintColorPageCount());
        }

        return $this->_printCombinedPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getCopyBlackPageCount ()
    {
        if (!isset($this->_copyBlackPageCount))
        {
            $this->_copyBlackPageCount = new Proposalgen_Model_PageCount();
            $this->_copyBlackPageCount->setDaily($this->calculateAverageDailyPageCount($this->startMeterCopyBlack, $this->endMeterCopyBlack));
        }

        return $this->_copyBlackPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getCopyColorPageCount ()
    {
        if (!isset($this->_copyColorPageCount))
        {
            $this->_copyColorPageCount = new Proposalgen_Model_PageCount();
            $this->_copyColorPageCount->setDaily($this->calculateAverageDailyPageCount($this->startMeterCopyColor, $this->endMeterCopyColor));
        }

        return $this->_copyColorPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getCopyCombinedPageCount ()
    {
        if (!isset($this->_copyCombinedPageCount))
        {
            $this->_copyCombinedPageCount = new Proposalgen_Model_PageCount();
            $this->_copyCombinedPageCount->add($this->getCopyBlackPageCount());
            $this->_copyCombinedPageCount->add($this->getCopyColorPageCount());
        }

        return $this->_copyCombinedPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getFaxPageCount ()
    {
        if (!isset($this->_faxPageCount))
        {
            $this->_faxPageCount = new Proposalgen_Model_PageCount();
            $this->_faxPageCount->setDaily($this->calculateAverageDailyPageCount($this->startMeterFax, $this->endMeterFax));
        }

        return $this->_faxPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getScanPageCount ()
    {
        if (!isset($this->_scanPageCount))
        {
            $this->_scanPageCount = new Proposalgen_Model_PageCount();
            $this->_scanPageCount->setDaily($this->calculateAverageDailyPageCount($this->startMeterScan, $this->endMeterScan));
        }

        return $this->_scanPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getPrintA3BlackPageCount ()
    {
        if (!isset($this->_printA3BlackPageCount))
        {
            $this->_printA3BlackPageCount = new Proposalgen_Model_PageCount();
            $this->_printA3BlackPageCount->setDaily($this->calculateAverageDailyPageCount($this->startMeterPrintA3Black, $this->endMeterPrintA3Black));
        }

        return $this->_printA3BlackPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getPrintA3ColorPageCount ()
    {
        if (!isset($this->_printA3ColorPageCount))
        {
            $this->_printA3ColorPageCount = new Proposalgen_Model_PageCount();
            $this->_printA3ColorPageCount->setDaily($this->calculateAverageDailyPageCount($this->startMeterPrintA3Color, $this->endMeterPrintA3Color));
        }

        return $this->_printA3ColorPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public Function getPrintA3CombinedPageCount ()
    {
        if (!isset($this->_printA3CombinedPageCount))
        {
            $this->_printA3CombinedPageCount = new Proposalgen_Model_PageCount();
            $this->_printA3CombinedPageCount->add($this->getPrintA3BlackPageCount());
            $this->_printA3CombinedPageCount->add($this->getPrintA3ColorPageCount());
        }

        return $this->_printA3CombinedPageCount;
    }

    /**
     * @return Proposalgen_Model_PageCount
     */
    public function getLifePageCount ()
    {
        if (!isset($this->_lifePageCount))
        {
            $this->_lifePageCount = new Proposalgen_Model_PageCount();
            $this->_lifePageCount->setDaily($this->calculateAverageDailyPageCount($this->startMeterLife, $this->endMeterLife));
        }

        return $this->_lifePageCount;
    }

    /**
     * Checks to see if it has printed ever
     *
     * @return bool
     */
    public function hasPrintedInLife ()
    {
        return $this->endMeterLife > 0;
    }

    /**
     * Checks to see if it has printed within the time span
     *
     * @return bool
     */
    public function hasPrintedInTimeSpan ()
    {
        return $this->getLifePageCount()->getDaily() > 0;
    }
}