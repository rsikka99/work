<?php

class Proposalgen_Form_Survey_General extends Proposalgen_Form_Survey_BaseSurveyForm
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
        $this->setAttrib('class', '');
        
        $numb_employees = new Zend_Form_Element_Text('numb_employees');
        $numb_employees->setAttrib('style', 'width: 35px;')
            ->setAttrib('maxlength', 4)
            ->setRequired(true)
            ->addValidator('digits')
            ->setAutoInsertNotEmptyValidator(true)
            ->addValidator('greaterThan', true, array (
                'min' => 0 
        ));
        $numb_employees->getValidator('digits')->setMessage('Please enter a number. (With no decimal places)');
        $emplQst = "How many office employees do you have at the site(s) to be covered by managed print services?";
        $numb_employees->setLabel($emplQst);
        $this->addElement($numb_employees);
        
        $rateText = new My_Form_Element_Paragraph('ratingQuetions');
        $rateText->setLabel("Rate the following goals for managed print services from highest to lowest, with 1 being the most important goal and 5 being the least important goal.");
        $this->addElement($rateText);
        
        $ratingQuestions = array (
                1 => "Ensure your printing hardware matches your print volume needs.", 
                2 => "Increase uptime and productivity for your employees", 
                3 => "Streamline logistics for supplies, service and hardware acquisition.", 
                4 => "Reduce environmental impact", 
                5 => "Reduce costs" 
        );
        
        $ratingRanks = array (
                1, 
                2, 
                3, 
                4, 
                5 
        );
        
        foreach ( $ratingQuestions as $questionNumber => $questionText )
        {
            $hasLabel = false;
            foreach ( $ratingRanks as $rank )
            {
                $multi_option = new Zend_Form_Element_Radio('rank' . $rank);
                if (! $hasLabel)
                {
                    $multi_option->setLabel($questionText);
                    $hasLabel = true;
                }
                
                $multi_option->addMultiOption($questionNumber);
                $this->addElement($multi_option, 'test');
            }
        }
        /*
         * $goals_hardware = new Zend_Form_Element_Radio('goals_hardware'); $goals_hardware->setRequired(true)
         * ->setAutoInsertNotEmptyValidator(true) ->setAttrib('tmtw', 'numeric') ->setAttrib('id', '6')
         * ->addPrefixPath('Tangent_Validate', 'Tangent/Validate/', 'validate') ->addValidator('Range')
         * ->addMultiOptions(array ( "1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5" )) ->setSeparator('');
         * $goals_hardwareQst = "Ensure your printing hardware matches your print volume needs. ";
         * $goals_hardware->getDecorator('Label')->setOption('escape', false); $label =
         * $goals_hardware->getDecorator('label'); $goals_hardware->setLabel($goals_hardwareQst);
         * $this->addElement($goals_hardware); $goals_employee = new Zend_Form_Element_Radio('goals_employee');
         * $goals_employee->setRequired(true) ->setAutoInsertNotEmptyValidator(true) ->setAttrib('tmtw', 'numeric')
         * ->setAttrib('id', '7') ->addPrefixPath('Tangent_Validate', 'Tangent/Validate/', 'validate')
         * ->addValidator('Range') ->addMultiOptions(array ( "1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5"
         * )) ->setSeparator(' '); $goals_employeeQst = "Increase uptime and productivity for your employees.";
         * $goals_employee->setLabel($goals_employeeQst); $label = $goals_employee->getDecorator('label');
         * $this->addElement($goals_employee); $goals_logistics = new Zend_Form_Element_Radio('goals_logistics');
         * $goals_logistics->setRequired(true) ->setAutoInsertNotEmptyValidator(true) ->setAttrib('tmtw', 'numeric')
         * ->setAttrib('id', '8') ->addPrefixPath('Tangent_Validate', 'Tangent/Validate/', 'validate')
         * ->addValidator('Range') ->addMultiOptions(array ( "1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5"
         * )) ->setSeparator(' '); $goals_logisticsQst = "Streamline logistics for supplies, service and hardware
         * acquisition."; $goals_logistics->setLabel($goals_logisticsQst); $label =
         * $goals_logistics->getDecorator('label'); $this->addElement($goals_logistics); $goals_enviroment = new
         * Zend_Form_Element_Radio('goals_enviroment'); $goals_enviroment->setRequired(true)
         * ->setAutoInsertNotEmptyValidator(true) ->setAttrib('tmtw', 'numeric') ->setAttrib('id', '9')
         * ->addPrefixPath('Tangent_Validate', 'Tangent/Validate/', 'validate') ->addValidator('Range')
         * ->addMultiOptions(array ( "1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5" )) ->setDecorators(array
         * ( array ( 'Description', array ( 'escape' => false, 'tag' => false ) ), 'ViewHelper', 'Errors', array (
         * 'HtmlTag', array ( 'id' => 'goals_enviroment-element' ) ), array ( 'Label' ) )) ->setSeparator(' ');
         * $goals_enviromentQst = "Reduce environmental impact"; $goals_enviroment->setLabel($goals_enviromentQst);
         * $label = $goals_enviroment->getDecorator('label'); $this->addElement($goals_enviroment); $goals_cost = new
         * Zend_Form_Element_Radio('goals_costs'); $goals_cost->setRequired(true) ->setAutoInsertNotEmptyValidator(true)
         * ->setAttrib('tmtw', 'numeric') ->setAttrib('id', '10') ->addPrefixPath('Tangent_Validate',
         * 'Tangent/Validate/', 'validate') ->addValidator('Range') ->addMultiOptions(array ( "1" => "1", "2" => "2",
         * "3" => "3", "4" => "4", "5" => "5" )) ->setSeparator(' '); $goals_costQst = "Reduce costs";
         * $goals_cost->setLabel($goals_costQst); $label = $goals_cost->getDecorator('label');
         * $this->addElement($goals_cost);
         */
        parent::init();
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'survey/form/general.phtml' 
                        ) 
                ) 
        ));
    }
}