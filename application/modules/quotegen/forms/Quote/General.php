<?php

/**
 * Class Quotegen_Form_Quote_General
 */
class Quotegen_Form_Quote_General extends Twitter_Bootstrap_Form_Vertical
{

    /**
     * This represents the current quote being worked on
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;

    /**
     * @param Quotegen_Model_Quote $quote
     * @param null|array           $options
     */
    public function __construct (Quotegen_Model_Quote $quote, $options = null)
    {
        $this->_quote = $quote;
        $this->addPrefixPath('My_Form_Element', 'My/Form/Element', 'element');

        parent::__construct($options);
        //         Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_NEXT, $this);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->_addClassNames('form-center-actions');

        $minYear   = (int)date('Y') - 2;
        $maxYear   = $minYear + 4;
        $quoteDate = $this->createElement('DateTimePicker', 'quoteDate');
        $quoteDate->setLabel('Quote Date:')
                  ->setJQueryParam('dateFormat', 'yy-mm-dd')
                  ->setJqueryParam('timeFormat', 'hh:mm')
                  ->setJQueryParam('changeYear', 'true')
                  ->setJqueryParam('changeMonth', 'true')
                  ->setJqueryParam('yearRange', "{$minYear}:{$maxYear}")
                  ->addValidator(new My_Validate_DateTime())
                  ->setRequired(false);
        $quoteDate->addFilters(array(
            'StringTrim',
            'StripTags'
        ));
        $this->addElement($quoteDate);

        $quoteName = $this->createElement('text', 'name');
        $quoteName->setLabel('Quote Name')->setValue($this->getQuote()->name);
        $quoteName->addFilters(array(
            'StringTrim',
            'StripTags'
        ));
        $this->addElement($quoteName);

        $this->addElement('button', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_SUCCESS,
            'label'      => 'Update',
            'type'       => 'submit',
            'icon'       => 'check',
            'whiteIcon'  => true
        ));
    }

    /**
     * Gets the quote
     *
     * @return Quotegen_Model_Quote
     */
    public function getQuote ()
    {
        return $this->_quote;
    }
}