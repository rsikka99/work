<?php

namespace MPSToolbox\Services;

class PriceLevelService {

    public function insert($dealerId, $name, $margin, $isDefault = false) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare('insert into dealer_price_levels set dealerId=:dealerId, name=:name, margin=:margin');
        $st->execute(['dealerId'=>$dealerId, 'name'=>$name, 'margin'=>$margin]);
        $id = $db->lastInsertId();
        if ($isDefault) {
            $db->query('update clients set priceLevelId='.intval($id).' where priceLevelId is null and dealerId='.intval($dealerId));
        }
        $this->shopifyUpdate($dealerId);
    }

    public function update($dealerId, $id, $name, $margin) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare('update dealer_price_levels set `name`=?, margin=? where id=?');
        $st->execute([$name, $margin, $id]);
        $this->shopifyUpdate($dealerId);
    }

    public function delete($dealerId, $id) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare('delete from dealer_price_levels where id=?');
        $st->execute([$id]);
        $this->shopifyUpdate($dealerId);
    }

    public function listByDealer($dealerId) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->query('select id, name, margin, id IN (SELECT priceLevelId FROM clients) as is_used from dealer_price_levels where dealerId='.intval($dealerId).' order by `margin`');
        return $st->fetchAll();
    }

    public function replaceCategoryPriceLevel($dealerId, $categoryId, $manufacturerId, $type, $arr) {
        $this->deleteCategoryPriceLevel($dealerId, $categoryId, $manufacturerId, $type);
        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare('insert into dealer_category_price_level set categoryId=?, priceLevelId=?, manufacturerId=?, type=?, margin=?');
        foreach ($arr as $priceLevelId=>$margin) {
            $st->execute([$categoryId, $priceLevelId, $manufacturerId, $type, $margin]);
        }
        $this->shopifyUpdate($dealerId);
    }

    public function deleteCategoryPriceLevel($dealerId, $categoryId, $manufacturerId, $type) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $db->query("delete from dealer_category_price_level where categoryId={$categoryId} and priceLevelId in (select id from dealer_price_levels where dealerId={$dealerId}) and `manufacturerId` ".($manufacturerId?"={$manufacturerId}":"is null")." and `type` ".($type?"='{$type}'":"is null"));
    }

    public function listByDealerAndCategory($dealerId, $categoryId, $manufacturerId, $type) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        return $db->query(
"
  select dealer_price_levels.*, dealer_category_price_level.margin as category_margin
    from dealer_price_levels
      left join dealer_category_price_level on
        dealer_category_price_level.categoryId={$categoryId}
        and dealer_category_price_level.manufacturerId ".($manufacturerId?"={$manufacturerId}":"is null")."
        and dealer_category_price_level.`type` ".($type?"='{$type}'":"is null")."
        and dealer_price_levels.id=dealer_category_price_level.priceLevelId
    where dealerId={$dealerId} order by margin
"
        )->fetchAll();
    }

    public function shopifyUpdate($dealerId) {
        file_get_contents('http://proxy.mpstoolbox.com/shopify/dist_update.php?dealerId='.intval($dealerId).'&origin='.$_SERVER['HTTP_HOST']);
    }

}