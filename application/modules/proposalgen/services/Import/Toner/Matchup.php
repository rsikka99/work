<?php

/**
 * Class Proposalgen_Service_Import_Toner_Matchup
 */
class Proposalgen_Service_Import_Toner_Matchup extends Proposalgen_Service_Import_Abstract
{
    const TONER_MATCHUP_DEVICE_NAME            = "Device Name";
    const TONER_MATCHUP_MANUFACTURER           = "Manufacturer*";
    const TONER_MATCHUP_OEM_TONER_SKU          = "OEM Toner SKU*";
    const TONER_MATCHUP_OEM_DEALER_TONER_SKU   = "OEM Dealer Toner SKU";
    const TONER_MATCHUP_OEM_DEALER_COST        = "OEM Dealer Cost";
    const TONER_MATCHUP_COLOR                  = "Color*";
    const TONER_MATCHUP_COMPATIBLE_VENDOR_NAME = "Compatible Vendor Name*";
    const TONER_MATCHUP_COMPATIBLE_VENDOR_SKU  = "Compatible Vendor SKU*";
    const TONER_MATCHUP_COMPATIBLE_DEALER_SKU  = "Compatible Dealer SKU*";
    const TONER_MATCHUP_COMPATIBLE_YIELD       = "Compatible Yield*";
    const TONER_MATCHUP_COMPATIBLE_DEALER_COST = "Compatible Dealer Cost*";

    public $csvHeaders = array(
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
    );

    public function __construct ()
    {
        $this->_inputFilter = new Zend_Filter_Input(
            array(
                '*'                                        => array(
                    'StringTrim',
                    'StripTags',
                ),
                self::TONER_MATCHUP_COMPATIBLE_DEALER_COST => array(
                    new My_Filter_StringReplace(array("find" => "$",))
                ),
                self::TONER_MATCHUP_COMPATIBLE_YIELD       => array(
                    'Alnum'
                ),
            ),
            array(
                '*'                                        => array(
                    'allowEmpty' => true,
                ),
                self::TONER_MATCHUP_MANUFACTURER           => array(
                    'allowEmpty' => false,
                ),
                self::TONER_MATCHUP_OEM_TONER_SKU          => array(
                    'allowEmpty' => false,
                ),
                self::TONER_MATCHUP_COLOR                  => array(
                    'allowEmpty' => false,
                ),
                self::TONER_MATCHUP_COMPATIBLE_VENDOR_NAME => array(
                    'allowEmpty' => false,
                ),
                self::TONER_MATCHUP_COMPATIBLE_VENDOR_SKU  => array(
                    'allowEmpty' => false,
                ),
                self::TONER_MATCHUP_COMPATIBLE_DEALER_SKU  => array(
                    'allowEmpty' => false,
                ),
                self::TONER_MATCHUP_COMPATIBLE_YIELD       => array(
                    new Zend_Validate_Int(),
                    array('GreaterThan', 0),
                ),
                self::TONER_MATCHUP_COMPATIBLE_DEALER_COST => array(
                    new Zend_Validate_Float(),
                    array('GreaterThan', 0)
                ),
            )
        );
    }

    /*
     * @param $data
     * @return \Zend_Filter_Input
     */
    public function processValidation ($data)
    {
        try
        {
            $this->_inputFilter->setData($data);

            $parsedTonerData = array(
                'parsedToners' => array(
                    'oem'  => array(),
                    'comp' => array()
                )
            );

            // Do we have a valid row of data
            if ($this->_inputFilter->isValid())
            {
                $oemTonerData                           = array(
                    'sku'              => $this->_inputFilter->getEscaped(self::TONER_MATCHUP_OEM_TONER_SKU),
                    'manufacturerName' => $this->_inputFilter->getEscaped(self::TONER_MATCHUP_MANUFACTURER),
                    'colorName'        => $this->_inputFilter->getEscaped(self::TONER_MATCHUP_COLOR),
                );
                $parsedTonerData['parsedToners']['oem'] = $this->_parseTonerData($oemTonerData);

                $compTonerData                           = array(
                    'sku'              => $this->_inputFilter->getEscaped(self::TONER_MATCHUP_COMPATIBLE_VENDOR_SKU),
                    'cost'             => $this->_inputFilter->getEscaped(self::TONER_MATCHUP_COMPATIBLE_DEALER_COST),
                    'yield'            => $this->_inputFilter->getEscaped(self::TONER_MATCHUP_COMPATIBLE_YIELD),
                    'manufacturerName' => $this->_inputFilter->getEscaped(self::TONER_MATCHUP_COMPATIBLE_VENDOR_NAME),
                    'colorName'        => $this->_inputFilter->getEscaped(self::TONER_MATCHUP_COLOR),
                    'dealerSku'        => $this->_inputFilter->getEscaped(self::TONER_MATCHUP_COMPATIBLE_DEALER_SKU),
                );
                $parsedTonerData['parsedToners']['comp'] = $this->_parseTonerData($compTonerData);
            }
            else
            {
                return array(
                    "error" => array(
                        "invalid" => $this->_inputFilter->getInvalid()
                    )
                );
            }

            // Is our parsed data valid if we got a valid manufacturer id we know its valid
            if (!isset($parsedTonerData['parsedToners']['comp']['manufacturerId']))
            {
                $errors ['parseError'][key($parsedTonerData['parsedToners']['comp'])] = $parsedTonerData['parsedToners']['comp'];
            }
            if (!isset($parsedTonerData['parsedToners']['oem']['manufacturerId']))
            {
                $errors ['parseError'][key($parsedTonerData['parsedToners']['oem'])] = $parsedTonerData['parsedToners']['oem'];
            }

            if (isset($errors['parseError']))
            {
                return array(
                    "error" => $errors,
                );
            }

            return array_merge($this->_inputFilter->getEscaped(), $parsedTonerData);
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
        //$toner = Proposalgen_Model_Mapper_Toner::getInstance()->searchBySku($tonerData['sku']);
        $toner = Proposalgen_Model_Mapper_Toner::getInstance()->fetchBySku($tonerData['sku']);
        // Persist the id if we have found the toner by the SKU
        if ($toner instanceof Proposalgen_Model_Toner)
        {
            $tonerData['id']             = $toner->id;
            $tonerData['manufacturerId'] = $toner->manufacturerId;;
        }
        else
        {
            $manufacturer = Proposalgen_Model_Mapper_Manufacturer::getInstance()->searchByName($tonerData['manufacturerName']);

            if (empty($manufacturer))
            {
                // This an OEM or comp toner
                if (isset($tonerData['dealerSku']))
                {
                    return array(self::TONER_MATCHUP_COMPATIBLE_VENDOR_NAME => "Manufacturer does not exist, or it is spelt incorrectly.");
                }
                else
                {
                    return array(self::TONER_MATCHUP_MANUFACTURER => "Manufacturer does not exist, or it is spelt incorrectly.");
                }
            }

            $tonerData['manufacturerId'] = $manufacturer[0]->id;

            $tonerColorId = array_search($tonerData['colorName'], Proposalgen_Model_TonerColor::$ColorNames);

            if ($tonerColorId)
            {
                $tonerData['tonerColorId'] = $tonerColorId;
            }
            else
            {
                return array(self::TONER_MATCHUP_COLOR => "Color does not exist, or it is spelt incorrectly.");
            }
        }

        return $tonerData;
    }
}
