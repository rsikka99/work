<?php

namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class RmsProviderModel
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Models
 */
class RmsProviderModel extends My_Model_Abstract
{
    const RMS_PROVIDER_PRINTFLEET_TWO   = 1;
    const RMS_PROVIDER_FMAUDIT          = 2;
    const RMS_PROVIDER_XEROX            = 3;
    const RMS_PROVIDER_PRINT_AUDIT      = 4;
    const RMS_PROVIDER_NER_DATA         = 5;
    const RMS_PROVIDER_PRINTFLEET_THREE = 6;
    const RMS_PROVIDER_PRINT_TRACKER    = 7;
    const RMS_PROVIDER_LEXMARK          = 8;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;


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

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "id"   => $this->id,
            "name" => $this->name,
        );
    }
}