<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormText
 *
 * @author Lee Robert
 *
 */
class RenderFormCurrency extends RenderFormAbstract
{

    public function RenderFormCurrency(\Zend_Form_Element $element, $elementClasses = [], $prependAddOn = '', $appendAddOn = '')
    {
        $html = [];
        if ($element instanceof \Zend_Form_Element_Text)
        {
            if (!is_array($elementClasses))
            {
                $elementClasses = [$elementClasses];
            }
            array_unshift($elementClasses, 'form-control');

            if ($element->hasErrors())
            {
                $elementClasses[] = 'has-error';
            }

            $description = '';
            if (strlen($element->getDescription()) > 0)
            {
                $elementClasses[] = 'js-input-tooltip';
            }

            $attributes = $element->getAttribs();
            if ($element->isRequired())
            {
                $attributes['required'] = 'true';
            }

            /**
             *  Prep attributes
             */
            $attributes['class']  = $this->processClasses($attributes, $elementClasses);
            $serializedAttributes = $this->serializeAttributes($attributes, ['options']);
            $element->setAttribs($attributes);

            $html[] = '<div class="input-group">';
            $html[] = sprintf('<span class="input-group-addon">%1$s</span>', \MPSToolbox\Services\CurrencyService::getSymbol());

            $type = 'text';
            if (in_array('type-email',$elementClasses)) $type='email';
            if (in_array('type-number',$elementClasses)) $type='number';
            $html [] = sprintf(
                '<input type="'.$type.'" name="%1$s" value="%2$s" title="%3$s" %4$s id="%5$s">',
                $element->getName(),
                number_format($element->getValue(),2,'.',''),
                $element->getDescription(),
                $serializedAttributes,
                $element->getId()
            );

            $html[] = '</div>';
        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render %1$s as an &lt;input type="text"&gt;</div>', $elementType);
        }

        return implode('', $html);
    }
}
