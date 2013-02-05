<?php
class Proposalgen_View_Helper_ProposalMenu extends Zend_View_Helper_Abstract
{

    /**
     * @param Proposalgen_Model_Report_Step[] $reportSteps
     *
     * @return string
     */
    public function ProposalMenu ($reportSteps = null)
    {
        $html = array();
        if ($reportSteps !== null)
        {
            $html [] = '<ul class="nav nav-pills assessmentMenu">';

            foreach ($reportSteps as $step)
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