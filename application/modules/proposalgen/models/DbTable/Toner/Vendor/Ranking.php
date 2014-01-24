<?php

/**
 * Class Proposalgen_Model_DbTable_Toner_Vendor_Ranking
 */
class Proposalgen_Model_DbTable_Toner_Vendor_Ranking extends Zend_Db_Table_Abstract
{
    protected $_name = "toner_vendor_rankings";
    protected $_primary = array("tonerVendorRankingSetId", "manufacturerId");
}