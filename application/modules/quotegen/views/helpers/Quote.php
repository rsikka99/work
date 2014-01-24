<?php

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
     * @return Quotegen_Model_Quote The quote model
     */
    public function Quote ()
    {
        if (!isset($this->_quote))
        {
            $quoteId = $this->getRequestVariable('quoteId');
            if ($quoteId)
            {
                $this->_quote = Quotegen_Model_Mapper_Quote::getInstance()->find($quoteId);
            }
        }

        return $this->_quote;
    }
}
