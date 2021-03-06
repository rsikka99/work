<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import;

use Zend_Filter_Input;
use Zend_Validate_Float;
use Zend_Validate_Int;

/**
 * Class DeviceFeaturesImportService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import
 */
class DeviceFeaturesImportService extends AbstractImportService
{
    const DEVICE_FEATURES_MASTER_PRINTER_ID    = "Master Printer ID";
    const DEVICE_FEATURES_MANUFACTURER         = "Manufacturer";
    const DEVICE_FEATURES_PRINTER_MODEL        = "Printer Model";
    const DEVICE_FEATURES_DUPLEX               = "Duplex";
    const DEVICE_FEATURES_SCAN                 = "Scan";
    const DEVICE_FEATURES_REPORTS_TONER_LEVELS = "Reports Toner Levels";
    const DEVICE_FEATURES_PPM_MONOCHROME       = "PPM Monochrome";
    const DEVICE_FEATURES_PPM_COLOR            = "PPM Color";
    const DEVICE_FEATURES_OPERATING_WATTAGE    = "Operating Wattage";
    const DEVICE_FEATURES_IDLE_WATTAGE         = "Idle Wattage";
    const DEVICE_FEATURES_JIT_COMPATIBILITY    = "JIT Compatibility";

    public $csvHeaders = [
        self::DEVICE_FEATURES_MASTER_PRINTER_ID,
        self::DEVICE_FEATURES_MANUFACTURER,
        self::DEVICE_FEATURES_PRINTER_MODEL,
        self::DEVICE_FEATURES_DUPLEX,
        self::DEVICE_FEATURES_SCAN,
        self::DEVICE_FEATURES_REPORTS_TONER_LEVELS,
        self::DEVICE_FEATURES_PPM_MONOCHROME,
        self::DEVICE_FEATURES_PPM_COLOR,
        self::DEVICE_FEATURES_OPERATING_WATTAGE,
        self::DEVICE_FEATURES_IDLE_WATTAGE,
        self::DEVICE_FEATURES_JIT_COMPATIBILITY,
    ];

    public function __construct ()
    {
        $this->_inputFilter = new Zend_Filter_Input(
            [
                '*' => [
                    'StringTrim',
                    'StripTags',
                ]
            ],
            [
                '*'                                        => [
                    'allowEmpty' => true,
                ],
                self::DEVICE_FEATURES_DUPLEX               => [
                    new Zend_Validate_Int(),
                    ['Between', 0, 1, true],
                ],
                self::DEVICE_FEATURES_SCAN                 => [
                    new Zend_Validate_Int(),
                    ['Between', 0, 1, true],
                ],
                self::DEVICE_FEATURES_REPORTS_TONER_LEVELS => [
                    new Zend_Validate_Int(),
                    ['Between', 0, 1, true],
                ],
                self::DEVICE_FEATURES_PPM_MONOCHROME       => [
                    'allowEmpty' => true,
                    new Zend_Validate_Float(),
                    ['Between', 0, 1000, true],
                ],
                self::DEVICE_FEATURES_PPM_COLOR            => [
                    'allowEmpty' => true,
                    new Zend_Validate_Float(),
                    ['Between', 0, 1000, true],
                ],
                self::DEVICE_FEATURES_OPERATING_WATTAGE    => [
                    new Zend_Validate_Float(),
                    ['Between', 0, 10000, true],
                ],
                self::DEVICE_FEATURES_IDLE_WATTAGE         => [
                    new Zend_Validate_Float(),
                    ['Between', 0, 10000, true],
                ],
                self::DEVICE_FEATURES_JIT_COMPATIBILITY    => [
                    new Zend_Validate_Int(),
                    ['Between', 0, 1, true],
                ],
            ]
        );
    }
}