<?php

use Zend\Form;

class My_Form_Form extends Zend_Form
{

    protected function addMyOptions(&$element, &$options) {
        if ($element == 'text_currency') {
            if (empty($options['validators'])) $options['validators'] = ['Float'];
            if (empty($options['filters'])) $options['filters'] = ['StringTrim', 'StripTags', 'LocalizedToNormalized'];
            $element = 'text';
        }
        if ($element == 'text_float') {
            if (empty($options['validators'])) $options['validators'] = ['Float'];
            if (empty($options['filters'])) $options['filters'] = ['StringTrim', 'StripTags', 'LocalizedToNormalized'];
            $element = 'text';
        }
        if ($element == 'text_int') {
            if (empty($options['validators'])) $options['validators'] = ['Int'];
            if (empty($options['filters'])) $options['filters'] = ['StringTrim', 'StripTags', 'LocalizedToNormalized'];
            $element = 'text';
        }
    }

    public function addElement($element, $name = null, $options = null)
    {
        if (is_string($element)) $this->addMyOptions($element, $options);
        return parent::addElement($element, $name, $options);
    }

    public function createElement($type, $name, $options = null)
    {
        if (is_string($type)) $this->addMyOptions($type, $options);
        return parent::createElement($type, $name, $options);
    }

}