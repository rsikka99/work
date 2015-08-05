<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import;

use Exception;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use My_Filter_StringReplace;
use Zend_Filter_Input;
use Zend_Validate_Float;
use Zend_Validate_Int;

/**
 * Class TonerMatchupImportService
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Services\Import
 */
class TonerMatchupImportService extends AbstractImportService
{
    const TONER_MATCHUP_DEVICE_NAME            = "Device Name";
    const TONER_MATCHUP_MANUFACTURER           = "Manufacturer*";
    const TONER_MATCHUP_OEM_TONER_SKU          = "OEM Toner SKU*";
    const TONER_MATCHUP_OEM_DEALER_TONER_SKU   = "OEM Dealer Toner SKU";
    const TONER_MATCHUP_OEM_DEALER_COST        = "OEM Dealer Cost";
    const TONER_MATCHUP_COLOR                  = "Color";
    const TONER_MATCHUP_COMPATIBLE_VENDOR_NAME = "Compatible Vendor Name*";
    const TONER_MATCHUP_COMPATIBLE_VENDOR_SKU  = "Compatible Vendor SKU*";
    const TONER_MATCHUP_COMPATIBLE_DEALER_SKU  = "Compatible Dealer SKU";
    const TONER_MATCHUP_COMPATIBLE_YIELD       = "Compatible Yield*";
    const TONER_MATCHUP_COMPATIBLE_DEALER_COST = "Compatible Dealer Cost*";

    public $csvHeaders = [
        self::TONER_MATCHUP_DEVICE_NAME,
        self::TONER_MATCHUP_MANUFACTURER,
        self::TONER_MATCHUP_OEM_TONER_SKU,
        self::TONER_MATCHUP_OEM_DEALER_TONER_SKU,
        self::TONER_MATCHUP_OEM_DEALER_COST,
        self::TONER_MATCHUP_COLOR,
        self::TONER_MATCHUP_COMPATIBLE_VENDOR_NAME,
        self::TONER_MATCHUP_COMPATIBLE_VENDOR_SKU,
        self::TONER_MATCHUP_COMPATIBLE_DEALER_SKU,
        self::TONER_MATCHUP_COMPATIBLE_YIELD,
        self::TONER_MATCHUP_COMPATIBLE_DEALER_COST,
    ];

    public function __construct ()
    {
        $this->_inputFilter = new Zend_Filter_Input(
            [
                '*'                                        => [
                    'StringTrim',
                    'StripTags',
                ],
                self::TONER_MATCHUP_COMPATIBLE_DEALER_COST => [
                    new My_Filter_StringReplace(["find" => "$",])
                ],
                self::TONER_MATCHUP_COMPATIBLE_YIELD       => [
                    'Alnum'
                ],
            ],
            [
                '*'                                        => [
                    'allowEmpty' => true,
                ],
                self::TONER_MATCHUP_MANUFACTURER           => [
                    'allowEmpty' => false,
                ],
                self::TONER_MATCHUP_OEM_TONER_SKU          => [
                    'allowEmpty' => false,
                ],
                self::TONER_MATCHUP_COMPATIBLE_VENDOR_NAME => [
                    'allowEmpty' => false,
                ],
                self::TONER_MATCHUP_COMPATIBLE_VENDOR_SKU  => [
                    'allowEmpty' => false,
                ],
                self::TONER_MATCHUP_COMPATIBLE_YIELD       => [
                    new Zend_Validate_Int(),
                    ['GreaterThan', 0],
                ],
                self::TONER_MATCHUP_COMPATIBLE_DEALER_COST => [
                    new Zend_Validate_Float(),
                    ['GreaterThan', 0]
                ],
            ]
        );
    }

    /**
     * @param $data
     *
     * @throws Exception
     * @return \Zend_Filter_Input
     */
    public function processValidation ($data)
    {
        try
        {
            $this->_inputFilter->setData($data);

            $parsedTonerData = [
                'parsedToners' => [
                    'oem'  => [],
                    'comp' => [],
                ],
            ];

            // Do we have a valid row of data
            if ($this->_inputFilter->isValid())
            {
                $oemTonerData                           = [
                    'sku'              => $this->_inputFilter->getUnescaped(self::TONER_MATCHUP_OEM_TONER_SKU),
                    'manufacturerName' => $this->_inputFilter->getUnescaped(self::TONER_MATCHUP_MANUFACTURER),
                    'colorName'        => $this->_inputFilter->getUnescaped(self::TONER_MATCHUP_COLOR),
                ];
                $parsedTonerData['parsedToners']['oem'] = $this->_parseTonerData($oemTonerData);

                $compTonerData                           = [
                    'sku'              => $this->_inputFilter->getUnescaped(self::TONER_MATCHUP_COMPATIBLE_VENDOR_SKU),
                    'cost'             => $this->_inputFilter->getUnescaped(self::TONER_MATCHUP_COMPATIBLE_DEALER_COST),
                    'yield'            => $this->_inputFilter->getUnescaped(self::TONER_MATCHUP_COMPATIBLE_YIELD),
                    'manufacturerName' => $this->_inputFilter->getUnescaped(self::TONER_MATCHUP_COMPATIBLE_VENDOR_NAME),
                    'dealerSku'        => $this->_inputFilter->getUnescaped(self::TONER_MATCHUP_COMPATIBLE_DEALER_SKU),
                    'colorName'        => $this->_inputFilter->getUnescaped(self::TONER_MATCHUP_COLOR),
                ];
                $parsedTonerData['parsedToners']['comp'] = $this->_parseTonerData($compTonerData);
            }
            else
            {
                return [
                    "error" => [
                        "invalid" => $this->_inputFilter->getInvalid(),
                    ],
                ];
            }

            // Is our parsed data valid if we got a valid manufacturer id we know its valid
            if (!isset($parsedTonerData['parsedToners']['comp']['manufacturerId']))
            {
                $errors ['parseError'][self::TONER_MATCHUP_COMPATIBLE_VENDOR_NAME] = ['Could not find manufacturer with the name of ' . $data[self::TONER_MATCHUP_COMPATIBLE_VENDOR_NAME]];
            }

            if (!isset($parsedTonerData['parsedToners']['oem']['manufacturerId']))
            {
                $errors ['parseError'][self::TONER_MATCHUP_MANUFACTURER] = ['Could not find manufacturer with the name of ' . $data[self::TONER_MATCHUP_MANUFACTURER]];
            }

            if (isset($errors['parseError']))
            {
                return [
                    "error" => $errors,
                ];
            }

            return array_merge($this->_inputFilter->getUnescaped(), $parsedTonerData);
        }
        catch (Exception $e)
        {
            throw new Exception("Passing exception up the chain.", 0, $e);
        }
    }

    /**
     * @param $tonerData
     *
     * @return string| array
     */
    private function _parseTonerData ($tonerData)
    {
        $toner = TonerMapper::getInstance()->fetchBySku($tonerData['sku']);

        // Persist the id if we have found the toner by the SKU
        if ($toner instanceof TonerModel)
        {
            $tonerData['id']             = $toner->id;
            $tonerData['manufacturerId'] = $toner->manufacturerId;
            $tonerData['tonerColorId']   = $toner->tonerColorId;
        }
        else
        {
            $manufacturer = ManufacturerMapper::getInstance()->searchByName($tonerData['manufacturerName']);

            if (empty($manufacturer))
            {
                // This an OEM or comp toner
                if (array_key_exists('dealerSku', $tonerData))
                {
                    return [self::TONER_MATCHUP_COMPATIBLE_VENDOR_NAME => "Manufacturer does not exist, or it is spelt incorrectly. (" . $tonerData['manufacturerName'] . ")"];
                }
            }
            else
            {
                $tonerData['manufacturerId'] = $manufacturer[0]->id;

                if (strlen($tonerData['colorName']) > 0)
                {
                    //$tonerColorId = array_search($tonerData['colorName'], TonerColorModel::$ColorNames);
                    $tonerColorId = array_search(strtoupper($tonerData['colorName']),array_map('strtoupper',TonerColorModel::$ColorNames));

                    if ($tonerColorId)
                    {
                        $tonerData['tonerColorId'] = $tonerColorId;
                    }
                    else
                    {
                        return [self::TONER_MATCHUP_COLOR => "Color does not exist, or it is spelt incorrectly."];
                    }
                }
            }
        }

        return $tonerData;
    }
}
