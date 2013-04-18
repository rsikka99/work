<?php
class My_Navigation_Step extends My_Model_Abstract
{
    /**
     * The previous step in a proposal
     *
     * @var Proposalgen_Model_Assessment_Step
     */
    public $previousStep = null;

    /**
     * The next step in a proposal
     *
     * @var Proposalgen_Model_Assessment_Step
     */
    public $nextStep = null;

    /**
     * The name of the step
     *
     * @var string
     */
    public $name;

    /**
     * The module that the step is on
     *
     * @var string
     */
    public $module;

    /**
     * The controller that the step is on
     *
     * @var string
     */
    public $controller;

    /**
     * The action that the step is on
     *
     * @var string
     */
    public $action;

    /**
     * Is the step active?
     *
     * @var string
     */
    public $active = false;

    /**
     * Is the step active?
     *
     * @var string
     */
    public $canAccess = false;

    /**
     * The value of the step in the database
     *
     * @var string
     */
    public $enumValue;

    /**
     * @param array $params An array of data to populate the model with
     */
    public function populate ($params)
    {
        if (is_array($params))
        {
            $params = new ArrayObject($params, ArrayObject::ARRAY_AS_PROPS);
        }

        if (isset($params->previousStep) && !is_null($params->previousStep))
        {
            $this->previousStep = $params->previousStep;
        }

        if (isset($params->nextStep) && !is_null($params->nextStep))
        {
            $this->nextStep = $params->nextStep;
        }

        if (isset($params->name) && !is_null($params->name))
        {
            $this->name = $params->name;
        }

        if (isset($params->module) && !is_null($params->module))
        {
            $this->module = $params->module;
        }

        if (isset($params->controller) && !is_null($params->controller))
        {
            $this->controller = $params->controller;
        }

        if (isset($params->action) && !is_null($params->action))
        {
            $this->action = $params->action;
        }

        if (isset($params->active) && !is_null($params->active))
        {
            $this->active = $params->active;
        }

        if (isset($params->canAccess) && !is_null($params->canAccess))
        {
            $this->canAccess = $params->canAccess;
        }

        if (isset($params->enumValue) && !is_null($params->enumValue))
        {
            $this->enumValue = $params->enumValue;
        }

    }

    /**
     * @return array
     */
    public function toArray ()
    {
        return array(
            "previousStep" => $this->previousStep,
            "nextStep"     => $this->nextStep,
            "name"         => $this->name,
            "module"       => $this->module,
            "controller"   => $this->controller,
            "action"       => $this->action,
            "active"       => $this->active,
            "canAccess"    => $this->canAccess,
            "enumValue"    => $this->enumValue,
        );
    }
}