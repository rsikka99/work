<?php
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
     * @var bool
     */
    public $isScanner;

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
    public $serviceCostPerPage;

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

        if (isset($params->isLeased) && !is_null($params->isLeased))
        {
            $this->isLeased = $params->isLeased;
        }

        if (isset($params->isScanner) && !is_null($params->isScanner))
        {
            $this->isScanner = $params->isScanner;
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

        if (isset($params->serviceCostPerPage) && !is_null($params->serviceCostPerPage))
        {
            $this->serviceCostPerPage = $params->serviceCostPerPage;
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
            "id"                     => $this->id,
            "rmsProviderId"          => $this->rmsProviderId,
            "rmsModelId"             => $this->rmsModelId,
            "hasCompleteInformation" => $this->hasCompleteInformation,
            "modelName"              => $this->modelName,
            "manufacturer"           => $this->manufacturer,
            "cost"                   => $this->cost,
            "dutyCycle"              => $this->dutyCycle,
            "isColor"                => $this->isColor,
            "isCopier"               => $this->isCopier,
            "isFax"                  => $this->isFax,
            "isLeased"               => $this->isLeased,
            "isScanner"              => $this->isScanner,
            "launchDate"             => $this->launchDate,
            "leasedTonerYield"       => $this->leasedTonerYield,
            "ppmBlack"               => $this->ppmBlack,
            "ppmColor"               => $this->ppmColor,
            "serviceCostPerPage"     => $this->serviceCostPerPage,
            "tonerConfigId"          => $this->tonerConfigId,
            "wattsPowerNormal"       => $this->wattsPowerNormal,
            "wattsPowerIdle"         => $this->wattsPowerIdle,
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
            "tonerLevelBlack"        => $this->tonerLevelBlack,
            "tonerLevelCyan"         => $this->tonerLevelCyan,
            "tonerLevelMagenta"      => $this->tonerLevelMagenta,
            "tonerLevelYellow"       => $this->tonerLevelYellow,
        );
    }
}