<?php

class Proposalgen_Form_Survey_Users extends Proposalgen_Form_Survey_BaseSurveyForm
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
        $this->setAttrib('class', 'surveyForm form-horizontal');
        
        /**
         * The raw text is now located within the view partial
         */
//         $pageCoverageQuestion = "Page coverages range from 5% to 10% for monochrome and 15% to 25% for color. Estimate your own average page coverages.";
//         $pageCoverageQuestion = new My_Form_Element_Paragraph('pageCoverageQuestion');
//         $pageCoverageQuestion->setLabel($pageCoverageQuestion);
//         $this->addElement($pageCoverageQuestion);
        
        $pageCoverage_BW = new Zend_Form_Element_Text('pageCoverage_BW');
        $pageCoverage_BW->setRequired(true)
            ->setLabel('Monochrome Coverage:')
            ->setAttrib('maxlength', 5)
            ->setAttrib('class', 'span1')
            ->setDescription('%')
            ->addValidator('float')
            ->addValidator(new Zend_Validate_Between(1, 100));
        
        $pageCoverage_BW->getValidator('Between')->setMessage('Must be between 1 and 100');
        $this->addElement($pageCoverage_BW);
        
        $pageCoverage_Colour = new Zend_Form_Element_Text('pageCoverage_Color');
        $pageCoverage_Colour->setRequired(true)
            ->setLabel('Color Coverage:')
            ->setAttrib('maxlength', 5)
            ->setAttrib('class', 'span1')
            ->setDescription('%')
            ->addValidator('float')
            ->addValidator(new Zend_Validate_Between(1, 100));
        $pageCoverage_Colour->getValidator('Between')->setMessage('Must be between 1 and 100');
        $this->addElement($pageCoverage_Colour);
        
        $volumeOptions = array (
                5 => 'Less than 10%', 
                18 => '10% to 25%', 
                38 => '26% to 50%', 
                75 => 'More than 50%' 
        );
        
        $volumeQuestion = "What percent of your print volume is done on inkjet and other desktop printers?";
        
        $printVolumeRadio = new Zend_Form_Element_Radio('printVolume');
        $printVolumeRadio->setLabel($volumeQuestion)
            ->setMultiOptions($volumeOptions)
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator('InArray', false, array (
                array_keys($volumeOptions) 
        ));
        $this->addElement($printVolumeRadio);
        
        $repairTimeOptions = array (
                '0.5' => 'Less than a day', 
                1 => 'One day', 
                2 => 'Two days', 
                3 => 'Three to five days', 
                5 => 'More than five days' 
        );
        $repairTimeQuestion = "How long does it take, on average, for a printer to be fixed after it has broken down?";
        
        $repairTimeRadio = new Zend_Form_Element_Radio('repairTime');
        $repairTimeRadio->setLabel($repairTimeQuestion)
            ->setMultiOptions($repairTimeOptions)
            ->setRequired(true)
            ->addFilter('StringTrim')
            ->addValidator('InArray', false, array (
                array_keys($repairTimeOptions) 
        ));
        $this->addElement($repairTimeRadio);
        
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
                                'viewScript' => 'survey/form/users.phtml' 
                        ) 
                ) 
        ));
    }
}