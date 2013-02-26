<?php

class Proposalgen_Form_SelectReport extends EasyBib_Form
{
    protected $_reports;

    /**
     * The constructor of the form
     *
     * @param array $reports
     *            An array of Proposalgen_Model_Report to show in the dropdown
     * @param mixed $options            
     */
    public function __construct (array $reports, $options = null)
    {
        $this->_reports = $reports;
        parent::__construct($options);
    }

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
        //$this->setAttrib('class', 'form-horizontal');
        

        $select = new Zend_Form_Element_Select('reportId');
        //$select->setLabel("Please select a report:");
        /* @var $report Proposalgen_Model_Assessment */
        foreach ( $this->_reports as $report )
        {
            $reportName = $report-> getClient()->companyName;
            if ($report->dateCreated !== null)
            {
                $date = strftime("%B %d, %Y", strtotime($report->dateCreated));
                $reportName .= ' (' . $date . ')';
            }
            $select->addMultiOption($report->id, $reportName);
        }
        
        $this->addElement($select);
        
        // Add the submit button
        

        $this->addElement('submit', 'selectAssessment', array (
                'ignore' => true, 
                'label' => 'Next' 
        ));
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'selectAssessment', 'cancel');
    }
}
