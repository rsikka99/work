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
        $this->addPrefixPath('My_Form_Element', 'My/Form/Element', 'element');

        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $minYear   = (int)date('Y') - 2;
        $maxYear   = $minYear + 4;
        $quoteDate = $this->createElement('DateTimePicker', 'quoteDate', array(
            'label'    => 'Quote Date',
            'filters'  => array('StringTrim', 'StripTags'),
            'required' => false
        ));
        $quoteDate->setJQueryParam('dateFormat', 'yy-mm-dd')
                  ->setJqueryParam('timeFormat', 'hh:mm')
                  ->setJQueryParam('changeYear', 'true')
                  ->setJqueryParam('changeMonth', 'true')
                  ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}")
                  ->addValidator(new My_Validate_DateTime());
        $this->addElement($quoteDate);

        $this->addElement('text', 'name', array(
            'label'   => 'Quote Name',
            'value'   => $this->getQuote()->name,
            'filters' => array('StringTrim', 'StripTags'),
        ));

        $this->addElement('submit', 'submit', array(
            'label'     => 'Update',
            'icon'      => 'check',
            'whiteIcon' => true
        ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/quotegen/quote/quote-general-form.phtml'
                )
            )
        ));
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