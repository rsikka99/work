<?php

class Proposalgen_Form_Survey_It extends Proposalgen_Form_Survey_BaseSurveyForm
{

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        /**
         * Add class to form for label alignment
         *
         * - Vertical .form-vertical (not required)	Stacked, left-aligned labels
         * over controls (default)
         * - Inline .form-inline Left-aligned label and inline-block controls
         * for compact style
         * - Search .form-search Extra-rounded text input for a typical search
         * aesthetic
         * - Horizontal .form-horizontal
         *
         * Use .form-horizontal to have same experience as with Bootstrap v1!
         */
        $this->setAttrib('class', 'surveyForm form-vertical');
        $this->setAttrib('id', 'itSurveyForm');
        
        $multiOptions = array (
                'guess' => 'I don\'t know', 
                'I know the exact amount' => 'I know the exact amount' 
        );
        
        $itHoursRadio = new Zend_Form_Element_Radio('itHoursRadio');
        $itHoursRadio->addMultiOptions($multiOptions);
        
        $this->addElement($itHoursRadio);
        $this->getElement('itHoursRadio')->setValue('I know the exact amount');
        
        $itHours = new Zend_Form_Element_Text('itHours');
        $itHours->setAttrib('maxlength', 3)
            ->setAttrib('class', 'span1')
            ->setDescription('hours')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('itHoursRadio', 'I know the exact amount', array (
                new Zend_Validate_NotEmpty(), 
                new Zend_Validate_Float() 
        )), true);
        $itHoursQst = "How many hours per week do IT personnel spend servicing and supporting printers? If you select \"I don't know\", an average of 15 minutes per week per printer will be used.";
        $itHours->setLabel($itHoursQst);
        $this->addElement($itHours);
        
        $monthlyBreakdown = new Zend_Form_Element_Text('monthlyBreakdown');
        $monthlyBreakdown->setAttrib('maxlength', 3)
            ->setAttrib('class', 'span1')
            ->setDescription('breakdowns per month')
            ->setAllowEmpty(false)
            ->addValidator(new Custom_Validate_FieldDependsOnValue('monthlyBreakdownRadio', 'I know the exact amount', array (
                new Zend_Validate_NotEmpty(), 
                new Zend_Validate_Float() 
        )), true);
        $monthlyBreakdownQst = "How many times per month, on average, does your internal IT staff or an external service company need to be called to repair a broken printer in your fleet? If you select \"I don't know\", an average of 1 repair per month for every 20 printers will be used.";
        $monthlyBreakdown->setLabel($monthlyBreakdownQst);
        $this->addElement($monthlyBreakdown);
        
        $monthlyBreakdownRadio = new Zend_Form_Element_Radio('monthlyBreakdownRadio');
        $monthlyBreakdownRadio->addMultiOptions($multiOptions);
        $monthlyBreakdownRadio->setLabel($monthlyBreakdownQst);
        
        $this->addElement($monthlyBreakdownRadio);
        $this->getElement('monthlyBreakdownRadio')->setValue('I know the exact amount');
        
        $location_trackingQst = "Do you currently have a tracking mechanism for the location of printing devices based on their IP address?";
        $location_tracking = new Zend_Form_Element_Select('location_tracking');
        $location_tracking->setMultiOptions(array (
                'Y' => 'Yes', 
                'N' => 'No' 
        ));
        
        $location_tracking->setLabel($location_trackingQst);
        $this->addElement($location_tracking);
        
        parent::init();
    }

    /**
     * Validate the form
     *
     * @param array $data            
     * @return boolean
     */
    public function isValid ($data)
    {
        if (! is_array($data))
        {
            require_once 'Zend/Form/Exception.php';
            throw new Zend_Form_Exception(__METHOD__ . ' expects an array');
        }
        
        // Validate our radio buttons
        if (true || $data ['name'] == 'xyz')
        {
            //$this->getElement('toner_cost_radio')->addError('Wrong name provided!');
        }
        return parent::isValid($data);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'survey/form/it.phtml' 
                        ) 
                ) 
        ));
    }
}