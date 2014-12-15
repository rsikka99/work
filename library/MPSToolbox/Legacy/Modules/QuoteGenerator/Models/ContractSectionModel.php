<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class ContractSectionModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class ContractSectionModel extends My_Model_Abstract
{
    /*
     * Great America contract sections
     */
    const SECTION_GA_CUSTOMER_YOU_YOUR               = 1;
    const SECTION_GA_VENDOR                          = 2;
    const SECTION_GA_CONTRACT                        = 3;
    const SECTION_GA_CUSTOMERS_AUTH_SIGNATURE        = 4;
    const SECTION_GA_OWNER_WE_US_OUR                 = 5;
    const SECTION_GA_UNCONDITIONAL_GUARANTY          = 6;
    const SECTION_GA_MPS_CONTRACT_DETAILS            = 7;
    const SECTION_GA_HARDWARE_CONTRACT_DETAILS       = 8;
    const SECTION_GA_EQUIPMENT_LIST                  = 9;
    const SECTION_GA_ADDITIONAL_TERMS_AND_CONDITIONS = 10;

    /*
     * De Lage Landen contract sections
     */
    const SECTION_DLL_LESSEE_INFO          = 11;
    const SECTION_DLL_PAYMENT_INFO         = 12;
    const SECTION_DLL_TERMS_AND_CONDITIONS = 13;
    const SECTION_DLL_LESSEE_SIGNATURE     = 14;
    const SECTION_DLL_LESSOR_SIGNATURE     = 15;
    const SECTION_DLL_ACCEPTANCE           = 16;
    const SECTION_DLL_GUARANTY             = 17;
    const SECTION_DLL_SCHEDULE             = 18;

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