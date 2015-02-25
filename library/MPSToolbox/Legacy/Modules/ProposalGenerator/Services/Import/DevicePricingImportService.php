<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import;

use My_Filter_StringReplace;
use Zend_Filter_Input;
use Zend_Validate_Float;

/**
 * Class DevicePricingImportService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import
 */
class DevicePricingImportService extends AbstractImportService
{
    const DEVICE_PRICING_MASTER_PRINTER_ID = "Master Printer ID";
    const DEVICE_PRICING_MANUFACTURER      = "Manufacturer";
    const DEVICE_PRICING_PRINTER_MODEL     = "Printer Model";
    const DEVICE_PRICING_LABOR_CPP         = "Labor CPP";
    const DEVICE_PRICING_PARTS_CPP         = "Parts CPP";

    public $csvHeaders = [
        self::DEVICE_PRICING_MASTER_PRINTER_ID,
        self::DEVICE_PRICING_MANUFACTURER,
        self::DEVICE_PRICING_PRINTER_MODEL,
        self::DEVICE_PRICING_LABOR_CPP,
        self::DEVICE_PRICING_PARTS_CPP,
    ];

    public function __construct ()
    {
        $this->_inputFilter = new Zend_Filter_Input(
            [
                '*'                            => [
                    'StripTags',
                    'StringTrim',
                ],
                self::DEVICE_PRICING_LABOR_CPP => [
                    new My_Filter_StringReplace(
                        [
                            "find" => "$"
                        ]
                    )
                ],
                self::DEVICE_PRICING_PARTS_CPP => [
                    new My_Filter_StringReplace(
                        [
                            "find" => "$"
                        ]
                    )
                ],
            ],
            [
                '*'                            => [
                    'allowEmpty' => true,
                ],
                self::DEVICE_PRICING_LABOR_CPP => [
                    'allowEmpty' => true,
                    new Zend_Validate_Float(),
                    ['Between', 0, 5, true],
                ],
                self::DEVICE_PRICING_PARTS_CPP => [
                    'allowEmpty' => true,
                    new Zend_Validate_Float(),
                    ['Between', 0, 5, true],
                ]
            ]
        );
    }

}