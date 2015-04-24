<?php

abstract class My_Navigation_Abstract
{
    /**
     * @var My_Navigation_Step[]
     */
    public $steps;

    /**
     * @var My_Navigation_Step
     */
    public $activeStep;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $clientName;

    /**
     * Sets which steps are accessible
     *
     * @param string $stepName The last step that is accessible
     */
    public function updateAccessibleSteps ($stepName)
    {
        $canAccess = true;
        /* @var $step My_Navigation_Step */
        foreach ($this->steps as $step)
        {
            $step->canAccess = $canAccess;

            if (strcasecmp($step->enumValue, $stepName) === 0)
            {
                $canAccess = false;
            }
        }
    }

    public function setActiveStep ($stepName)
    {
        $this->activeStep = null;
        foreach ($this->steps as $step)
        {
            $step->active = false;
            if (strcasecmp($step->enumValue, $stepName) === 0)
            {
                $this->activeStep = $step;
                $step->active     = true;
                break;
            }
        }
    }

    /**
     * Creates the linked list for navigation
     *
     * @param array $steps
     */
    protected function _setNewSteps ($steps)
    {
        $previousStep = null;
        $currentStep  = null;
        $this->steps  = [];

        // Add Steps: WARNING, logic here will mess with your head.
        foreach ($steps as $stepName => $step)
        {
            // Move the old current step to be the previous step.
            $previousStep = $currentStep;

            // Create our new step
            $currentStep            = new My_Navigation_Step($step);
            $currentStep->enumValue = $stepName;

            // Set the previous step of our current step
            if ($previousStep instanceof My_Navigation_Step)
            {
                $currentStep->previousStep = $previousStep;

                // Add the current step as the last step's next step.
                $previousStep->nextStep = $currentStep;
            }

            $this->steps [] = $currentStep;
        }
    }
}