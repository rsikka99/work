<?php
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\QuoteMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel;

/**
 * Quotegen_View_Helper_Quote
 *
 * @author Lee Robert
 *
 */
class Quotegen_View_Helper_Quote extends My_View_Helper_Abstract
{
    protected $_quote;

    /**
     * Gets a quote model if the url parameter was set.
     *
     * @return QuoteModel The quote model
     */
    public function Quote ()
    {
        if (!isset($this->_quote))
        {
            $quoteId = $this->getRequestVariable('quoteId');
            if ($quoteId)
            {
                $this->_quote = QuoteMapper::getInstance()->find($quoteId);
            }
        }

        return $this->_quote;
    }
}
