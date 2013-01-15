<?php
class Proposalgen_Model_TicketCategory extends My_Model_Abstract
{
    const PRINTFLEET_DEVICE_SUPPORT = 1;

    /**
     * @var int
     */
    public $categoryId;

    /**
     * @var string
     */
    public $categoryName;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->categoryId) && !is_null($params->categoryId))
        {
            $this->categoryId = $params->categoryId;
        }

        if (isset($params->categoryName) && !is_null($params->categoryName))
        {
            $this->categoryName = $params->categoryName;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "categoryId"   => $this->categoryId,
            "categoryName" => $this->categoryName,
        );
    }
}