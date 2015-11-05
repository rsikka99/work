<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import;

use My_Filter_StringReplace;
use Zend_Filter_Input;
use Zend_Validate_Float;

/**
 * Class TonerPricingImportService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import
 */
class TonerPricingImportService extends AbstractImportService
{
    const TONER_PRICING_TONER_ID     = "Toner ID";
    const TONER_PRICING_MANUFACTURER = "Manufacturer";
    const TONER_PRICING_SKU          = "SKU";
    const TONER_PRICING_NAME         = "Name";
    const TONER_PRICING_COLOR        = "Color";
    const TONER_PRICING_YIELD        = "Yield";
    const TONER_PRICING_SYSTEM_PRICE = "System Price";
    const TONER_PRICING_DEALER_SKU   = "Dealer SKU";
    const TONER_PRICING_NEW_PRICE    = "New Price";
    const TONER_PRICING_LEVEL_1      = "Price Level 1";
    const TONER_PRICING_LEVEL_2      = "Price Level 2";
    const TONER_PRICING_LEVEL_3      = "Price Level 3";
    const TONER_PRICING_LEVEL_4      = "Price Level 4";
    const TONER_PRICING_LEVEL_5      = "Price Level 5";

    public $csvHeaders = [
        self::TONER_PRICING_TONER_ID,
        self::TONER_PRICING_MANUFACTURER,
        self::TONER_PRICING_SKU,
        self::TONER_PRICING_NAME,
        self::TONER_PRICING_COLOR,
        self::TONER_PRICING_YIELD,
        self::TONER_PRICING_SYSTEM_PRICE,
        self::TONER_PRICING_DEALER_SKU,
        self::TONER_PRICING_NEW_PRICE,
        self::TONER_PRICING_LEVEL_1,
        self::TONER_PRICING_LEVEL_2,
        self::TONER_PRICING_LEVEL_3,
        self::TONER_PRICING_LEVEL_4,
        self::TONER_PRICING_LEVEL_5,
    ];

    public function __construct ()
    {
        $this->_inputFilter = new Zend_Filter_Input(
            [
                '*'                           => [
                    'StripTags',
                    'StringTrim',
                ],
                self::TONER_PRICING_NEW_PRICE => [
                    new My_Filter_StringReplace(
                        [
                            "find" => "$"
                        ]
                    )
                ]
            ],
            [
                '*'                           => [
                    'allowEmpty' => true,
                ],
                self::TONER_PRICING_NEW_PRICE => [
                    'allowEmpty' => true,
                    new Zend_Validate_Float(),
                    ['GreaterThan', 0]
                ]
            ]
        );

    }
}