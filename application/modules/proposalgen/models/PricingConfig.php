<?php
/**
 * Class Proposalgen_Model_PricingConfig
 */
class Proposalgen_Model_PricingConfig extends My_Model_Abstract
{
    const NONE              = 1;
    const OEM               = 2;
    const COMP              = 3;
    const OEMMONO_COMPCOLOR = 4;
    const COMPMONO_OEMCOLOR = 5;

    /**
     * An array of nice configuration names with the config id as the array key
     *
     * @var string[]
     */
    public static $ConfigNames = array(
        1 => "",
        2 => "OEM",
        3 => "COMP",
        4 => "OEM Mono, COMP Color",
        5 => "COMP Mono, OEM Color"
    );

    /**
     * @var int
     */
    public $pricingConfigId;

    /**
     * @var string
     */
    public $configName;

    /**
     * @var int
     */
    public $colorTonerPartTypeId;

    /**
     * @var int
     */
    public $monoTonerPartTypeId;

    // Extra variables
    /**
     * @var Proposalgen_Model_PartType
     */
    protected $_colorTonerPartType;

    /**
     * @var Proposalgen_Model_PartType
     */
    protected $_monoTonerPartType;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->pricingConfigId) && !is_null($params->pricingConfigId))
        {
            $this->pricingConfigId = $params->pricingConfigId;
        }

        if (isset($params->configName) && !is_null($params->configName))
        {
            $this->configName = $params->configName;
        }

        if (isset($params->colorTonerPartTypeId) && !is_null($params->colorTonerPartTypeId))
        {
            $this->colorTonerPartTypeId = $params->colorTonerPartTypeId;
        }

        if (isset($params->monoTonerPartTypeId) && !is_null($params->monoTonerPartTypeId))
        {
            $this->monoTonerPartTypeId = $params->monoTonerPartTypeId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "pricingConfigId"      => $this->pricingConfigId,
            "configName"           => $this->configName,
            "colorTonerPartTypeId" => $this->colorTonerPartTypeId,
            "monoTonerPartTypeId"  => $this->monoTonerPartTypeId,
        );
    }

    /**
     * Gets the nice name for a pricing configuration
     *
     * @return string
     */
    public function getNiceConfigName ()
    {
        return self::$ConfigNames [$this->pricingConfigId];
    }


    /**
     * @return Proposalgen_Model_PartType
     */
    public function getColorTonerPartType ()
    {
        if (!isset($this->_colorTonerPartType))
        {
            if (isset($this->colorTonerPartTypeId))
            {
                $this->_colorTonerPartType = Proposalgen_Model_Mapper_PartType::getInstance()->find($this->colorTonerPartTypeId);
            }
        }

        return $this->_colorTonerPartType;
    }

    /**
     * @param $ColorTonerPartType
     *
     * @return Proposalgen_Model_PricingConfig
     */
    public function setColorTonerPartType ($ColorTonerPartType)
    {
        $this->_colorTonerPartType = $ColorTonerPartType;

        return $this;
    }

    /**
     * @return Proposalgen_Model_PartType
     */
    public function getMonoTonerPartType ()
    {
        if (!isset($this->_monoTonerPartType))
        {
            if (isset($this->monoTonerPartTypeId))
            {
                $this->_monoTonerPartType = Proposalgen_Model_Mapper_PartType::getInstance()->find($this->monoTonerPartTypeId);
            }
        }

        return $this->_monoTonerPartType;
    }

    /**
     * @param $MonoTonerPartType
     *
     * @return Proposalgen_Model_PricingConfig
     */
    public function setMonoTonerPartType ($MonoTonerPartType)
    {
        $this->_monoTonerPartType = $MonoTonerPartType;

        return $this;
    }
}