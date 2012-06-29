<?php

/**
 * Adds support for the Twitter Bootstrap dropdown menus Javascript plugin
 * to the Zend_View_Helper_Navigation_Menu class.
 *
 * @author Michael Moussa <michael.moussa@gmail.com>
 */
class My_View_Helper_Navigation_Menu extends Zend_View_Helper_Navigation_Menu
{

    /**
     * Intercept renderMenu() call and apply custom Twitter Bootstrap class/id
     * attributes.
     *
     * @see Zend_View_Helper_Navigation_Menu::renderMenu()
     * @param Zend_Navigation_Container $container
     *            (Optional) The navigation container.
     * @param array $options
     *            (Optional) Options for controlling rendering.
     *            
     * @return string
     */
    public function renderMenu (Zend_Navigation_Container $container = null, array $options = array())
    {
        return $this->applyBootstrapClassesAndIds(parent::renderMenu($container, $options));
    }
    
    ///////////////////////////////////////////////////////////////////////////
    

    /**
     * Applies the custom Twitter Bootstrap dropdown class/id attributes where
     * necessary.
     *
     * @param string $html
     *            The HTML
     * @return string
     */
    protected function applyBootstrapClassesAndIds ($html)
    {
        if (strlen($html) === 0)
        {
            return $html;
        }
        $domDoc = new DOMDocument('1.0', 'utf-8');
        $domDoc->loadXML('<?xml version="1.0" encoding="utf-8"?>' . $html);
        
        $xpath = new DOMXPath($domDoc);
        /* @var $item DOMNode */
        foreach ( $xpath->query('//a[starts-with(@class, "dropdown")]') as $item )
        {
            
            $result = $xpath->query('../ul', $item);
            
            if ($result->length === 1)
            {                
                $ul = $result->item(0);
                $ul->setAttribute('class', 'dropdown-menu');
                
                $item->parentNode->setAttribute('id', substr($item->getAttribute('href'), 1));
                $item->parentNode->setAttribute('class', 'dropdown ' . $item->parentNode->getAttribute('class'));
                $item->setAttribute('href', '#');
                
                $item->setAttribute('data-toggle', 'dropdown');
                
                if (($existingClass = $item->getAttribute('class')) !== '')
                {
                    $item->setAttribute('class', $item->getAttribute('class') . ' dropdown-toggle');
                }
                else
                {
                    $item->setAttribute('class', 'dropdown-toggle');
                }
                
                if (strpos($item->getAttribute('class'), 'subdropdown'))
                {
                    $caret = $domDoc->createElement('b', '');
                    $caret->setAttribute('class', 'right-caret pull-right');
                    $item->insertBefore($caret, $item->childNodes->item(0));
                }
                else
                {
                    $caret = $domDoc->createElement('b', '');
                    $caret->setAttribute('class', 'caret');
                    $item->appendChild($caret);
                }
                
                
            }
        }
        
        
        return $domDoc->saveXML($xpath->query('/ul')
            ->item(0));
    }
}
