<?php
namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Models;

use ArrayObject;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\LeasingSchemaTermMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteMapper;
use My_Model_Abstract;

/**
 * Class QuoteLeaseTermModel
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Models
 */
class QuoteLeaseTermModel extends My_Model_Abstract
{
    /**
     * @var int
     */
    public $quoteId;

    /**
     * @var int
     */
    public $leasingSchemaTermId;

    /**
     * The quote
     *
     * @var QuoteModel
     */
    protected $_quote;

    /**
     * The leasing term object
     *
     * @var LeasingSchemaTermModel
     */
    protected $_leaseTerm;

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

        if (isset($params->leasingSchemaTermId) && !is_null($params->leasingSchemaTermId))
        {
            $this->leasingSchemaTermId = $params->leasingSchemaTermId;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "quoteId"             => $this->quoteId,
            "leasingSchemaTermId" => $this->leasingSchemaTermId,
        );
    }

    /**
     * Gets the quote object
     *
     * @return QuoteModel
     */
    public function getQuote ()
    {
        if (!isset($this->_quote))
        {
            $this->_quote = QuoteMapper::getInstance()->find($this->quoteId);
        }

        return $this->_quote;
    }

    /**
     * Sets the quote object
     *
     * @param QuoteModel $_quote
     *
     * @return $this
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;

        return $this;
    }

    /**
     * Gets the lease term object
     *
     * @return LeasingSchemaTermModel
     */
    public function getLeaseTerm ()
    {
        if (!isset($this->_quote))
        {
            $this->_leaseTerm = LeasingSchemaTermMapper::getInstance()->find($this->leasingSchemaTermId);
        }

        return $this->_leaseTerm;
    }

    /**
     * Sets the lease term object
     *
     * @param LeasingSchemaTermModel $_leaseTerm
     *
     * @return $this
     */
    public function setLeaseTerm ($_leaseTerm)
    {
        $this->_leaseTerm = $_leaseTerm;

        return $this;
    }
}