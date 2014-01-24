<?php

/**
 * Class Proposalgen_Service_Import_Device_Pricing
 */
class Proposalgen_Service_Import_Device_Pricing extends Proposalgen_Service_Import_Abstract
{
    const DEVICE_PRICING_MASTER_PRINTER_ID = "Master Printer ID";
    const DEVICE_PRICING_MANUFACTURER      = "Manufacturer";
    const DEVICE_PRICING_PRINTER_MODEL     = "Printer Model";
    const DEVICE_PRICING_LABOR_CPP         = "Labor CPP";
    const DEVICE_PRICING_PARTS_CPP         = "Parts CPP";

    public $csvHeaders = array(
        self::DEVICE_PRICING_MASTER_PRINTER_ID,
        self::DEVICE_PRICING_MANUFACTURER,
        self::DEVICE_PRICING_PRINTER_MODEL,
        self::DEVICE_PRICING_LABOR_CPP,
        self::DEVICE_PRICING_PARTS_CPP
    );

    public function __construct ()
    {
        $this->_inputFilter = new Zend_Filter_Input(
            array(
                '*'                            => array(
                    'StripTags',
                    'StringTrim',
                ),
                self::DEVICE_PRICING_LABOR_CPP => array(
                    new My_Filter_StringReplace(
                        array(
                            "find" => "$"
                        )
                    )
                ),
                self::DEVICE_PRICING_PARTS_CPP => array(
                    new My_Filter_StringReplace(
                        array(
                            "find" => "$"
                        )
                    )
                ),
            ),
            array(
                '*'                            => array(
                    'allowEmpty' => true,
                ),
                self::DEVICE_PRICING_LABOR_CPP => array(
                    'allowEmpty' => true,
                    new Zend_Validate_Float(),
                    array('Between', 0, 5, true),
                ),
                self::DEVICE_PRICING_PARTS_CPP => array(
                    'allowEmpty' => true,
                    new Zend_Validate_Float(),
                    array('Between', 0, 5, true),
                )
            )
        );
    }

}