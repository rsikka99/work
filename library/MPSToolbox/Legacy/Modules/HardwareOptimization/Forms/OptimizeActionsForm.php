<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Forms;

use Zend_Form;

/**
 * Class OptimizeActionsForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Forms
 */
class OptimizeActionsForm extends Zend_Form
{

    public function init ()
    {
        $this->setMethod('post');

        // Add button(s) to form
        $this->addElement('submit', 'Submit', [
            'label'     => 'Re-calculate',
            'icon'      => 'arrow-right',
            'whiteIcon' => true,
            'ignore'    => false,
            'title'     => 'Calculates and saves new totals based on current devices in Action column.',
        ]);

        $this->addElement('submit', 'Analyze', [
            'label'     => 'Auto Analyze',
            'class'     => 'pull-right btn btn-primary',
            'icon'      => 'refresh',
            'style'     => 'margin-right:10px;',
            'whiteIcon' => true,
            'ignore'    => false,
            'title'     => "Removes any replacement devices previously saved. Then determines the optimal devices based on target monochrome/color CPP and cost delta threshold settings.",
        ]);

        $this->addElement('submit', 'ResetReplacements', [
            'label'     => 'Reset',
            'class'     => 'pull-right btn btn-warning',
            'icon'      => 'exclamation-sign',
            'whiteIcon' => true,
            'ignore'    => false,
            'title'     => "Sets all the replacement devices back to their default action.",

        ]);
    }


    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardwareoptimization/optimize-actions-form.phtml']]]);
    }
}