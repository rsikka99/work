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
        $this->setAttrib('class', 'proposalForm form-vertical');
        
        /*
         * Please note that this validator will not display errors in the correct location. This is just a failsafe
         * against anyone who may want to try to hack a different value in. The average user will never trigger this
         * error.
         */
        $inArrayValidator = new Zend_Validate_InArray(array (
                '1', 
                '2', 
                '3', 
                '4', 
                '5' 
        ));
        $inArrayValidator->setMessage('You must select a rank between 1 and 5');
        
        /*
         * The Rating questions are in the view script. This form element is a bit tricky because we have to build a
         * table for the radio buttons. Validation mostly happens in the isValid function of this form.
         */
        for($i = 1; $i <= 5; $i ++)
        {
            $myRadio = new My_Form_Element_Radio("rank{$i}");
            $myRadio->addMultiOptions(array (
                    '1' => '', 
                    '2' => '', 
                    '3' => '', 
                    '4' => '', 
                    '5' => '' 
            ));
            $myRadio->addValidator($inArrayValidator);
            $this->addElement($myRadio);
        }
        
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
        
        $valid = parent::isValid($data);
        
        /*
         * This is the start of the validation for ranks. The difficult thing is that ranks are grouped in vertical
         * rows, but the values we care about are in horizontal groups. This makes displaying appropriate errors to the
         * user harder to do than normal.
         */
        
        /*
         * First we get all our values into an array. We need to ensure everything is set, even if it's null. 
         * This lets us display the "Please choose a rank" in the right row.
         */
        $myData ["rank1"] = (isset($data ["rank1"])) ? $data ["rank1"] : null;
        $myData ["rank2"] = (isset($data ["rank2"])) ? $data ["rank2"] : null;
        $myData ["rank3"] = (isset($data ["rank3"])) ? $data ["rank3"] : null;
        $myData ["rank4"] = (isset($data ["rank4"])) ? $data ["rank4"] : null;
        $myData ["rank5"] = (isset($data ["rank5"])) ? $data ["rank5"] : null;
        
        // A set of flags to see if we have entered data for a specific row.
        $rankOptionsSet [1] = false;
        $rankOptionsSet [2] = false;
        $rankOptionsSet [3] = false;
        $rankOptionsSet [4] = false;
        $rankOptionsSet [5] = false;
        
        /*
         * Here we need to check for duplicate entries. In theory our javascript prevents the user from doing this but
         * we can never trust client side restrictions.
         */
        foreach ( $myData as $key => $value )
        {
            if ($value !== null)
            {
                // Set our flag that a value has been set. (Used in the next section)
                $rankOptionsSet [$value] = true;
                
                // Check this value against all other values that we got from the form.
                foreach ( $myData as $key2 => $value2 )
                {
                    // Only check against other values and not ourselves.
                    if ($key !== $key2)
                    {
                        // If they match then we mark the element as an error.
                        if ($value == $value2)
                        {
                            $this->getElement("rank$value")->setErrors(array (
                                    'You can not select duplicate ranks.' 
                            ));
                            $valid = false;
                        }
                    }
                }
            }
        }
        
        // Here we check our flags to see if any rows are missing a selection.
        foreach ( $rankOptionsSet as $key => $value )
        {
            if (! $value)
            {
                $this->getElement("rank$key")->setErrors(array (
                        'You must choose a rank.' 
                ));
                $valid = false;
            }
        }
        
        return $valid;
    }
}