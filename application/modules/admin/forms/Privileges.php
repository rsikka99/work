<?php

/**
 * Class Admin_Form_Privileges
 */
class Admin_Form_Privileges extends EasyBib_Form
{
    /**
     * @var array
     */
    protected $_availablePrivileges;

    /**
     * @param null|array $availablePrivileges
     * @param null|array $options
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
        $this->setAttrib('class', 'form-horizontal button-styled');

        $multiOptions = array();

        foreach ($this->_availablePrivileges as $module)
        {
            foreach ($module->controllers as $controller)
            {
                foreach ($controller->actions as $action)
                {
                    $multiOptions[$action->permissionPath] = $action->permissionPath;
                }
            }
        }

        $this->addElement('MultiCheckbox', 'privileges', array(
                                                              'label'        => 'Select Privileges:',
                                                              'required'     => true,
                                                              'multiOptions' => $multiOptions
                                                         ));

        // Add the submit button
        $this->addElement('submit', 'submit', array(
                                                   'ignore' => true,
                                                   'label'  => 'Save'
                                              ));

        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit');
    }
}
