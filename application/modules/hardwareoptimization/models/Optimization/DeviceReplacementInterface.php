<?php

/**
 * Interface Hardwareoptimization_Model_Optimization_DeviceReplacementInterface
 */
interface Hardwareoptimization_Model_Optimization_DeviceReplacementInterface
{
    /**
     * @param Proposalgen_Model_DeviceInstance $deviceInstance
     *
     * @return Proposalgen_Model_MasterDevice
     */
    public function findReplacement ($deviceInstance);
}