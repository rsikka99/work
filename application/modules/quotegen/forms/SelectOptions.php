<?php

/**
 * Class Quotegen_Form_SelectOptions
 */
class Quotegen_Form_SelectOptions extends EasyBib_Form
{
    /**
     * @var Quotegen_Model_Option[]
     */
    protected $_availableOptions = array();

    /**
     * @param null|Quotegen_Model_Option[] $availableOptions
     * @param null|array                   $options
     */
    public function __construct ($availableOptions, $options = null)
    {
        if (is_array($availableOptions) && count($availableOptions) > 0)
        {
            $this->_availableOptions = $availableOptions;
        }
        parent::__construct($options);
    }

    public function init ()
    {
        // Set the method for the display form to POST
        $this->setMethod('POST');

        $this->setAttrib('class', 'form-horizontal form-center-actions');

        $optionList = array();
        /* @var $option Quotegen_Model_Option */
        if (count($this->_availableOptions) > 0)
        {
            foreach ($this->_availableOptions as $option)
            {
                $optionList [$option->id] = $option->name . ": " . $option->description;
            }

            $this->addElement('multiCheckbox', 'options', array(
                'label'        => 'Options',
                'multiOptions' => $optionList
            ));

            // Add the submit button
            $this->addElement('submit', 'submit', array(
                'ignore' => true,
                'label'  => 'Save'
            ));
        }
        else
        {
            $paragraph = new My_Form_Element_Paragraph('test');
            $paragraph->setLabel('All available options have been added to the device already.');
            $this->addElement($paragraph);
        }
        // Add the cancel button
        $this->addElement('submit', 'cancel', array(
            'ignore' => true,
            'label'  => 'Cancel'
        ));

        EasyBib_Form_Decorator::setFormDecorator($this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel');
    }
}
