<?php

use Tangent\Controller\Action;

class HardwareLibrary_SupplyMappingController extends Action {

    private function strip_supply($s) {
        return trim(strtoupper(str_replace('-','',preg_replace('/([#\/]\w+|\(m\))/i','',$s))));
    }

    public function indexAction() {
        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        $this->_pageTitle    = 'Supply Mapping';
        $db = Zend_Db_Table::getDefaultAdapter();
        $where = ($dealerId==1?'':'where id in (select supplierId from dealer_suppliers where dealerId='.$dealerId.')');
        $this->view->suppliers = $db->query("select * from suppliers {$where} order by name");
        $this->view->lines = [];

        $supplierId = $this->getParam('supplierId');

        $count = 0;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $page_size = 30;

        $and = "and (consumableManufacturerId not in (select `manufacturerId` from toner_vendor_manufacturers) or consumableManufacturerId in (select v.manufacturerId from dealer_toner_vendors v where v.dealerId={$dealerId}))\n";
        if ($dealerId==1) $and="";
        else $and.="and concat(supplierId,',',supplierSku) in (select concat(supplierId,',',supplierSku) from supplier_price where dealerId={$dealerId})\n";

        if ($supplierId) {

            $sql = "
                select m.id, m.displayname
                from supplier_consumable
                  join supplier_product using (supplierId, supplierSku)
                  join base_product on supplier_product.baseProductId=base_product.id
                  join manufacturers m on supplier_consumable.oemManufacturerId=m.id
                where supplier_product.supplierId=?
                {$and}
                group by displayname
                order by displayname
            ";

            $this->view->manufacturers = $db->query($sql, [$supplierId]);
            $selectedManufacturer = $this->getParam('manufacturer');

            if ($selectedManufacturer) {

                $manufacturer_names = [];
                $manufacturers = [];
                foreach ($db->query('SELECT * FROM manufacturers') as $line) {
                    $manufacturer_names[$line['id']] = $line['displayname'];
                    $n = strtoupper($line['fullname']);
                    $manufacturers[$n] = $line['id'];
                    if ($n == 'HEWLETT-PACKARD') $manufacturers['HEWLETT PACKARD'] = $line['id'];
                    if ($n == 'OKI') $manufacturers['OKIDATA'] = $line['id'];
                    if ($n == 'COPYSTAR') $manufacturers['ROYAL COPYSTAR'] = $line['id'];
                    $n = strtoupper($line['displayname']);
                    $manufacturers[$n] = $line['id'];
                }

                $supply_skus = [];
                $supply_names = [];
                foreach ($db->query('SELECT id, manufacturerId, sku, name FROM base_product JOIN base_printer_consumable USING (id)') as $line) {
                    $supply_skus[$line['id']] = $this->strip_supply($line['sku']);
                    $supply_names[$line['id']] = [];
                    foreach (explode(',', $line['name']) as $e) {
                        $supply_names[$line['id']][] = $this->strip_supply($e);
                    }
                }

                $printers = [];
                $printer_names = [];
                foreach ($db->query('SELECT id, manufacturerId, sku, name, synonyms FROM base_product JOIN base_printer USING (id)') as $line) {
                    $printers[$line['id']] = $line;
                    $name = strtoupper(str_replace(array(' ', '-', '/'), array('', '', ''), $line['name']));
                    $printer_names[$line['manufacturerId']][$name] = $line['id'];
                    $e = explode(',', $line['synonyms']);
                    foreach ($e as $str) {
                        $str = strtoupper(str_replace(array(' ', '-', '/'), array('', '', ''), trim($str)));
                        if (!empty($str)) {
                            $printer_names[$line['manufacturerId']][$str] = $line['id'];
                        }
                    }
                }

                $compatible_printer_consumable = [];
                foreach ($db->query('SELECT * FROM compatible_printer_consumable') as $line) {
                    $compatible_printer_consumable[$line['compatible']][] = $line['oem'];
                }

                $oem_printing_device_consumable = [];
                $oem_printing_device_consumable_per_printer = [];
                foreach ($db->query('SELECT * FROM oem_printing_device_consumable') as $line) {
                    $oem_printing_device_consumable[$line['printer_consumable']][] = $line['printing_device'];
                    $oem_printing_device_consumable_per_printer[$line['printing_device']][] = $line['printer_consumable'];
                }

                $sql = "
                select count(*)
                from supplier_consumable
                  join supplier_product using (supplierId, supplierSku)
                  join base_product on supplier_product.baseProductId=base_product.id
                where supplier_product.supplierId=?
                and oemManufacturerId=?
                {$and}
            ";
                $count = $db->query($sql, [$supplierId, $selectedManufacturer])->fetchColumn(0);

                $limit = 'limit ' . (($page - 1) * $page_size) . ',' . $page_size;
                //$limit = 'limit 3,1';

                $sql = "
                select
                  `status`,
                  base_product.id,
                  supplier_consumable.type,
                  supplier_consumable.yield,
                  consumableManufacturer as cmfg,
                  oemManufacturer as mfg,
                  oemSku,
                  base_product.sku,
                  base_printer_consumable.cost,
                  base_printer_cartridge.colorId,
                  base_printer_cartridge.colorStr,
                  group_concat(concat(pr.manufacturerId,';',pr.model) SEPARATOR ';;') as cmp_pr
                from supplier_consumable
                  join supplier_product using (supplierId, supplierSku)
                  join base_product on supplier_product.baseProductId=base_product.id
                    join base_printer_consumable using (id) left join base_printer_cartridge using (id)
                  left join supplier_consumable_compatible pr using (supplierId, supplierSku)
                where supplier_product.supplierId=?
                and oemManufacturerId=?
                {$and}
                group by supplierId, supplierSku
                order by cmfg
                {$limit}
            ";

                $lines = [];
                foreach ($db->query($sql, [$supplierId, $selectedManufacturer]) as $line) {
                    #--
                    $mfg = [];
                    $mfg_ids = [];
                    $mfg_names = $line['mfg'];
                    foreach (explode(',', $line['mfg']) as $str) {
                        $str = trim($str);
                        if (empty($str)) continue;
                        $n = strtoupper($str);
                        if (isset($manufacturers[$n])) {
                            $mfg_ids[] = $manufacturers[$n];
                            $mfg[] = '<span class="found">' . str_replace(' ', '&nbsp;', $str) . '</span>';
                        } else {
                            $mfg[] = '<span class="not-found">' . str_replace(' ', '&nbsp;', $str) . '</span>';
                        }
                    }
                    $line['mfg'] = implode(' ', $mfg);
                    #--
                    $printer_supplies = [];
                    switch ($line['status']) {
                        case 'OEM' : {
                            $printer_supplies[] = $line['id'];
                            if ($line['oemSku'] == $line['sku']) {
                                $line['oemSku'] = '<span class="found is-supply">' . $line['oemSku'] . '</span>';
                            } else {
                                $oemSku = [];
                                $m = str_replace('-', '', preg_replace('/[#\/]\w\w\w/', '', $line['sku']));
                                $names = $supply_names[$line['id']];
                                foreach (explode(',', $line['oemSku']) as $str) {
                                    $str = trim($str);
                                    if (empty($str)) continue;
                                    $n = str_replace('-', '', preg_replace('/[#\/]\w\w\w/', '', $str));
                                    if (strcasecmp($n, $m) == 0) {
                                        $oemSku[] = '<span class="found is-supply"><a href="javascript:;" onclick="editToner(' . $line['id'] . '); return false;">' . str_replace(' ', '&nbsp;', $str) . '</a></span>';
                                    } else if (in_array(strtoupper($str), $names)) {
                                        $oemSku[] = '<span class="found is-alias"><a href="javascript:;" onclick="editToner(' . $line['id'] . '); return false;">' . str_replace(' ', '&nbsp;', $str) . '</a></span>';
                                    } else {
                                        $oemSku[] = '<span class="not-found">' . str_replace(' ', '&nbsp;', $str) . '</span>';
                                    }
                                }
                                $line['oemSku'] = implode(' ', $oemSku);
                            }
                            break;
                        }
                        default : {
                            $oemSku = [];
                            $cmp = [];
                            if (isset($compatible_printer_consumable[$line['id']])) {
                                $cmp = $compatible_printer_consumable[$line['id']];
                            }

                            $oemFound = [];
                            $foundOne = false;
                            foreach (explode(',', $line['oemSku']) as $str) {
                                $str = trim($str);
                                if (empty($str)) continue;
                                $n = $this->strip_supply($str);
                                $found = false;
                                foreach ($cmp as $cmp_oem_id) {
                                    $cmp_oem_sku = $supply_skus[$cmp_oem_id];
                                    if ((strcasecmp($n, $cmp_oem_sku) == 0) || in_array($n, $supply_names[$cmp_oem_id])) {
                                        $found = $cmp_oem_id;
                                        $printer_supplies[] = $found;
                                        $foundOne = true;
                                    }
                                }
                                $oemFound[$str] = $found;
                            }
                            foreach ($oemFound as $str => $found) {
                                if ($found) {
                                    $oemSku[] = '<span class="found"><a href="javascript:;" onclick="editToner(' . $found . '); return false;">' . str_replace(' ', '&nbsp;', $str) . '</a></span>';
                                } else {
                                    $oemSku[] = '<span class="not-found"><a href="javascript:;" onclick="unknown_supply(this); return false" data-id="' . $line['id'] . '" data-mfg-ids="' . implode(',', $mfg_ids) . '" data-mfg-id="' . current($mfg_ids) . '" data-mfg-names="' . htmlentities($mfg_names, ENT_QUOTES) . '" data-name="' . htmlentities($str, ENT_QUOTES) . '" data-yield="' . $line['yield'] . '" data-cost="' . $line['cost'] . '" data-type="' . $line['type'] . '" data-color="' . $line['colorId'] . '" data-color-str="' . $line['colorStr'] . '">' . str_replace(' ', '&nbsp;', $str) . '</a></span>';
                                }
                            }
                            $line['oemSku'] = implode(' ', $oemSku);
                        }
                    }
                    #--
                    $cmp_pr = [];
                    foreach (explode(';;', $line['cmp_pr']) as $str) {
                        if (empty($str)) continue;
                        list($cmp_mfg, $cmp_model) = explode(';', $str, 2);
                        $cmp_model_str = strtoupper(str_replace(array(' ', '-', '/'), array('', '', ''), $cmp_model));
                        $found = false;
                        $pr = [];
                        if (isset($oem_printing_device_consumable[$line['id']])) $pr = $oem_printing_device_consumable[$line['id']];
                        else if (isset($compatible_printer_consumable[$line['id']])) {
                            foreach ($compatible_printer_consumable[$line['id']] as $cmp_oem_id) {
                                if (isset($oem_printing_device_consumable[$cmp_oem_id])) {
                                    foreach ($oem_printing_device_consumable[$cmp_oem_id] as $printer_id) {
                                        $pr[] = $printer_id;
                                    }
                                }
                            }
                        }

                        if (isset($printer_names[$cmp_mfg][$cmp_model_str])) {
                            $printer_id = $printer_names[$cmp_mfg][$cmp_model_str];
                            $found = in_array($printer_id, $pr) ? $printer_id : false;
                        }

                        if ($found) {
                            $has_supply=false;
                            foreach ($printer_supplies as $supply_id) {
                                if (in_array($supply_id, $oem_printing_device_consumable_per_printer[$found])) {
                                    $has_supply = true;
                                    break;
                                }
                            }
                            $found = $has_supply;
                        }

                        if ($found) {
                            $cmp_pr[] = '<span class="found"><a href="javascript:;" onclick="editDeviceModel(' . $found . '); return false;">' . str_replace(' ', '&nbsp;', $manufacturer_names[$cmp_mfg] . ' ' . $cmp_model) . '</a></span>';
                        } else {
                            if (isset($printer_names[$cmp_mfg][$cmp_model_str])) {
                                if (!empty($printer_supplies)) {
                                    $cmp_pr[] = '<span class="not-linked"><a href="javascript:;" onclick="link_device(this); return false;" data-supplies="' . implode(',', $printer_supplies) . '" data-device-id="' . $printer_names[$cmp_mfg][$cmp_model_str] . '" data-device-name="' . htmlentities($manufacturer_names[$cmp_mfg] . ' ' . $cmp_model, ENT_QUOTES) . '">' . str_replace(' ', '&nbsp;', $manufacturer_names[$cmp_mfg] . ' ' . $cmp_model) . '</a></span>';
                                } else {
                                    $cmp_pr[] = '<span class="not-linked"><a href="javascript:;" onclick="editDeviceModel(' . $printer_names[$cmp_mfg][$cmp_model_str] . '); return false;">' . str_replace(' ', '&nbsp;', $manufacturer_names[$cmp_mfg] . ' ' . $cmp_model) . '</a></span>';
                                }
                            } else {
                                $cmp_pr[] = '<span class="not-found"><a href="javascript:;" onclick="unknown_device(this); return false" data-supplies="' . implode(',', $printer_supplies) . '" data-mfg-id="' . $cmp_mfg . '" data-mfg="' . $manufacturer_names[$cmp_mfg] . '" data-model="' . htmlentities($cmp_model, ENT_QUOTES) . '">' . str_replace(' ', '&nbsp;', $manufacturer_names[$cmp_mfg] . ' ' . $cmp_model) . '</a></span>';
                            }
                        }
                    }
                    $line['cmp_pr'] = implode(' ', $cmp_pr);
                    #--
                    $lines[] = $line;
                }
                $this->view->lines = $lines;
            }
        }
        $this->view->count = $count;
        $this->view->page = $page;
        $this->view->page_size = $page_size;
    }

    public function searchPrinterAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mfg = $this->getParam('mfg');
        $q = $this->getParam('q');
        $results = [['id'=>0,'text'=>'Create a new device']];

        if (!empty($mfg) && !empty($q)) {
            $mfg_name = $db->query('SELECT displayname FROM manufacturers WHERE id=' . intval($mfg))->fetchColumn(0);
            foreach ($db->query("SELECT id, name FROM base_product WHERE manufacturerId=? AND base_type='printer' AND name LIKE ?", [$mfg, '%' . $q . '%']) as $line) {
                $results[] = ['id' => $line['id'], 'text' => $mfg_name . ' ' . $line['name']];
            }
        }

        $this->sendJson(['results'=>$results]);
    }

    public function addSynonymAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $device = $this->getParam('device');
        $synonym = $this->getParam('synonym');
        $e = explode(',', $db->query('select synonyms from base_printer where id=?', [$device])->fetchColumn(0));
        $new_arr = [];
        foreach ($e as $s) {
            $s = trim($s);
            if (!empty($s)) {
                $new_arr[] = $s;
                if (strcasecmp($s, $synonym) == 0) $synonym = '';
            }
        }
        if (!empty($synonym)) {
            $new_arr[] = $synonym;
        }
        $new_str = implode(', ', $new_arr);

        $db->query('update base_printer set synonyms = ? where id=?', [$new_str, $device]);
        $this->sendJson(['ok'=>true]);
    }

    public function lookupSuppliesAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $supplies = $this->getParam('supplies');
        $result = [];
        if (!empty($supplies)) {
            $result = $db->query("
                SELECT bp.id, concat(mfg.displayname,' ',bp.sku) AS name, tc.name AS color, pageYield AS yield
                FROM base_product bp JOIN base_printer_consumable pc USING (id) LEFT JOIN base_printer_cartridge pr USING(id)
                 JOIN manufacturers mfg ON bp.manufacturerId=mfg.id
                 LEFT JOIN toner_colors tc ON pr.colorId=tc.id
                 where bp.id in (".$supplies.")
            ")->fetchAll();
        }
        $this->sendJson(['lines'=>$result]);
    }

    public function linkSuppliesAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $device = $this->getParam('device');
        $supplies = $this->getParam('supplies');
        $userId = \MPSToolbox\Legacy\Services\NavigationService::$userId;
        foreach ($supplies as $id) {
            $db->query('REPLACE INTO oem_printing_device_consumable SET printing_device=?, printer_consumable=?, userId=?, isApproved=1', [
                $device,
                $id,
                $userId,
            ]);
        }
        $this->sendJson(['ok'=>true]);
    }

    public function searchSupplyAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mfg = $this->getParam('mfg');
        $q = $this->getParam('q');
        $results = [['id'=>0,'text'=>'Create a new supply']];

        if (!empty($mfg) && !empty($q)) {
            foreach ($db->query("
              select base_product.id, concat(manufacturers.displayname,' ',base_product.sku) as `text`, base_product.name
                from base_product
                  join base_printer_consumable using (id)
                  join manufacturers on base_product.manufacturerId=manufacturers.id
                where
                 manufacturerId in ({$mfg}) and (sku like :q or name like :q or replace(sku,'-','') like :q or replace(name,'-','') like :q)
            ", ['q'=>'%' . $q . '%']) as $line) {
                $results[] = ['id' => $line['id'], 'text' => $line['text'].($line['name'] ? ' ('.$line['name'].')':'')];
            }
        }

        $this->sendJson(['results'=>$results]);
    }

    public function createLinkAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $compatible = $this->getParam('compatible');
        $oem = $this->getParam('oem');
        $db->query('replace into compatible_printer_consumable set oem=?, compatible=?', [$oem, $compatible]);
        $this->sendJson(['ok'=>true]);
    }

}