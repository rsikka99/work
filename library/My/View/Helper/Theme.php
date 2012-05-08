<?php

class Zend_View_Helper_Theme extends Zend_View_Helper_Abstract
{

    protected $theme;

    /**
     * Returns the path to a theme resource that includes baseurl
     *
     * @param $resource string           
     */
    public function Theme ($resource)
    {
        if (is_null($this->theme)) {
            $this->theme = Zend_Registry::get('config')->app->theme;
        }
        return $this->view->baseUrl("/themes/" . $this->theme . "/$resource");
    }
}