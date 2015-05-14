<?php

namespace Tangent;
use Zend_Loader_PluginLoader;

class PluginLoader extends Zend_Loader_PluginLoader {

    /**
     * @Override
     * @param string $name
     * @param bool $throwExceptions
     * @return false|string
     * @throws \Zend_Loader_PluginLoader_Exception
     */
    public function load($name, $throwExceptions = true)
    {
        $name = $this->_formatName($name);
        if ($this->isLoaded($name)) {
            return $this->getClassName($name);
        }

        if ($this->_useStaticRegistry) {
            $registry = self::$_staticPrefixToPaths[$this->_useStaticRegistry];
        } else {
            $registry = $this->_prefixToPaths;
        }

        $registry = array_reverse($registry, true);
        foreach ($registry as $prefix => $paths) {
            $className = $prefix . $name;

            if (class_exists($className, false)) {
                if ($this->_useStaticRegistry) {
                    self::$_staticLoadedPlugins[$this->_useStaticRegistry][$name]     = $className;
                } else {
                    $this->_loadedPlugins[$name]     = $className;
                }
                return $className;
            }
        }

        return parent::load($name, $throwExceptions);
    }

}