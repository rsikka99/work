<?php

class Quotegen_Form_Group extends EasyBib_Form
{
    /**
     * This represents the current quote being worked on
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;

    public function __construct ($quote = null, $options = null)
    {
        $this->_quote = $quote;
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
        $this->setAttrib('class', 'form-horizontal form-center-actions');
        
        $this->addElement('text', 'quantity', array (
                'label' => 'Quantity', 
                'class' => 'span1',
                'value' => 1, 
        ));
        
        $deviceDropdown = new Zend_Form_Element_Select('devices', array (
                'label' => 'Devices:' 
        ));
        
        /* @var $quoteDeviceGroups Quotegen_Model_QuoteDeviceGroup */
        $quoteDeviceGroups = $this->_quote->getQuoteDeviceGroups();

        foreach ( $quoteDeviceGroups as $quoteDeviceGroup )
        {
            /* @var $device Quotegen_Model_QuoteDevice */
            foreach ( $quoteDeviceGroup->getQuoteDevices() as $device )
            {
                $deviceDropdown->addMultiOption($device->getId(), $device->getName());
            }
        }
        
        $this->addElement($deviceDropdown);     
        
        $groupDropdown = new Zend_Form_Element_Select('groups', array (
                'label' => 'Groups:'
        ));
       
        $groupDropdown->addMultiOption('1', 'Default Group (Ungrouped)');
        $this->addElement($groupDropdown);
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
    
    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array (
                array (
                        'ViewScript', 
                        array (
                                'viewScript' => 'quote/groups/form/group.phtml' 
                        ) 
                ) 
        ));
    }
	/**
     * @return the $_quote
     */
    public function getQuote ()
    {
        return $this->_quote;
    }

	/**
     * @param Quotegen_Model_Quote $_quote
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;
        return this;
    }

}

?>