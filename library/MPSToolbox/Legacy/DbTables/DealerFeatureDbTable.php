<?php

namespace MPSToolbox\Legacy\DbTables;

use My_Feature_DbTableInterface;
use Zend_Auth;
use Zend_Db_Table_Abstract;

/**
 * Class DealerFeatureDbTable
 *
 * @package MPSToolbox\Legacy\DbTables
 */
class DealerFeatureDbTable extends Zend_Db_Table_Abstract implements My_Feature_DbTableInterface
{
    public $col_dealerId  = 'dealerId';
    public $col_featureId = 'featureId';

    protected $_name    = "dealer_features";
    protected $_primary = array(
        'featureId',
        'dealerId'
    );

    /**
     * Should get a array of feature names and return them in string form
     *
     * @return string[]
     */
    public function getFeatures ()
    {
        /**
         * FIXME lrobert: How can we inject Zend_Auth/dealerId
         */
        $zendAuth    = Zend_Auth::getInstance();
        $featureList = array();

        if ($zendAuth->hasIdentity() && isset($zendAuth->getIdentity()->dealerId))
        {
            $select = $this->select()
                           ->from($this->_name, $this->col_featureId)
                           ->where("{$this->col_dealerId} = ?", $zendAuth->getIdentity()->dealerId);
            $data   = $this->fetchAll($select);

            foreach ($data as $feature)
            {
                $featureList[] = $feature[$this->col_featureId];
            }
        }

        return $featureList;
    }
}