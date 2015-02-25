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

        $this->addElement("submit", "purchasedQuote", [
            "label" => "Export Purchased Quote",
            "class" => "btn btn-primary",
            'title' => "Exports replaced devices into a purchased quote.",
        ]);

        $this->addElement("submit", "leasedQuote", [
            "label" => "Export Leased Quote",
            "class" => "btn btn-primary",
            'title' => "Exports replaced devices into a leased quote.",
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardwareoptimization/hardwareoptimization-quote-form.phtml']]]);
    }
}