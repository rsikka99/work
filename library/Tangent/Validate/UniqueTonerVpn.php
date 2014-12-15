<?php

namespace Tangent\Validate;

use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;

/**
 * Validates a toner VPN (Vendor Product Number/SKU) to be unique
 * amongst the manufacturer selected.
 *
 * @uses   Zend_Validate_Abstract
 */
class UniqueTonerVpn extends \Zend_Validate_Abstract
{

    const VPN_EXISTS           = 'skuExists';
    const INVALID_MANUFACTURER = 'invalidManufacturer';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::INVALID_MANUFACTURER => 'Invalid Manufacturer',
        self::VPN_EXISTS           => 'This VPN is already in use.'
    ];

    /**
     * The ID of the toner to exclude in the search
     *
     * @var int|null
     */
    protected $tonerId;

    /**
     * Either the manufacturerId or the contextKey to get the id from
     *
     * @var int|string
     */
    protected $manufacturerId;

    /**
     * @param int $manufacturerId The manufacturer ID to look at
     * @param int $tonerId        The toner ID to exclude (in the case of saving ourselves)
     */
    public function __construct ($manufacturerId, $tonerId = null)
    {
        $this->manufacturerId = $manufacturerId;
        $this->tonerId        = ($tonerId) ? $tonerId : false;
    }

    /**
     * Ensures that a VPN in the database is unique to the manufacturer selected
     *
     * @param  string $value   The value of the current element
     * @param  array  $context An array of all the other form values.
     *
     * @return boolean
     */
    public function isValid ($value, $context = null)
    {
        $manufacturerId = false;
        if (array_key_exists($this->manufacturerId, $context))
        {
            $manufacturerId = $context[$this->manufacturerId];
        }

        if ($manufacturerId === false)
        {
            $manufacturerId = (int)$this->manufacturerId;
        }

        // We need to ensure we have a valid manufacturer before proceeding
        if (!ManufacturerMapper::getInstance()->exists($manufacturerId))
        {
            $this->_error(self::INVALID_MANUFACTURER);

            return false;
        }

        // Usually we don't want to modify the data but we always trim our VPNs
        $sku = trim($value);

        /**
         * In the system a VPN must be unique to the manufacturer. It's impossible
         * to have the same VPN for two different products.
         */
        $toner = TonerMapper::getInstance()->fetchBySkuAndManufacturer($sku, $manufacturerId);
        if ($toner instanceof TonerModel && ($this->tonerId === false || (int)$toner->id != (int)$this->tonerId))
        {
            $this->_error(self::VPN_EXISTS);

            return false;
        }

        return true;
    }
}