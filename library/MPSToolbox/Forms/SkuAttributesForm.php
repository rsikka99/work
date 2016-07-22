<?php

namespace MPSToolbox\Forms;

use My_Brand;
use My_Validate_DateTime;
use Zend_Form;

class SkuAttributesForm extends \My_Form_Form
{

    protected $properties;

    protected function arrToMulti($arr) {
        $result = [];
        foreach ($arr as $value) $result[$value] = $value;
        return $result;
    }

    protected $_isAllowedToEditFields = false;

    public function __construct (array $properties, $options = null, $isAllowedToEditFields = false)
    {
        $this->properties = $properties;

        $this->_isAllowedToEditFields = $isAllowedToEditFields;
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

        parent::__construct($options);
    }

    public function init ()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'skuAttributes');

        foreach ($this->properties as $property) {
            $el = $this->createElement($property['type'], $property['name'], $property['attributes']);
            if (!$this->_isAllowedToEditFields) $el->setAttrib('disabled', true);
            $this->addElement($el);
        }
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/sku-attributes-form.phtml']]]);
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

}
