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
        $this->currency = new Zend_Currency();
        $this->currencyRegex = '/^\d+(?:\.\d{0,2})?$/';
        
        // This runs, among other things, the init functions. Therefore it must come before anything that affects the form.
        parent::__construct($options);
        
        $this->addPrefixPath('Tangent_Form_Element', 'Tangent/Form/Element/', 'element');
    }

    public function init ()
    {
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP);
    }
}