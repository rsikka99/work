<?php

/**
 * Class Hardwareoptimization_ViewModel_Situation
 */
class Hardwareoptimization_ViewModel_Situation implements JsonSerializable
{
    /**
     * @var float
     */
    public $weightedMonochromeCostPerPage;

    /**
     * @var float
     */
    public $weightedMonochromePageVolume;

    /**
     * @var float
     */
    public $weightedColorCostPerPage;

    /**
     * @var float
     */
    public $weightedColorPageVolume;

    /**
     * @var float
     */
    public $totalCost;

    /**
     * @var float
     */
    public $totalRevenue;

    /**
     * @var float
     */
    public $marginDollar;

    /**
     * @var float
     */
    public $marginPercent;

    /**
     * @return array
     */
    public function toArray ()
    {
        return [
            'weightedMonochromeCostPerPage' => $this->weightedMonochromeCostPerPage,
            'weightedMonochromePageVolume'  => $this->weightedMonochromePageVolume,
            'weightedColorCostPerPage'      => $this->weightedColorCostPerPage,
            'weightedColorPageVolume'       => $this->weightedColorPageVolume,
            'totalCost'                     => $this->totalCost,
            'totalRevenue'                  => $this->totalRevenue,
            'marginDollar'                  => $this->marginDollar,
            'marginPercent'                 => $this->marginPercent,
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