<?php

namespace MPSToolbox\Legacy\Modules\HardwareOptimization\Forms;

use Zend_Form;

/**
 * Class HardwareOptimizationQuoteForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareOptimization\Forms
 */
class HardwareOptimizationQuoteForm extends Zend_Form
{
    public function init ()
    {
        $this->setMethod('post');

        $this->addElement("submit", "purchasedQuote", array(
            "label" => "Export Purchased Quote",
            "class" => "btn btn-primary",
            'title' => "Exports replaced devices into a purchased quote.",
        ));

        $this->addElement("submit", "leasedQuote", array(
            "label" => "Export Leased Quote",
            "class" => "btn btn-primary",
            'title' => "Exports replaced devices into a leased quote.",
        ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
            array(
                'ViewScript',
                array(
                    'viewScript' => 'forms/hardwareoptimization/hardwareoptimization-quote-form.phtml'
                )
            )
        ));
    }
}