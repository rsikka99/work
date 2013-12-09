<?php

class My_View_Helper_Theme extends Zend_View_Helper_Abstract
{
    protected $theme;

    /**
     * Returns the path to a theme resource that includes base url
     *
     * @param string $resource
     * @param bool   $returnFilesystemPath
     *
     * @return string
     */
    public function Theme ($resource, $returnFilesystemPath = false)
    {
        if (is_null($this->theme))
        {
            $this->theme = Zend_Registry::get('config')->app->theme;
        }
        $resource = trim($resource, "/");

        $path = "/themes/" . $this->theme . "/$resource";

        /**
         * If we asked for just the filesystem path then return it instead of the url version
         */
        if ($returnFilesystemPath)
        {
            return PUBLIC_PATH . $path;
        }

        return $this->view->baseUrl($path);
    }
}