<?php

use Tangent\Controller\Action;

class HardwareLibrary_SupplyMappingController extends Action {

    public function indexAction() {
        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        $this->_pageTitle    = 'Supply Mapping';
        $db = Zend_Db_Table::getDefaultAdapter();
        $where = ($dealerId==1?'':'where id in (select supplierId from dealer_suppliers where dealerId='.$dealerId.')');
        $this->view->suppliers = $db->query("select * from suppliers {$where} order by name");
        $this->view->lines = [];

        $supplierId = $this->getParam('supplierId');

        $and = "and (consumableManufacturerId not in (select `manufacturerId` from toner_vendor_manufacturers) or consumableManufacturerId in (select v.manufacturerId from dealer_toner_vendors v where v.dealerId={$dealerId}))\n";
        if ($dealerId==1) $and="";
        else $and.="and concat(supplierId,',',supplierSku) in (select concat(supplierId,',',supplierSku) from supplier_price where dealerId={$dealerId})\n";

        if ($supplierId) {

            $manufacturer_names = [];
            $manufacturers = [];
            foreach ($db->query('select * from manufacturers') as $line) {
                $manufacturer_names[$line['id']] = $line['displayname'];
                $n = strtoupper($line['fullname']);
                $manufacturers[$n] = $line['id'];
                if ($n=='HEWLETT-PACKARD') $manufacturers['HEWLETT PACKARD'] = $line['id'];
                if ($n=='OKI') $manufacturers['OKIDATA'] = $line['id'];
                if ($n=='COPYSTAR') $manufacturers['ROYAL COPYSTAR'] = $line['id'];
                $n = strtoupper($line['displayname']);
                $manufacturers[$n] = $line['id'];
            }

            $supply_skus = [];
            $supply_names = [];
            foreach ($db->query('select id, manufacturerId, sku, name from base_product join base_printer_consumable using (id)') as $line) {
                $n = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$line['sku']));
                $supply_skus[$line['id']] = $n;
                $supply_names[$line['id']] = [];
                foreach (explode(',', $line['name']) as $e) {
                    $supply_names[$line['id']][] = strtoupper(trim($e));
                }
            }

            $printers = [];
            $printer_names = [];
            foreach ($db->query('select id, manufacturerId, sku, name from base_product join base_printer using (id)') as $line) {
                $printers[$line['id']] = $line;
                $name = strtoupper(str_replace(array(' ', '-', '/'), array('', '', ''), $line['name']));
                $printer_names[$line['manufacturerId']][$name] = $line['id'];
            }

            $compatible_printer_consumable = [];
            foreach ($db->query('select * from compatible_printer_consumable') as $line) {
                $compatible_printer_consumable[$line['compatible']][] = $line['oem'];
            }

            $oem_printing_device_consumable = [];
            foreach ($db->query('select * from oem_printing_device_consumable') as $line) {
                $oem_printing_device_consumable[$line['printer_consumable']][] = $line['printing_device'];
            }

            $sql = "
                select
                  `status`,
                  base_product.id,
                  supplier_consumable.type,
                  consumableManufacturer as cmfg,
                  oemManufacturer as mfg,
                  oemSku,
                  base_product.sku,
                  group_concat(concat(pr.manufacturerId,';',pr.model) SEPARATOR ';;') as cmp_pr
                from supplier_consumable
                  join supplier_product using (supplierId, supplierSku)
                  left join base_product on supplier_product.baseProductId=base_product.id
                    join base_printer_consumable using (id) left join base_printer_cartridge using (id)
                  left join supplier_consumable_compatible pr using (supplierId, supplierSku)
                where supplier_product.supplierId=?
                {$and}
                group by supplierId, supplierSku
                order by cmfg
                limit 5
            ";

            $lines = [];
            foreach ($db->query($sql, [$supplierId]) as $line) {
                #--
                $mfg = [];
                $mfg_ids = [];
                foreach (explode(',', $line['mfg']) as $str) {
                    $str = trim($str);
                    if (empty($str)) continue;
                    $n = strtoupper($str);
                    if (isset($manufacturers[$n])) {
                        $mfg_ids[] = $manufacturers[$n];
                        $mfg[] = '<span class="found">'.str_replace(' ','&nbsp;',$str).'</span>';
                    } else {
                        $mfg[] = '<span class="not-found">'.str_replace(' ','&nbsp;',$str).'</span>';
                    }
                }
                $line['mfg'] = implode(' ',$mfg);
                #--
                $printer_supplies = [];
                switch ($line['status']) {
                    case 'OEM' : {
                        $printer_supplies[] = $line['id'];
                        if ($line['oemSku']==$line['sku']) {
                            $line['oemSku'] = '<span class="found is-supply">'.$line['oemSku'].'</span>';
                        } else {
                            $oemSku = [];
                            $m = str_replace('-', '', preg_replace('/[#\/]\w\w\w/', '', $line['sku']));
                            $names = $supply_names[$line['id']];
                            foreach (explode(',', $line['oemSku']) as $str) {
                                $str = trim($str);
                                if (empty($str)) continue;
                                $n = str_replace('-', '', preg_replace('/[#\/]\w\w\w/', '', $str));
                                if (strcasecmp($n,$m)==0) {
                                    $oemSku[] = '<span class="found is-supply"><a href="javascript:;" onclick="editToner('.$line['id'].'); return false;">'.str_replace(' ','&nbsp;',$str).'</a></span>';
                                } else if (in_array(strtoupper($str), $names)) {
                                    $oemSku[] = '<span class="found is-alias"><a href="javascript:;" onclick="editToner('.$line['id'].'); return false;">'.str_replace(' ','&nbsp;',$str).'</a></span>';
                                } else {
                                    $oemSku[] = '<span class="not-found">'.str_replace(' ','&nbsp;',$str).'</span>';
                                }
                            }
                            $line['oemSku'] = implode(' ',$oemSku);
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
                        foreach (explode(',', $line['oemSku']) as $str) {
                            $str = trim($str);
                            if (empty($str)) continue;
                            $n = str_replace('-', '', preg_replace('/[#\/]\w\w\w/', '', $str));
                            $found = false;
                            foreach ($cmp as $cmp_oem_id) {
                                $cmp_oem_sku = $supply_skus[$cmp_oem_id];
                                if (strcasecmp($n, $cmp_oem_sku) == 0) {
                                    $found = $cmp_oem_id;
                                    $printer_supplies[] = $found;
                                }
                            }
                            $oemFound[$str] = $found;
                        }
                        foreach ($oemFound as $str=>$found) {
                            if ($found) {
                                $oemSku[] = '<span class="found is-supply"><a href="javascript:;" onclick="editToner('.$found.'); return false;">'.str_replace(' ','&nbsp;',$str).'</a></span>';
                            } else {
                                $is_name=false;
                                foreach ($oemFound as $str1=>$found1) if ($found1) {
                                    if (in_array(strtoupper($str), $supply_names[$found1])) {
                                        $is_name=$found1;
                                    }
                                }
                                if ($is_name) {
                                    $oemSku[] = '<span class="found is-alias"><a href="javascript:;" onclick="editToner('.$is_name.'); return false;">'.str_replace(' ','&nbsp;',$str).'</a></span>';
                                } else {
                                    $oemSku[] = '<span class="not-found">' . str_replace(' ', '&nbsp;', $str) . '</span>';
                                }
                            }
                        }
                        $line['oemSku'] = implode(' ',$oemSku);
                    }
                }
                #--
                $cmp_pr = [];
                foreach (explode(';;', $line['cmp_pr']) as $str) {
                    if (empty($str)) continue;
                    list($cmp_mfg,$cmp_model) = explode(';',$str,2);
                    $cmp_model_str = strtoupper(str_replace(array(' ','-','/'),array('','',''),$cmp_model));
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
                    foreach ($pr as $printer_id) {
                        if ($cmp_mfg == $printers[$printer_id]['manufacturerId']) {
                            $printer_model = strtoupper(str_replace(array(' ', '-', '/'), array('', '', ''), $printers[$printer_id]['name']));
                            if (strcasecmp($printer_model, $cmp_model_str)==0) {
                                $found = $printer_id;
                            }
                        }
                    }
                    if ($found) {
                        $cmp_pr[] = '<span class="found">' . str_replace(' ', '&nbsp;', $manufacturer_names[$cmp_mfg].' '.$cmp_model) . '</span>';
                    } else {
                        if (isset($printer_names[$cmp_mfg][$cmp_model_str])) {
                            $cmp_pr[] = '<span class="not-linked">' . str_replace(' ', '&nbsp;', $manufacturer_names[$cmp_mfg] . ' ' . $cmp_model) . '</span>';
                        } else {
                            $cmp_pr[] = '<span class="not-found"><a href="javascript:;" onclick="unknown_device(this); return false" data-supplies="'.implode(',', $printer_supplies).'" data-mfg-id="'.$cmp_mfg.'" data-mfg="'.$manufacturer_names[$cmp_mfg].'" data-model="'.htmlentities($cmp_model, ENT_QUOTES).'">' . str_replace(' ', '&nbsp;', $manufacturer_names[$cmp_mfg] . ' ' . $cmp_model) . '</a></span>';
                        }
                    }
                }
                $line['cmp_pr'] = implode(' ',$cmp_pr);
                #--
                $lines[] = $line;
            }
            $this->view->lines = $lines;
        }
    }

    public function searchPrinterAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $mfg = $this->getParam('mfg');
        $q = $this->getParam('q');
        $results = [['id'=>0,'text'=>'Create a new device']];

        $mfg_name = $db->query('select displayname from manufacturers where id='.intval($mfg))->fetchColumn(0);
        foreach ($db->query("select id, name from base_product where manufacturerId=? and base_type='printer' and name like ?", [$mfg, '%'.$q.'%']) as $line) {
            $results[] = ['id'=>$line['id'], 'text'=>$mfg_name.' '.$line['name']];
        }

        $this->sendJson(['results'=>$results]);
    }

}