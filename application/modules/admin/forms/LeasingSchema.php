<?php

class Admin_Form_LeasingSchema extends EasyBib_Form
{

    protected $_dealerManagement;

    /*
* (non-PHPdoc) @see Zend_Form::__construct()
*/
    public function __construct ($dealerManagement = false)
    {
        $this->_dealerManagement = $dealerManagement;

        parent::__construct();
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        /**
         * Add class to form for label alignment
         *
         * - Vertical .form-vertical (not required)    Stacked, left-aligned labels
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

        $this->addElement('text', 'name', array(
                                               'label'      => 'Name:',
                                               'required'   => true,
                                               'filters'    => array(
                                                   'StringTrim',
                                                   'StripTags'
                                               ),
                                               'validators' => array(
                                                   array(
                                                       'validator' => 'StringLength',
                                                       'options'   => array(
                                                           1,
                                                           255
                                                       )
                                                   )
                                               )
                                          ));

        $dealerSelect  = null;
        $isSystemAdmin = $this->getView()->IsAllowed(Application_Model_Acl::RESOURCE_ADMIN_USER_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN);
        if ($isSystemAdmin && $this->_dealerManagement == false)
        {
            $firstDealerId = null;
            $dealers       = array();
            foreach (Admin_Model_Mapper_Dealer::getInstance()->fetchAll() as $dealer)
            {
                // Use this to grab the first id in the leasing schema dropdown
                if (!$firstDealerId)
                {
                    $firstDealerId = $dealer->id;
                }
                $dealers [$dealer->id] = $dealer->dealerName;
            }
            if ($dealers)
            {
                $this->addElement('select', 'dealerId', array(
                                                             'label'        => 'Dealer:',
                                                             'class'        => 'input-medium',
                                                             'multiOptions' => $dealers,
                                                             'required'     => true,
                                                             'value'        => $firstDealerId));
            }
        }

        // Add the submit button
        $this->addElement('submit', 'submit', array(
                                                   'ignore' => true,
                                                   'label'  => 'Save'
                                              ));

        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
                                                   'ignore' => true,
                                                   'label'  => 'Cancel'
                                              ));

        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
