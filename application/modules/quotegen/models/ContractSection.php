<?php

/**
 * Class Quotegen_Model_ContractSection
 */
class Quotegen_Model_ContractSection extends My_Model_Abstract
{
    const SECTION_CUSTOMER_YOU_YOUR               = 1;
    const SECTION_VENDOR                          = 2;
    const SECTION_CONTRACT                        = 3;
    const SECTION_CUSTOMERS_AUTH_SIGNATURE        = 4;
    const SECTION_OWNER_WE_US_OUR                 = 5;
    const SECTION_UNCONDITIONAL_GUARANTY          = 6;
    const SECTION_MPS_CONTRACT_DETAILS            = 7;
    const SECTION_HARDWARE_CONTRACT_DETAILS       = 8;
    const SECTION_EQUIPMENT_LIST                  = 9;
    const SECTION_ADDITIONAL_TERMS_AND_CONDITIONS = 10;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $sectionDefaultName;

    /**
     * @var string
     */
    public $sectionDefaultText;


    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->id) && !is_null($params->id))
        {
            $this->id = $params->id;
        }

        if (isset($params->sectionDefaultName) && !is_null($params->sectionDefaultName))
        {
            $this->sectionDefaultName = $params->sectionDefaultName;
        }

        if (isset($params->sectionDefaultText) && !is_null($params->sectionDefaultText))
        {
            $this->sectionDefaultText = $params->sectionDefaultText;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"                 => $this->id,
            "sectionDefaultName" => $this->sectionDefaultName,
            "sectionDefaultText" => $this->sectionDefaultText,
        );
    }

}