<?php
class Proposalgen_Model_TicketPFRequest extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $ticketId;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $devicePfId;

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
    public $launchDate;

    /**
     * @var float
     */
    public $devicePrice;

    /**
     * @var float
     */
    public $serviceCostPerPage;

    /**
     * @var int
     */
    public $tonerConfig;

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
    public $ppmBlack;

    /**
     * @var int
     */
    public $ppmColor;

    /**
     * @var int
     */
    public $dutyCycle;

    /**
     * @var int
     */
    public $wattsPowerNormal;

    /**
     * @var int
     */
    public $wattsPowerIdle;

    /**
     * @var Proposalgen_Model_DevicePf
     */
    protected $_devicePf;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->ticketId) && !is_null($params->ticketId))
        {
            $this->ticketId = $params->ticketId;
        }

        if (isset($params->userId) && !is_null($params->userId))
        {
            $this->userId = $params->userId;
        }

        if (isset($params->devicePfId) && !is_null($params->devicePfId))
        {
            $this->devicePfId = $params->devicePfId;
        }

        if (isset($params->deviceManufacturer) && !is_null($params->deviceManufacturer))
        {
            $this->deviceManufacturer = $params->deviceManufacturer;
        }

        if (isset($params->printerModel) && !is_null($params->printerModel))
        {
            $this->printerModel = $params->printerModel;
        }

        if (isset($params->launchDate) && !is_null($params->launchDate))
        {
            $this->launchDate = $params->launchDate;
        }

        if (isset($params->devicePrice) && !is_null($params->devicePrice))
        {
            $this->devicePrice = $params->devicePrice;
        }

        if (isset($params->serviceCostPerPage) && !is_null($params->serviceCostPerPage))
        {
            $this->serviceCostPerPage = $params->serviceCostPerPage;
        }

        if (isset($params->tonerConfig) && !is_null($params->tonerConfig))
        {
            $this->tonerConfig = $params->tonerConfig;
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

        if (isset($params->ppmBlack) && !is_null($params->ppmBlack))
        {
            $this->ppmBlack = $params->ppmBlack;
        }

        if (isset($params->ppmColor) && !is_null($params->ppmColor))
        {
            $this->ppmColor = $params->ppmColor;
        }

        if (isset($params->dutyCycle) && !is_null($params->dutyCycle))
        {
            $this->dutyCycle = $params->dutyCycle;
        }

        if (isset($params->wattsPowerNormal) && !is_null($params->wattsPowerNormal))
        {
            $this->wattsPowerNormal = $params->wattsPowerNormal;
        }

        if (isset($params->wattsPowerIdle) && !is_null($params->wattsPowerIdle))
        {
            $this->wattsPowerIdle = $params->wattsPowerIdle;
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "ticketId"           => $this->ticketId,
            "userId"             => $this->userId,
            "devicePfId"         => $this->devicePfId,
            "deviceManufacturer" => $this->deviceManufacturer,
            "printerModel"       => $this->printerModel,
            "launchDate"         => $this->launchDate,
            "devicePrice"        => $this->devicePrice,
            "serviceCostPerPage" => $this->serviceCostPerPage,
            "tonerConfig"        => $this->tonerConfig,
            "isCopier"           => $this->isCopier,
            "isFax"              => $this->isFax,
            "isDuplex"           => $this->isDuplex,
            "isScanner"          => $this->isScanner,
            "ppmBlack"           => $this->ppmBlack,
            "ppmColor"           => $this->ppmColor,
            "dutyCycle"          => $this->dutyCycle,
            "wattsPowerNormal"   => $this->wattsPowerNormal,
            "wattsPowerIdle"     => $this->wattsPowerIdle,
        );
    }

    /**
     * Gets the device pf
     *
     * @return Proposalgen_Model_DevicePf
     */
    public function getDevicePf ()
    {
        if (!isset($this->_devicePf))
        {
            $id = $this->devicePfId;
            if (isset($id))
            {
                $this->_devicePf = Proposalgen_Model_Mapper_DevicePf::getInstance()->find($id);
            }
        }

        return $this->_devicePf;
    }

    /**
     * Sets the device pf
     *
     * @param Proposalgen_Model_DevicePf $DevicePf
     *
     * @return Proposalgen_Model_TicketPFRequest
     */
    public function setDevicePf ($DevicePf)
    {
        $this->_devicePf = $DevicePf;

        return $this;
    }

}