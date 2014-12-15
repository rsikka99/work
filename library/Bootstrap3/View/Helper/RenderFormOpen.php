<?php

namespace Bootstrap3\View\Helper;

use Zend_Form;
use Zend_Form_Element_File;

/**
 * RenderFormOpen
 *
 * @author Lee Robert
 *
 */
class RenderFormOpen extends RenderFormAbstract
{

    /**
     * @param \Zend_Form $form
     * @param array      $formClasses
     *
     * @return string
     */
    public function RenderFormOpen (\Zend_Form $form, $formClasses = array())
    {
        $html = array();
        if ($form instanceof \Zend_Form)
        {
            if (!is_array($formClasses))
            {
                $formClasses = array($formClasses);
            }

            /**
             * Get the form attributes
             */
            $attributes = $form->getAttribs();

            if (!isset($attributes['class']))
            {
                $attributes['class'] = $formClasses;
            }
            else
            {
                if (is_array($attributes['class']))
                {
                    $attributes['class'] = array_merge($attributes['class'], $formClasses);
                }
                else
                {
                    $attributes['class'] = array_merge(array($attributes['class']), $formClasses);
                }
            }

            $attributes['class'] = implode(' ', $attributes['class']);


            foreach ($form->getElements() as $element)
            {
                if ($element instanceof Zend_Form_Element_File)
                {
                    $form->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
                    break;
                }
            }

            $attributeString = "";
            foreach ($attributes as $attrName => $attrValue)
            {
                if ($attrName != 'options' && $attrValue)
                {
                    $attributeString .= ' ' . $attrName . '="' . $attrValue . '"';
                }
            }

            $html[] = sprintf('<form%1$s enctype="%2$s">', $attributeString, $form->getEnctype());
        }
        else
        {
            $elementType = get_class($form);
            $html[]      = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render the &lt;form&gt; for a "%1$s"</div>', $elementType);
            $html[]      = '<form>';
        }

        return implode('', $html);
    }
}
