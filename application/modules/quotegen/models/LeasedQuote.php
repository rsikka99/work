<?php
/**
 * Class Quotegen_Model_LeasedQuote
 */
class Quotegen_Model_LeasedQuote extends My_Model_Abstract
{

    /**
     * @var int
     */
    public $quoteId = 0;

    /**
     * @var int
     */
    public $rate;

    /**
     * @var int
     */
    public $term;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->quoteId) && !is_null($params->quoteId))
        {
            $this->quoteId = $params->quoteId;
        }

        if (isset($params->rate) && !is_null($params->rate))
        {
            $this->rate = $params->rate;
        }

        if (isset($params->term) && !is_null($params->term))
        {
            $this->term = $params->term;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "quoteId" => $this->quoteId,
            "rate"    => $this->rate,
            "term"    => $this->term,
        );
    }
}
