<?php

/**
 * Class Admin_Service_Onboarding_Compatible
 */
class Admin_Service_Onboarding_Compatible extends Admin_Service_Onboarding_Abstract
{
    const COLUMN_TONER_MANUFACTURER                 = 'tonerManufacturer';
    const COLUMN_TONER_SKU                          = 'tonerSku';
    const COLUMN_TONER_DEALER_SKU                   = 'tonerDealerSku';
    const COLUMN_TONER_COLOR                        = 'tonerColor';
    const COLUMN_TONER_DEALER_COST                  = 'tonerDealerCost';
    const COLUMN_TONER_YIELD                        = 'tonerYield';
    const COLUMN_TONER_COMPATIBLE_WITH_SKU          = 'compatibleWithTonerSku';
    const COLUMN_TONER_COMPATIBLE_WITH_MANUFACTURER = 'compatibleWithTonerManufacturer';

    /**
     * Column mapping for CSV -> Upload Row.
     * This lets us accept different column names for a report and map the values to these.
     *
     * @var array
     */
    protected $_columnMapping = array(
        'Manufacturer'                 => self::COLUMN_TONER_MANUFACTURER,
        'Manufacturer Sku'             => self::COLUMN_TONER_SKU,
        'Dealer Sku'                   => self::COLUMN_TONER_DEALER_SKU,
        'Color'                        => self::COLUMN_TONER_COLOR,
        'Cost'                         => self::COLUMN_TONER_DEALER_COST,
        'Yield'                        => self::COLUMN_TONER_YIELD,
        'Compatible With Sku'          => self::COLUMN_TONER_COMPATIBLE_WITH_SKU,
        'Compatible With Manufacturer' => self::COLUMN_TONER_COMPATIBLE_WITH_MANUFACTURER,
    );

    /**
     * The fields that must be present within the CSV format
     *
     * @var array
     */
    protected $_requiredHeaders = array(
        self::COLUMN_TONER_MANUFACTURER                 => true,
        self::COLUMN_TONER_SKU                          => true,
        self::COLUMN_TONER_DEALER_SKU                   => true,
        self::COLUMN_TONER_COLOR                        => false,
        self::COLUMN_TONER_DEALER_COST                  => true,
        self::COLUMN_TONER_YIELD                        => false,
        self::COLUMN_TONER_COMPATIBLE_WITH_SKU          => true,
        self::COLUMN_TONER_COMPATIBLE_WITH_MANUFACTURER => true,
    );


    /**
     * The constructor for this object.
     * Sets up filters and validators for our data.
     */
    public function __construct ()
    {
        $filters = array(
            '*'                            => array(
                'StringTrim',
            ),
            self::COLUMN_TONER_DEALER_COST => array(
                'StringTrim',
                'Float',
            ),
            self::COLUMN_TONER_YIELD       => array(
                'StringTrim',
                'Float',
            ),
        );

        $this->_inputFilter = new Zend_Filter_Input($filters, array());

        // If we haven't set a filter for the data, use this one
        $this->_inputFilter->setDefaultEscapeFilter(new Zend_Filter_StringTrim());
    }

    /**
     * Processes the csv file
     *
     * @param string $filename
     * @param int    $dealerId
     *
     * @return array|bool|string
     */
    public function processFile ($filename, $dealerId)
    {
        $csvLines = $this->getCsvContents($filename);

        $messages = array();

        if (is_array($csvLines) && count($csvLines) > 0)
        {
            $dealerTonerAttributeMapper = Proposalgen_Model_Mapper_Dealer_Toner_Attribute::getInstance();
            $deviceTonerMapper          = Proposalgen_Model_Mapper_DeviceToner::getInstance();
            $manufacturers              = array();
            $manufacturerMapper         = Proposalgen_Model_Mapper_Manufacturer::getInstance();
            $tonerColors                = Proposalgen_Model_TonerColor::$ColorNames;
            $tonerMapper                = Proposalgen_Model_Mapper_Toner::getInstance();

            /**
             * We're going to validate everything first so that we can get some friendly error messages about the file.
             */
            foreach ($csvLines as $csvLine)
            {
                /**
                 * Compatible Manufacturer
                 */
                $manufacturerName = $csvLine[self::COLUMN_TONER_MANUFACTURER];

                if (!isset($manufacturers[$manufacturerName]))
                {
                    $manufacturerList = $manufacturerMapper->searchByName($manufacturerName);
                    $manufacturer     = reset($manufacturerList);

                    if ($manufacturer instanceof Proposalgen_Model_Manufacturer)
                    {
                        $manufacturers[$manufacturerName] = $manufacturer->id;
                    }
                    else
                    {
                        $messages[]                       = sprintf('The manufacturer "%1$s" can not be found. Check your spelling or create the manufacturer in the system.', $manufacturerName);
                        $manufacturers[$manufacturerName] = false;
                    }
                }

                /**
                 * OEM Manufacturer
                 */
                $oemManufacturerName = $csvLine[self::COLUMN_TONER_COMPATIBLE_WITH_MANUFACTURER];

                if (!isset($manufacturers[$oemManufacturerName]))
                {
                    $manufacturerList = $manufacturerMapper->searchByName($manufacturerName);
                    $manufacturer     = reset($manufacturerList);

                    if ($manufacturer instanceof Proposalgen_Model_Manufacturer)
                    {
                        $manufacturers[$oemManufacturerName] = $manufacturer->id;
                    }
                    else
                    {
                        $messages[]                          = sprintf('The manufacturer "%1$s" can not be found. Check your spelling or create the manufacturer in the system.', $oemManufacturerName);
                        $manufacturers[$oemManufacturerName] = false;
                    }
                }

                /**
                 * Toner Color
                 */
                $compTonerColorName = $csvLine[self::COLUMN_TONER_COLOR];
                $validTonerColor    = false;
                foreach ($tonerColors as $tonerColorName)
                {
                    if (strcasecmp($tonerColorName, $compTonerColorName) === 0)
                    {
                        $validTonerColor = true;
                        break;
                    }
                }

                if (!$validTonerColor)
                {
                    $messages[] = sprintf('The toner color "%1$s" is not valid. Valid Names are: "%2$s"', $compTonerColorName, implode('", "', $tonerColors));
                }
            }

            /**
             * As long as we have no messages we can proceed and attempt to update pricing
             */
            if (count($messages) < 1)
            {
                foreach ($csvLines as $csvLine)
                {
                    $compatibleManufacturerName = $csvLine[self::COLUMN_TONER_MANUFACTURER];
                    $compatibleSku              = $csvLine[self::COLUMN_TONER_SKU];
                    $compatibleYield            = $csvLine[self::COLUMN_TONER_YIELD];
                    $dealerCost                 = $csvLine[self::COLUMN_TONER_DEALER_COST];
                    $dealerSku                  = $csvLine[self::COLUMN_TONER_DEALER_SKU];
                    $oemTonerSku                = $csvLine[self::COLUMN_TONER_COMPATIBLE_WITH_SKU];
                    $oemTonerManufacturerName   = $csvLine[self::COLUMN_TONER_COMPATIBLE_WITH_MANUFACTURER];

                    /**
                     * Get the color of the toner
                     */
                    $compTonerColorName     = $csvLine[self::COLUMN_TONER_COLOR];
                    $compatibleTonerColorId = 1;
                    foreach ($tonerColors as $tonerColorId => $tonerColorName)
                    {
                        if (strcasecmp($tonerColorName, $compTonerColorName) === 0)
                        {
                            $compatibleTonerColorId = $tonerColorId;
                            break;
                        }
                    }

                    /**
                     * Make sure the compatible toner is in the system
                     */
                    $compatibleToner = $tonerMapper->fetchBySkuAndManufacturer($compatibleSku, $manufacturers[$compatibleManufacturerName]);
                    if (!$compatibleToner instanceof Proposalgen_Model_Toner)
                    {
                        $compatibleToner                 = new Proposalgen_Model_Toner();
                        $compatibleToner->sku            = $compatibleSku;
                        $compatibleToner->manufacturerId = $manufacturers[$compatibleManufacturerName];
                        $compatibleToner->tonerColorId   = $compatibleTonerColorId;
                        $compatibleToner->userId         = 1;
                        $compatibleToner->yield          = $compatibleYield;
                        $compatibleToner->cost           = Proposalgen_Service_Toner::obfuscateTonerCost($dealerCost);
                        $compatibleToner->isSystemDevice = false;
                        $tonerMapper->insert($compatibleToner);
                    }
                    
                    /**
                     * Toner colors must match. If we're inserting into the system this should always be true.
                     */
                    if ($compatibleToner->tonerColorId === $compatibleTonerColorId)
                    {
                        /**
                         * Update the compatible toner dealer price
                         */
                        $dealerTonerAttribute = $dealerTonerAttributeMapper->findTonerAttributeByTonerId($compatibleToner->id, $dealerId);
                        if ($dealerTonerAttribute instanceof Proposalgen_Model_Dealer_Toner_Attribute)
                        {
                            $dealerTonerAttribute->cost = $dealerCost;
                            if (strlen($dealerSku) > 0 && strcasecmp($compatibleToner->sku, $dealerSku) !== 0)
                            {
                                $dealerTonerAttribute->dealerSku = $dealerSku;
                            }

                            $dealerTonerAttributeMapper->save($dealerTonerAttribute);
                        }
                        else
                        {
                            $dealerTonerAttribute           = new Proposalgen_Model_Dealer_Toner_Attribute();
                            $dealerTonerAttribute->tonerId  = $compatibleToner->id;
                            $dealerTonerAttribute->dealerId = $dealerId;
                            $dealerTonerAttribute->cost     = $dealerCost;

                            if (strlen($dealerSku) > 0)
                            {
                                $dealerTonerAttribute->dealerSku = $dealerSku;
                            }

                            $dealerTonerAttributeMapper->insert($dealerTonerAttribute);
                        }

                        /**
                         * Map the compatible SKU to the same devices as the OEM SKU
                         */
                        $oemToner = $tonerMapper->fetchBySkuAndManufacturer($oemTonerSku, $manufacturers[$oemTonerManufacturerName]);
                        if ($oemToner)
                        {
                            $existingDeviceToners = $deviceTonerMapper->fetchDeviceTonersByTonerId($oemToner->id);
                            foreach ($existingDeviceToners as $existingDeviceToner)
                            {
                                if (!$deviceTonerMapper::getInstance()->find(array($compatibleToner->id, $existingDeviceToner->master_device_id)) instanceof Proposalgen_Model_DeviceToner)
                                {
                                    $deviceToner                   = new Proposalgen_Model_DeviceToner();
                                    $deviceToner->toner_id         = $compatibleToner->id;
                                    $deviceToner->master_device_id = $existingDeviceToner->master_device_id;
                                    Proposalgen_Model_Mapper_DeviceToner::getInstance()->insert($deviceToner);
                                }
                            }
                        }
                    }
                    else
                    {
                        $messages[] = sprintf('The toner color specified in the file ("%1$s") does not match the toner color of the system toner ("%2$s").', $compTonerColorName, $tonerColors[$compatibleToner->tonerColorId]);
                    }
                }
            }
        }
        else
        {
            $messages[] = $csvLines;
        }

        return $messages;
    }
}