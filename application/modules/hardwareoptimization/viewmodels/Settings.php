<?php

/**
 * Class Hardwareoptimization_ViewModel_Settings
 */
class Hardwareoptimization_ViewModel_Settings implements JsonSerializable
{
    /**
     * @var float
     */
    public $costThreshold;

    /**
     * @var bool
     */
    public $functionalityUpgradeEnabled;

    /**
     * @var float
     */
    public $lossThreshold;

    /**
     * @var float
     */
    public $optimizedTargetMonochromeCostPerPage;

    /**
     * @var float
     */
    public $optimizedTargetColorCostPerPage;

    /**
     * @var float
     */
    public $targetMonochromeCostPerPage;

    /**
     * @var float
     */
    public $targetColorCostPerPage;


    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            'costThreshold'                        => $this->costThreshold,
            'functionalityUpgradeEnabled'          => $this->functionalityUpgradeEnabled,
            'lossThreshold'                        => $this->lossThreshold,
            'optimizedTargetMonochromeCostPerPage' => $this->optimizedTargetMonochromeCostPerPage,
            'optimizedTargetColorCostPerPage'      => $this->optimizedTargetColorCostPerPage,
            'targetMonochromeCostPerPage'          => $this->targetMonochromeCostPerPage,
            'targetColorCostPerPage'               => $this->targetColorCostPerPage,
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