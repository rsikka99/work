<?php

namespace MPSToolbox\Legacy\Modules\QuoteGenerator\Forms;

use Zend_Form;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Models\OptionModel;
use My_Form_Element_Paragraph;

/**
 * Class SelectOptionsForm
 *
 * @package MPSToolbox\Legacy\Modules\QuoteGenerator\Forms
 */
class SelectOptionsForm extends Zend_Form
{
    /**
     * @var OptionModel[]
     */
    protected $_availableOptions = [];

    /**
     * @param null|OptionModel[] $availableOptions
     * @param null|array         $options
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

        $optionList = [];
        /* @var $option OptionModel */
        if (count($this->_availableOptions) > 0)
        {
            foreach ($this->_availableOptions as $option)
            {
                $optionList [$option->id] = $option->name . ": " . $option->description;
            }

            $this->addElement('multiCheckbox', 'options', [
                'label'        => 'Options',
                'multiOptions' => $optionList,
            ]);
        }

        // Add the submit button
        $this->addElement('submit', 'submit', [
            'ignore' => true,
            'label'  => 'Save',
        ]);

        // Add the cancel button
        $this->addElement('submit', 'cancel', [
            'ignore' => true,
            'label'  => 'Cancel',
        ]);

    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/quotegen/add-options-form.phtml', 'availableOptions' => $this->_availableOptions]]]);
    }
}
