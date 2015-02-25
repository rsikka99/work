<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use My_Model_Abstract;

/**
 * Class LeasedQuoteModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class LeasedQuoteModel extends My_Model_Abstract
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
        return [
            "quoteId" => $this->quoteId,
            "rate"    => $this->rate,
            "term"    => $this->term,
        ];
    }
}
