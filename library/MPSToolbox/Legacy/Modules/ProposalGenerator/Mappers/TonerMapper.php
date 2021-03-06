<?php
namespace MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers;

use Exception;
use MPSToolbox\Legacy\Entities\DealerEntity;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\ManufacturerModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerConfigModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;
use MPSToolbox\Services\CurrencyService;
use My_Model_Mapper_Abstract;
use Zend_Auth;
use Zend_Db_Table;
use Zend_Db_Table_Select;
use Zend_Validate_Db_NoRecordExists;

/**
 * Class TonerMapper
 *
 * @package MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers
 */
class TonerMapper extends My_Model_Mapper_Abstract
{

    /**
     * The default db table class to use
     *
     * @var String
     *
     */
    protected $_defaultDbTable = 'MPSToolbox\Legacy\Modules\ProposalGenerator\DbTables\TonerDbTable';

    /*
     * Define the primary key of the model association
    */
    public $col_id             = 'id';
    public $col_manufacturerId = 'manufacturerId';
    public $col_tonerColorId   = 'tonerColorId';
    public $col_sku            = 'sku';

    private $cache = [];

    /**
     * Gets an instance of the mapper
     *
     * @return TonerMapper
     */
    public static function getInstance ()
    {
        return self::getCachedInstance();
    }

    /**
     * Saves an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel to the database.
     * If the id is null then it will insert a new row
     *
     * @param $object TonerModel
     *                The object to insert
     * @return int The primary key of the new row
     */
    public function insert ($object)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        // Insert the data
        //$id = $this->getDbTable()->insert($data);
        $object->dateCreated = date('Y-m-d');
        $productData = $object->toProductArray();
        $sql=[];
        foreach (array_keys($productData) as $k) {
            $sql[] = "`{$k}`=?";
        }
        $st = $db->prepare('insert into base_product set '.implode(',', $sql));
        $st->execute(array_values($productData));
        $object->id = $db->lastInsertId();

        $consumableArray = $object->toConsumableArray();
        $sql=["`id`={$object->id}"];
        foreach (array_keys($consumableArray) as $k) {
            $sql[] = "`{$k}`=?";
        }
        $st = $db->prepare('insert into base_printer_consumable set '.implode(',', $sql));
        $st->execute(array_values($consumableArray));

        if ($productData['base_type']=='printer_cartridge') {
            $cartridgeData = $object->toCartridgeArray();
            $sql = ["`id`={$object->id}"];
            foreach (array_keys($cartridgeData) as $k) {
                $sql[] = "`{$k}`=?";
            }
            $st = $db->prepare('INSERT INTO base_printer_cartridge SET ' . implode(',', $sql));
            $st->execute(array_values($cartridgeData));
        }

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object->id;
    }

    /**
     * Saves (updates) an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel to the database.
     *
     * @param $object     TonerModel
     *                    The toner to save to the database
     * @param $primaryKey mixed
     *                    Optional: The original primary key, in case we're changing it
     *
     * @return int The number of rows affected
     */
    public function save ($object, $primaryKey = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $productData = $object->toProductArray();
        $sql=[];
        foreach (array_keys($productData) as $k) {
            $sql[] = "`{$k}`=?";
        }
        $st = $db->prepare('update base_product set '.implode(',', $sql)." where `id`={$object->id}");
        $st->execute(array_values($productData));

        $consumableArray = $object->toConsumableArray();
        $sql=[];
        foreach (array_keys($consumableArray) as $k) {
            $sql[] = "`{$k}`=?";
        }
        $st = $db->prepare('update base_printer_consumable set '.implode(',', $sql)." where `id`={$object->id}");
        $st->execute(array_values($consumableArray));

        if ($productData['base_type']=='printer_cartridge') {
            $cartridgeData = $object->toCartridgeArray();
            $sql = [];
            foreach (array_keys($cartridgeData) as $k) {
                $sql[] = "`{$k}`=?";
            }
            $st = $db->prepare('replace into base_printer_cartridge set ' . implode(',', $sql) . ", `id`={$object->id}");
            $st->execute(array_values($cartridgeData));
        } else {
            $st = $db->prepare("delete from base_printer_cartridge where `id`={$object->id}");
            $st->execute([]);
        }

        // Save the object into the cache
        $this->saveItemToCache($object);

        return 1;
    }

    /**
     * Deletes rows from the database.
     *
     * @param $object mixed
     *                This can either be an instance of MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel or the
     *                primary key to delete
     *
     * @return int The number of rows deleted
     */
    public function delete ($object)
    {
        $st = Zend_Db_Table::getDefaultAdapter()->prepare('delete from base_product where id=?');
        if ($object instanceof TonerModel)
        {
            $st->execute([$object->id]);
        }
        else
        {
            $st->execute([$object]);
        }
        $rowsAffected = $st->rowCount();
        return $rowsAffected;
    }

    public function base_find($id) {
        $result = $this->getItemFromCache($id);
        if ($result instanceof TonerModel) return $result;

        $db = Zend_Db_Table::getDefaultAdapter();
        $data = $db->query('select * from base_product join base_printer_consumable using(id) left join base_printer_cartridge using(id) where base_product.id='.intval($id))->fetch(\PDO::FETCH_ASSOC);
        if (empty($data)) return null;

        $data['yield'] = $data['pageYield'];
        $data['tonerColorId'] = $data['colorId'];
        $object = new TonerModel($data);

        $this->saveItemToCache($object);
        return $object;
    }

    /**
     * Finds a toner based on it's primaryKey
     *
     * @param int|array $ids The id of the toner to find
     *
     * @return TonerModel|TonerModel[]
     */
    public function find ($ids)
    {
        if (is_array($ids))
        {
            $idsNotCached = [];
            $toners       = [];
            foreach ($ids as $id)
            {
                $result = $this->getItemFromCache($id);
                if ($result instanceof TonerModel)
                {
                    $toners[] = $result;
                }
                else
                {
                    $idsNotCached[] = $id;
                }
            }

            if (count($idsNotCached) > 0)
            {
                $results = $this->getDbTable()->find($ids);

                foreach ($results as $result)
                {
                    $toner = new TonerModel($result->toArray());

                    // Save the object into the cache
                    $this->saveItemToCache($toner);

                    $toners[] = $toner;
                }
            }

            return $toners;
        }
        else
        {
            return $this->base_find(intval($ids));
        }
    }

    public function findBySku($mfg, $sku)
    {
        $mfg = ManufacturerMapper::getInstance()->findByFullName($mfg);
        if (!$mfg) return null;

        foreach ($this->_rowHashTable as $k=>$toner) {
            /** @var $toner TonerModel */
            if (($toner->manufacturerId == $mfg->id) && (strcasecmp($toner->sku, $sku)===0)) {
                return $toner;
            }
        }

        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare('select * from toners where manufacturerId=? and sku=?');
        $st->execute([$mfg->id, $sku]);
        $row = $st->fetch();
        if (empty($row)) return null;

        $object = new TonerModel($row);
        $this->saveItemToCache($object);
        return $object;
    }


    /**
     * Fetches a toner
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: A SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: A SQL ORDER clause.
     * @param $offset int
     *                OPTIONAL: A SQL OFFSET value.
     *
     * @return TonerModel
     */
    public function fetch ($where = null, $order = null, $offset = null)
    {
        $row = $this->getDbTable()->fetchRow($where, $order, $offset);
        if (is_null($row))
        {
            return false;
        }

        $object = new TonerModel($row->toArray());

        // Save the object into the cache
        $this->saveItemToCache($object);

        return $object;
    }

    /**
     * Fetches all toners
     *
     * @param $where  string|array|Zend_Db_Table_Select
     *                OPTIONAL: An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param $order  string|array
     *                OPTIONAL: An SQL ORDER clause.
     * @param $count  int
     *                OPTIONAL: An SQL LIMIT count. (Defaults to 25)
     * @param $offset int
     *                OPTIONAL: An SQL LIMIT offset.
     *
     * @return TonerModel[]
     */
    public function fetchAll ($where = null, $order = null, $count = 25, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = [];
        foreach ($resultSet as $row)
        {
            $object = new TonerModel($row->toArray());

            // Save the object into the cache
            $this->saveItemToCache($object);

            $entries [] = $object;
        }

        return $entries;
    }

    /**
     * Gets a where clause for filtering by id
     *
     * @param int $id
     *
     * @return array
     */
    public function getWhereId ($id)
    {
        return [
            "{$this->col_id} = ?" => $id,
        ];
    }

    public function setCompatible($oem_id, $compatible_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare('replace into compatible_printer_consumable set oem=?, compatible=?');
        $st->execute([$oem_id, $compatible_id]);
    }


    /**
     * @param TonerModel $object
     *
     * @return int
     */
    public function getPrimaryKeyValueForObject ($object)
    {
        return $object->id;
    }

    /**
     * Gets all the toners for a device
     *
     * @param $masterDeviceId
     *
     * @return TonerModel[][][]
     * @throws Exception
     */
    public function getTonersForDevice ($masterDeviceId)
    {
        $toners = [];
        try
        {
            $deviceToners = DeviceTonerMapper::getInstance()->getDeviceToners($masterDeviceId);
            if ($deviceToners)
            {
                /* @var $deviceToner DeviceTonerModel */
                foreach ($deviceToners as $deviceToner)
                {
                    $toner                                                     = $this->find($deviceToner->toner_id);
                    $toners [$toner->manufacturerId] [$toner->tonerColorId] [] = $toner;
                }
            }
        }
        catch (Exception $e)
        {
            throw new Exception("Error fetching all toners for a master device", 0, $e);
        }

        return $toners;
    }


    /**
     * Gets a list of all the toner pricing for a master device by dealer
     * This list will have the cost of the toner resolved.
     *
     * @param int $masterDeviceId
     * @param int $dealerId
     * @param int $clientId
     *
     * @return TonerModel []
     */
    public function getReportToners($masterDeviceId, $dealerId, $clientId = null, $level = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $_view_cheapest_toner_cost = '_view_cheapest_toner_cost';
        if ($level) $_view_cheapest_toner_cost = "_view_{$level}_toner_cost";

        $select  =
"
select
  toners.*,
  dealer_toner_attributes.dealerSku,
  false as isUsingCustomerPricing,
  {$_view_cheapest_toner_cost}.cost as calculatedCost,
  {$_view_cheapest_toner_cost}.isUsingDealerPricing
from toners
  join device_toners on device_toners.toner_id=toners.id
  left join dealer_toner_attributes on dealer_toner_attributes.tonerId = toners.id and dealer_toner_attributes.dealerId=:dealerId
  left join {$_view_cheapest_toner_cost} on {$_view_cheapest_toner_cost}.tonerId=toners.id and {$_view_cheapest_toner_cost}.dealerId=:dealerId
where device_toners.master_device_id=:masterDeviceId
";
        $stmt = $db->query($select, ['dealerId'=>$dealerId, 'masterDeviceId'=>$masterDeviceId]);
        $result     = $stmt->fetchAll();

        $tonerArray = [];

        foreach ($result as $row)
        {
            $toner = $this->getItemFromCache($row['id']);
            if (!$toner instanceof TonerModel)
            {
                $toner = new TonerModel($row);
                $this->saveItemToCache($toner);
            }
            else
            {
                $toner->calculatedCost         = $row['calculatedCost'];
                $toner->isUsingCustomerPricing = $row['isUsingCustomerPricing'];
                $toner->isUsingDealerPricing   = $row['isUsingDealerPricing'];
            }

            $tonerArray [$toner->manufacturerId] [$toner->getTonerColor()->id] [] = $toner;
        }

        return $tonerArray;
    }


    /**
     * Fetches a list of toners for a device. (Used by Proposalgen_AdminController::devicetonersAction()
     *
     * @param $masterDeviceId
     *
     * @return TonerModel []
     */
    public function fetchTonersAssignedToDevice ($masterDeviceId)
    {
        $db = $this->getDbTable()->getAdapter();

        $sql =
'
SELECT
    `toners`.*,
    `toner_colors`.`name`              AS `toner_color_name`,
    `manufacturers`.`fullname`         AS `manufacturer_name`,
    `device_toners`.`master_device_id` AS `master_device_id`
FROM `toners`
    LEFT JOIN `device_toners` ON `device_toners`.`toner_id` = `toners`.`id`
    LEFT JOIN `toner_colors` ON `toner_colors`.`id` = `toners`.`tonerColorId`
    LEFT JOIN `manufacturers` ON `manufacturers`.`id` = `toners`.`manufacturerId`
WHERE `device_toners`.`master_device_id` = :masterDeviceId
';
        $query = $db->query($sql, ['masterDeviceId'=>$masterDeviceId]);

        $toners = [];
        foreach ($query->fetchAll() as $row)
        {
            $toner = $this->getItemFromCache($row[$this->col_id]);
            if (!$toner instanceof TonerModel)
            {
                $toner = new TonerModel($row);
                $this->saveItemToCache($toner);
            }
            $toners[] = $toner;
        }

        return $toners;
    }

    public function fetchOemSupplyIdsForDevice($masterDeviceId) {
        $result = [];
        $db = $this->getDbTable()->getAdapter();
        foreach ($db->query("select printer_consumable from oem_printing_device_consumable where printing_device={$masterDeviceId}") as $line) {
            $result[] = $line['printer_consumable'];
        }
        return $result;
    }

    public function fetchTonersAssignedToDeviceForCurrentDealer($masterDeviceId, $justCount=false)
    {
        $db = $this->getDbTable()->getAdapter();
        $dealerId = intval(DealerEntity::getDealerId());

        //$and = "(device_toners.isSystemDevice=1 or device_toners.userId in (select id from `users` where dealerId = {$dealerId})) AND";
        $and = "(toners.manufacturerId in (select manufacturerId from dealer_toner_vendors where dealer_toner_vendors.dealerId = {$dealerId}) or toners.manufacturerId in (select manufacturerId from master_devices)) AND ";
        if ($dealerId==1) $and = '';

        $sql = "
SELECT
    toners.id                           AS id,
    t2.id                               AS is_oem,
    toners.isSystemDevice               AS isSystemDevice,
    manufacturers.id                    AS manufacturerId,
    manufacturers.fullname              AS manufacturer,
    toners.sku                          AS systemSku,
    dealer_toner_attributes.dealerSku   AS dealerSku,
    toners.cost                         AS systemCost,
    if (_view_cheapest_toner_cost.isUsingDealerPricing=0, null, _view_cheapest_toner_cost.cost) AS dealerCost,
    toners.yield,
    toners.tonerColorId,
    device_toners.isSystemDevice       AS deviceTonersIsSystemDevice,
    (SELECT GROUP_CONCAT( CONCAT( fullname, ' ', modelName ) SEPARATOR ', ' ) FROM device_toners vw_dt, master_devices vw_md, manufacturers vw_mf where toners.id=vw_dt.toner_id and vw_dt.master_device_id = vw_md.id and vw_md.manufacturerId = vw_mf.id GROUP BY toner_id) as device_list
FROM `device_toners`
    JOIN `toners` ON `device_toners`.`toner_id` = `toners`.`id`
    left join `toners` t2 on toners.id=t2.id and t2.manufacturerId in (select manufacturerId from master_devices)
    LEFT JOIN `toner_colors` ON `toner_colors`.`id` = `toners`.`tonerColorId`
    LEFT JOIN `manufacturers` ON `manufacturers`.`id` = `toners`.`manufacturerId`
    LEFT JOIN dealer_toner_attributes ON (dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = :dealerId)
    left join _view_cheapest_toner_cost on (_view_cheapest_toner_cost.tonerId = toners.id AND _view_cheapest_toner_cost.dealerId = :dealerId)
WHERE {$and} `device_toners`.`master_device_id` = :masterDeviceId
";
        $query = $db->query($sql, ['masterDeviceId'=>$masterDeviceId, 'dealerId'=>$dealerId]);


        $arr = $query->fetchAll();
        if ($justCount) {
            return count($arr);
        }

        return $arr;
    }

    public function countTonersForDealer($filterManufacturerId = false, $filterTonerSku = false, $filterTonerColorId = false, $filterTonerPriced = false) {
        if (!$filterManufacturerId && !$filterTonerColorId && !$filterTonerSku && !$filterTonerPriced) {
            return 0;
        }

        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        $db       = $this->getDbTable()->getAdapter();
        $sql      = "
SELECT
    count(*) as c
FROM base_product toners join base_printer_consumable using(id) left join base_printer_cartridge using(id)
    JOIN manufacturers ON manufacturers.id = toners.manufacturerId
    left join dealer_toner_attributes on dealer_toner_attributes.tonerId=toners.id and dealer_toner_attributes.dealerId = {$dealerId}
    where (toners.manufacturerId in (select manufacturerId from dealer_toner_vendors where dealer_toner_vendors.dealerId = {$dealerId}) or toners.manufacturerId in (select manufacturerId from master_devices))
";
        /**
         * Filters toners to a specific manufacturer
         */
        if ($filterManufacturerId)
        {
            $filterManufacturerId = $db->quote($filterManufacturerId, 'INTEGER');
            $sql .= " AND manufacturers.id = {$filterManufacturerId}";
        }

        /**
         * Filters toners to a specific color
         */
        if ($filterTonerColorId)
        {
            $filterTonerColorId = $db->quote($filterTonerColorId, 'INTEGER');
            $sql .= " AND colorId = {$filterTonerColorId}";
        }

        /**
         * Filters toners to a specific VPN/SKU
         */
        if ($filterTonerSku)
        {
            $filterTonerSku = $db->quote("%$filterTonerSku%", 'TEXT');
            $sql .= " AND (sku LIKE {$filterTonerSku} OR dealer_toner_attributes.dealerSku LIKE {$filterTonerSku})";
        }

        if ($filterTonerPriced)
        {
            $sql .= " AND dealer_toner_attributes.sellPrice>0";
        }

        $stmt   = $db->query($sql);
        $arr = $stmt->fetch();
        return $arr['c'];
    }

    /**
     * @param null $orders
     * @param int $count
     * @param int $offset
     * @param bool $filterManufacturerId
     * @param bool $filterTonerSku
     * @param bool $filterTonerColorId
     * @return array
     *
     */
    public function fetchTonersForDealer($orders = null, $count = 25, $offset = 0, $filterManufacturerId = false, $filterTonerSku = false, $filterTonerColorId = false, $filterId=false, $filterTonerPriced=false) {
        if (!$filterManufacturerId && !$filterTonerColorId && !$filterTonerSku && !$filterId && !$filterTonerPriced) {
            return [];
        }

        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;

        $currency = DealerEntity::getCurrency();
        $rate = CurrencyService::getInstance()->getRate();

        $db       = $this->getDbTable()->getAdapter();

        $sql      = "SELECT
    toners.id                                 AS id,
    if (oem.manufacturerId,1,0)               as is_oem,
    toners.isSystemProduct                    AS isSystemDevice,
    manufacturers.id                          AS manufacturerId,
    manufacturers.fullname                    AS manufacturer,
    toners.sku                                AS systemSku,
    dealer_toner_attributes.dealerSku         AS dealerSku,
    toners.name                               AS toner_name,
    base_printer_consumable.cost              AS base_systemCost,
    COALESCE(cv1.value, base_printer_consumable.cost*{$rate})  as systemCost,
    if (_view_cheapest_toner_cost.isUsingDealerPricing=0, null, _view_cheapest_toner_cost.cost) AS dealerCost,
    pageYield                                 as yield,
    colorId                                   as tonerColorId,
    toner_colors.name                         as tonerColor,
    toners.imageFile,
    dl.devices as device_list,
    concat('[',dl.json,']') as json_device_list
FROM base_product toners join base_printer_consumable using(id) left join base_printer_cartridge using(id)
    JOIN manufacturers ON manufacturers.id = toners.manufacturerId
    left join _view_cheapest_toner_cost on (_view_cheapest_toner_cost.tonerId = toners.id AND _view_cheapest_toner_cost.dealerId = {$dealerId})
    left join toner_colors on colorId = toner_colors.id
    left join device_list dl on toners.id=dl.toner_id
    left join oem_manufacturers oem on toners.manufacturerId=oem.manufacturerId
    LEFT JOIN dealer_toner_attributes ON (dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = {$dealerId})
    left join currency_value cv1 on cv1.`table`='toners' and cv1.`field`='cost' and cv1.id=toners.id and cv1.currency='{$currency}'
    where (toners.manufacturerId in (select manufacturerId from dealer_toner_vendors where dealer_toner_vendors.dealerId = {$dealerId}) or toners.manufacturerId in (select manufacturerId from master_devices))
";

        /**
         * Filters toners to a specific manufacturer
         */
        if ($filterManufacturerId)
        {
            $filterManufacturerId = $db->quote($filterManufacturerId, 'INTEGER');
            $sql .= " AND manufacturers.id = {$filterManufacturerId}";
        }

        /**
         * Filters toners to a specific color
         */
        if ($filterTonerColorId)
        {
            $filterTonerColorId = $db->quote($filterTonerColorId, 'INTEGER');
            $sql .= " AND colorId = {$filterTonerColorId}";
        }

        /**
         * Filters toners to a specific VPN/SKU
         */
        if ($filterTonerSku)
        {
            $filterTonerSku = $db->quote("%$filterTonerSku%", 'TEXT');
            $sql .= " AND (sku LIKE {$filterTonerSku} OR dealer_toner_attributes.dealerSku LIKE {$filterTonerSku})";
        }

        if ($filterTonerPriced)
        {
            $sql .= " AND dealer_toner_attributes.sellPrice>0";
        }

        if ($filterId)
        {
            $sql .= " AND (toners.id=".intval($filterId).")";
        }

        if ($orders)
        {
            $sql .= sprintf(' ORDER BY %s', implode(',', $orders));

        }

        if ($count) {
            $sql .= " LIMIT " . $offset . ", " . $count . " ";
        }

        $stmt   = $db->query($sql);
        $arr = $stmt->fetchAll();

        return $arr;
    }

    /**
     * Fetches a list of toners.
     *
     * @param string $tonerIdList A string of id's (comma separated)
     *
     * @param null  $masterDeviceId
     * @param null  $order
     * @param bool  $justCount
     *
     * @return array
     */
    public function fetchListOfToners ($tonerIdList, $masterDeviceId = null, $order = null, $justCount = false)
    {
        $dealerId = Zend_Auth::getInstance()->getIdentity()->dealerId;
        $db       = $this->getDbTable()->getAdapter();

        $masterDeviceId = intval($masterDeviceId);

        $sql = "SELECT
            toners.id                          AS id,
            t2.id                              AS is_oem,
            toners.isSystemDevice              AS isSystemDevice,
            manufacturers.fullname             AS manufacturer,
            toners.manufacturerId              AS manufacturerId,
            toners.sku                         AS systemSku,
            dealer_toner_attributes.dealerSku  AS dealerSku,
            toners.cost                        AS systemCost,
            _view_cheapest_toner_cost.cost     AS dealerCost,
            toners.yield,
            toners.tonerColorId,
            dt3.isSystemDevice                  AS deviceTonersIsSystemDevice,
            IF(dt3.toner_id is null and dt1.toner_id IN ({$tonerIdList}),'1','0') AS is_added,
            (SELECT GROUP_CONCAT( CONCAT( fullname, ' ', modelName ) SEPARATOR ', ' ) FROM device_toners vw_dt, master_devices vw_md, manufacturers vw_mf where toners.id=vw_dt.toner_id and vw_dt.master_device_id = vw_md.id and vw_md.manufacturerId = vw_mf.id GROUP BY toner_id) as device_list
FROM `toners`
    left join `toners` t2 on toners.id=t2.id and t2.manufacturerId in (select manufacturerId from master_devices)
    LEFT JOIN device_toners dt1 ON toners.id = dt1.toner_id
    LEFT JOIN device_toners dt3 ON toners.id = dt3.toner_id AND dt3.master_device_id = {$masterDeviceId}
    LEFT JOIN `toner_colors` ON `toner_colors`.`id` = `toners`.`tonerColorId`
    LEFT JOIN `manufacturers` ON `manufacturers`.`id` = `toners`.`manufacturerId`
    LEFT JOIN dealer_toner_attributes ON dealer_toner_attributes.tonerId = toners.id AND dealer_toner_attributes.dealerId = {$dealerId}
    left join _view_cheapest_toner_cost on (_view_cheapest_toner_cost.tonerId = toners.id AND _view_cheapest_toner_cost.dealerId = {$dealerId})
WHERE `toners`.`id` IN ({$tonerIdList})
                GROUP BY id";

        if ($order)
        {
            $sql .= " ORDER BY " . implode(" ", $order);
        }

        $query = $db->query($sql);

        $arr = $query->fetchAll();
        if ($justCount)
        {
            return count($arr);
        }

        return $arr;
    }

    /**
     * Fetches a list of toners.
     *
     * @param array $tonerIdList A string of id's (comma separated)
     *
     * @return array
     */
    public function fetchListOfTonersWithMachineCompatibility ($tonerIdList)
    {
        $db = $this->getDbTable()->getAdapter();

        $sql = " SELECT
            toner_id                          AS id,
            manufacturers.fullname            AS manufacturer,
            toners.sku                        AS systemSku,
            dealer_toner_attributes.dealerSku AS dealerSku,
            toners.cost                       AS systemCost,
            dealer_toner_attributes.cost      AS dealerCost,
            toners.yield,
            toners.tonerColorId,
            GROUP_CONCAT(CONCAT(manufacturers.fullname,\" \",master_devices.modelName) SEPARATOR \"; \") AS `device_list`
            FROM `toners`
                LEFT JOIN `toner_colors` ON `toner_colors`.`id` = `toners`.`tonerColorId`
                LEFT JOIN `manufacturers` ON `manufacturers`.`id` = `toners`.`manufacturerId`
                LEFT JOIN `device_toners` ON `device_toners`.`toner_id` = `toners`.`id`
                LEFT JOIN `master_devices` ON `master_devices`.`id` = `device_toners`.`master_device_id`
                LEFT JOIN dealer_toner_attributes ON dealer_toner_attributes.tonerId = toners.id
            WHERE `toners`.`id` IN ({$tonerIdList})";

        $query = $db->query($sql);

        return $query->fetchAll();
    }

    private function getTonerAggr($db, $dealerId, $toners) {
        if (empty($toners)) return [];

        $ids = [];
        foreach ($toners as $line) {
            $ids[] = $line['id'];
        }

        $dist = $db->query(
            'select 1 as dist, dealer_toner_attributes.tonerId, 1 as stock, dealer_toner_attributes.cost
    from
      dealer_toner_attributes
    where cost is not null and dealerId='.$dealerId.' and tonerId in ('.implode(',', $ids).')')->fetchAll();

        $dist = array_merge($dist, $db->query(
            '
select 1+supplierId, baseProductId as tonerId, isStock as stock, price as cost
    from
      supplier_product
      join supplier_price using (supplierId, supplierSku)
    where dealerId='.$dealerId.' and baseProductId in ('.implode(',', $ids).')')->fetchAll());

        $aggr = [];
        foreach ($toners as $toner) {
            foreach($dist as $d) {
                if ($d['tonerId']==$toner['id']) {
                    $line = ['toner'=>$toner,'dist'=>$d];
                    $aggr[] = $line;
                }
            }
        }

        usort($aggr, function($a, $b) {
            $dist_a = $a['dist'];
            $dist_b = $b['dist'];
            if ($a['stock'] && !$b['stock']) return -1;
            if ($b['stock'] && !$a['stock']) return 1;

            $toner_a = $a['toner'];
            $toner_b = $b['toner'];

            if (!$toner_a['yield'] || !$toner_b['yield']) {
                if ($toner_a['cost'] < $toner_b['cost']) return -1;
                if ($toner_b['cost'] < $toner_a['cost']) return 1;
                return 0;
            }

            $cpp_a = $dist_a['cost'] / $toner_a['yield'];
            $cpp_b = $dist_b['cost'] / $toner_b['yield'];

            if ($cpp_a < $cpp_b) return -1;
            if ($cpp_b < $cpp_a) return 1;
            return 0;
        });

        return $aggr;
    }

    /**
     * Gets the cheapest toners
     *
     * @param int   $masterDeviceId                   The master device id to get toners for
     * @param int   $dealerId                         The dealer id to get toner pricing for
     * @param array $monochromeManufacturerPreference CSV list of manufacturer ids
     * @param array $colorManufacturerPreference      CSV list of manufacturer ids
     * @param int   $clientId                         The client id to get toner pricing for
     *
     * @return TonerModel[]
     */
    public function getCheapestTonersForDevice ($masterDeviceId, $dealerId, $monochromeManufacturerPreferenceStr, $colorManufacturerPreferenceStr, $clientId = null, $level = null)
    {
        $cache_key = sprintf("%s_%s_%s_%s_%s", $masterDeviceId, $dealerId, $monochromeManufacturerPreferenceStr, $colorManufacturerPreferenceStr, $level);
        if (isset($this->cache['getCheapestTonersForDevice'][$cache_key])) {
            return $this->cache['getCheapestTonersForDevice'][$cache_key];
        }

        $toners = [];
        $db                               = $this->getDbTable()->getDefaultAdapter();
        $masterDeviceId                   = intval($masterDeviceId);
        $dealerId                         = intval($dealerId);

        $monochromeManufacturerPreference=[];
        foreach (explode(',',trim($monochromeManufacturerPreferenceStr,',')) as $id) if ($id && is_numeric($id)) $monochromeManufacturerPreference[$id] = $id;
        $colorManufacturerPreference=[];
        foreach (explode(',',trim($colorManufacturerPreferenceStr,',')) as $id) if ($id && is_numeric($id)) $colorManufacturerPreference[$id] = $id;

        $_view_cheapest_toner_cost = '_view_cheapest_toner_cost';
        if ($level) $_view_cheapest_toner_cost = "_view_{$level}_toner_cost";

        $sql = 'select * from master_devices where id='.$masterDeviceId;
        $query = $db->query($sql);
        $masterDevice = $query->fetch();
        if (empty($masterDevice)) return [];

        $monochromeManufacturerPreference[$masterDevice['manufacturerId']] = $masterDevice['manufacturerId'];
        $monochromeManufacturerPreference=implode(',',$monochromeManufacturerPreference);
        $colorManufacturerPreference[$masterDevice['manufacturerId']] = $masterDevice['manufacturerId'];
        $colorManufacturerPreference=implode(',',$colorManufacturerPreference);

        $rate = 1 / CurrencyService::getInstance($dealerId)->getRate();

        $monochrome_toners = $db->query(
            "select toners.*
              from toners
                join device_toners on toners.id=device_toners.toner_id
              where
                toners.tonerColorId = 1 and
                device_toners.master_device_id={$masterDeviceId} and
                toners.manufacturerId in ({$monochromeManufacturerPreference})
            "
        )->fetchAll();

        $color_toners = [];
        foreach ($db->query(
            "select toners.*
              from toners
                join device_toners on toners.id=device_toners.toner_id
              where
                toners.tonerColorId > 1 and
                device_toners.master_device_id={$masterDeviceId} and
                toners.manufacturerId in ({$colorManufacturerPreference})
            "
        )->fetchAll() as $line) {
            $color_toners[$line['tonerColorId']][] = $line;
        };

        $lines = [];

        if (!empty($monochrome_toners)) {
            $arr = $this->getTonerAggr($db, $dealerId, $monochrome_toners);
            if (!empty($arr)) {
                $line = current($arr);
                $lines[1] = $line;
            }
        }
        if (!empty($color_toners)) {
            foreach ($color_toners as $i=>$t) {
                $arr = $this->getTonerAggr($db, $dealerId, $t);
                if (!empty($arr)) {
                    $line = current($arr);
                    $lines[$i] = $line;
                }
            }
        }

        foreach ($lines as $i=>$line) {
            $toner = $line['toner'];
            $isUsingDealerPricing = $line['dist']['dist'] == 1;
            $toner['isUsingCustomerPricing'] = false;
            $toner['isUsingDealerPricing'] = $isUsingDealerPricing;
            $toner['calculatedCost'] = $isUsingDealerPricing ? $line['dist']['cost'] * $rate : $line['dist']['cost'];
            $toner['costPerPage'] = $toner['yield'] ? $toner['calculatedCost'] / $toner['yield'] : 0;
            $toner['isOem'] = ($toner['manufacturerId'] == $masterDevice['manufacturerId']);
            $toners[$i] = new TonerModel($toner);
        }

        $this->cache['getCheapestTonersForDevice'][$cache_key] = $toners;
        return $toners;
    }

    /**
     * Finds an instance of a MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel by it's SKU
     *
     * @param $sku
     *
     * @return TonerModel
     */
    public function fetchBySku ($sku)
    {
        return $this->fetch(["{$this->col_sku} = ?" => "{$sku}"]);
    }

    /**
     * Finds an instance of a MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel by it's SKU and manufacturer
     *
     * @param string $sku
     * @param int    $manufacturerId
     *
     * @return TonerModel
     */
    public function fetchBySkuAndManufacturer ($sku, $manufacturerId)
    {
        return $this->fetch([
            "{$this->col_sku} = ?"            => "{$sku}",
            "{$this->col_manufacturerId} = ?" => "{$manufacturerId}",
        ]);
    }

    /**
     * Exports the toner pricing for the dealership
     *
     * @param $manufacturerId
     * @param $dealerId
     *
     * @return array
     */
    public function getTonerPricingForExport ($manufacturerId, $dealerId)
    {
        $manufacturers = [];
        foreach (ManufacturerMapper::getInstance()->fetchTonerManufacturersForDealer($dealerId) as $line) {
            $manufacturers[$line['id']] = $line['id'];
        }


        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()
                     ->from(['t' => 'toners'], ['toners_id' => 'id', 'sku', 'name', 'yield', 'imageFile', 'manufacturerId', "systemCost" => "cost"])
                     ->joinLeft(['dt' => 'device_toners'], 'dt.toner_id = t.id', ['master_device_id'])
                     ->joinLeft(['tm' => 'manufacturers'], 'tm.id = t.manufacturerId', ['displayname'])
                     ->joinLeft(['tc' => 'toner_colors'], 'tc.id = t.tonerColorId', ['name AS toner_color'])
                     ->joinLeft(['dta' => 'dealer_toner_attributes'], "dta.tonerId = t.id AND dta.dealerId = {$dealerId}", ['cost', 'dealerSku','level1','level2','level3','level4','level5','level6','level7','level8','level9']) //'distributor',
                     ->where("t.id > 0")
                     ->group('t.id')
                     ->order(['tm.fullname']);

        if ($manufacturerId > 0)
        {
            $select->where("manufacturerId = ?", $manufacturerId);
        }
        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();

        $oemMfg=[];
        $oemSku=[];
        $sql='select m.displayName, t.sku, comp.oem, comp.compatible from toners t join manufacturers m on t.manufacturerId=m.id join compatible_printer_consumable comp on t.id=comp.oem';
        foreach ($db->query($sql)->fetchAll() as $line) {
            $oemMfg[$line['compatible']] = $line['displayName'];
            $oemSku[$line['compatible']][] = $line['sku'];
        }

        $fieldList = [];
        foreach ($result as $value)
        {
            if (isset($manufacturers[$value['manufacturerId']])) {
                $fieldList[] = [
                    $value ['toners_id'],
//                    $value ['distributor'],
                    $value ['displayname'],
                    $value ['sku'],
                    $value ['name'],
                    isset($oemMfg[$value['toners_id']])? $oemMfg[$value['toners_id']] : '',
                    isset($oemSku[$value['toners_id']])? implode(', ', $oemSku[$value['toners_id']]) : '',
                    $value ['toner_color'],
                    $value ['yield'],
                    $value ['systemCost'],
                    $value ['dealerSku'],
                    $value ['cost'],
                    //$value ['imageFile']?'Yes':'No',
                    $value ['level1'],
                    $value ['level2'],
                    $value ['level3'],
                    $value ['level4'],
                    $value ['level5'],
                    $value ['level6'],
                    $value ['level7'],
                    $value ['level8'],
                    $value ['level9'],
                ];
            }
        }

        return $fieldList;
    }

    public function getTonerMatchupForExport($manufacturerId, $dealerId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $select = $db->select()
            ->from(['t' => 'toners'], ['toners_id' => 'id', 'sku'])
            ->joinLeft(['dt' => 'device_toners'], 'dt.toner_id = t.id', ['master_device_id'])
            ->joinLeft(['md' => 'master_devices'], 'dt.master_device_id = md.id', ['modelName'])
            ->joinLeft(['tm' => 'manufacturers'], 'tm.id = t.manufacturerId', ['fullname'])
            ->joinLeft(['tc' => 'toner_colors'], 'tc.id = t.tonerColorId', ['name AS toner_color'])
            ->joinLeft(['dta' => 'dealer_toner_attributes'], "dta.tonerId = t.id AND dta.dealerId = {$dealerId}", ['cost', 'dealerSku'])
            ->where("t.manufacturerId in (select manufacturerId from master_devices where isSystemDevice=1)")
            ->group('t.id')
            ->order(['tm.fullname', 't.sku']);

        if ($manufacturerId > 0)
        {
            $select->where("manufacturerId = ?", $manufacturerId);
        }

        $stmt   = $db->query($select);
        $result = $stmt->fetchAll();

        $fieldList = [];
        foreach ($result as $value)
        {

/*
        self::TONER_MATCHUP_DEVICE_NAME,
        self::TONER_MATCHUP_MANUFACTURER,
        self::TONER_MATCHUP_OEM_TONER_SKU,
        self::TONER_MATCHUP_OEM_DEALER_TONER_SKU,
        self::TONER_MATCHUP_OEM_DEALER_COST,
        self::TONER_MATCHUP_COLOR,
        self::TONER_MATCHUP_COMPATIBLE_VENDOR_NAME,
        self::TONER_MATCHUP_COMPATIBLE_VENDOR_SKU,
        self::TONER_MATCHUP_COMPATIBLE_DEALER_SKU,
        self::TONER_MATCHUP_COMPATIBLE_YIELD,
        self::TONER_MATCHUP_COMPATIBLE_DEALER_COST,
*/

            $fieldList [] = [
                $value ['modelName'],
                $value ['fullname'],
                $value ['sku'],
                $value ['dealerSku'],
                $value ['cost'],
                $value ['toner_color'],
                '',
                '',
                '',
                '',
                ''
            ];
        }

        return $fieldList;

    }

    /**
     * Gets compatible toners
     *
     * @param int|TonerModel $tonerId
     * @param null|int       $clientId
     * @param null|int       $dealerId
     *
     * @return TonerModel[]
     */
    public function findCompatibleToners ($tonerId, $clientId = null, $dealerId = null)
    {
        $db     = $this->getDbTable()->getDefaultAdapter();
        $toners = [];

        if ($tonerId instanceof TonerModel)
        {
            $tonerId = $tonerId->id;
        }


        $sql = 'SELECT
    toners.*,
    _view_cheapest_toner_cost.cost                  AS calculatedCost,
    FALSE                                           AS isUsingCustomerPricing,
    _view_cheapest_toner_cost.isUsingDealerPricing,
    _view_cheapest_toner_cost.cost / toners.yield   AS cpp
FROM device_toners AS dt
    LEFT JOIN toners ON toners.id = dt.toner_id
    LEFT JOIN _view_cheapest_toner_cost ON _view_cheapest_toner_cost.tonerId = dt.toner_id AND _view_cheapest_toner_cost.dealerId = :dealerId
WHERE dt.master_device_id IN (SELECT dt2.master_device_id
                              FROM device_toners AS dt2
                              WHERE dt2.toner_id = :tonerId) AND dt.toner_id != :tonerId AND toners.tonerColorId IN (SELECT tonerColorId FROM toners WHERE toners.id = :tonerId)
GROUP BY dt.toner_id
ORDER BY cpp
';
        $stmt     = $db->query($sql, ['dealerId'=>$dealerId, 'tonerId'=>$tonerId]);
        $result   = $stmt->fetchAll();

        foreach ($result as $tonerData)
        {
            $newTonerId = $tonerData['id'];
            $toner      = $this->getItemFromCache($newTonerId);
            if (!$toner instanceof TonerModel)
            {
                $toner                         = $this->find($newTonerId);
                $toner->calculatedCost         = $tonerData['calculatedCost'];
                $toner->isUsingCustomerPricing = $tonerData['isUsingCustomerPricing'];
                $toner->isUsingDealerPricing   = $tonerData['isUsingDealerPricing'];
                $this->saveItemToCache($toner);
            }

            $toners[] = $toner;
        }

        return $toners;
    }

    /**
     * Gets other OEM toners
     *
     * @param int|TonerModel $tonerId
     * @param null|int       $clientId
     * @param null|int       $dealerId
     *
     * @return TonerModel[]
     */
    public function findOemToners ($tonerId, $clientId = null, $dealerId = null)
    {
        $toners = [];

        if ($tonerId instanceof TonerModel)
        {
            $tonerId = $tonerId->id;
        }

        $db  = $this->getDbTable()->getDefaultAdapter();
        $sql = '
SELECT
    toners.*,
    _view_cheapest_toner_cost.cost                 AS calculatedCost,
    false                                          AS isUsingCustomerPricing,
    _view_cheapest_toner_cost.isUsingDealerPricing,
    _view_cheapest_toner_cost.cost / toners.yield  AS cpp
FROM device_toners
    JOIN toners ON toners.id = device_toners.toner_id
    JOIN master_devices ON master_devices.id = device_toners.master_device_id
    LEFT JOIN _view_cheapest_toner_cost ON _view_cheapest_toner_cost.tonerId = device_toners.toner_id AND _view_cheapest_toner_cost.dealerId = :dealerId
WHERE device_toners.master_device_id IN
      (
          SELECT md2.id
          FROM master_devices AS md2
              JOIN device_toners AS dt2 ON dt2.master_device_id = md2.id
              JOIN toners AS t2 ON dt2.toner_id = t2.id
          WHERE dt2.toner_id = :tonerId
      )
      AND device_toners.toner_id != :tonerId
      AND toners.tonerColorId IN (SELECT tonerColorId FROM toners WHERE toners.id = :tonerId)
      AND toners.manufacturerId = master_devices.manufacturerId
GROUP BY device_toners.toner_id
ORDER BY cpp
';

        $result = $db->query($sql, ['dealerId'=>$dealerId, 'tonerId'=>$tonerId])->fetchAll();

        foreach ($result as $tonerData)
        {
            $newTonerId = $tonerData['id'];
            $toner      = $this->getItemFromCache($newTonerId);
            if (!$toner instanceof TonerModel)
            {
                $toner                         = $this->find($newTonerId);
                $toner->calculatedCost         = $tonerData['calculatedCost'];
                $toner->isUsingCustomerPricing = $tonerData['isUsingCustomerPricing'];
                $toner->isUsingDealerPricing   = $tonerData['isUsingDealerPricing'];
                $this->saveItemToCache($toner);
            }

            $toners[] = $toner;
        }

        return $toners;
    }
}