<?php

/**
 * Class Admin_Form_LeasingSchema
 */
class Admin_Form_LeasingSchema extends EasyBib_Form
{
    /**
     * @var bool
     */
    protected $_dealerManagement;

    /**
     * @param bool       $dealerManagement
     * @param null|array $options
     */
    public function __construct ($dealerManagement = false, $options = null)
    {
        $this->_dealerManagement = $dealerManagement;

        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
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

        $dealerSelect = null;
        $isAdmin      = $this->getView()->IsAllowed(Admin_Model_Acl::RESOURCE_ADMIN_LEASINGSCHEMA_WILDCARD, Application_Model_Acl::PRIVILEGE_ADMIN);
        if ($isAdmin && $this->_dealerManagement == false)
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
