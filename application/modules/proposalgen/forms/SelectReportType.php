<?php

class Proposalgen_Form_SelectReportType extends EasyBib_Form
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
        $this->setAttrib('class', 'form-horizontal');
        
        $radio = new Zend_Form_Element_Radio('proposalOptions');
        $radio->setLabel("Please select one of the following options:");
        
        $radio->addMultiOption("ViewUnfinishedStartNewProposal", "View Unfinished/Start New Proposal");
        $radio->addMultiOption("ViewFinishedProposal", "View Finished Proposal");
        $radio->setAttrib('onclick', 'this.form.submit()');
        
        $this->addElement($radio);
    }

}
