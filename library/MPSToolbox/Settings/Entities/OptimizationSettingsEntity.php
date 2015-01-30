<?php

namespace MPSToolbox\Settings\Entities;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\TonerVendorRankingSetMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerVendorRankingSetModel;

/**
 * Class OptimizationSettingsEntity
 *
 * @package MPSToolbox\Settings\Entities
 *
 * @property int   id
 * @property float optimizedTargetMonochromeCostPerPage
 * @property float optimizedTargetColorCostPerPage
 * @property float costThreshold
 * @property float lossThreshold
 * @property bool  autoOptimizeFunctionality
 * @property float blackToColorRatio
 * @property int   monochromeTonerVendorRankingSetId
 * @property int   colorTonerVendorRankingSetId
 * @property int   minimumPageCount
 */
class OptimizationSettingsEntity extends EloquentModel
{
    protected $table      = 'optimization_settings';
    public    $timestamps = false;

    protected $monochromeRankSet;
    protected $colorRankSet;

    /**
     * @return TonerVendorRankingSetModel
     */
    public function getMonochromeRankSet ()
    {
        if (!isset($this->monochromeRankSet))
        {
            if ($this->monochromeTonerVendorRankingSetId > 0)
            {
                $this->monochromeRankSet = TonerVendorRankingSetMapper::getInstance()->find($this->monochromeTonerVendorRankingSetId);
            }
            else
            {
                $this->monochromeRankSet                 = new TonerVendorRankingSetModel();
                $this->monochromeTonerVendorRankingSetId = TonerVendorRankingSetMapper::getInstance()->insert($this->monochromeRankSet);
                $this->save();
            }
        }

        return $this->monochromeRankSet;
    }

    /**
     * @return TonerVendorRankingSetModel
     */
    public function getColorRankSet ()
    {
        if (!isset($this->colorRankSet))
        {
            if ($this->colorTonerVendorRankingSetId > 0)
            {
                $this->colorRankSet = TonerVendorRankingSetMapper::getInstance()->find($this->colorTonerVendorRankingSetId);
            }
            else
            {
                $this->colorRankSet                 = new TonerVendorRankingSetModel();
                $this->colorTonerVendorRankingSetId = TonerVendorRankingSetMapper::getInstance()->insert($this->colorRankSet);
                $this->save();
            }
        }

        return $this->colorRankSet;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getOptimizedTargetMonochromeCostPerPageAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getOptimizedTargetColorCostPerPageAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getCostThresholdAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getLossThresholdAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * Accessor to remove trailing 0's on decimals
     *
     * @param $value
     *
     * @return float
     */
    public function getBlackToColorRatioAttribute ($value)
    {
        return ($value !== null) ? floatval($value) : null;
    }

    /**
     * This overridden version will create new objects for toner vendor preferences instead of cloning the relationship to them
     *
     * Clone the model into a new, non-existing instance.
     *
     * @param  array $except
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function replicate (array $except = null)
    {
        $except = $except
            ?: [
                $this->getKeyName(),
                $this->getCreatedAtColumn(),
                $this->getUpdatedAtColumn(),
            ];

        $attributes = array_except($this->attributes, $except);

        with($instance = new static)->setRawAttributes($attributes);

        $tonerVendorRankingMapper    = TonerVendorRankingMapper::getInstance();
        $tonerVendorRankingSetMapper = TonerVendorRankingSetMapper::getInstance();

        /**
         * Clone monochrome toner rank set
         */
        if ($this->monochromeTonerVendorRankingSetId > 0)
        {
            $originalMonochromeRankSet                   = $this->getMonochromeRankSet();
            $instance->monochromeRankSet                 = new TonerVendorRankingSetModel();
            $instance->monochromeTonerVendorRankingSetId = $tonerVendorRankingSetMapper->insert($instance->monochromeRankSet);

            $newMonochromeRankings = [];
            foreach ($originalMonochromeRankSet->getRankings() as $ranking)
            {
                $newMonochromeRanking                          = new TonerVendorRankingModel($ranking->toArray());
                $newMonochromeRanking->tonerVendorRankingSetId = $instance->monochromeRankSet->id;
                $tonerVendorRankingMapper->save($newMonochromeRanking);
                $newMonochromeRankings[] = $newMonochromeRanking;
            }
            $instance->monochromeRankSet->setRankings($newMonochromeRankings);

        }

        /**
         * Clone color toner rank set
         */
        if ($this->colorTonerVendorRankingSetId > 0)
        {
            $originalColorRankSet                   = $this->getColorRankSet();
            $instance->colorRankSet                 = new TonerVendorRankingSetModel();
            $instance->colorTonerVendorRankingSetId = $tonerVendorRankingSetMapper->insert($instance->colorRankSet);

            $newColorRankings = [];
            foreach ($originalColorRankSet->getRankings() as $ranking)
            {
                $newColorRanking                          = new TonerVendorRankingModel($ranking->toArray());
                $newColorRanking->tonerVendorRankingSetId = $instance->colorRankSet->id;
                $tonerVendorRankingMapper->save($newColorRanking);
                $newColorRankings[] = $newColorRanking;
            }
            $instance->colorRankSet->setRankings($newColorRankings);

        }

        // Disabled setRelations since we don't want to copy those
        // $instance->setRelations($this->relations);
        return $instance;
    }
}