<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\DealerTonerAttributeMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerColorMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorManufacturerMapper;
use MPSToolbox\Services\CurrencyService;
use My_Model_Abstract;

/**
 * Class TonerModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class TonerModel extends My_Model_Abstract
{

    /**
     * The monochrome coverage used to calculate the yield of the cartridge.
     *
     * This comes from ISO/IEC 19752 (5% coverage when testing)
     */
    const MANUFACTURER_ASSUMED_MONO_COVERAGE              = 0.05;

    /**
     * ISO/IEC 19752 tests for yield have a mixture of images and text that
     * roughly equals 5% for each color (CYMK) resulting in a total of 20%
     */
    const MANUFACTURER_ASSUMED_COLOR_SINGLE_COVERAGE               = 0.05;

    /**
     * In the case of a three color combined cartridge, we should be using 15%
     * as the base coverage since all three colors are in the same cartridge.
     */
    const MANUFACTURER_ASSUMED_COLOR_THREE_COLOR_COMBINED_COVERAGE = 0.15;

    // Database fields
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
    public $isSystemDevice;

    /**
     * @var string
     */
    public $sku;

    /**
     * @var float
     */
    public $cost;

    /**
     * @var int
     */
    public $yield;

    /** @var  float */
    public $weight;

    /** @var  string */
    public $UPC;

    /**
     * @var int
     */
    public $manufacturerId;

    /**
     * @var int
     */
    public $tonerColorId;

    /**
     * @var float
     */
    public $calculatedCost;

    /**
     * @var bool
     */
    public $isUsingCustomerPricing = false;

    /**
     * @var bool
     */
    public $isUsingDealerPricing = false;

    /**
     * @var string
     */
    public $imageUrl = '';

    /**
     * @var string
     */
    public $imageFile = '';

    /**
     * @var string
     */
    public $name = '';
    public $otherSkus = '';

    public $type = null;
    public $compatiblePrinters = null;
    public $mlYield = null;
    public $colorStr = null;

    /**
     * @var ManufacturerModel
     */
    protected $_manufacturer;

    /**
     * @var TonerColorModel
     */
    protected $_tonerColor;

    /**
     * @var DealerTonerAttributeModel
     */
    protected $_dealerTonerAttribute;

    /**
     * @var bool
     */
    protected $_isCompatible;

    /**
     * @var TonerModel[]
     */
    protected $_compatibleToners;

    /**
     * @var TonerModel[]
     */
    protected $_oemToners;

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

        if (isset($params->isSystemDevice) && !is_null($params->isSystemDevice))
        {
            $this->isSystemDevice = $params->isSystemDevice;
        }

        if (isset($params->sku) && !is_null($params->sku))
        {
            $this->sku = $params->sku;
        }

        if (isset($params->cost) && !is_null($params->cost))
        {
            $this->cost = $params->cost;
        }

        if (isset($params->yield) && !is_null($params->yield))
        {
            $this->yield = $params->yield;
        }

        if (isset($params->weight) && !is_null($params->weight)) $this->weight = $params->weight;
        if (isset($params->UPC) && !is_null($params->UPC)) $this->UPC = $params->UPC;

        if (isset($params->manufacturerId) && !is_null($params->manufacturerId))
        {
            $this->manufacturerId = $params->manufacturerId;
        }

        if (isset($params->tonerColorId) && !is_null($params->tonerColorId))
        {
            $this->tonerColorId = $params->tonerColorId;
        }

        if (isset($params->calculatedCost) && !is_null($params->calculatedCost))
        {
            $this->calculatedCost = $params->calculatedCost;
        }

        if (isset($params->isUsingCustomerPricing) && !is_null($params->isUsingCustomerPricing))
        {
            $this->isUsingCustomerPricing = $params->isUsingCustomerPricing;
        }

        if (isset($params->isUsingDealerPricing) && !is_null($params->isUsingDealerPricing))
        {
            $this->isUsingDealerPricing = $params->isUsingDealerPricing;
        }

        if (isset($params->imageUrl) && !is_null($params->imageUrl))
        {
            $this->imageUrl = $params->imageUrl;
        }

        if (isset($params->imageFile) && !is_null($params->imageFile))
        {
            $this->imageFile = $params->imageFile;
        }

        if (isset($params->name) && !is_null($params->name)) $this->name = $params->name;

        if (isset($params->type) && !is_null($params->type)) $this->type = $params->type;
        if (isset($params->compatiblePrinters) && !is_null($params->compatiblePrinters)) $this->compatiblePrinters = $params->compatiblePrinters;
        if (isset($params->mlYield) && !is_null($params->mlYield)) $this->mlYield = $params->mlYield;
        if (isset($params->colorStr) && !is_null($params->colorStr)) $this->colorStr = $params->colorStr;
        if (isset($params->otherSkus) && !is_null($params->otherSkus)) $this->otherSkus = $params->otherSkus;
    }

    public function __get($property) {
        switch ($property) {
            case 'calculatedCost' :
                return $this->getCalculatedCost();
        }
    }

    public function getCalculatedCost() {
        return $this->calculatedCost * CurrencyService::getInstance()->getRate();
    }

    public function isMainType() {
        return in_array($this->type, ['Inkjet Cartridge','Laser Cartridge','Printhead','Monochrome Toner','Color Toner']);
    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            "id"             => $this->id,
            "userId"         => $this->userId,
            "isSystemDevice" => $this->isSystemDevice,
            "sku"            => $this->sku,
            "name"           => $this->name,
            "cost"           => $this->cost,
            "yield"          => $this->yield,
            "manufacturerId" => $this->manufacturerId,
            "tonerColorId"   => $this->tonerColorId,
            "imageUrl"       => $this->imageUrl,
            "imageFile"      => $this->imageFile,
            "weight"      => $this->weight,
            "UPC"      => $this->UPC,
            "type"      => $this->type,
            "compatiblePrinters"      => $this->compatiblePrinters,
            "mlYield"      => $this->mlYield,
            "colorStr"      => $this->colorStr,
            "otherSkus"      => $this->otherSkus,
        ];
    }

    public function toProductArray ()
    {
        return [
            "base_type"      => $this->tonerColorId ? 'printer_cartridge' : 'printer_consumable',
            "userId"         => $this->userId,
            "isSystemProduct" => $this->isSystemDevice,
            "sku"            => $this->sku,
            "otherSkus"            => $this->otherSkus,
            "name"           => $this->name,
            "manufacturerId" => $this->manufacturerId,
            "imageUrl"       => $this->imageUrl,
            "imageFile"      => $this->imageFile,
            "weight"      => $this->weight,
            "UPC"      => $this->UPC,
        ];
    }

    public function toConsumableArray ()
    {
        return [
            "cost"               => $this->cost,
            "pageYield"          => $this->yield,
            "type"               => $this->type,
            "compatiblePrinters" => $this->compatiblePrinters,
        ];
    }

    public function toCartridgeArray ()
    {
        return [
            "colorId"   => $this->tonerColorId,
            "mlYield"      => $this->mlYield,
            "colorStr"      => $this->colorStr,
        ];
    }

    /**
     * @return ManufacturerModel
     */
    public function getManufacturer ()
    {
        if (!isset($this->_manufacturer))
        {
            $this->_manufacturer = ManufacturerMapper::getInstance()->find($this->manufacturerId);
        }

        return $this->_manufacturer;
    }

    /**
     * @param ManufacturerModel $Manufacturer
     *
     * @return TonerModel
     */
    public function setManufacturer ($Manufacturer)
    {
        $this->_manufacturer = $Manufacturer;

        return $this;
    }

    /**
     * @return TonerColorModel
     */
    public function getTonerColor ()
    {
        if (!isset($this->_tonerColor))
        {
            $this->_tonerColor = TonerColorMapper::getInstance()->find($this->tonerColorId);
        }

        return $this->_tonerColor;
    }

    /**
     * @param TonerColorModel $tonerColor
     *
     * @return $this
     */
    public function setTonerColor ($tonerColor)
    {
        $this->_tonerColor = $tonerColor;

        return $this;
    }

    /**
     * @param int $dealerId
     *
     * @return DealerTonerAttributeModel
     */
    public function getDealerTonerAttribute ($dealerId)
    {
        if (!isset($this->_dealerTonerAttribute))
        {
            $this->_dealerTonerAttribute = [];
        }

        if (!isset($this->_dealerTonerAttribute[$dealerId]))
        {
            $this->_dealerTonerAttribute[$dealerId] = DealerTonerAttributeMapper::getInstance()->find([$this->id, $dealerId]);
            if (empty($this->_dealerTonerAttribute[$dealerId])) {
                $this->_dealerTonerAttribute[$dealerId] = new DealerTonerAttributeModel();
            }
        }

        return $this->_dealerTonerAttribute[$dealerId];
    }

    /**
     * @param DealerTonerAttributeModel $dealerTonerAttribute
     *
     * @return DealerTonerAttributeModel
     */
    public function setDealerTonerAttribute ($dealerTonerAttribute)
    {
        $this->_dealerTonerAttribute[$dealerTonerAttribute->dealerId] = $dealerTonerAttribute;

        return $this;
    }

    /**
     * Calculates the coverage adjusted cost per page
     *
     * @param CostPerPageSettingModel $costPerPageSetting
     *            The settings to use when calculating cost per page
     *
     * @return CostPerPageModel
     */
    public function calculateCostPerPage (CostPerPageSettingModel $costPerPageSetting)
    {
        // Make sure our array is initialized
        if (!isset($this->_cachedCostPerPage))
        {
            $this->_cachedCostPerPage = [];
        }

        // Turn coverage into a decimal
        $monochromeCoverage = $costPerPageSetting->pageCoverageMonochrome / 100;
        $colorCoverage      = $costPerPageSetting->pageCoverageColor / 100;

        $cacheKey = "{$monochromeCoverage}_{$colorCoverage}";
        if (!array_key_exists($cacheKey, $this->_cachedCostPerPage))
        {
            $costPerPage = new CostPerPageModel();

            /*
             * Cost per page is calculated by dividing the price by the yield. When providing new coverage we need to
             * divide the manufacturers coverage by our coverage in order to arrive at the right number.
             */
            $monochromeCostPerPage = 0;
            $colorCostPerPage      = 0;

            switch ($this->tonerColorId)
            {
                case TonerColorModel::BLACK :
                    $monochromeCostPerPage = $this->calculatedCost / ($this->yield * (self::MANUFACTURER_ASSUMED_MONO_COVERAGE / $monochromeCoverage));
                    break;
                case TonerColorModel::CYAN :
                case TonerColorModel::MAGENTA :
                case TonerColorModel::YELLOW :
                    $colorCostPerPage = $this->calculatedCost / ($this->yield * (self::MANUFACTURER_ASSUMED_COLOR_SINGLE_COVERAGE / ($colorCoverage / 4)));
                    break;
                case TonerColorModel::THREE_COLOR :
                    $colorCostPerPage = $this->calculatedCost / ($this->yield * (self::MANUFACTURER_ASSUMED_COLOR_THREE_COLOR_COMBINED_COVERAGE / ($colorCoverage / 4 * 3)));
                    break;
                case TonerColorModel::FOUR_COLOR :
                    $monochromeCostPerPage = $this->calculatedCost / ($this->yield * (self::MANUFACTURER_ASSUMED_MONO_COVERAGE / $monochromeCoverage));
                    $colorCostPerPage      = $this->calculatedCost / ($this->yield * (self::MANUFACTURER_ASSUMED_COLOR_THREE_COLOR_COMBINED_COVERAGE / ($colorCoverage / 4 * 3)));
                    break;
            }

            $costPerPage->monochromeCostPerPage = $monochromeCostPerPage;
            $costPerPage->colorCostPerPage      = $colorCostPerPage;

            $this->_cachedCostPerPage [$cacheKey] = $costPerPage;
        }

        return $this->_cachedCostPerPage [$cacheKey];
    }

    /**
     * Whether or not this toner is a compatible toner
     *
     * @return bool
     */
    public function isCompatible ()
    {
        if (!isset($this->_isCompatible))
        {

            $this->_isCompatible = (TonerVendorManufacturerMapper::getInstance()->find($this->manufacturerId) instanceof TonerVendorManufacturerModel);
        }

        return $this->_isCompatible;
    }

    /**
     * Finds other compatible toners that can be used in place of this one.
     *
     * @param null $clientId
     * @param null $dealerId
     *
     * @return TonerModel[]
     */
    public function getCompatibleToners ($clientId = null, $dealerId = null)
    {
        if (!isset($this->_compatibleToners))
        {
            if ($this->isCompatible())
            {
                $this->_compatibleToners = [];
            }
            else
            {
                $this->_compatibleToners = TonerMapper::getInstance()->findCompatibleToners($this->id, $clientId, $dealerId);
            }
        }

        return $this->_compatibleToners;
    }

    /**
     * Finds other OEM toners that can be used in place of this one.
     *
     * @param null $clientId
     * @param null $dealerId
     *
     * @return TonerModel[]
     */
    public function getOemToners ($clientId = null, $dealerId = null)
    {
        if (!isset($this->_oemToners))
        {
            if ($this->isCompatible())
            {
                $this->_oemToners = TonerMapper::getInstance()->findOemToners($this->id, $clientId, $dealerId);
            }
            else
            {
                $this->_oemToners = [];
            }
        }

        return $this->_oemToners;
    }

    public function getLocalCost() {
        return number_format(CurrencyService::getInstance()->getObjectValue($this, 'toners', 'cost'),2);
    }
    public function setLocalCost($cost) {
        CurrencyService::getInstance()->setObjectValue($this, 'toners', 'cost', $cost);
    }

}