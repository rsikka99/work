<?php

class Api_MergeController extends \Tangent\Controller\Action {

    public function tonerAction() {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($this->getParam('upc')) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            if ($this->getParam('merge') && $this->getParam('ids')) {
                $merge = $this->getParam('merge');
                $ids = explode('|', $this->getParam('ids'));

                if ($this->getRequest()->isPost()) {
                    #-- attributes
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

                    #--
                    foreach ($ids as $id) if ($id!=$merge) {
                        $db->query('DELETE FROM base_product WHERE id=?', [$id]);
                    }

                    header('Location: /api/merge/toner?upc=1');
                    return;
                }

                $rows = [];
                foreach ($db->query('select id, upc, sku, name, weight, cost, pageYield, quantity, `type`, compatiblePrinters, colorId, mlYield from base_product join base_printer_consumable using (id) left join base_printer_cartridge using (id) where id in ('.implode(', ', $ids).')')->fetchAll() as $line) {
                    $is_supplier = $db->query('select vpn from supplier_product where baseProductId='.$line['id'])->fetchColumn(0);
                    $rows['_supplier'][$line['id']] = $is_supplier;
                    foreach ($line as $k=>$v) {
                        //if ($k=='id') continue;
                        $rows[$k][$line['id']] = $v;
                    }
                }
                echo '<form action="/api/merge/toner?upc=1&ids='.$this->getParam('ids').'" method="post">';
                echo '<table cellpadding="5" border="1">';
                foreach ($rows as $k=>$a) {
                    echo '<tr>';
                    echo '<td width="'.(100/(1+count($ids))).'%">'.$k.'</td>';
                    foreach ($a as $id=>$v) {
                        if ($k=='id') {
                            echo '<td width="' . (100 / (1 + count($ids))) . '%"><label><input ' . ($id == $merge ? 'checked="checked"' : '') . ' type="radio" name="merge" value="' . $id . '">' . $v . '</label></td>';
                        } else {
                            echo '<td width="' . (100 / (1 + count($ids))) . '%">'.$v.'</td>';
                        }
                    }
                    echo '</tr>';
                }
                echo '</table>';
                echo '<button>Submit</button>';
                echo '</form>';
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
                    $skus[] = '<a href="/api/merge/toner?upc=1&merge='.$ids[$i].'&ids='.implode('|',$ids).'">'.$sku.'</a>';
                }
                echo '<tr>';
                echo '<td>'.$line['upc'].'</td>';
                echo '<td>'.$line['c'].'</td>';
                echo '<td>'.implode('&nbsp;&nbsp;&nbsp;',$skus).'</td>';
                echo '<td>'.$line['yields'].'</td>';
                echo '</tr>';
            }
            echo '</table>';
            return;
        }

        $this->sendJson(['ok'=>true]);
    }

}