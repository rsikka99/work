<?php
/**
 * Class Proposalgen_Model_Rms_Upload_Row
 */
class Proposalgen_Model_Rms_Upload_Row extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $rmsProviderId;

    /**
     * @var int
     */
    public $rmsModelId;

    /**
     * @var string
     */
    public $fullDeviceName;

    /**
     * @var bool
     */
    public $hasCompleteInformation;

    /**
     * @var string
     */
    public $modelName;

    /**
     * @var string
     */
    public $manufacturer;

    /**
     * @var int
     */
    public $manufacturerId;

    /**
     * @var float
     */
    public $cost;

    /**
     * @var int
     */
    public $dutyCycle;

    /**
     * @var bool
     */
    public $isColor;

    /**
     * @var bool
     */
    public $isDuplex;

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
    public $isLeased;

    /**
     * @var string
     */
    public $launchDate;

    /**
     * @var int
     */
    public $leasedTonerYield;

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
    public $partsCostPerPage;

    /**
     * @var float
     */
    public $laborCostPerPage;

    /**
     * @var int
     */
    public $tonerConfigId;

    /**
     * @var int
     */
    public $wattsPowerNormal;

    /**
     * @var int
     */
    public $wattsPowerIdle;

    /**
     * @var string
     */
    public $oemBlackTonerSku;

    /**
     * @var int
     */
    public $oemBlackTonerYield;

    /**
     * @var float
     */
    public $oemBlackTonerCost;

    /**
     * @var string
     */
    public $oemCyanTonerSku;

    /**
     * @var int
     */
    public $oemCyanTonerYield;

    /**
     * @var float
     */
    public $oemCyanTonerCost;

    /**
     * @var string
     */
    public $oemMagentaTonerSku;

    /**
     * @var int
     */
    public $oemMagentaTonerYield;

    /**
     * @var float
     */
    public $oemMagentaTonerCost;

    /**
     * @var string
     */
    public $oemYellowTonerSku;

    /**
     * @var int
     */
    public $oemYellowTonerYield;

    /**
     * @var float
     */
    public $oemYellowTonerCost;

    /**
     * @var string
     */
    public $oemThreeColorTonerSku;

    /**
     * @var int
     */
    public $oemThreeColorTonerYield;

    /**
     * @var float
     */
    public $oemThreeColorTonerCost;

    /**
     * @var string
     */
    public $oemFourColorTonerSku;

    /**
     * @var int
     */
    public $oemFourColorTonerYield;

    /**
     * @var float
     */
    public $oemFourColorTonerCost;

    /**
     * @var string
     */
    public $compBlackTonerSku;

    /**
     * @var int
     */
    public $compBlackTonerYield;

    /**
     * @var float
     */
    public $compBlackTonerCost;

    /**
     * @var string
     */
    public $compCyanTonerSku;

    /**
     * @var int
     */
    public $compCyanTonerYield;

    /**
     * @var float
     */
    public $compCyanTonerCost;

    /**
     * @var string
     */
    public $compMagentaTonerSku;

    /**
     * @var int
     */
    public $compMagentaTonerYield;

    /**
     * @var float
     */
    public $compMagentaTonerCost;

    /**
     * @var string
     */
    public $compYellowTonerSku;

    /**
     * @var int
     */
    public $compYellowTonerYield;

    /**
     * @var float
     */
    public $compYellowTonerCost;

    /**
     * @var string
     */
    public $compThreeColorTonerSku;

    /**
     * @var int
     */
    public $compThreeColorTonerYield;

    /**
     * @var float
     */
    public $compThreeColorTonerCost;

    /**
     * @var string
     */
    public $compFourColorTonerSku;

    /**
     * @var int
     */
    public $compFourColorTonerYield;

    /**
     * @var float
     */
    public $compFourColorTonerCost;

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
     * @var Proposalgen_Model_Manufacturer
     */
    protected $_manufacturer;


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

        if (isset($params->rmsProviderId) && !is_null($params->rmsProviderId))
        {
            $this->rmsProviderId = $params->rmsProviderId;
        }

        if (isset($params->rmsModelId) && !is_null($params->rmsModelId))
        {
            $this->rmsModelId = $params->rmsModelId;
        }

        if (isset($params->fullDeviceName) && !is_null($params->fullDeviceName))
        {
            $this->fullDeviceName = $params->fullDeviceName;
        }

        if (isset($params->hasCompleteInformation) && !is_null($params->hasCompleteInformation))
        {
            $this->hasCompleteInformation = $params->hasCompleteInformation;
        }

        if (isset($params->modelName) && !is_null($params->modelName))
        {
            $this->modelName = $params->modelName;
        }

        if (isset($params->manufacturer) && !is_null($params->manufacturer))
        {
            $this->manufacturer = $params->manufacturer;
        }

        if (isset($params->manufacturerId) && !is_null($params->manufacturerId))
        {
            $this->manufacturerId = $params->manufacturerId;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->dutyCycle) && !is_null($params->dutyCycle))
        {
            $this->dutyCycle = $params->dutyCycle;
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

        if (isset($params->isDuplex) && !is_null($params->isDuplex))
        {
            $this->isDuplex = $params->isDuplex;
        }


        if (isset($params->isLeased) && !is_null($params->isLeased))
        {
            $this->isLeased = $params->isLeased;
        }

        if (isset($params->launchDate) && !is_null($params->launchDate))
        {
            $this->launchDate = $params->launchDate;
        }

        if (isset($params->leasedTonerYield) && !is_null($params->leasedTonerYield))
        {
            $this->leasedTonerYield = $params->leasedTonerYield;
        }

        if (isset($params->ppmBlack) && !is_null($params->ppmBlack))
        {
            $this->ppmBlack = $params->ppmBlack;
        }

        if (isset($params->ppmColor) && !is_null($params->ppmColor))
        {
            $this->ppmColor = $params->ppmColor;
        }

        if (isset($params->partsCostPerPage) && !is_null($params->partsCostPerPage))
        {
            $this->partsCostPerPage = $params->partsCostPerPage;
        }

        if (isset($params->laborCostPerPage) && !is_null($params->laborCostPerPage))
        {
            $this->laborCostPerPage = $params->laborCostPerPage;
        }

        if (isset($params->tonerConfigId) && !is_null($params->tonerConfigId))
        {
            $this->tonerConfigId = $params->tonerConfigId;
        }

        if (isset($params->wattsPowerNormal) && !is_null($params->wattsPowerNormal))
        {
            $this->wattsPowerNormal = $params->wattsPowerNormal;
        }

        if (isset($params->wattsPowerIdle) && !is_null($params->wattsPowerIdle))
        {
            $this->wattsPowerIdle = $params->wattsPowerIdle;
        }

        if (isset($params->oemBlackTonerSku) && !is_null($params->oemBlackTonerSku))
        {
            $this->oemBlackTonerSku = $params->oemBlackTonerSku;
        }

        if (isset($params->oemBlackTonerYield) && !is_null($params->oemBlackTonerYield))
        {
            $this->oemBlackTonerYield = $params->oemBlackTonerYield;
        }

        if (isset($params->oemBlackTonerCost) && !is_null($params->oemBlackTonerCost))
        {
            $this->oemBlackTonerCost = $params->oemBlackTonerCost;
        }

        if (isset($params->oemCyanTonerSku) && !is_null($params->oemCyanTonerSku))
        {
            $this->oemCyanTonerSku = $params->oemCyanTonerSku;
        }

        if (isset($params->oemCyanTonerYield) && !is_null($params->oemCyanTonerYield))
        {
            $this->oemCyanTonerYield = $params->oemCyanTonerYield;
        }

        if (isset($params->oemCyanTonerCost) && !is_null($params->oemCyanTonerCost))
        {
            $this->oemCyanTonerCost = $params->oemCyanTonerCost;
        }

        if (isset($params->oemMagentaTonerSku) && !is_null($params->oemMagentaTonerSku))
        {
            $this->oemMagentaTonerSku = $params->oemMagentaTonerSku;
        }

        if (isset($params->oemMagentaTonerYield) && !is_null($params->oemMagentaTonerYield))
        {
            $this->oemMagentaTonerYield = $params->oemMagentaTonerYield;
        }

        if (isset($params->oemMagentaTonerCost) && !is_null($params->oemMagentaTonerCost))
        {
            $this->oemMagentaTonerCost = $params->oemMagentaTonerCost;
        }

        if (isset($params->oemYellowTonerSku) && !is_null($params->oemYellowTonerSku))
        {
            $this->oemYellowTonerSku = $params->oemYellowTonerSku;
        }

        if (isset($params->oemYellowTonerYield) && !is_null($params->oemYellowTonerYield))
        {
            $this->oemYellowTonerYield = $params->oemYellowTonerYield;
        }

        if (isset($params->oemYellowTonerCost) && !is_null($params->oemYellowTonerCost))
        {
            $this->oemYellowTonerCost = $params->oemYellowTonerCost;
        }

        if (isset($params->oemThreeColorTonerSku) && !is_null($params->oemThreeColorTonerSku))
        {
            $this->oemThreeColorTonerSku = $params->oemThreeColorTonerSku;
        }

        if (isset($params->oemThreeColorTonerYield) && !is_null($params->oemThreeColorTonerYield))
        {
            $this->oemThreeColorTonerYield = $params->oemThreeColorTonerYield;
        }

        if (isset($params->oemThreeColorTonerCost) && !is_null($params->oemThreeColorTonerCost))
        {
            $this->oemThreeColorTonerCost = $params->oemThreeColorTonerCost;
        }

        if (isset($params->oemFourColorTonerSku) && !is_null($params->oemFourColorTonerSku))
        {
            $this->oemFourColorTonerSku = $params->oemFourColorTonerSku;
        }

        if (isset($params->oemFourColorTonerYield) && !is_null($params->oemFourColorTonerYield))
        {
            $this->oemFourColorTonerYield = $params->oemFourColorTonerYield;
        }

        if (isset($params->oemFourColorTonerCost) && !is_null($params->oemFourColorTonerCost))
        {
            $this->oemFourColorTonerCost = $params->oemFourColorTonerCost;
        }

        if (isset($params->compBlackTonerSku) && !is_null($params->compBlackTonerSku))
        {
            $this->compBlackTonerSku = $params->compBlackTonerSku;
        }

        if (isset($params->compBlackTonerYield) && !is_null($params->compBlackTonerYield))
        {
            $this->compBlackTonerYield = $params->compBlackTonerYield;
        }

        if (isset($params->compBlackTonerCost) && !is_null($params->compBlackTonerCost))
        {
            $this->compBlackTonerCost = $params->compBlackTonerCost;
        }

        if (isset($params->compCyanTonerSku) && !is_null($params->compCyanTonerSku))
        {
            $this->compCyanTonerSku = $params->compCyanTonerSku;
        }

        if (isset($params->compCyanTonerYield) && !is_null($params->compCyanTonerYield))
        {
            $this->compCyanTonerYield = $params->compCyanTonerYield;
        }

        if (isset($params->compCyanTonerCost) && !is_null($params->compCyanTonerCost))
        {
            $this->compCyanTonerCost = $params->compCyanTonerCost;
        }

        if (isset($params->compMagentaTonerSku) && !is_null($params->compMagentaTonerSku))
        {
            $this->compMagentaTonerSku = $params->compMagentaTonerSku;
        }

        if (isset($params->compMagentaTonerYield) && !is_null($params->compMagentaTonerYield))
        {
            $this->compMagentaTonerYield = $params->compMagentaTonerYield;
        }

        if (isset($params->compMagentaTonerCost) && !is_null($params->compMagentaTonerCost))
        {
            $this->compMagentaTonerCost = $params->compMagentaTonerCost;
        }

        if (isset($params->compYellowTonerSku) && !is_null($params->compYellowTonerSku))
        {
            $this->compYellowTonerSku = $params->compYellowTonerSku;
        }

        if (isset($params->compYellowTonerYield) && !is_null($params->compYellowTonerYield))
        {
            $this->compYellowTonerYield = $params->compYellowTonerYield;
        }

        if (isset($params->compYellowTonerCost) && !is_null($params->compYellowTonerCost))
        {
            $this->compYellowTonerCost = $params->compYellowTonerCost;
        }

        if (isset($params->compThreeColorTonerSku) && !is_null($params->compThreeColorTonerSku))
        {
            $this->compThreeColorTonerSku = $params->compThreeColorTonerSku;
        }

        if (isset($params->compThreeColorTonerYield) && !is_null($params->compThreeColorTonerYield))
        {
            $this->compThreeColorTonerYield = $params->compThreeColorTonerYield;
        }

        if (isset($params->compThreeColorTonerCost) && !is_null($params->compThreeColorTonerCost))
        {
            $this->compThreeColorTonerCost = $params->compThreeColorTonerCost;
        }

        if (isset($params->compFourColorTonerSku) && !is_null($params->compFourColorTonerSku))
        {
            $this->compFourColorTonerSku = $params->compFourColorTonerSku;
        }

        if (isset($params->compFourColorTonerYield) && !is_null($params->compFourColorTonerYield))
        {
            $this->compFourColorTonerYield = $params->compFourColorTonerYield;
        }

        if (isset($params->compFourColorTonerCost) && !is_null($params->compFourColorTonerCost))
        {
            $this->compFourColorTonerCost = $params->compFourColorTonerCost;
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

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                       => $this->id,
            "rmsProviderId"            => $this->rmsProviderId,
            "rmsModelId"               => $this->rmsModelId,
            "fullDeviceName"           => $this->fullDeviceName,
            "hasCompleteInformation"   => $this->hasCompleteInformation,
            "modelName"                => $this->modelName,
            "manufacturer"             => $this->manufacturer,
            "manufacturerId"           => $this->manufacturerId,
            "cost"                     => $this->cost,
            "dutyCycle"                => $this->dutyCycle,
            "isColor"                  => $this->isColor,
            "isCopier"                 => $this->isCopier,
            "isFax"                    => $this->isFax,
            "isLeased"                 => $this->isLeased,
            "isDuplex"                 => $this->isDuplex,
            "launchDate"               => $this->launchDate,
            "leasedTonerYield"         => $this->leasedTonerYield,
            "ppmBlack"                 => $this->ppmBlack,
            "ppmColor"                 => $this->ppmColor,
            "partsCostPerPage"         => $this->partsCostPerPage,
            "laborCostPerPage"         => $this->laborCostPerPage,
            "tonerConfigId"            => $this->tonerConfigId,
            "wattsPowerNormal"         => $this->wattsPowerNormal,
            "wattsPowerIdle"           => $this->wattsPowerIdle,
            "oemBlackTonerSku"         => $this->oemBlackTonerSku,
            "oemBlackTonerYield"       => $this->oemBlackTonerYield,
            "oemBlackTonerCost"        => $this->oemBlackTonerCost,
            "oemCyanTonerSku"          => $this->oemCyanTonerSku,
            "oemCyanTonerYield"        => $this->oemCyanTonerYield,
            "oemCyanTonerCost"         => $this->oemCyanTonerCost,
            "oemMagentaTonerSku"       => $this->oemMagentaTonerSku,
            "oemMagentaTonerYield"     => $this->oemMagentaTonerYield,
            "oemMagentaTonerCost"      => $this->oemMagentaTonerCost,
            "oemYellowTonerSku"        => $this->oemYellowTonerSku,
            "oemYellowTonerYield"      => $this->oemYellowTonerYield,
            "oemYellowTonerCost"       => $this->oemYellowTonerCost,
            "oemThreeColorTonerSku"    => $this->oemThreeColorTonerSku,
            "oemThreeColorTonerYield"  => $this->oemThreeColorTonerYield,
            "oemThreeColorTonerCost"   => $this->oemThreeColorTonerCost,
            "oemFourColorTonerSku"     => $this->oemFourColorTonerSku,
            "oemFourColorTonerYield"   => $this->oemFourColorTonerYield,
            "oemFourColorTonerCost"    => $this->oemFourColorTonerCost,
            "compBlackTonerSku"        => $this->compBlackTonerSku,
            "compBlackTonerYield"      => $this->compBlackTonerYield,
            "compBlackTonerCost"       => $this->compBlackTonerCost,
            "compCyanTonerSku"         => $this->compCyanTonerSku,
            "compCyanTonerYield"       => $this->compCyanTonerYield,
            "compCyanTonerCost"        => $this->compCyanTonerCost,
            "compMagentaTonerSku"      => $this->compMagentaTonerSku,
            "compMagentaTonerYield"    => $this->compMagentaTonerYield,
            "compMagentaTonerCost"     => $this->compMagentaTonerCost,
            "compYellowTonerSku"       => $this->compYellowTonerSku,
            "compYellowTonerYield"     => $this->compYellowTonerYield,
            "compYellowTonerCost"      => $this->compYellowTonerCost,
            "compThreeColorTonerSku"   => $this->compThreeColorTonerSku,
            "compThreeColorTonerYield" => $this->compThreeColorTonerYield,
            "compThreeColorTonerCost"  => $this->compThreeColorTonerCost,
            "compFourColorTonerSku"    => $this->compFourColorTonerSku,
            "compFourColorTonerYield"  => $this->compFourColorTonerYield,
            "compFourColorTonerCost"   => $this->compFourColorTonerCost,
            "tonerLevelBlack"          => $this->tonerLevelBlack,
            "tonerLevelCyan"           => $this->tonerLevelCyan,
            "tonerLevelMagenta"        => $this->tonerLevelMagenta,
            "tonerLevelYellow"         => $this->tonerLevelYellow,
        );
    }


    /**
     * Getter for manufacturer
     *
     * @return Proposalgen_Model_Manufacturer
     */
    public function getManufacturer ()
    {
        if (!isset($this->_manufacturer) && $this->manufacturerId > 0)
        {
            $this->_manufacturer = Proposalgen_Model_Mapper_Manufacturer::getInstance()->find($this->manufacturerId);
        }

        return $this->_manufacturer;
    }

    /**
     * Setter for manufacturer
     *
     * @param Proposalgen_Model_Manufacturer $manufacturer
     *
     * @return Proposalgen_Model_Rms_Upload_Row
     */
    public function setManufacturer ($manufacturer)
    {
        $this->_manufacturer = $manufacturer;

        return $this;
    }
}