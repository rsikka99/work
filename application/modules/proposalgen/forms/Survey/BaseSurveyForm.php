<?php

/**
 * This base survey form keeps validators, formatters and data that are global to all the survey forms.
 */
class Proposalgen_Form_Survey_BaseSurveyForm extends EasyBib_Form
{
    protected $currency;
    protected $currencyRegex;

    public function __construct ($options = null)
    {
        parent::__construct($options);
        
        $this->currency = new Zend_Currency();
        $this->currencyRegex = '/^\d+(?:\.\d{0,2})?$/';
        
        $this->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
    }

    public function init ()
    {
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Save' 
        ));
        
        // Add the cancel button
        $this->addElement('submit', 'cancel', array (
                'ignore' => true, 
                'label' => 'Cancel' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}