<?php
/**
 * Class Proposalgen_Model_Rms_Provider
 */
class Proposalgen_Model_Rms_Provider extends My_Model_Abstract
{
    const RMS_PROVIDER_PRINTFLEET  = 1;
    const RMS_PROVIDER_FMAUDIT     = 2;
    const RMS_PROVIDER_XEROX       = 3;
    const RMS_PROVIDER_PRINT_AUDIT = 4;

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