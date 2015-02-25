<?php

namespace Bootstrap3\View\Helper;

/**
 * RenderFormDescription
 *
 * @author Lee Robert
 *
 */
class RenderFormDescription extends RenderFormAbstract
{

    /**
     * @param \Zend_Form_Element $element
     * @param string             $class
     *
     * @return string
     */
    public function RenderFormDescription (\Zend_Form_Element $element, $class = "help-block")
    {
        $html = [];
        if ($element instanceof \Zend_Form_Element)
        {
            if (strlen($element->getDescription()) > 0)
            {
                $html[] = sprintf('<span class="%1$s">%2$s</span>', $class, $element->getDescription());
            }

        }
        else
        {
            $elementType = get_class($element);
            $html []     = sprintf('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Tried to render the &lt;errors&gt; for a "%1$s"</div>', $elementType);
        }

        return implode('', $html);
    }
}
