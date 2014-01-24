<?php

/**
 * Class Memjetoptimization_Form_Memjet_Optimization_Quote
 */
class Memjetoptimization_Form_Memjet_Optimization_Quote extends Twitter_Bootstrap_Form_Vertical
{
    public function init ()
    {
        $this->setMethod('post');
        $this->_addClassNames('form-center-actions');

        $this->addElement("submit", "purchasedQuote", array(
            "label" => "Export Purchased Quote",
            "class" => "btn-primary",
            'title' => "Exports replaced devices into a purchased quote.",
        ));

        $this->addElement("submit", "leasedQuote", array(
            "label" => "Export Leased Quote",
            "class" => "btn-primary",
            'title' => "Exports replaced devices into a leased quote.",
        ));
    }
}