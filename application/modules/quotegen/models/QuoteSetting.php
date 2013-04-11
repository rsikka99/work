<?php
class Quotegen_Model_QuoteSetting extends My_Model_Abstract
{
    const SYSTEM_ROW_ID = 1;

    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var double
     */
    public $pageCoverageMonochrome;

    /**
     * @var double
     */
    public $pageCoverageColor;

    /**
     * @var double
     */
    public $deviceMargin;

    /**
     * @var double
     */
    public $pageMargin;

    /**
     * @var int
     */
    public $pricingConfigId;

    /**
     * @var float
     */
    public $adminCostPerPage;

    /**
     * A pricing config object
     *
     * @var Proposalgen_Model_PricingConfig
     */
    protected $_pricingConfig;

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

        if (isset($params->pageCoverageMonochrome) && !is_null($params->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $params->pageCoverageMonochrome;
        }

        if (isset($params->pageCoverageColor) && !is_null($params->pageCoverageColor))
        {
            $this->pageCoverageColor = $params->pageCoverageColor;
        }

        if (isset($params->deviceMargin) && !is_null($params->deviceMargin))
        {
            $this->deviceMargin = $params->deviceMargin;
        }

        if (isset($params->pageMargin) && !is_null($params->pageMargin))
        {
            $this->pageMargin = $params->pageMargin;
        }

        if (isset($params->pricingConfigId) && !is_null($params->pricingConfigId))
        {
            $this->pricingConfigId = $params->pricingConfigId;
        }

        if (isset($params->adminCostPerPage) && !is_null($params->adminCostPerPage))
        {
            $this->adminCostPerPage = $params->adminCostPerPage;
        }

    }

    /**
     * Takes an array or a quote setting object and applies overrides
     *
     * @param
     *            <array, Quotegen_Model_QuoteSetting> $settings
     */
    public function applyOverride ($settings)
    {
        // Turn an object into an array
        if ($settings instanceof Quotegen_Model_QuoteSetting)
        {
            $settings = $settings->toArray();
        }

        // Turn an array into an ArrayObject
        if (is_array($settings))
        {
            $settings = new ArrayObject($settings, ArrayObject::ARRAY_AS_PROPS);
        }

        // Do the same logic as populate, except never set the id.
        if (isset($settings->pageCoverageMonochrome) && !is_null($settings->pageCoverageMonochrome))
        {
            $this->pageCoverageMonochrome = $settings->pageCoverageMonochrome;
        }
        if (isset($settings->pageCoverageColor) && !is_null($settings->pageCoverageColor))
        {
            $this->pageCoverageColor = $settings->pageCoverageColor;
        }
        if (isset($settings->deviceMargin) && !is_null($settings->deviceMargin))
        {
            $this->deviceMargin = $settings->deviceMargin;
        }
        if (isset($settings->pageMargin) && !is_null($settings->pageMargin))
        {
            $this->pageMargin = $settings->pageMargin;
        }
        if (isset($settings->adminCostPerPage) && !is_null($settings->adminCostPerPage))
        {
            $this->adminCostPerPage = $settings->adminCostPerPage;
        }

        if (isset($settings->pricingConfigId) && !is_null($settings->pricingConfigId))
        {
            if ($settings->pricingConfigId !== Proposalgen_Model_PricingConfig::NONE)
            {
                $this->pricingConfigId = $settings->pricingConfigId;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                     => $this->id,
            "pageCoverageMonochrome" => $this->pageCoverageMonochrome,
            "pageCoverageColor"      => $this->pageCoverageColor,
            "deviceMargin"           => $this->deviceMargin,
            "pageMargin"             => $this->pageMargin,
            "pricingConfigId"        => $this->pricingConfigId,
            "adminCostPerPage"       => $this->adminCostPerPage,
        );
    }

    /**
     * Gets the pricing config object
     *
     * @return Proposalgen_Model_PricingConfig The pricing config object.
     */
    public function getPricingConfig ()
    {
        if (!isset($this->_pricingConfig))
        {
            $this->_pricingConfig = Proposalgen_Model_Mapper_PricingConfig::getInstance()->find($this->pricingConfigId);
        }

        return $this->_pricingConfig;
    }

    /**
     * Sets the pricing config object
     *
     * @param Proposalgen_Model_PricingConfig $_pricingConfig
     *            The new princing config.
     */
    public function setPricingConfig ($_pricingConfig)
    {
        $this->_pricingConfig = $_pricingConfig;

        return $this;
    }
}