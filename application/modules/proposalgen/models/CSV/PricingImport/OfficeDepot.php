<?php

class Proposalgen_Model_CSV_PricingImport_OfficeDepot extends Proposalgen_Model_CSV_Abstract
{
    public $test = array (
            "errors" => array (), 
            "rows" => array () 
    );
    public $validHeaders = array (
            "printermodelid" => self::FIELD_NOTREQUIRED, 
            "modelname" => self::FIELD_REQUIRED, 
            "modelmfg" => self::FIELD_REQUIRED, 
            "black oem sku" => self::FIELD_REQUIRED_DEPENDS, 
            "black oem cost" => self::FIELD_REQUIRED_DEPENDS, 
            "black oem yield" => self::FIELD_REQUIRED_DEPENDS, 
            "cyan oem sku" => self::FIELD_REQUIRED_DEPENDS, 
            "cyan oem cost" => self::FIELD_REQUIRED_DEPENDS, 
            "cyan oem yield" => self::FIELD_REQUIRED_DEPENDS, 
            "magenta oem sku" => self::FIELD_REQUIRED_DEPENDS, 
            "magenta oem cost" => self::FIELD_REQUIRED_DEPENDS, 
            "magenta oem yield" => self::FIELD_REQUIRED_DEPENDS, 
            "yellow oem sku" => self::FIELD_REQUIRED_DEPENDS, 
            "yellow oem cost" => self::FIELD_REQUIRED_DEPENDS, 
            "yellow oem yield" => self::FIELD_REQUIRED_DEPENDS, 
            "3 color oem sku" => self::FIELD_REQUIRED_DEPENDS, 
            "3 color oem cost" => self::FIELD_REQUIRED_DEPENDS, 
            "3 color oem yield" => self::FIELD_REQUIRED_DEPENDS, 
            "4 color oem sku" => self::FIELD_REQUIRED_DEPENDS, 
            "4 color oem cost" => self::FIELD_REQUIRED_DEPENDS, 
            "4 color oem yield" => self::FIELD_REQUIRED_DEPENDS, 
            "black compatible sku" => self::FIELD_REQUIRED_DEPENDS, 
            "black compatible man." => self::FIELD_REQUIRED_DEPENDS, 
            "black compatible cost" => self::FIELD_REQUIRED_DEPENDS, 
            "black compatible yield" => self::FIELD_REQUIRED_DEPENDS, 
            "cyan compatible sku" => self::FIELD_REQUIRED_DEPENDS, 
            "cyan compatible man." => self::FIELD_REQUIRED_DEPENDS, 
            "cyan compatible cost" => self::FIELD_REQUIRED_DEPENDS, 
            "cyan compatible yield" => self::FIELD_REQUIRED_DEPENDS, 
            "magenta compatible sku" => self::FIELD_REQUIRED_DEPENDS, 
            "magenta compatible man." => self::FIELD_REQUIRED_DEPENDS, 
            "magenta compatible cost" => self::FIELD_REQUIRED_DEPENDS, 
            "magenta compatible yield" => self::FIELD_REQUIRED_DEPENDS, 
            "yellow compatible sku" => self::FIELD_REQUIRED_DEPENDS, 
            "yellow compatible man." => self::FIELD_REQUIRED_DEPENDS, 
            "yellow compatible cost" => self::FIELD_REQUIRED_DEPENDS, 
            "yellow compatible yield" => self::FIELD_REQUIRED_DEPENDS, 
            "3 color compatible sku" => self::FIELD_REQUIRED_DEPENDS, 
            "3 color compatible man." => self::FIELD_REQUIRED_DEPENDS, 
            "3 color compatible cost" => self::FIELD_REQUIRED_DEPENDS, 
            "3 color compatible yield" => self::FIELD_REQUIRED_DEPENDS, 
            "4 color compatible sku" => self::FIELD_REQUIRED_DEPENDS, 
            "4 color compatible man." => self::FIELD_REQUIRED_DEPENDS, 
            "4 color compatible cost" => self::FIELD_REQUIRED_DEPENDS, 
            "4 color compatible yield" => self::FIELD_REQUIRED_DEPENDS, 
            
            "pf oem black prodcode" => self::FIELD_NOTREQUIRED, 
            "pf oem cyan prodcode" => self::FIELD_NOTREQUIRED, 
            "pf oem magenta prodcode" => self::FIELD_NOTREQUIRED, 
            "pf oem yellow prodcode" => self::FIELD_NOTREQUIRED, 
            "pf oem black yield" => self::FIELD_NOTREQUIRED, 
            "pf oem black cost" => self::FIELD_NOTREQUIRED, 
            "pf oem cyan yield" => self::FIELD_NOTREQUIRED, 
            "pf oem cyan cost" => self::FIELD_NOTREQUIRED, 
            "pf oem magenta yield" => self::FIELD_NOTREQUIRED, 
            "pf oem magenta cost" => self::FIELD_NOTREQUIRED, 
            "pf oem yellow yield" => self::FIELD_NOTREQUIRED, 
            "pf oem yellow cost" => self::FIELD_NOTREQUIRED, 
            "wattspowernormal" => self::FIELD_NOTREQUIRED, 
            "wattspowersave" => self::FIELD_NOTREQUIRED, 
            "is_copier" => self::FIELD_REQUIRED, 
            "is_scanner" => self::FIELD_REQUIRED, 
            "is_fax" => self::FIELD_REQUIRED, 
            "dateintroduction" => self::FIELD_REQUIRED, 
            "is_duplex" => self::FIELD_NOTREQUIRED 
    );
    public static $validToners = array (
            Proposalgen_Model_PartType::OEM => array (
                    Proposalgen_Model_TonerColor::BLACK => array (
                            "black oem" 
                    ), 
                    Proposalgen_Model_TonerColor::CYAN => array (
                            "cyan oem" 
                    ), 
                    Proposalgen_Model_TonerColor::MAGENTA => array (
                            "magenta oem" 
                    ), 
                    Proposalgen_Model_TonerColor::YELLOW => array (
                            "yellow oem" 
                    ), 
                    Proposalgen_Model_TonerColor::THREE_COLOR => array (
                            "3 color oem" 
                    ), 
                    Proposalgen_Model_TonerColor::FOUR_COLOR => array (
                            "4 color oem" 
                    ) 
            ), 
            Proposalgen_Model_PartType::COMP => array (
                    Proposalgen_Model_TonerColor::BLACK => array (
                            "black compatible" 
                    ), 
                    Proposalgen_Model_TonerColor::CYAN => array (
                            "cyan compatible" 
                    ), 
                    Proposalgen_Model_TonerColor::MAGENTA => array (
                            "magenta compatible" 
                    ), 
                    Proposalgen_Model_TonerColor::YELLOW => array (
                            "yellow compatible" 
                    ), 
                    Proposalgen_Model_TonerColor::THREE_COLOR => array (
                            "3 color compatible" 
                    ), 
                    Proposalgen_Model_TonerColor::FOUR_COLOR => array (
                            "4 color compatible" 
                    ) 
            ) 
    );
    public static $threeColorSeperatedToners = array (
            Proposalgen_Model_TonerColor::CYAN => "cyan", 
            Proposalgen_Model_TonerColor::MAGENTA => "magenta", 
            Proposalgen_Model_TonerColor::YELLOW => "yellow" 
    );
    public static $validPartTypes = array (
            Proposalgen_Model_PartType::OEM => "oem", 
            Proposalgen_Model_PartType::COMP => "compatible" 
    );
    public static $thresholds = array (
            Proposalgen_Model_PartType::OEM => array (
                    "min_cost" => 10, 
                    "max_cost" => 500, 
                    "min_yield" => 150, 
                    "max_yield" => 100000 
            ), 
            Proposalgen_Model_PartType::COMP => array (
                    "min_cost" => 5, 
                    "max_cost" => 500, 
                    "min_yield" => 150, 
                    "max_yield" => 100000 
            ) 
    );
    public static $skuList = array ();
    public static $modelNameList = array ();

    protected function checkRowForErrors (&$row)
    {
        set_time_limit(10);
        $hasData = false;
        $result = parent::checkRowForErrors($row);
        if ($result === FALSE && $this->getValidateCSV())
        {
            $errorMessage = false;
            $invalidTonerData = true;
            $tonerList = array ();
            // Make sure we have all our required fields
            foreach ( $this->validHeaders as $header => $required )
            {
                // Make sure the key exists
                if (array_key_exists($header, $row))
                {
                    // Set to null if the row is empty
                    if (strtolower(trim($row [$header])) === "null" || strlen($row [$header]) < 1)
                    {
                        $row [$header] = null;
                    }
                }
                else
                {
                    $row [$header] = null;
                }
                
                if (is_null($row [$header]) && $required === self::FIELD_REQUIRED)
                {
                    
                    $errorMessage = "Required column $header was not provided." . var_export($row [$header], true);
                    break;
                }
            }
            
            if (! $errorMessage)
            {
                if (((int)$row ["wattspowernormal"]) <= 0)
                {
                    $row ["wattspowernormal"] = null;
                }
                
                if (((int)$row ["wattspowersave"]) <= 0)
                {
                    $row ["wattspowersave"] = null;
                }
                
                // Validate toner information
                foreach ( self::$validToners as $partType => $tonersByColor )
                {
                    foreach ( $tonersByColor as $tonerColor => $tonerArray )
                    {
                        foreach ( $tonerArray as $toner )
                        {
                            // If any fields are set
                            if (strlen($row ["$toner sku"]) > 1 || strlen($row ["$toner cost"]) > 1 || strlen($row ["$toner yield"]) > 1)
                            {
                                $hasData = true;
                                $row ["$toner yield"] = str_replace(",", "", $row ["$toner yield"]);
                                $row ["$toner cost"] = str_replace(",", "", $row ["$toner cost"]);
                                $sku = strtolower($row ["$toner sku"]);
                                // If all field are set
                                if (strlen($row ["$toner sku"]) > 1 && strlen($row ["$toner cost"]) > 1 && strlen($row ["$toner yield"]) > 1)
                                {
                                    if (array_key_exists($sku, self::$skuList))
                                    {
                                        if (self::$skuList [$sku]->tonerColor !== $tonerColor || self::$skuList [$sku]->sku !== $row ["$toner sku"] || self::$skuList [$sku]->cost !== $row ["$toner cost"] || self::$skuList [$sku]->yield !== $row ["$toner yield"])
                                        
                                        {
                                            $errorMessage = "Duplicate SKU detected but toner data does not match.";
                                        }
                                    }
                                    else
                                    {
                                        $tonerObj = new stdClass();
                                        $tonerObj->tonerColor = $tonerColor;
                                        $tonerObj->sku = $row ["$toner sku"];
                                        $tonerObj->cost = $row ["$toner cost"];
                                        $tonerObj->yield = $row ["$toner yield"];
                                        self::$skuList [$sku] = $tonerObj;
                                    }
                                    
                                    $tonerList [$tonerColor] = true;
                                    $cost = (int)$row ["$toner cost"];
                                    $yield = (int)$row ["$toner yield"];
                                    
                                    $invalidTonerData = true;
                                    if ($cost < self::$thresholds [$partType] ["min_cost"])
                                    {
                                        $errorMessage = "Invalid Data for [$toner]. COST: $" . $cost . " < MIN($" . self::$thresholds [$partType] ["min_cost"] . ")";
                                    }
                                    else if ($cost > self::$thresholds [$partType] ["max_cost"])
                                    {
                                        $errorMessage = "Invalid Data for [$toner]. COST: $" . $cost . " > MAX($" . self::$thresholds [$partType] ["max_cost"] . ")";
                                    }
                                    else if ($yield <= self::$thresholds [$partType] ["min_yield"])
                                    {
                                        $errorMessage = "Invalid Data for [$toner]. YIELD " . $yield . " < MIN(" . self::$thresholds [$partType] ["min_yield"] . ")";
                                    }
                                    else if ($yield >= self::$thresholds [$partType] ["max_yield"])
                                    {
                                        $errorMessage = "Invalid Data for [$toner]. YIELD " . $yield . " > MAX(" . self::$thresholds [$partType] ["max_yield"] . ")";
                                    }
                                    else
                                    {
                                        $invalidTonerData = false;
                                    }
                                    
                                    if ($invalidTonerData)
                                        break;
                                }
                                else
                                {
                                    // Incomplete Data
                                    $invalidBlackTonerData = true;
                                    break;
                                }
                            }
                        }
                        // If we had bad color data, get out of the loop
                        if ($invalidTonerData)
                            break;
                    }
                    // If we had bad color data, get out of the loop
                    if ($invalidTonerData)
                        break;
                }
                
                // If we found a toner with bad data
                if (! $errorMessage)
                {
                    // Assume all our toners are valid, make sure the
                    // configuration is correct
                    $tonerConfig = null;
                    $requiresColorToner = false;
                    
                    if (array_key_exists(Proposalgen_Model_TonerColor::BLACK, $tonerList))
                    {
                        $tonerConfig = Proposalgen_Model_TonerConfig::BLACK_ONLY;
                    }
                    
                    if (stripos($row ['modelname'], "color") !== FALSE || stripos($row ['modelname'], "colour") !== FALSE)
                    {
                        // Device is some kind of color
                        $requiresColorToner = true;
                    }
                    
                    // Check to see if we have any of the three color seperated
                    // toners
                    if (array_key_exists(Proposalgen_Model_TonerColor::CYAN, $tonerList) || array_key_exists(Proposalgen_Model_TonerColor::MAGENTA, $tonerList) || array_key_exists(Proposalgen_Model_TonerColor::YELLOW, $tonerList))
                    {
                        // If we are missing any color, or black, it's an error
                        if ($tonerConfig !== Proposalgen_Model_TonerConfig::BLACK_ONLY)
                        {
                            $errorMessage = "Missing Black Toner";
                        }
                        else if (! array_key_exists(Proposalgen_Model_TonerColor::CYAN, $tonerList))
                        {
                            $errorMessage = "Missing Cyan Toner";
                        }
                        
                        else if (! array_key_exists(Proposalgen_Model_TonerColor::MAGENTA, $tonerList))
                        {
                            $errorMessage = "Missing Magenta Toner";
                        }
                        else if (! array_key_exists(Proposalgen_Model_TonerColor::YELLOW, $tonerList))
                        {
                            $errorMessage = "Missing Yellow Toner";
                        }
                        else
                        {
                            $tonerConfig = Proposalgen_Model_TonerConfig::THREE_COLOR_SEPARATED;
                        }
                    }
                    
                    // Check to see if we have a 3 color toner
                    if (array_key_exists(Proposalgen_Model_TonerColor::THREE_COLOR, $tonerList))
                    {
                        if ($tonerConfig == Proposalgen_Model_TonerColor::BLACK)
                        {
                            $tonerConfig = Proposalgen_Model_TonerConfig::THREE_COLOR_COMBINED;
                        }
                        else
                        {
                            $errorMessage = "Missing black toner for Three Color Toner Config";
                        }
                    }
                    
                    // Check to see if we have a 4 color toner
                    if (array_key_exists(Proposalgen_Model_TonerColor::FOUR_COLOR, $tonerList))
                    {
                        if (is_null($tonerConfig))
                        {
                            $tonerConfig = Proposalgen_Model_TonerConfig::FOUR_COLOR_COMBINED;
                        }
                        else
                        {
                            $errorMessage = "Other types of toners were found with a Four Color Toner";
                        }
                    }
                    
                    if (is_null($tonerConfig))
                    {
                        if (! $errorMessage)
                            $errorMessage = "No valid toners";
                    }
                    else if ($tonerConfig === Proposalgen_Model_TonerConfig::BLACK_ONLY && $requiresColorToner)
                    {
                        if (! $errorMessage)
                            $errorMessage = "Printer should have color toners but is missing one or more color toners";
                    }
                    else
                    {
                        $row ["tonerConfig"] = $tonerConfig;
                    }
                }
            }
            
            $deviceAlreadyExists = false;
            $lowercaseDeviceName = strtolower($row ["modelname"]);
            $lowercaseMFG = strtolower($row ["modelmfg"]);
            
            if (array_key_exists($lowercaseMFG, self::$modelNameList))
            {
                for($i = 0; $i < count(self::$modelNameList [$lowercaseMFG]); $i ++)
                {
                    if (0 === strcmp(self::$modelNameList [$lowercaseMFG] [$i], $lowercaseDeviceName))
                    {
                        $deviceAlreadyExists = true;
                        break;
                    }
                }
            }
            
            // Error if we already have one in the csv
            if ($deviceAlreadyExists)
            {
                $errorMessage = "Device is a duplicate of an existing device.";
            }
            else
            {
                self::$modelNameList [$lowercaseMFG] [] = $lowercaseDeviceName;
            }
            
            if ($errorMessage)
            {
                $result = $errorMessage;
                if ($hasData)
                {
                    $this->test ["errors"] [] = $errorMessage;
                    $this->test ["rows"] [] = $row;
                }
            }
        }
        return $result;
    }
}