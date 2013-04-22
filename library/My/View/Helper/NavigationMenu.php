<?php
class My_View_Helper_NavigationMenu extends Zend_View_Helper_Abstract
{

    /**
     * @param My_Navigation_Step[] $steps
     *
     * @return string
     */
    public function NavigationMenu ($steps = null)
    {
        $html = array();
        if ($steps !== null)
        {
            $html [] = '<ul class="nav nav-tabs" id="navigationMenu">';

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
                $url  = $this->view->url(array(
                                              'controller' => $step->controller,
                                              'action'     => $step->action
                                         ));
                $name = $step->name;

                if ($step->canAccess)
                {
                    $html [] = "<a href='{$url}'>{$name}</a>";
                }
                else
                {
                    $html [] = "<p>{$name}</p>";
                }


                $html [] = "<li>";

                if ($step->nextStep !== null && !$step->canAccess)
                {
//                    break;
                }
            }
            $html [] = '</ul>';
        }

        return implode(PHP_EOL, $html);
    }
}