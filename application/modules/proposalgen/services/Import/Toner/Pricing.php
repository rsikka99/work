<?php
/**
 * Class Proposalgen_Service_Import_Toner_Pricing
 */
class Proposalgen_Service_Import_Toner_Pricing extends Proposalgen_Service_Import_Abstract
{
    const TONER_PRICING_TONER_ID     = "Toner ID";
    const TONER_PRICING_MANUFACTURER = "Manufacturer";
    const TONER_PRICING_SKU          = "SKU";
    const TONER_PRICING_COLOR        = "Color";
    const TONER_PRICING_YIELD        = "Yield";
    const TONER_PRICING_SYSTEM_PRICE = "System Price";
    const TONER_PRICING_DEALER_SKU   = "Dealer Sku";
    const TONER_PRICING_NEW_PRICE    = "New Price";

    public $csvHeaders = array(
        self::TONER_PRICING_TONER_ID,
        self::TONER_PRICING_MANUFACTURER,
        self::TONER_PRICING_SKU,
        self::TONER_PRICING_COLOR,
        self::TONER_PRICING_YIELD,
        self::TONER_PRICING_SYSTEM_PRICE,
        self::TONER_PRICING_DEALER_SKU,
        self::TONER_PRICING_NEW_PRICE,
    );

    public function __construct ()
    {
        $this->_inputFilter = new Zend_Filter_Input(
            array(
                 '*'                           => array(
                     'StripTags',
                     'StringTrim',
                 ),
                 self::TONER_PRICING_NEW_PRICE => array(
                     new My_Filter_StringReplace(
                         array(
                              "find" => "$"
                         )
                     )
                 )
            ),
            array(
                 '*'                           => array(
                     'allowEmpty' => true,
                 ),
                 self::TONER_PRICING_NEW_PRICE => array(
                     'allowEmpty' => true,
                     new Zend_Validate_Float(),
                     array('GreaterThan', 0)
                 )
            )
        );

    }
}