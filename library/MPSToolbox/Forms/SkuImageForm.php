<?php

namespace MPSToolbox\Forms;

class SkuImageForm extends \My_Form_Form
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
        $this->setAttrib('id', 'skuImage');

        $this->addElement('hidden', 'id', [
            'label'    => 'ID',
        ]);
        $this->addElement('text', 'imageUrl', [
            'label'    => 'Image URL',
            'disabled' => (!$this->_isAllowedToEditFields) ? true : false,
            'maxlength'  => 255,
            'filters'    => ['StringTrim', 'StripTags'],
        ]);
        $this->addElement('text', 'imageFile', [
            'label'    => 'Upload Image',
            'disabled' => (!$this->_isAllowedToEditFields) ? true : false,
        ]);
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/sku-image-form.phtml']]]);
    }
}