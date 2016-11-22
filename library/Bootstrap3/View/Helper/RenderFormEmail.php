<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormEmail
 *
 * @author Lee Robert
 *
 */
class RenderFormEmail extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     * @param string             $prependAddOn
     * @param string             $appendAddOn
     *
     * @return string
     */
    public function RenderFormEmail (\Zend_Form_Element $element, $elementClasses = [], $prependAddOn = '', $appendAddOn = '')
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

            $html [] = sprintf('<input type="email" name="%1$s" value="%2$s" %3$s>',
                $element->getName(),
                htmlentities($element->getValue(), ENT_QUOTES, 'UTF-8'),
                $serializedAttributes
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
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render %1$s as an &lt;input type="email"&gt;</div>', $elementType);
        }

        return implode('', $html);
    }
}
