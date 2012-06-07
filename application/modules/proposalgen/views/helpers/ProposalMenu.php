<?php

class Proposalgen_View_Helper_ProposalMenu extends Zend_View_Helper_Abstract
{

    /**
     * Returns application settings
     */
    public function ProposalMenu ($reportSteps = null)
    {
        $html = array ();
        if ($reportSteps !== null)
        {
            $html [] = '<ul class="nav nav-pills">';
            /* @var $step Proposalgen_Model_Report_Step */
            $groupCount = 0;
            $lastGroup = "";
            foreach ( $reportSteps as $step )
            {
                if ($step->getActive())
                {
                    $html [] = "<li class='active'>";
                }
                else
                {
                    $html [] = "<li class='disabled'>";
                }
                
                // Get the url and name
                $url = $this->view->url(array (
                        'controller' => $step->getController(), 
                        'action' => $step->getAction() 
                ));
                $name = $step->getName();
                
                if ($step->getCanAccess())
                {
                    $html [] = "<a href='{$url}'>{$name}</a>";
                }
                else
                {
                    //$html [] = "<a href='{$url}'>{$name}</a>";
                    $html [] = "<p>{$name}</p>";
                }
                
                
                $html [] = "<li>";
                
                if ($step->getNextStep() !== null && ! $step->getCanAccess())
                {
                    //break;
                }
            }
            $html [] = '</ul>';
        }
        return implode(PHP_EOL, $html);
    }
}