<?php
class Proposalgen_Model_Hardware_Optimization extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $clientId;

    /**
     * @var int
     */
    public $rmsUploadId;

    /**
     * @var int
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

        if (isset($params->clientId) && !is_null($params->clientId))
        {
            $this->clientId = $params->clientId;
        }

        if (isset($params->rmsUploadId) && !is_null($params->rmsUploadId))
        {
            $this->rmsUploadId = $params->rmsUploadId;
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
            "id"          => $this->id,
            "clientId"    => $this->clientId,
            "rmsUploadId" => $this->rmsUploadId,
            "name"        => $this->name,
        );
    }
}