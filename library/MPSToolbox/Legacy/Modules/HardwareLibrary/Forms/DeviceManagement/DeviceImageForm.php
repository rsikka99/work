<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use My_Brand;
use My_Validate_DateTime;
use Zend_Form;

/**
 * Class DeviceImageForm
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class DeviceImageForm extends \My_Form_Form
{
    protected $_isAllowedToEditFields = false;

    /**
     * @param null $options
     * @param bool $isAllowedToEditFields
     */
    public function __construct ($options = null, $isAllowedToEditFields = false)
    {
        $this->_isAllowedToEditFields = $isAllowedToEditFields;

        if (!$this->_prefixesInitialized)
        {
            if (null !== $this->getView())
            {
                $this->getView()->addHelperPath(
                    'ZendX/JQuery/View/Helper',
                    'ZendX_JQuery_View_Helper'
                );
            }

            $this->addPrefixPath(
                'ZendX_JQuery_Form_Element',
                'ZendX/JQuery/Form/Element',
                'element'
            );

            $this->addElementPrefixPath(
                'ZendX_JQuery_Form_Decorator',
                'ZendX/JQuery/Form/Decorator',
                'decorator'
            );

            $this->addDisplayGroupPrefixPath(
                'ZendX_JQuery_Form_Decorator',
                'ZendX/JQuery/Form/Decorator'
            );

        }

        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'deviceImage');

        $this->addElement('text', 'id', [
            'label'    => 'ID',
            'disabled' => (!$this->_isAllowedToEditFields) ? true : false,
        ]);
        $this->addElement('text', 'imageUrl', [
            'label'    => 'Image URL',
            'disabled' => (!$this->_isAllowedToEditFields) ? true : false,
        ]);
        $this->addElement('text', 'imageFile', [
            'label'    => 'Upload Image',
            'disabled' => (!$this->_isAllowedToEditFields) ? true : false,
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/device-image-form.phtml']]]);
    }
}