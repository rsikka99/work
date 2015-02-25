<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormPassword
 *
 * @author Lee Robert
 *
 */
class RenderFormPassword extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param array              $elementClasses
     * @param string             $prependAddOn
     * @param string             $appendAddOn
     *
     * @return string
     */
    public function RenderFormPassword (\Zend_Form_Element $element, $elementClasses = [], $prependAddOn = '', $appendAddOn = '')
    {
        $html = [];
        if ($element instanceof \Zend_Form_Element_Password)
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

            $html [] = sprintf('<input type="password" name="%1$s" %2$s>', $element->getName(), $serializedAttributes);

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
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render %1$s as an &lt;input type="password"&gt;</div>', $elementType);
        }

        return implode('', $html);
    }
}
