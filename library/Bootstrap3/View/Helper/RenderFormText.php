<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormText
 *
 * @author Lee Robert
 *
 */
class RenderFormText extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     * @param string             $prependAddOn
     * @param string             $appendAddOn
     *
     * @return string
     */
    public function RenderFormText (\Zend_Form_Element $element, $elementClasses = [], $prependAddOn = '', $appendAddOn = '')
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

            $hasAddOns = (strlen($prependAddOn) > 0 || strlen($appendAddOn) > 0);

            if ($hasAddOns)
            {
                $html[] = '<div class="input-group">';
                if (strlen($prependAddOn) > 0)
                {
                    $html[] = sprintf('<span class="input-group-addon">%1$s</span>', $prependAddOn);
                }
            }

            $type = 'text';
            if (in_array('type-email',$elementClasses)) $type='email';
            if (in_array('type-number',$elementClasses)) $type='number';
            $html [] = sprintf(
                '<input type="'.$type.'" name="%1$s" value="%2$s" title="%3$s" %4$s id="%5$s">',
                $element->getName(),
                $element->getValue(),
                $element->getDescription(),
                $serializedAttributes,
                $element->getId()
            );

            if ($hasAddOns)
            {
                if (strlen($appendAddOn) > 0)
                {
                    $html[] = sprintf('<span class="input-group-addon">%1$s</span>', $appendAddOn);
                }
                $html[] = '</div>';
            }

        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render %1$s as an &lt;input type="text"&gt;</div>', $elementType);
        }

        return implode('', $html);
    }
}
