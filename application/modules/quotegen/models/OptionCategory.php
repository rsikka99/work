<?php
class Quotegen_Model_OptionCategory extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $categoryId = 0;

    /**
     * @var int
     */
    public $optionId = 0;


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

        if (isset($params->optionId) && !is_null($params->optionId))
        {
            $this->optionId = $params->optionId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "categoryId" => $this->categoryId,
            "optionId"   => $this->optionId,
        );
    }
}