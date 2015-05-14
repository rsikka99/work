<?php

namespace Tangent;
use Zend_Application_Module_Bootstrap;

class ModuleBootstrap extends Zend_Application_Module_Bootstrap {

    /**
     * @Override
     * @return PluginLoader
     */
    public function getPluginLoader()
    {
        if ($this->_pluginLoader === null) {
            $options = array(
                'Zend_Application_Resource'  => 'Zend/Application/Resource',
                'ZendX_Application_Resource' => 'ZendX/Application/Resource'
            );

            $this->_pluginLoader = new PluginLoader($options);
        }

        return $this->_pluginLoader;
    }
}
