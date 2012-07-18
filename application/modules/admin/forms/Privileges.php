<?php

class Admin_Form_Privileges extends EasyBib_Form
{
    protected $_availablePrivileges;
    
    /*
     * (non-PHPdoc) @see Zend_Form::__construct()
     */
    public function __construct ($availablePrivileges = null, $options = null)
    {
        $this->_availablePrivileges = $availablePrivileges;
        
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
        $this->setAttrib('class', 'form-horizontal button-styled');
        
        $multiOptions = array ();
        
        foreach ( $this->_availablePrivileges as $module )
        {
            foreach ( $module->controllers as $controller )
            {
                foreach ( $controller->actions as $action )
                {
                    $multiOptions[$action->permissionPath] = $action->permissionPath;
                }
            }
        }
        
        $this->addElement('multicheckbox', 'privileges', array (
                'label' => 'Select Privileges:', 
                'required' => true,
                'multiOptions' => $multiOptions
        ));
        
        // Add the submit button
        $this->addElement('submit', 'submit', array (
                'ignore' => true, 
                'label' => 'Save' 
        ));
        
        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit');
    }
}
