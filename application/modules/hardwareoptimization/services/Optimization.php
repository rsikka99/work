<?php

/**
 * Class Hardwareoptimization_Service_Optimization
 */
class Hardwareoptimization_Service_Optimization
{
    /**
     * @var \MPSToolbox\Legacy\Modules\HardwareOptimization\Models\OptimizationAbstractModel
     */
    protected $optimization;

    /**
     * @param \MPSToolbox\Legacy\Modules\HardwareOptimization\Models\OptimizationAbstractModel $optimization
     */
    public function __construct ($optimization)
    {
        $this->optimization = $optimization;
    }

    public function createOptimizationViewModel ()
    {
        $optimizationViewModel = new Hardwareoptimization_ViewModel_Optimization();
        $settings              = new Hardwareoptimization_ViewModel_Settings();
        $currentSituation      = new Hardwareoptimization_ViewModel_Situation();
        $optimizedSituation    = new Hardwareoptimization_ViewModel_Situation();
    }
}