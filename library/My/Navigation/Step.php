<?php

class My_Navigation_Step extends My_Model_Abstract
{
    /**
     * The previous step in a proposal
     *
     * @var My_Navigation_Step
     */
    public $previousStep = null;

    /**
     * The next step in a proposal
     *
     * @var My_Navigation_Step
     */
    public $nextStep = null;

    /**
     * The name of the step
     *
     * @var string
     */
    public $name;

    /**
     * The route name of the step
     *
     * @var string
     */
    public $route;

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

        if (isset($params->route) && !is_null($params->route))
        {
            $this->route = $params->route;
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
        return [
            "previousStep" => $this->previousStep,
            "nextStep"     => $this->nextStep,
            "name"         => $this->name,
            "route"        => $this->route,
            "active"       => $this->active,
            "canAccess"    => $this->canAccess,
            "enumValue"    => $this->enumValue,
        ];
    }
}