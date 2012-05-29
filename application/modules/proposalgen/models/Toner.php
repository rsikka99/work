<?php

/**
 * Class Proposalgen_Model_Toner
 * @author "Lee Robert"
 */
class Proposalgen_Model_Toner extends Tangent_Model_Abstract
{
    const EMILY_RANDOM_NUMBER = 0.05;
    
    static $ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE;
    static $ACTUAL_PAGE_COVERAGE_COLOR;
    static $ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE;
    static $ESTIMATED_PAGE_COVERAGE_COLOR;
    
    static $DefaultToners;
    
    protected $TonerId;
    protected $TonerSKU;
    protected $TonerPrice;
    protected $TonerYield;
    protected $PartTypeId;
    protected $ManufacturerId;
    protected $TonerColorId;
    
    // Related Objects
    protected $PartType;
    protected $Manufacturer;
    protected $TonerColor;
    
    // Calculated Fields
    protected $CostPerPage;

    /**
     * @return the $DefaultToners
     */
    public static function getDefaultToners ()
    {
        if (! isset(Proposalgen_Model_Toner::$DefaultToners))
        {
            $testarray = array ();
            $tonerOverrides ["BW"] = array (
                    "Cost" => 0,
                    "Yield" => 0
            );
            $tonerOverrides ["Color"] = array (
                    "Cost" => 0,
                    "Yield" => 0
            );
            $tonerOverrides ["ThreeColor"] = array (
                    "Cost" => 0,
                    "Yield" => 0
            );
            $tonerOverrides ["FourColor"] = array (
                    "Cost" => 0,
                    "Yield" => 0
            );
            
            $overrideLocation ["User"] = Proposalgen_Model_User::getCurrentUser();
            $overrideLocation ["Dc"] = Proposalgen_Model_DealerCompany::getCurrentUserCompany();
            $overrideLocation ["Dc"] = Proposalgen_Model_DealerCompany::getMasterCompany();
            
            foreach ( $tonerOverrides as $type => $override )
            {
                // This is a fancy way of looping through the objects dynamically
                // Written by Lee Robert
                

                // For the cost
                foreach ( $overrideLocation as $FuncPrefix => $object )
                {
                    $functionCall = "get" . $FuncPrefix . "Default" . $type . "TonerCost";
                    if ($object->$functionCall())
                    {
                        $override ["Cost"] = $object->$functionCall();
                        break;
                    }
                }
                
                if ($override ["Cost"] <= 0)
                {
                    throw new Exception("Cost of " . $override ["Cost"] . " detected for " . $type . " toner. Cost must be greater than zero.");
                }
                
                // For the yield
                foreach ( $overrideLocation as $FuncPrefix => $object )
                {
                    $functionCall = "get" . $FuncPrefix . "Default" . $type . "TonerYield";
                    if ($object->$functionCall())
                    {
                        $override ["Yield"] = $object->$functionCall();
                        break;
                    }
                }
                
                if ($override ["Yield"] <= 0)
                {
                    throw new Exception("Yield of " . $override ["Yield"] . " detected for " . $type . " toner. Yield must be greater than zero.");
                }
                $tonerOverrides [$type] = $override;
            }
            
            $blackToner = new Proposalgen_Model_Toner();
            $blackToner->setTonerPrice($tonerOverrides ["BW"] ["Cost"]);
            $blackToner->setTonerYield($tonerOverrides ["BW"] ["Yield"]);
            $blackToner->setTonerColorId(Proposalgen_Model_TonerColor::BLACK);
            
            $cyanToner = new Proposalgen_Model_Toner();
            $cyanToner->setTonerPrice($tonerOverrides ["Color"] ["Cost"]);
            $cyanToner->setTonerYield($tonerOverrides ["Color"] ["Yield"]);
            $cyanToner->setTonerColorId(Proposalgen_Model_TonerColor::CYAN);
            
            $magentaToner = new Proposalgen_Model_Toner();
            $magentaToner->setTonerPrice($tonerOverrides ["Color"] ["Cost"]);
            $magentaToner->setTonerYield($tonerOverrides ["Color"] ["Yield"]);
            $magentaToner->setTonerColorId(Proposalgen_Model_TonerColor::MAGENTA);
            
            $yellowToner = new Proposalgen_Model_Toner();
            $yellowToner->setTonerPrice($tonerOverrides ["Color"] ["Cost"]);
            $yellowToner->setTonerYield($tonerOverrides ["Color"] ["Yield"]);
            $yellowToner->setTonerColorId(Proposalgen_Model_TonerColor::YELLOW);
            
            $threeColorToner = new Proposalgen_Model_Toner();
            $threeColorToner->setTonerPrice($tonerOverrides ["ThreeColor"] ["Cost"]);
            $threeColorToner->setTonerYield($tonerOverrides ["ThreeColor"] ["Yield"]);
            $threeColorToner->setTonerColorId(Proposalgen_Model_TonerColor::THREE_COLOR);
            
            $fourColorToner = new Proposalgen_Model_Toner();
            $fourColorToner->setTonerPrice($tonerOverrides ["FourColor"] ["Cost"]);
            $fourColorToner->setTonerYield($tonerOverrides ["FourColor"] ["Yield"]);
            $fourColorToner->setTonerColorId(Proposalgen_Model_TonerColor::FOUR_COLOR);
            
            $defaultToners = array ();
            $defaultToners [Proposalgen_Model_TonerColor::BLACK] = $blackToner;
            $defaultToners [Proposalgen_Model_TonerColor::CYAN] = $cyanToner;
            $defaultToners [Proposalgen_Model_TonerColor::MAGENTA] = $magentaToner;
            $defaultToners [Proposalgen_Model_TonerColor::YELLOW] = $yellowToner;
            $defaultToners [Proposalgen_Model_TonerColor::THREE_COLOR] = $threeColorToner;
            $defaultToners [Proposalgen_Model_TonerColor::FOUR_COLOR] = $fourColorToner;
            Proposalgen_Model_Toner::$DefaultToners = $defaultToners;
        }
        return Proposalgen_Model_Toner::$DefaultToners;
    }

    /**
     * @param field_type $DefaultToners
     */
    public static function setDefaultToners ($DefaultToners)
    {
        Proposalgen_Model_Toner::$DefaultToners = $DefaultToners;
        return $this;
    }

    /**
     * @return the $CostPerPage
     */
    public function getCostPerPage ()
    {
        if (! isset($this->CostPerPage))
        {
            $actualBlackCoverage = 0;
            $actualColorCoverage = 0;
            $estimatedBlackCoverage = 0;
            $estimatedColorCoverage = 0;
            switch ($this->getTonerColor()->getTonerColorId())
            {
                case Proposalgen_Model_TonerColor::BLACK :
                    $actualBlackCoverage = self::getACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE();
                    $estimatedBlackCoverage = self::getESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE();
                    break;
                case Proposalgen_Model_TonerColor::CYAN :
                case Proposalgen_Model_TonerColor::MAGENTA :
                case Proposalgen_Model_TonerColor::YELLOW :
                    $actualColorCoverage = self::getACTUAL_PAGE_COVERAGE_COLOR() / 4;
                    $estimatedColorCoverage = self::getESTIMATED_PAGE_COVERAGE_COLOR() / 4;
                    break;
                case Proposalgen_Model_TonerColor::THREE_COLOR :
                    $actualColorCoverage = (self::getACTUAL_PAGE_COVERAGE_COLOR() / 4) * 3;
                    $estimatedColorCoverage = (self::getESTIMATED_PAGE_COVERAGE_COLOR() / 4) * 3;
                    break;
                case Proposalgen_Model_TonerColor::FOUR_COLOR :
                    $actualBlackCoverage = self::getACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE();
                    $estimatedBlackCoverage = self::getESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE();
                    
                    $actualColorCoverage = self::getACTUAL_PAGE_COVERAGE_COLOR();
                    $estimatedColorCoverage = self::getESTIMATED_PAGE_COVERAGE_COLOR();
                    break;
            }
            
            // tonerCost / (tonerYield * (0.05 / coverage)
            // 0.05 is EMILY_RANDOM_NUMBER
            $costPerPage = new stdClass();
            $costPerPage->Actual = new stdClass();
            $costPerPage->Actual->BlackAndWhite = 0;
            $costPerPage->Actual->Color = 0;
            $costPerPage->Raw = new stdClass();
            $costPerPage->Raw->BlackAndWhite = 0;
            $costPerPage->Raw->Color = 0;
            $costPerPage->Estimated = new stdClass();
            $costPerPage->Estimated->BlackAndWhite = 0;
            $costPerPage->Estimated->Color = 0;
            // We must have a toner yield
            if ($this->getTonerYield())
            {
                // Check to make sure we have a black coverage
                if ($actualBlackCoverage && $estimatedBlackCoverage)
                {
                    $costPerPage->Actual->BlackAndWhite = $this->getTonerPrice() / ($this->getTonerYield() * (self::EMILY_RANDOM_NUMBER / $actualBlackCoverage));
                    $costPerPage->Raw->BlackAndWhite = $this->getTonerPrice() / $this->getTonerYield();
                    $costPerPage->Estimated->BlackAndWhite = $this->getTonerPrice() / ($this->getTonerYield() * (self::EMILY_RANDOM_NUMBER / $estimatedBlackCoverage));
                }
                // Check to make sure we have a color coverage
                if ($actualColorCoverage && $estimatedColorCoverage)
                {
                    $costPerPage->Actual->Color = $this->getTonerPrice() / ($this->getTonerYield() * (self::EMILY_RANDOM_NUMBER / $actualColorCoverage));
                    $costPerPage->Raw->Color = $this->getTonerPrice() / $this->getTonerYield();
                    $costPerPage->Estimated->Color = $this->getTonerPrice() / ($this->getTonerYield() * (self::EMILY_RANDOM_NUMBER / $estimatedColorCoverage));
                }
            }
            else
            {
                throw new Exception("Toner has a yield of zero! ID:" . $this->TonerYield);
            }
            
            // This is a ranking to be able to compare a toner with other toners of the same kind
            $costPerPage->Rank = $costPerPage->Estimated->BlackAndWhite + $costPerPage->Estimated->Color;
            
            $this->CostPerPage = $costPerPage;
        }
        return $this->CostPerPage;
    }

    /**
     * @param field_type $CostPerPage
     */
    public function setCostPerPage ($CostPerPage)
    {
        $this->CostPerPage = $CostPerPage;
        return $this;
    }

    /**
     * @return the $TonerId
     */
    public function getTonerId ()
    {
        if (! isset($this->TonerId))
        {
            
            $this->TonerId = null;
        }
        return $this->TonerId;
    }

    /**
     * @param field_type $TonerId
     */
    public function setTonerId ($TonerId)
    {
        $this->TonerId = $TonerId;
        return $this;
    }

    /**
     * @return the $TonerSKU
     */
    public function getTonerSKU ()
    {
        if (! isset($this->TonerSKU))
        {
            $this->TonerSKU = "DefaultTonerSKU";
        }
        return $this->TonerSKU;
    }

    /**
     * @param field_type $TonerSKU
     */
    public function setTonerSKU ($TonerSKU)
    {
        $this->TonerSKU = $TonerSKU;
        return $this;
    }

    /**
     * @return the $TonerPrice
     */
    public function getTonerPrice ()
    {
        if (! isset($this->TonerPrice))
        {
            
            $this->TonerPrice = null;
        }
        return $this->TonerPrice;
    }

    /**
     * @param field_type $TonerPrice
     */
    public function setTonerPrice ($TonerPrice)
    {
        $this->TonerPrice = $TonerPrice;
        return $this;
    }

    /**
     * @return the $TonerYield
     */
    public function getTonerYield ()
    {
        if (! isset($this->TonerYield))
        {
            
            $this->TonerYield = null;
        }
        return $this->TonerYield;
    }

    /**
     * @param field_type $TonerYield
     */
    public function setTonerYield ($TonerYield)
    {
        $this->TonerYield = $TonerYield;
        return $this;
    }

    /**
     * @return the $PartTypeId
     */
    public function getPartTypeId ()
    {
        if (! isset($this->PartTypeId))
        {
            $this->PartTypeId = Proposalgen_Model_PartType::OEM;
        }
        return $this->PartTypeId;
    }

    /**
     * @param field_type $PartTypeId
     */
    public function setPartTypeId ($PartTypeId)
    {
        $this->PartTypeId = $PartTypeId;
        return $this;
    }

    /**
     * @return the $ManufacturerId
     */
    public function getManufacturerId ()
    {
        if (! isset($this->ManufacturerId))
        {
            
            $this->ManufacturerId = null;
        }
        return $this->ManufacturerId;
    }

    /**
     * @param field_type $ManufacturerId
     */
    public function setManufacturerId ($ManufacturerId)
    {
        $this->ManufacturerId = $ManufacturerId;
        return $this;
    }

    /**
     * @return the $TonerColorId
     */
    public function getTonerColorId ()
    {
        if (! isset($this->TonerColorId))
        {
            
            $this->TonerColorId = null;
        }
        return $this->TonerColorId;
    }

    /**
     * @param field_type $TonerColorId
     */
    public function setTonerColorId ($TonerColorId)
    {
        $this->TonerColorId = $TonerColorId;
        return $this;
    }

    /**
     * @return the $PartType
     */
    public function getPartType ()
    {
        if (! isset($this->PartType))
        {
            $partTypeMapper = Proposalgen_Model_Mapper_PartType::getInstance();
            $this->PartType = $partTypeMapper->find($this->getPartTypeId());
        }
        return $this->PartType;
    }

    /**
     * @param field_type $PartType
     */
    public function setPartType ($PartType)
    {
        $this->PartType = $PartType;
        return $this;
    }

    /**
     * @return the $Manufacturer
     */
    public function getManufacturer ()
    {
        if (! isset($this->Manufacturer))
        {
            $this->Manufacturer = Proposalgen_Model_Mapper_Manufacturer::getInstance()->find($this->getManufacturerId());
        }
        return $this->Manufacturer;
    }

    /**
     * @param field_type $Manufacturer
     */
    public function setManufacturer ($Manufacturer)
    {
        $this->Manufacturer = $Manufacturer;
        return $this;
    }

    /**
     * @return the $TonerColor
     */
    public function getTonerColor ()
    {
        if (! isset($this->TonerColor))
        {
            $tonerColorMapper = Proposalgen_Model_Mapper_TonerColor::getInstance();
            $this->TonerColor = $tonerColorMapper->find($this->getTonerColorId());
        }
        return $this->TonerColor;
    }

    /**
     * @param field_type $TonerColor
     */
    public function setTonerColor ($TonerColor)
    {
        $this->TonerColor = $TonerColor;
        return $this;
    }

    /**
     * @return the $ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE
     */
    public static function getACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE ()
    {
        if (! isset(Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE))
        {
            throw new Exception("Actual Black And White Page Coverage not set!!");
        }
        return Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE;
    }

    /**
     * @param field_type $ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE
     */
    public static function setACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE ($ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE)
    {
        Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE = $ACTUAL_PAGE_COVERAGE_BLACK_AND_WHITE;
    }

    /**
     * @return the $ACTUAL_PAGE_COVERAGE_COLOR
     */
    public static function getACTUAL_PAGE_COVERAGE_COLOR ()
    {
        if (! isset(Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_COLOR))
        {
            throw new Exception("Actual Color Page Coverage not set!!");
        }
        return Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_COLOR;
    }

    /**
     * @param field_type $ACTUAL_PAGE_COVERAGE_COLOR
     */
    public static function setACTUAL_PAGE_COVERAGE_COLOR ($ACTUAL_PAGE_COVERAGE_COLOR)
    {
        Proposalgen_Model_Toner::$ACTUAL_PAGE_COVERAGE_COLOR = $ACTUAL_PAGE_COVERAGE_COLOR;
    }

    /**
     * @return the $ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE
     */
    public static function getESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE ()
    {
        if (! isset(Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE))
        {
            throw new Exception("Estimated Black And White Page Coverage not set!!");
        }
        return Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE;
    }

    /**
     * @param field_type $ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE
     */
    public static function setESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE ($ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE)
    {
        Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE = $ESTIMATED_PAGE_COVERAGE_BLACK_AND_WHITE;
    }

    /**
     * @return the $ESTIMATED_PAGE_COVERAGE_COLOR
     */
    public static function getESTIMATED_PAGE_COVERAGE_COLOR ()
    {
        if (! isset(Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_COLOR))
        {
            throw new Exception("Estimated Color Page Coverage not set!!");
        }
        return Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_COLOR;
    }

    /**
     * @param field_type $ESTIMATED_PAGE_COVERAGE_COLOR
     */
    public static function setESTIMATED_PAGE_COVERAGE_COLOR ($ESTIMATED_PAGE_COVERAGE_COLOR)
    {
        Proposalgen_Model_Toner::$ESTIMATED_PAGE_COVERAGE_COLOR = $ESTIMATED_PAGE_COVERAGE_COLOR;
    }

}