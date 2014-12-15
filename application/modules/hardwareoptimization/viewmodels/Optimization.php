<?php

/**
 * Class Hardwareoptimization_ViewModel_Optimization
 */
class Hardwareoptimization_ViewModel_Optimization implements JsonSerializable
{
    /**
     * @var Hardwareoptimization_ViewModel_Situation
     */
    public $currentSituation;

    /**
     * @var Hardwareoptimization_ViewModel_Situation
     */
    public $optimizedSituation;

    /**
     * @var Hardwareoptimization_ViewModel_Settings
     */
    public $settings;

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            'settings'           => $this->settings,
            'currentSituation'   => $this->currentSituation,
            'optimizedSituation' => $this->optimizedSituation,

        ];
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *       which is a value of any type other than a resource.
     */
    function jsonSerialize ()
    {
        return $this->toArray();
    }
}