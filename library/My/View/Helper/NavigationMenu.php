<?php

class My_View_Helper_NavigationMenu extends Zend_View_Helper_Abstract
{

    /**
     * @param My_Navigation_Abstract $navigation
     *
     * @param array                  $parameters
     *
     * @return string
     */
    public function NavigationMenu ($navigation = null, $parameters = array())
    {
        $steps = $navigation->steps;
        $html  = array();
        if ($steps !== null)
        {
            $html [] = '<ul class="nav navbar-nav" id="navigationMenu">';

            foreach ($steps as $step)
            {
                if ($step->active)
                {
                    $html [] = "<li class='active'>";
                }
                else if ($step->canAccess)
                {
                    $html [] = "<li>";
                }
                else
                {
                    $html [] = "<li class='disabled'>";
                }

                // Get the url and name
                $url  = $this->view->url($parameters, $step->route);
                $name = $step->name;

                if ($step->canAccess)
                {
                    $html [] = "<a href='{$url}'>{$name}</a>";
                }
                else
                {
                    $html [] = "<a>{$name}</a>";
                }


                $html [] = "<li>";

                if ($step->nextStep !== null && !$step->canAccess)
                {
//                    break;
                }
            }
            $html [] = '</ul>';

            $html [] = sprintf('<p class="navbar-text navbar-right"><i class="fa fa-user fa-fw"></i> %1$s > <i class="fa fa-file-text fa-fw"></i> %2$s</p>', $navigation->clientName, $navigation->title);
        }

        return implode(PHP_EOL, $html);
    }
}