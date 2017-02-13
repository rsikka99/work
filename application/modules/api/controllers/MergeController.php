<?php

class Api_MergeController extends \Tangent\Controller\Action {

    private function doMerge($merge, $ids) {
        $db = Zend_Db_Table::getDefaultAdapter();

        $db->query('update base_product set upc=?, sku=?, otherSkus=?, name=?, weight=? where id=?',[
            $_POST['upc'][$merge],
            $_POST['sku'][$merge],
            $_POST['otherSkus'][$merge],
            $_POST['name'][$merge],
            $_POST['weight'][$merge],
            $merge
        ]);
        $db->query('update base_printer_consumable set cost=?, pageYield=?, quantity=?, type=? where id=?',[
            $_POST['cost'][$merge],
            $_POST['pageYield'][$merge],
            $_POST['quantity'][$merge],
            $_POST['type'][$merge],
            $merge
        ]);
        $db->query('update base_printer_cartridge set colorId=? where id=?',[
            $_POST['colorId'][$merge],
            $merge
        ]);

        $attr = [$merge=>[]];
        foreach ($ids as $id) {
            $attr[$id] = $db->query('select * from dealer_toner_attributes where tonerId='.$id)->fetchAll();
        }
        foreach ($ids as $id) if ($id!=$merge) {
            foreach ($attr[$id] as $line) {
                $dealerId = $line['dealerId'];
                $found = false;
                foreach ($attr[$merge] as $search_line) {
                    if ($search_line['dealerId']==$dealerId) {
                        $found = $search_line;
                    }
                }
                if ($found) {
                    if (!$found['cost']) $db->query('update dealer_toner_attributes set cost=? where tonerId='.$merge.' and dealerId='.$dealerId, [$line['cost']]);
                    if (!$found['dealerSku']) $db->query('update dealer_toner_attributes set dealerSku=? where tonerId='.$merge.' and dealerId='.$dealerId, [$line['dealerSku']]);
                    if (!$found['webId']) $db->query('update dealer_toner_attributes set webId=? where tonerId='.$merge.' and dealerId='.$dealerId, [$line['webId']]);
                    if (!$found['sellPrice']) $db->query('update dealer_toner_attributes set sellPrice=? where tonerId='.$merge.' and dealerId='.$dealerId, [$line['sellPrice']]);
                    $db->query('delete from dealer_toner_attributes where tonerId='.$id.' and dealerId='.$dealerId);
                } else {
                    $db->query('update dealer_toner_attributes set tonerId='.$merge.' where tonerId='.$id.' and dealerId='.$dealerId);
                }
            }
        }

        #-- compatible_printer_consumable
        $comp = [$merge=>[]];
        foreach ($ids as $id) {
            $comp[$id] = $db->query('select * from compatible_printer_consumable where oem='.$id)->fetchAll();
        }
        foreach ($ids as $id) if ($id!=$merge) {
            foreach ($comp[$id] as $line) {
                $db->query('REPLACE INTO compatible_printer_consumable SET oem=?, compatible=?', [$merge, $line['compatible']]);
            }
        }

        #-- oem_printing_device_consumable
        $comp = [$merge=>[]];
        foreach ($ids as $id) {
            $comp[$id] = $db->query('select * from oem_printing_device_consumable where printer_consumable='.$id)->fetchAll();
        }
        foreach ($ids as $id) if ($id!=$merge) {
            foreach ($comp[$id] as $line) {
                $db->query('REPLACE INTO oem_printing_device_consumable SET printer_consumable=?, printing_device=?, userId=?, isApproved=?', [$merge, $line['printing_device'], $line['userId'], $line['isApproved']]);
            }
        }

        #-- supplier_product
        foreach ($ids as $id) if ($id!=$merge) {
            $db->query('update supplier_product set baseProductId=? where baseProductId=?', [$merge, $id]);
        }

        #--
        foreach ($ids as $id) if ($id!=$merge) {
            $db->query('DELETE FROM base_product WHERE id=?', [$id]);
        }
    }

    private function compare($merge, $ids, $url) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $rows = [];
        foreach ($db->query('select id, upc, sku, otherSkus, name, weight, cost, pageYield, quantity, `type`, colorId, mlYield from base_product join base_printer_consumable using (id) left join base_printer_cartridge using (id) where id in ('.implode(', ', $ids).')')->fetchAll() as $line) {
            $is_supplier = $db->query('select vpn from supplier_product where baseProductId='.$line['id'])->fetchColumn(0);
            $rows['_supplier'][$line['id']] = $is_supplier;
            foreach ($line as $k=>$v) {
                //if ($k=='id') continue;
                $rows[$k][$line['id']] = $v;
            }
            $rows['printers'][$line['id']] = [];
            foreach ($db->query('select * from oem_printing_device_consumable where printer_consumable=?', [$line['id']])->fetchAll() as $pr) {
                $rows['printers'][$line['id']][] = $pr['printing_device'];
            }
            $rows['printers'][$line['id']] = implode(', ', $rows['printers'][$line['id']]);
        }
        $colors = $db->query('select * from toner_colors')->fetchAll();
        $editable = ['upc','sku','otherSkus','name','weight','cost','pageYield','quantity','colorId','mlYield','type'];
        echo '<form action="'.$url.'?ids='.$this->getParam('ids').'" method="post">';
        echo '<table cellpadding="5" border="1">';
        foreach ($rows as $k=>$a) {
            echo '<tr>';
            echo '<td width="'.(100/(1+count($ids))).'%">'.$k.'</td>';
            foreach ($a as $id=>$v) {
                if (($k=='otherSkus') && !empty($_GET['name'])) {
                    $v = (empty($v)?'':$v.', ').$_GET['name'];
                }
                if ($k=='id') {
                    echo '<td width="' . (100 / (1 + count($ids))) . '%"><label><input ' . ($id == $merge ? 'checked="checked"' : '') . ' type="radio" name="merge" value="' . $id . '">' . $v . '</label></td>';
                } else if ($k=='colorId') {
                    echo '<td width="' . (100 / (1 + count($ids))) . '%"><select name="'.$k.'['.$id.']" value="'.$v.'">';
                    foreach ($colors as $c) {
                        echo '<option '.($v==$c['id']?'selected="selected"':'').' value="'.$c['id'].'">'.$c['name'].'</option>';
                    }
                    echo '</select></td>';
                } else if (in_array($k, $editable)) {
                    echo '<td width="' . (100 / (1 + count($ids))) . '%"><input type="text" name="'.$k.'['.$id.']" value="'.$v.'"></td>';
                } else {
                    echo '<td width="' . (100 / (1 + count($ids))) . '%">'.$v.'</td>';
                }
            }
            echo '</tr>';
        }
        echo '</table>';
        echo '<button>Submit</button>';
        echo '</form>';
    }

    public function upcAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getParam('merge') && $this->getParam('ids')) {
            $merge = $this->getParam('merge');
            $ids = explode('|', $this->getParam('ids'));

            if ($this->getRequest()->isPost()) {
                $this->doMerge($merge, $ids);
                header('Location: /api/merge/upc');
                return;
            }

            $this->compare($merge, $ids, '/api/merge/upc');
            return;
        }

        $q = $db->query("
select * from (
select upc, count(*) as c, group_concat(id separator ', ') as ids, group_concat(sku separator ', ') as skus, group_concat(pageYield separator ', ') as yields
from base_product join base_printer_consumable using (id)
where upc is not null and upc<>'' and upc<>'9999999999999' and upc<>'0' group by upc order by c desc
) as s1 where c>1
        ");

        echo '<table cellpadding="5" border="1">';
        foreach ($q as $line) {
            $skus = [];
            $ids = explode(', ', $line['ids']);
            foreach (explode(', ', $line['skus']) as $i=>$sku) {
                $skus[] = '<a href="/api/merge/upc?&merge='.$ids[$i].'&ids='.implode('|',$ids).'">'.$sku.'</a>';
            }
            echo '<tr>';
            echo '<td>'.$line['upc'].'</td>';
            echo '<td>'.$line['c'].'</td>';
            echo '<td>'.implode('&nbsp;&nbsp;&nbsp;',$skus).'</td>';
            echo '<td>'.$line['yields'].'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function matchupAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $skus = [];
        foreach ($db->query('select id, manufacturerId, sku, otherSkus from base_product join base_printer_consumable using(id)') as $line) {
            $sku = array_shift(explode('#', str_replace(['-',' '], ['',''], strtolower(trim($line['sku'])))));
            if (!empty($sku)) {
                $skus[$line['manufacturerId']][$sku] = $line['id'];
            }
            foreach (explode(',', $line['otherSkus']) as $otherSku) {
                $sku = array_shift(explode('#', str_replace(['-',' '], ['',''], strtolower(trim($otherSku)))));
                if (!empty($sku)) {
                    $skus[$line['manufacturerId']][$sku] = $line['id'];
                }
            }
        }
        ini_set('memory_limit','512M');
        $st = $db->prepare('update supplier_product set baseProductId=? where manufacturerId=? and vpn=?');
        $cursor = $db->query('select baseProductId, manufacturerId, vpn from supplier_product where manufacturerId is not null group by baseProductId, manufacturerId, vpn');
        while($line=$cursor->fetch()) {
            if (empty($line['vpn'])) continue;
            $sku = array_shift(explode('#', str_replace(['-',' '], ['',''], strtolower(trim($line['vpn'])))));
            if (!isset($skus[$line['manufacturerId']][$sku])) continue;
            if ($skus[$line['manufacturerId']][$sku] == $line['baseProductId']) continue;
            echo "{$line['vpn']}: {$line['baseProductId']} >>> {$skus[$line['manufacturerId']][$sku]}<br>\n";
            $st->execute([$skus[$line['manufacturerId']][$sku], $line['manufacturerId'], $line['vpn']]);
        }
    }

    public function skuAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (isset($_POST['otherSkus'])) {
            foreach ($_POST['otherSkus'] as $id=>$names) {
                $db->query('update base_product set otherSkus=? where id=?', [implode(', ', $names),$id]);
            }
        }

        if ($this->getParam('merge') && $this->getParam('ids')) {
            $merge = $this->getParam('merge');
            $ids = explode('|', $this->getParam('ids'));

            if ($this->getRequest()->isPost()) {
                $this->doMerge($merge, $ids);
                header('Location: /api/merge/sku');
                return;
            }

            $this->compare($merge, $ids, '/api/merge/sku');
            return;
        }
        $table = [];
        $arr = [];
        foreach ($db->query('select id, sku, name, manufacturerId, otherSkus from base_product join base_printer_consumable using(id) where manufacturerId in (select manufacturerId from oem_manufacturers)') as $line) {
            $arr[$line['manufacturerId']][$line['id']]=$line;
        }
        foreach ($arr as $mfg_id=>$lines) {
            $skus = [];
            foreach ($lines as $id=>$line) {
                $sku = array_shift(explode('#', str_replace(['-',' '], ['',''], strtolower($line['sku']))));
                $skus[$sku][] = $line;
            }
            $all_names = [];
            foreach ($skus as $sku=>$sku_lines) {
                if (count($sku_lines)>1) {
                    $table_ids = [];
                    $table_skus = [];
                    foreach ($sku_lines as $line) {
                        $table_ids[] = $line['id'];
                        $table_skus[] = $line['sku'];
                    }

                    $table[] = [
                        'type'=>'duplicate sku',
                        'ids'=>$table_ids,
                        'skus'=>$table_skus,
                    ];
                }
                foreach ($sku_lines as $line) {
                    if (empty($line['name'])) continue;
                    $otherSkus = explode(',', $line['otherSkus']);
                    foreach ($otherSkus as $i=>$otherSku) {
                        $otherSkus[$i] = str_replace(['-',' '], ['',''], strtolower(array_shift(explode('#', trim($otherSku)))));
                    }

                    $names = explode(',', $line['name']);
                    foreach ($names as $name) {
                        $name = array_shift(explode('#', trim($name)));
                        $name_lookup = str_replace(['-',' '], ['',''], strtolower($name));
                        if (empty($name_lookup)) continue;
                        if (preg_match('#(series|drum|original|Cartridge|unit|paper|film|pack|bottle|kit|toner|yield|standard|black|cyan|magenta|yellow)#i', $name_lookup)) continue;
                        $all_names[$name_lookup][] = ['name' => $name, 'id' => $line['id'],'otherSkus'=>$otherSkus];
                        if ($name_lookup == $sku) continue;
                        if (isset($skus[$name_lookup])) {
                            $t = [
                                'type'=>'duplicate name',
                                'ids'=>[$line['id']],
                                'skus'=>[$line['sku']],
                            ];
                            foreach ($skus[$name_lookup] as $sku_line) {
                                $t['ids'][] = $sku_line['id'];
                                $t['skus'][] = $sku_line['sku'];
                            }
                            $table[] = $t;
                        }
                    }
                }
            }
            if ($mfg_id!=5) foreach ($all_names as $name_lookup=>$name_lines) {
                if (count($name_lines)==1) {
                    $name_line = current($name_lines);
                    if (!in_array($name_lookup, $name_line['otherSkus'])) {
                        if (!is_numeric($name_line['name']) && (strlen($name_line['name']) > 3)) {
                            $table[] = [
                                'type' => 'otherSku',
                                'ids' => [$name_line['id']],
                                'skus' => [$name_line['name']],
                                'id' => $name_line['id'],
                                'name' => $name_line['name'],
                            ];
                        }
                    }
                }
            }

        }
        echo '<form method="post">';
        echo '<table cellpadding="5" border="1">';
        foreach ($table as $line) {
            $skus = [];
            foreach ($line['skus'] as $i=>$sku) {
                $skus[] = '<a href="/api/merge/sku?merge='.$line['ids'][$i].'&ids='.implode('|',$line['ids']).($line['name']?'&'.http_build_query(['name'=>$line['name']]):'').'">'.$sku.'</a>';
            }
            echo '<tr>';
            echo '<td>'.$line['type'].'</td>';
            if (!empty($line['name'])) {
                echo '<td><input type="checkbox" name="otherSkus['.$line['id'].'][]" value="'.$line['name'].'" checked="checked"></td>';
            } else {
                echo '<td></td>';
            }
            echo '<td>'.implode('&nbsp;&nbsp;&nbsp;',$skus).'</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<button type="submit">submit</button>';
        echo '</form>';
    }

}