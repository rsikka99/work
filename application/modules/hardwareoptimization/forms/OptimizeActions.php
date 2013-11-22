<?php

/**
 * Class Proposalgen_Form_DeviceSwapChoice
 */
class Hardwareoptimization_Form_OptimizeActions extends Twitter_Bootstrap_Form
{

    public function init ()
    {
        $this->setMethod('post');

        // Add button(s) to form
        $submitButton = $this->createElement('button', 'Submit', array(
                                                                      'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
                                                                      'label'      => 'Re-calculate',
                                                                      'type'       => 'submit',
                                                                      'class'      => 'pull-right',
                                                                      'icon'       => 'arrow-right',
                                                                      'whiteIcon'  => true,
                                                                      'ignore'     => false,
                                                                      'title'      => 'Calculates and saves new totals based on current devices in Action column.',
                                                                 ));

        $analyzeButton = $this->createElement('button', 'Analyze', array(
                                                                        'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
                                                                        'label'      => 'Auto Analyze',
                                                                        'type'       => 'submit',
                                                                        'class'      => 'pull-right',
                                                                        'icon'       => 'refresh',
                                                                        'whiteIcon'  => true,
                                                                        'ignore'     => false,
                                                                        'title'      => "Removes any replacement devices previously saved. Then determines the optimal devices based on target monochrome/color CPP and cost delta threshold settings.",
                                                                   ));

        $resetReplacementsButton = $this->createElement('button', 'ResetReplacements', array(
                                                                                            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_WARNING,
                                                                                            'label'      => 'Reset',
                                                                                            'type'       => 'submit',
                                                                                            'class'      => 'pull-right',
                                                                                            'icon'       => 'exclamation-sign',
                                                                                            'whiteIcon'  => true,
                                                                                            'ignore'     => false,
                                                                                            'title'      => "Sets all the replacement devices back to there default action.",

                                                                                       ));


        $this->addElements(array(
                                $resetReplacementsButton,
                                $analyzeButton,
                                $submitButton,
                           ));
    }
}