<?php

namespace MPSToolbox\Legacy\Controllers\Plugins;

use Zend_Controller_Action_HelperBroker;
use Zend_Controller_Plugin_Abstract;
use Zend_Controller_Request_Abstract;

/**
 * Class AddScriptPath
 *
 * This plugin adds the views folder in the application folder to the
 * stack so that we can keep things like form view helpers and
 * other reusable scripts in there and they will load successfully if they
 * are not found within the module they are being called from.14
 *
 * @package MPSToolbox\Legacy\Controllers\Plugins
 */
class AddScriptPath extends Zend_Controller_Plugin_Abstract
{
    /**
     * @param Zend_Controller_Request_Abstract $request
     *
     * @throws \Zend_Controller_Action_Exception
     */
    public function dispatchLoopStartup (Zend_Controller_Request_Abstract $request)
    {
        /* @var $viewRenderer \Zend_Controller_Action_Helper_ViewRenderer */
        /* @var $view \Zend_View */
        $viewRenderer = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer');
        $view         = $viewRenderer->view;
        $scriptPath   = sprintf('%s/views/scripts', APPLICATION_PATH);

        if ($view && file_exists($scriptPath))
        {
            $view->addScriptPath($scriptPath);
        }
    }
}