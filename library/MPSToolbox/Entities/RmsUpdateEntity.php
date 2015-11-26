<?php

namespace MPSToolbox\Entities;

/**
 * Class RmsUpdateEntity
 *
 * @Entity
 * @Table(name="rms_update")
 */
class RmsUpdateEntity extends BaseEntity {

    /**
     * @Id
     * @OneToOne(targetEntity="RmsDeviceInstanceEntity")
     * @JoinColumn(name="rmsDeviceInstanceId", referencedColumnName="id")
     */
    private $rmsDeviceInstance;

    /** @Column(type="boolean") */
    private $isColor;

    /** @Column(type="integer") */
    private $tonerLevelBlack;
    /** @Column(type="integer") */
    private $tonerLevelCyan;
    /** @Column(type="integer") */
    private $tonerLevelMagenta;
    /** @Column(type="integer") */
    private $tonerLevelYellow;

    /** @Column(type="float") */
    private $pageCoverageMonochrome;
    /** @Column(type="float") */
    private $pageCoverageCyan;
    /** @Column(type="float") */
    private $pageCoverageMagenta;
    /** @Column(type="float") */
    private $pageCoverageYellow;
    /** @Column(type="float") */
    private $pageCoverageColor;

    /** @Column(type="datetime") */
    private $monitorStartDate;
    /** @Column(type="datetime") */
    private $monitorEndDate;
    /** @Column(type="integer") */
    private $startMeterBlack;
    /** @Column(type="integer") */
    private $endMeterBlack;
    /** @Column(type="integer") */
    private $startMeterColor;
    /** @Column(type="integer") */
    private $endMeterColor;

    /** @var array - transient */
    private $daysLeft = array();

    /**
     * @return array
     */
    public function getDaysLeft($colorId)
    {
        return isset($this->daysLeft[$colorId]) ? $this->daysLeft[$colorId] : null;
    }

    /**
     * @param array $daysLeft
     */
    public function setDaysLeft($daysLeft)
    {
        $this->daysLeft = $daysLeft;
    }

    /**
     * @return RmsDeviceInstanceEntity
     */
    public function getRmsDeviceInstance()
    {
        return $this->rmsDeviceInstance;
    }

    /**
     * @param mixed $rmsDeviceInstance
     */
    public function setRmsDeviceInstance($rmsDeviceInstance)
    {
        $this->rmsDeviceInstance = $rmsDeviceInstance;
    }

    /**
     * @return mixed
     */
    public function getIsColor()
    {
        return $this->isColor;
    }

    /**
     * @param mixed $isColor
     */
    public function setIsColor($isColor)
    {
        $this->isColor = $isColor;
    }

    /**
     * @return mixed
     */
    public function getTonerLevelBlack()
    {
        return $this->tonerLevelBlack;
    }

    /**
     * @param mixed $tonerLevelBlack
     */
    public function setTonerLevelBlack($tonerLevelBlack)
    {
        $this->tonerLevelBlack = $tonerLevelBlack;
    }

    /**
     * @return mixed
     */
    public function getTonerLevelCyan()
    {
        return $this->tonerLevelCyan;
    }

    /**
     * @param mixed $tonerLevelCyan
     */
    public function setTonerLevelCyan($tonerLevelCyan)
    {
        $this->tonerLevelCyan = $tonerLevelCyan;
    }

    /**
     * @return mixed
     */
    public function getTonerLevelMagenta()
    {
        return $this->tonerLevelMagenta;
    }

    /**
     * @param mixed $tonerLevelMagenta
     */
    public function setTonerLevelMagenta($tonerLevelMagenta)
    {
        $this->tonerLevelMagenta = $tonerLevelMagenta;
    }

    /**
     * @return mixed
     */
    public function getTonerLevelYellow()
    {
        return $this->tonerLevelYellow;
    }

    /**
     * @param mixed $tonerLevelYellow
     */
    public function setTonerLevelYellow($tonerLevelYellow)
    {
        $this->tonerLevelYellow = $tonerLevelYellow;
    }

    /**
     * @return mixed
     */
    public function getPageCoverageMonochrome()
    {
        return $this->pageCoverageMonochrome;
    }

    /**
     * @param mixed $pageCoverageMonochrome
     */
    public function setPageCoverageMonochrome($pageCoverageMonochrome)
    {
        $this->pageCoverageMonochrome = $pageCoverageMonochrome;
    }

    /**
     * @return mixed
     */
    public function getPageCoverageCyan()
    {
        return $this->pageCoverageCyan;
    }

    /**
     * @param mixed $pageCoverageCyan
     */
    public function setPageCoverageCyan($pageCoverageCyan)
    {
        $this->pageCoverageCyan = $pageCoverageCyan;
    }

    /**
     * @return mixed
     */
    public function getPageCoverageMagenta()
    {
        return $this->pageCoverageMagenta;
    }

    /**
     * @param mixed $pageCoverageMagenta
     */
    public function setPageCoverageMagenta($pageCoverageMagenta)
    {
        $this->pageCoverageMagenta = $pageCoverageMagenta;
    }

    /**
     * @return mixed
     */
    public function getPageCoverageYellow()
    {
        return $this->pageCoverageYellow;
    }

    /**
     * @param mixed $pageCoverageYellow
     */
    public function setPageCoverageYellow($pageCoverageYellow)
    {
        $this->pageCoverageYellow = $pageCoverageYellow;
    }

    /**
     * @return mixed
     */
    public function getPageCoverageColor()
    {
        return $this->pageCoverageColor;
    }

    /**
     * @param mixed $pageCoverageColor
     */
    public function setPageCoverageColor($pageCoverageColor)
    {
        $this->pageCoverageColor = $pageCoverageColor;
    }

    /**
     * @return mixed
     */
    public function getMonitorStartDate()
    {
        return $this->monitorStartDate;
    }

    /**
     * @param mixed $monitorStartDate
     */
    public function setMonitorStartDate($monitorStartDate)
    {
        $this->monitorStartDate = $monitorStartDate;
    }

    /**
     * @return mixed
     */
    public function getMonitorEndDate()
    {
        return $this->monitorEndDate;
    }

    /**
     * @param mixed $monitorEndDate
     */
    public function setMonitorEndDate($monitorEndDate)
    {
        $this->monitorEndDate = $monitorEndDate;
    }

    /**
     * @return mixed
     */
    public function getStartMeterBlack()
    {
        return $this->startMeterBlack;
    }

    /**
     * @param mixed $startMeterBlack
     */
    public function setStartMeterBlack($startMeterBlack)
    {
        $this->startMeterBlack = $startMeterBlack;
    }

    /**
     * @return mixed
     */
    public function getEndMeterBlack()
    {
        return $this->endMeterBlack;
    }

    /**
     * @param mixed $endMeterBlack
     */
    public function setEndMeterBlack($endMeterBlack)
    {
        $this->endMeterBlack = $endMeterBlack;
    }

    /**
     * @return mixed
     */
    public function getStartMeterColor()
    {
        return $this->startMeterColor;
    }

    /**
     * @param mixed $startMeterColor
     */
    public function setStartMeterColor($startMeterColor)
    {
        $this->startMeterColor = $startMeterColor;
    }

    /**
     * @return mixed
     */
    public function getEndMeterColor()
    {
        return $this->endMeterColor;
    }

    /**
     * @param mixed $endMeterColor
     */
    public function setEndMeterColor($endMeterColor)
    {
        $this->endMeterColor = $endMeterColor;
    }


    public function needsToner($forColor, $dailyPrintVolume) {
        if ($dailyPrintVolume<=0) return false;

        /** @var MasterDeviceEntity $masterDevice */
        $masterDevice = $this->getRmsDeviceInstance()->getMasterDevice();
        if (!$masterDevice) return false;

        $tonerLevel = 0;
        $coverage = 0;
        switch ($forColor) {
            case TonerColorEntity::BLACK : {
                $tonerLevel = $this->getTonerLevelBlack();
                $coverage = $this->getPageCoverageMonochrome();
                break;
            }
            case TonerColorEntity::CYAN : {
                $tonerLevel = $this->getTonerLevelCyan();
                $coverage = $this->getPageCoverageCyan();
                if (!$coverage) $coverage = $this->getPageCoverageColor()/4;
                break;
            }
            case TonerColorEntity::MAGENTA : {
                $tonerLevel = $this->getTonerLevelMagenta();
                $coverage = $this->getPageCoverageMagenta();
                if (!$coverage) $coverage = $this->getPageCoverageColor()/4;
                break;
            }
            case TonerColorEntity::YELLOW : {
                $tonerLevel = $this->getTonerLevelYellow();
                $coverage = $this->getPageCoverageYellow();
                if (!$coverage) $coverage = $this->getPageCoverageColor()/4;
                break;
            }
        }

        if ($coverage<=0) $coverage=5;
        if ($tonerLevel<5) {
            $this->daysLeft[$forColor] = 0;
            return true;
        }

        $all_toners = $masterDevice->getToners();
        $my_toners = [];
        foreach ($all_toners as $toner) {
            /** @var TonerEntity $toner */
            /** @var TonerColorEntity $tonerColor */
            $tonerColor = $toner->getTonerColor();
            if ($tonerColor->getId() == $forColor) {
                $yield = intval($toner->getYield());
                $my_toners[$yield][] = $toner;
            }
        }
        if (empty($my_toners)) return false;
        ksort($my_toners);
        $toner = current(current($my_toners));

        $numberOfPages = $toner->getYield() / ((1 + ( $coverage - 5 ) / 10) );
        $numberOfPagesRemaining = ($tonerLevel/100) * $numberOfPages;
        $numberOfPages5percent = 0.05 * $numberOfPages;
        $this->daysLeft[$forColor] = round(max(0,($numberOfPagesRemaining - $numberOfPages5percent)) / $dailyPrintVolume);

        printf("%s - tonerLevel:%s daysLeft:%s:%s <br>\n", $this->getRmsDeviceInstance()->getIpAddress(), $tonerLevel, $forColor, $this->daysLeft[$forColor]);

        return $this->daysLeft[$forColor] < 10;
    }

}