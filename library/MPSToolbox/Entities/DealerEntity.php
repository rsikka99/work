<?php

namespace MPSToolbox\Entities;
use MPSToolbox\Legacy\Mappers\DealerFeatureMapper;

/**
 * Class DealerEntity
 * @package MPSToolbox\Entities
 *
 * @property int         id
 * @property string      dealerName
 *
 * @Entity
 * @Table(name="dealers")
 */
class DealerEntity extends BaseEntity {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(type="string") */
    private $dealerName;

    public static function getDealerId() {
        return \Zend_Auth::getInstance()->getIdentity()->dealerId;
    }
    public static function getCurrency() {
        return \Zend_Auth::getInstance()->getIdentity()->currency;
    }

    public static function hasEcommerce($dealerId = null) {
        $list = DealerFeatureMapper::getInstance()->fetchFeatureListForDealer($dealerId);
        foreach ($list as $feature) {
            if ($feature->featureId == 'ecommerce') {
                return true;
            }
        }
        return false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDealerName()
    {
        return $this->dealerName;
    }

    /**
     * @param string $dealerName
     */
    public function setDealerName($dealerName)
    {
        $this->dealerName = $dealerName;
    }



}