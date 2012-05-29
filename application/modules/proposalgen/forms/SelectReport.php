<?php

class Proposalgen_Form_SelectReport extends EasyBib_Form
{
    protected $_reports;

    /**
     * The constructor of the form
     *
     * @param array $reports
     *            An array of Application_Model_Report to show in the dropdown
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
        
        $select = new Zend_Form_Element_Select('select_proposal');
        //$select->setLabel("Please select a report:");
        /* @var $report Application_Model_Report */
        foreach ( $this->_reports as $report )
        {
            
            $reportName = $report->getCustomerCompanyName();
            if ($report->getDateCreated() !== null)
            {
                $date = strftime('%x', strtotime($report->getDateCreated()));
                $reportName .=  ' (' . $date . ')';
            }
            $select->addMultiOption($report->getReportId(), $reportName);
        }
        
        $this->addElement($select);
        
        // Add the submit button
        

        $this->addElement('submit', 'start_survey', array (
                'ignore' => true, 
                'label' => 'Next' 
        ));
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'start_survey', 'cancel');
    }

}
