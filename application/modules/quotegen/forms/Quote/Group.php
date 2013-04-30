<?php

/**
 * Class Quotegen_Form_Quote_Group
 */
class Quotegen_Form_Quote_Group extends Twitter_Bootstrap_Form_Inline
{
    /**
     * This represents the current quote being worked on
     *
     * @var Quotegen_Model_Quote
     */
    protected $_quote;

    /**
     * @param null|Quotegen_Model_Quote $quote
     * @param null|array                $options
     */
    public function __construct ($quote = null, $options = null)
    {
        $this->_quote = $quote;
        parent::__construct($options);
        Quotegen_Form_Quote_Navigation::addFormActionsToForm(Quotegen_Form_Quote_Navigation::BUTTONS_ALL, $this);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');
        $this->_addClassNames('form-center-actions');

        // ----------------------------------------------------------------------
        // Validation Variables
        // ----------------------------------------------------------------------
        $minDeviceQuantity = 0;
        $maxDeviceQuantity = 999;

        // ----------------------------------------------------------------------
        // Add device to group sub form
        // ----------------------------------------------------------------------
        $addDeviceToGroupSubForm = new Twitter_Bootstrap_Form_Inline();
        $addDeviceToGroupSubForm->setElementDecorators(array(
                                                            'FieldSize',
                                                            'ViewHelper',
                                                            'Addon',
                                                            'PopoverElementErrors',
                                                            'Wrapper'
                                                       ));

        $this->addSubForm($addDeviceToGroupSubForm, 'addDeviceToGroup');

        // Quantity of the new device
        $addDeviceToGroupSubForm->addElement('text', 'quantity', array(
                                                                      'label'      => 'Quantity',
                                                                      'class'      => 'span1',
                                                                      'value'      => 1,
                                                                      'validators' => array(
                                                                          'Int',
                                                                          array(
                                                                              'validator' => 'Between',
                                                                              'options'   => array(
                                                                                  'min' => $minDeviceQuantity,
                                                                                  'max' => $maxDeviceQuantity
                                                                              )
                                                                          )
                                                                      )
                                                                 ));

        // Available devices
        $deviceDropdown = $addDeviceToGroupSubForm->createElement('select', 'quoteDeviceId', array(
                                                                                                  'label' => 'Devices:'
                                                                                             ));

        /* @var $quoteDevice Quotegen_Model_QuoteDevice */
        foreach ($this->_quote->getQuoteDevices() as $quoteDevice)
        {
            $deviceDropdown->addMultiOption($quoteDevice->id, $quoteDevice->name);
        }

        $addDeviceToGroupSubForm->addElement($deviceDropdown);

        // Groups
        $groupDropdown = $addDeviceToGroupSubForm->createElement('select', 'quoteDeviceGroupId', array(
                                                                                                      'label' => 'Groups:'
                                                                                                 ));

        foreach ($this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            $groupDropdown->addMultiOption("{$quoteDeviceGroup->id}", $quoteDeviceGroup->name);
        }
        $addDeviceToGroupSubForm->addElement($groupDropdown);

        // Add button
        $addDeviceToGroupSubForm->addElement('button', 'addDevice', array(
                                                                         'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_SUCCESS,
                                                                         'type'       => 'submit',
                                                                         'label'      => 'Add'
                                                                    ));

        // ----------------------------------------------------------------------
        // Add group subform
        // ----------------------------------------------------------------------
        $addGroupSubform = new Twitter_Bootstrap_Form_Inline();
        $addGroupSubform->setElementDecorators(array(
                                                    'FieldSize',
                                                    'ViewHelper',
                                                    'Addon',
                                                    'PopoverElementErrors',
                                                    'Wrapper'
                                               ));
        $this->addSubForm($addGroupSubform, 'addGroup');

        // The add group button
        $addGroupButton = $addGroupSubform->createElement('button', 'addGroup', array(
                                                                                     'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_SUCCESS,
                                                                                     'type'       => 'submit',
                                                                                     'label'      => 'Add Group'
                                                                                ));

        // The name of the new group
        $addGroupSubform->addElement('text', 'name', array(
                                                          'required'   => true,
                                                          'prepend'    => $addGroupButton,
                                                          'filters'    => array(
                                                              'StringTrim',
                                                              'StripTags'
                                                          ),
                                                          'validators' => array(
                                                              array(
                                                                  'validator' => 'StringLength',
                                                                  'options'   => array(
                                                                      3,
                                                                      40
                                                                  )
                                                              )
                                                          )
                                                     ));

        // ----------------------------------------------------------------------
        // Quantity subform
        // ----------------------------------------------------------------------
        $deviceQuantitySubform = new Twitter_Bootstrap_Form_Inline();
        $this->addSubForm($deviceQuantitySubform, 'deviceQuantity');

        $deviceQuantitySubform->setElementDecorators(array(
                                                          'FieldSize',
                                                          'ViewHelper',
                                                          'Addon',
                                                          'PopoverElementErrors',
                                                          'Wrapper'
                                                     ));

        // Setup all the boxes


        $validQuoteGroupId_DeviceId_Combinations = array();
        $validQuoteGroupIds                      = array();
        /* @var $quoteDeviceGroup Quotegen_Model_QuoteDeviceGroup */
        foreach ($this->getQuote()->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {

            $validQuoteGroupIds [] = "{$quoteDeviceGroup->id}";
            $this->addElement("text", "groupName_{$quoteDeviceGroup->id}", array(
                                                                                'required'   => true,
                                                                                'class'      => 'span4',
                                                                                'value'      => $quoteDeviceGroup->name,
                                                                                'filters'    => array(
                                                                                    'StringTrim',
                                                                                    'StripTags'
                                                                                ),
                                                                                'validators' => array(
                                                                                    array(
                                                                                        'validator' => 'StringLength',
                                                                                        'options'   => array(
                                                                                            3,
                                                                                            40
                                                                                        )
                                                                                    )
                                                                                )
                                                                           ));

            /* @var $quoteDeviceGroupDevice Quotegen_Model_QuoteDeviceGroupDevice */
            foreach ($quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice)
            {
                $deviceQuantitySubform->addElement('text', "quantity_{$quoteDeviceGroupDevice->quoteDeviceGroupId}_{$quoteDeviceGroupDevice->quoteDeviceId}", array(
                                                                                                                                                                   'label'      => 'Quantity',
                                                                                                                                                                   'required'   => true,
                                                                                                                                                                   'class'      => 'span1',
                                                                                                                                                                   'value'      => $quoteDeviceGroupDevice->quantity,
                                                                                                                                                                   'validators' => array(
                                                                                                                                                                       'Int',
                                                                                                                                                                       array(
                                                                                                                                                                           'validator' => 'Between',
                                                                                                                                                                           'options'   => array(
                                                                                                                                                                               'min' => $minDeviceQuantity,
                                                                                                                                                                               'max' => $maxDeviceQuantity
                                                                                                                                                                           )
                                                                                                                                                                       )
                                                                                                                                                                   )
                                                                                                                                                              ));

                $validQuoteGroupId_DeviceId_Combinations [] = "{$quoteDeviceGroupDevice->quoteDeviceId}_{$quoteDeviceGroupDevice->quoteDeviceGroupId}";
            }
        }

        // Delete group button
        $deviceQuantitySubform->addElement('button', 'deleteGroup', array(
                                                                         'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_DANGER,
                                                                         'label'      => ' ',
                                                                         'icon'       => 'trash',
                                                                         'validators' => array(
                                                                             array(
                                                                                 'validator' => 'InArray',
                                                                                 'options'   => array(
                                                                                     'haystack' => $validQuoteGroupIds
                                                                                 )
                                                                             )
                                                                         ),
                                                                         'value'      => '1'
                                                                    ));

        // Delete device from group button
        $deviceQuantitySubform->addElement('button', 'deleteDeviceFromGroup', array(
                                                                                   'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_DANGER,
                                                                                   'label'      => ' ',
                                                                                   'icon'       => 'trash',
                                                                                   'validators' => array(
                                                                                       array(
                                                                                           'validator' => 'InArray',
                                                                                           'options'   => array(
                                                                                               'haystack' => $validQuoteGroupId_DeviceId_Combinations
                                                                                           )
                                                                                       )
                                                                                   ),
                                                                                   'value'      => '1'
                                                                              ));
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators(array(
                                  array(
                                      'ViewScript',
                                      array(
                                          'viewScript' => 'quote/groups/form/group.phtml'
                                      )
                                  )
                             ));
    }

    /**
     * Gets the quote
     *
     * @return Quotegen_Model_Quote
     */
    public function getQuote ()
    {
        return $this->_quote;
    }

    /**
     * Sets the quote
     *
     * @param Quotegen_Model_Quote $_quote
     *
     * @return $this
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;

        return $this;
    }
}