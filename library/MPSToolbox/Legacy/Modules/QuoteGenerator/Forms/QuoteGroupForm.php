<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use MPSToolbox\Legacy\Forms\FormWithNavigation;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceGroupModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\QuoteModel;
use Twitter_Bootstrap_Form_Inline;

/**
 * Class QuoteGroupForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class QuoteGroupForm extends FormWithNavigation
{
    /**
     * This represents the current quote being worked on
     *
     * @var QuoteModel
     */
    protected $_quote;

    /**
     * @param null|QuoteModel $quote
     * @param null|array      $options
     * @param array|null      $options
     * @param int             $formButtonMode
     * @param array           $buttons
     */
    public function __construct ($quote = null, $options = null, $options = null, $formButtonMode = self::FORM_BUTTON_MODE_NAVIGATION, $buttons = [self::BUTTONS_ALL])
    {
        $this->_quote = $quote;
        parent::__construct($options, $formButtonMode, $buttons);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        // ----------------------------------------------------------------------
        // Validation Variables
        // ----------------------------------------------------------------------
        $minDeviceQuantity = 0;
        $maxDeviceQuantity = 999;

        // ----------------------------------------------------------------------
        // Add device to group sub form
        // ----------------------------------------------------------------------
        $addDeviceToGroupSubForm = new Twitter_Bootstrap_Form_Inline();
        $addDeviceToGroupSubForm->setElementDecorators([
            'FieldSize',
            'ViewHelper',
            'Addon',
            'PopoverElementErrors',
            'Wrapper',
        ]);

        $this->addSubForm($addDeviceToGroupSubForm, 'addDeviceToGroup');

        // Quantity of the new device
        $addDeviceToGroupSubForm->addElement('text_int', 'quantity', [
            'label'      => 'Quantity',
            'class'      => 'span1',
            'value'      => 1,
            'validators' => [
                'Int',
                [
                    'validator' => 'Between',
                    'options'   => ['min' => $minDeviceQuantity, 'max' => $maxDeviceQuantity],
                ],
            ],
        ]);

        // Available devices
        $deviceDropdown = $addDeviceToGroupSubForm->createElement('select', 'quoteDeviceId', [
            'label' => 'Devices:',
        ]);

        /* @var $quoteDevice QuoteDeviceModel */
        foreach ($this->_quote->getQuoteDevices() as $quoteDevice)
        {
            $deviceDropdown->addMultiOption($quoteDevice->id, $quoteDevice->name);
        }

        $addDeviceToGroupSubForm->addElement($deviceDropdown);

        // Groups
        $groupDropdown = $addDeviceToGroupSubForm->createElement('select', 'quoteDeviceGroupId', [
            'label' => 'Groups:',
        ]);

        foreach ($this->_quote->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {
            $groupDropdown->addMultiOption("{$quoteDeviceGroup->id}", $quoteDeviceGroup->name);
        }
        $addDeviceToGroupSubForm->addElement($groupDropdown);

        // Add button
        $addDeviceToGroupSubForm->addElement('submit', 'addDevice', [
            'label' => 'Add',
        ]);

        // ----------------------------------------------------------------------
        // Add group subform
        // ----------------------------------------------------------------------
        $addGroupSubform = new Twitter_Bootstrap_Form_Inline();
        $addGroupSubform->setElementDecorators([
            'FieldSize',
            'ViewHelper',
            'Addon',
            'PopoverElementErrors',
            'Wrapper',
        ]);
        $this->addSubForm($addGroupSubform, 'addGroup');

        // The add group button
        $addGroupSubform->addElement('submit', 'addGroup', [
            'label' => 'Add Group',
        ]);

        // The name of the new group
        $addGroupSubform->addElement('text', 'name', [
            'label'      => 'Add a new group',
            'filters'    => ['StringTrim', 'StripTags'],
            'validators' => [
                [
                    'validator' => 'StringLength',
                    'options'   => [3, 40],
                ],
            ],
        ]);

        // ----------------------------------------------------------------------
        // Quantity subform
        // ----------------------------------------------------------------------
        $deviceQuantitySubform = new Twitter_Bootstrap_Form_Inline();
        $this->addSubForm($deviceQuantitySubform, 'deviceQuantity');

        $deviceQuantitySubform->setElementDecorators([
            'FieldSize',
            'ViewHelper',
            'Addon',
            'PopoverElementErrors',
            'Wrapper',
        ]);

        // Setup all the boxes


        $validQuoteGroupId_DeviceId_Combinations = [];
        $validQuoteGroupIds                      = [];
        /* @var $quoteDeviceGroup QuoteDeviceGroupModel */
        foreach ($this->getQuote()->getQuoteDeviceGroups() as $quoteDeviceGroup)
        {

            $validQuoteGroupIds [] = "{$quoteDeviceGroup->id}";
            $this->addElement("text", "groupName_{$quoteDeviceGroup->id}", [
                'required'   => true,
                'class'      => 'span4',
                'value'      => $quoteDeviceGroup->name,
                'filters'    => ['StringTrim', 'StripTags'],
                'validators' => [
                    [
                        'validator' => 'StringLength',
                        'options'   => [3, 40],
                    ],
                ],
            ]);

            /* @var $quoteDeviceGroupDevice QuoteDeviceGroupDeviceModel */
            foreach ($quoteDeviceGroup->getQuoteDeviceGroupDevices() as $quoteDeviceGroupDevice)
            {
                $deviceQuantitySubform->addElement('text_int', "quantity_{$quoteDeviceGroupDevice->quoteDeviceGroupId}_{$quoteDeviceGroupDevice->quoteDeviceId}", [
                    'label'      => 'Quantity',
                    'required'   => true,
                    'class'      => 'span1',
                    'value'      => $quoteDeviceGroupDevice->quantity,
                    'validators' => [
                        'Int',
                        [
                            'validator' => 'Between',
                            'options'   => ['min' => $minDeviceQuantity, 'max' => $maxDeviceQuantity],
                        ],
                    ],
                ]);

                $validQuoteGroupId_DeviceId_Combinations [] = "{$quoteDeviceGroupDevice->quoteDeviceId}_{$quoteDeviceGroupDevice->quoteDeviceGroupId}";
            }
        }

        // Delete group button
        $deviceQuantitySubform->addElement('submit', 'deleteGroup', [
            'label'      => ' ',
            'icon'       => 'trash',
            'validators' => [
                [
                    'validator' => 'InArray',
                    'options'   => ['haystack' => $validQuoteGroupIds],
                ],
            ],
            'value'      => '1',
        ]);

        // Delete device from group button
        $deviceQuantitySubform->addElement('submit', 'deleteDeviceFromGroup', [
            'label'      => ' ',
            'icon'       => 'trash',
            'validators' => [
                [
                    'validator' => 'InArray',
                    'options'   => ['haystack' => $validQuoteGroupId_DeviceId_Combinations],
                ],
            ],
            'value'      => '1',
        ]);

        QuoteNavigationForm::addFormActionsToForm(QuoteNavigationForm::BUTTONS_ALL, $this);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/quotegen/quote/group-form.phtml']]]);
    }

    /**
     * Gets the quote
     *
     * @return QuoteModel
     */
    public function getQuote ()
    {
        return $this->_quote;
    }

    /**
     * Sets the quote
     *
     * @param QuoteModel $_quote
     *
     * @return $this
     */
    public function setQuote ($_quote)
    {
        $this->_quote = $_quote;

        return $this;
    }
}