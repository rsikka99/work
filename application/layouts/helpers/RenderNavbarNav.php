<?php

/**
 * App_View_Helper_RenderNavbarNav
 *
 * @author Lee Robert
 *
 */
class App_View_Helper_RenderNavbarNav extends Zend_View_Helper_Abstract
{
    /**
     * @param Zend_Navigation|Zend_Navigation_Page|Zend_Navigation_Page[] $container
     * @param string                                                      $navClass
     * @param int                                                         $maxDepth
     *
     * @return string
     */
    public function RenderNavbarNav ($container=null , $navClass = "nav navbar-nav", $maxDepth = 2)
    {
        $html = [];
        if (!empty($container)) {
            $html[]  = sprintf('<ul class="%s">', $navClass);
            $html[]  = $this->_render_nav($container, $maxDepth);
            $html [] = '</ul>';
        }

        return implode(PHP_EOL, $html);
    }

    protected static $incompleteDevices = null;
    protected static $incompleteClientSettings = null;
    protected static $incompleteDealerSettings = null;

    public static function getIncompleteDevices() {
        if (self::$incompleteDevices === null) {
            $s = new \MPSToolbox\Services\RmsDeviceInstanceService();
            self::$incompleteDevices = count($s->getIncomplete());
        }
        return self::$incompleteDevices;
    }
    public static function getIncompleteClientSettings() {
        if (self::$incompleteClientSettings === null) {
            self::$incompleteClientSettings = 0;
            $dealerId = \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId();
            $clients = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->fetchAll(["dealerId=?" => $dealerId]);
            foreach ($clients as $client) {
                $contact = \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ContactMapper::getInstance()->getContactByClientId($client->id);
                if (empty($contact->email)) {
                    self::$incompleteClientSettings++;
                }
            }
        }
        return self::$incompleteClientSettings;
    }
    public static function getIncompleteDealerSettings() {
        if (!isset(self::$incompleteDealerSettings)) {
            self::$incompleteDealerSettings = 0;
            $settings = \MPSToolbox\Settings\Entities\DealerSettingsEntity::getDealerSettings();
            if (empty($settings->shopSettings->emailFromAddress) || empty($settings->shopSettings->emailFromName)) {
                self::$incompleteDealerSettings = 1;
            }
        }
        return self::$incompleteDealerSettings;
    }


    /**
     * Recursive function to render a nav
     *
     * @param Zend_Navigation_Page[] $pages
     * @param int                    $depth
     *
     * @return string
     */
    protected function _render_nav ($pages, $depth)
    {
        $html = [];
        foreach ($pages as $page)
        {
            if ($page->isVisible())
            {
                $dropdownContents = "";
                $hasDropDown      = false;
                if ($page->hasPages() && $depth > 1)
                {
                    $dropdownContents = $this->_render_nav($page, $depth - 1, $html);
                    $hasDropDown      = (strlen($dropdownContents) > 0);
                }

                $pageClasses = $page->getClass();

                if ($page->isActive())
                {
                    $pageClasses .= ' active';
                }

                if ($hasDropDown)
                {
                    $pageClasses .= ' dropdown';
                }

                if ($page->get('divider-above'))
                {
                    $html[] = '<li class="divider"></li>';
                }

                $html[] = sprintf('<li class="%s">', $pageClasses);

                $icon = ($page->get('icon')) ? sprintf('<i class="%s"></i> ', $page->get('icon')) : '';

                $label = $page->getLabel();
                if ($label=='E-commerce') {
                    $n = $this->getIncompleteDevices() + $this->getIncompleteDealerSettings() + $this->getIncompleteClientSettings();
                    if ($n>0) {
                        $label .= '<span class="span-todo" id="todo-all" data-item="all" data-n="'.$n.'"></span>';
                    }
                }
                if ($label=='Client Settings') {
                    $n = $this->getIncompleteClientSettings();
                    if ($n>0) {
                        $label .= '<span class="span-todo" id="todo-client" data-item="client" data-n="'.$n.'"></span>';
                    }
                }
                if ($label=='Dealer Settings') {
                    $n = $this->getIncompleteDealerSettings();
                    if ($n>0) {
                        $label .= '<span class="span-todo" id="todo-dealer" data-item="dealer" data-n="'.$n.'"></span>';
                    }
                }
                if ($label=='Device Settings') {
                    $n = $this->getIncompleteDevices();
                    if ($n>0) {
                        $label .= '<span class="span-todo" id="todo-device" data-item="device" data-n="'.$n.'"></span>';
                    }
                }

                if ($hasDropDown)
                {
                    $html[] = sprintf('<a href="#" class="dropdown-toggle" data-toggle="dropdown">%s %s <b class="caret"></b></a>', $icon, $label);
                }
                else
                {
                    $html[] = sprintf('<a href="%s">%s %s</a>', $page->getHref(), $icon, $label);
                }

                if (strlen($dropdownContents) > 0)
                {
                    $html[] = '<ul class="dropdown-menu">';
                    $html[] = $dropdownContents;
                    $html[] = '</ul>';
                }


                $html[] = '</li>';

                if ($page->get('divider-below'))
                {
                    $html[] = '<li class="divider"></li>';
                }
            }
        }

        return implode(PHP_EOL, $html);
    }
}
