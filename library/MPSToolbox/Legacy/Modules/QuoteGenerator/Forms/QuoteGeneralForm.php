<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel;
use My_Validate_DateTime;
use Zend_Form;

/**
 * Class QuoteGeneralForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuoteGeneralForm extends Zend_Form
{
    /**
     * This represents the current quote being worked on
     *
     * @var QuoteModel
     */
    protected $_quote;

    /**
     * @param QuoteModel $quote
     * @param null|array $options
     */
    public function __construct (QuoteModel $quote, $options = null)
    {
        $this->_quote = $quote;

        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $minYear   = (int)date('Y') - 2;
        $maxYear   = $minYear + 4;

        $this->addElement('text', 'quoteDate', [
            'label'      => 'Quote Date',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [new My_Validate_DateTime()],
            'required'   => false,
        ]);

        $this->addElement('text', 'name', [
            'label'   => 'Quote Name',
            'value'   => $this->getQuote()->name,
            'filters' => ['StringTrim', 'StripTags'],
        ]);

        $this->addElement('submit', 'submit', [
            'label'     => 'Update',
            'icon'      => 'check',
            'whiteIcon' => true
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/quotegen/quote/quote-general-form.phtml']]]);
    }

    /**
     * Gets the quote
     *
     * @return QuoteModel
     */
    public function getQuote ()
    {
        return $this->_quote;
    }
}