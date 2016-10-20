<?php

namespace MPSToolbox\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\RequestInterface;
use Tangent\Ftp\NcFtp;
use Tangent\Logger\Logger;
use XBase\Table;

class DistributorUpdateService {

    /** @var  \PDOStatement */
    private $insert_product;
    /** @var  \PDOStatement */
    private $insert_price;
    /** @var  \PDOStatement */
    private $update_product;
    /** @var  \PDOStatement */
    private $update_price;
    /** @var  \PDOStatement */
    private $base_product_statement;
    /** @var  \PDOStatement */
    private $sku_statement;

    private $remaining_product = [];
    private $supplier_product = [];
    private $supplier_price = [];

    private $compatibleStatements = [];

    private $total_result = [];

    const SUPPLIER_INGRAM   = 1;
    const SUPPLIER_SYNNEX   = 2;
    const SUPPLIER_TECHDATA = 3;
    const SUPPLIER_GENUINE = 4;
    const SUPPLIER_ACM = 5;
    const SUPPLIER_DH = 6;
    const SUPPLIER_CLOVER = 7;

    /** @var  \Zend_Filter_Compress_Zip */
    private $zipAdapter;

    /** @var  NcFtp */
    private $ftpClient;

    public function expandYield($str) {
        if (empty($str)) return null;
        if (preg_match('#^(.+)ml$#i', $str, $match)) {
            $str = trim($match[1]);
        }
        $str=preg_replace('#,(\d\d\d)#','$1',$str);
        if (is_numeric($str)) {
            return intval($str);
        }
        if (preg_match('#^(.+)k$#i', $str, $match)) {
            $f = floatval($match[1]);
            return round(1000 * $f);
        }
        error_log('cannot parse yield: '.$str);
        return 0;
    }

    /**
     * @return NcFtp
     */
    public function getFtpClient()
    {
        if (!$this->ftpClient) {
            $this->ftpClient = new NcFtp();
        }
        return $this->ftpClient;
    }

    /**
     * @param NcFtp $ftpClient
     */
    public function setFtpClient($ftpClient)
    {
        $this->ftpClient = $ftpClient;
    }

    /**
     * @return \ZipArchive
     */
    public function getZipAdapter()
    {
        if (!$this->zipAdapter) {
            $this->zipAdapter = new \ZipArchive(); //new \Zend_Filter_Compress_Zip();
        }
        return $this->zipAdapter;
    }

    /**
     * @param \Zend_Filter_Compress_Zip $zipAdapter
     */
    public function setZipAdapter(\Zend_Filter_Compress_Zip $zipAdapter)
    {
        $this->zipAdapter = $zipAdapter;
    }

    public function getDealerSuppliers() {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->query('select * from dealer_suppliers');
        return $st->fetchAll();
    }

    public function updatePrices($dealerSupplier) {
        $this->timerStart = time();
        ob_end_flush();
        ob_implicit_flush();

        $db = \Zend_Db_Table::getDefaultAdapter();

        #--
        $this->insert_product = $db->prepare("
                  insert into supplier_product SET
supplierId={$dealerSupplier['supplierId']},
supplierSku=:supplierSku,
manufacturer=:manufacturer,
manufacturerId=:manufacturerId,
vpn=:vpn,
`name`=:name,
msrp=:msrp,
weight=:weight,
length=:length,
width=:width,
height=:height,
upc=:upc,
description=:description,
isStock=:isStock,
qty=:qty,
package=:package,
category=:category,
categoryId=:categoryId,
dateCreated=:dateCreated,
_md5=:_md5");

        $this->insert_price = $db->prepare("
                  insert into supplier_price SET
supplierId={$dealerSupplier['supplierId']},
supplierSku=:supplierSku,
dealerId=:dealerId,
price=:price,
promotion=:promotion,
_md5=:_md5");

        $this->update_product = $db->prepare("
                  UPDATE supplier_product SET
manufacturer=:manufacturer,
manufacturerId=:manufacturerId,
vpn=:vpn,
`name`=:name,
msrp=:msrp,
weight=:weight,
length=:length,
width=:width,
height=:height,
upc=:upc,
description=:description,
isStock=:isStock,
qty=:qty,
package=:package,
category=:category,
categoryId=:categoryId,
dateCreated=:dateCreated,
_md5=:_md5
                  WHERE supplierSku=:supplierSku and supplierId={$dealerSupplier['supplierId']}");

        $this->update_price = $db->prepare("
                  UPDATE supplier_price SET
price=:price,
promotion=:promotion,
_md5=:_md5
                    WHERE dealerId=:dealerId and supplierSku=:supplierSku and supplierId={$dealerSupplier['supplierId']}");


        $this->base_product_statement = $db->prepare('update supplier_product set baseProductId=? where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?');
        $this->sku_statement = $db->prepare('update base_product set sku=(select vpn from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?) where id=?');

        #--
        $dealerId = intval($dealerSupplier['dealerId']);

        $this->supplier_product = [];
        $cursor = $db->query('select supplierSku, _md5 from supplier_product where supplierId='.$dealerSupplier['supplierId']);
        while($line = $cursor->fetch()) {
            $this->supplier_product[$line['supplierSku']] = $line['_md5'] ? $line['_md5'] : 'x';
        }
        $cursor->closeCursor();

        $this->remaining_product = [];
        $cursor = $db->query('select supplierSku, _md5 from supplier_product where supplierId='.$dealerSupplier['supplierId'].' and supplierSku in (select supplierSku from supplier_price where dealerId='.$dealerId.')');
        while($line = $cursor->fetch()) {
            $this->remaining_product[$line['supplierSku']] = $line['_md5'] ? $line['_md5'] : 'x';
        }
        $cursor->closeCursor();

        $this->supplier_price = [];
        $cursor = $db->query('select supplierSku, _md5 from supplier_price where dealerId='.$dealerId.' and supplierId='.$dealerSupplier['supplierId']);
        while($line = $cursor->fetch()) {
            $this->supplier_price[$line['supplierSku']] = $line['_md5'] ? $line['_md5'] : 'x';
        }
        $cursor->closeCursor();

        $cursor = null;
        gc_collect_cycles();

        echo "dealer: {$dealerSupplier['dealerId']}\n";
        echo "supplier: {$dealerSupplier['supplierId']}\n";
        echo "remaining_product: ".count($this->remaining_product)."\n";
        echo "supplier_product: ".count($this->supplier_product)."\n";
        echo "supplier_price: ".count($this->supplier_price)."\n";
        #--

        $this->total_result = [
            'products_updated'=>0,
            'products_added'=>0,
            'products_deleted'=>0,
            'products_total'=>0,
            'prices_updated'=>0,
            'prices_added'=>0,
            'prices_deleted'=>0,
            'prices_total'=>0,
        ];

        switch($dealerSupplier['supplierId']) {
            case self::SUPPLIER_INGRAM : {
                $this->updateIngram($dealerSupplier);
                break;
            }
            case self::SUPPLIER_SYNNEX : {
                $this->updateSynnex($dealerSupplier);
                break;
            }
            case self::SUPPLIER_TECHDATA : {
                $this->updateTechData($dealerSupplier);
                break;
            }
            case self::SUPPLIER_GENUINE : {
                $this->updateGenuine($dealerSupplier);
                break;
            }
            case self::SUPPLIER_ACM : {
                $this->updateAcm($dealerSupplier);
                break;
            }
            case self::SUPPLIER_DH : {
                $this->updateDH($dealerSupplier);
                break;
            }
            case self::SUPPLIER_CLOVER : {
                $this->updateClover($dealerSupplier);
                break;
            }
        }
        gc_collect_cycles();

        if ($this->total_result['products_total']==0) {
            $this->total_result['products_total'] = $db->query('SELECT count(*) FROM supplier_product WHERE supplierId=' . intval($dealerSupplier['supplierId']))->fetchColumn(0);
            $this->total_result['prices_total'] = $db->query('SELECT count(*) FROM supplier_price WHERE dealerId=' . intval($dealerId) . ' AND supplierId=' . intval($dealerSupplier['supplierId']))->fetchColumn(0);
        }

        $db->query("
          insert into distributor_import_result set
              `dealerId`={$dealerId},
              `supplierId`={$dealerSupplier['supplierId']},
              `products_added`={$this->total_result['products_added']},
              `products_deleted`={$this->total_result['products_deleted']},
              `products_updated`={$this->total_result['products_updated']},
              `products_total`={$this->total_result['products_total']},
              `prices_added`={$this->total_result['prices_added']},
              `prices_deleted`={$this->total_result['prices_deleted']},
              `prices_updated`={$this->total_result['prices_updated']},
              `prices_total`={$this->total_result['prices_total']}
        ");
    }

    private function populate($product, $price) {
        #--
        if (!empty($product)) {
            $sku = $product['supplierSku'];
            $product['_md5'] = md5(json_encode($product));
            if (isset($this->supplier_product[$sku])) {
                $db_md5 = $this->supplier_product[$sku];
                if ($db_md5 != $product['_md5']) {
                    $this->update_product->execute($product);
                    $this->total_result['products_updated'] += $this->update_product->rowCount();
                    #echo "updated product {$sku} because {$line['_md5']} != {$product['_md5']}\n";
                }
            } else {
                #echo "inserting product {$sku}\n";
                $this->insert_product->execute($product);
                $this->total_result['products_added'] += $this->insert_product->rowCount();

                $this->supplier_product[$sku] = md5(json_encode($product));
                #echo count($this->supplier_product)."\n";
            }
        }

        #--
        if (!empty($price)) {
            $sku = $price['supplierSku'];
            $price['_md5'] = md5(json_encode($price));
            if (isset($this->supplier_price[$sku])) {
                $db_md5 = $this->supplier_price[$sku];
                if ($db_md5 != $price['_md5']) {
                    $this->update_price->execute($price);
                    $this->total_result['prices_updated'] += $this->update_price->rowCount();
                }
            } else {
                $this->insert_price->execute($price);
                $this->total_result['prices_added'] += $this->insert_price->rowCount();

                $this->supplier_price[$sku] = $price['_md5'];
            }
        }
    }

    private function deleteRemainingProducts($supplierId, $dealerId) {
        $db = \Zend_Db_Table::getDefaultAdapter();

        $online_sku=[];
        foreach ($db->query('select supplierSku, baseProductId from supplier_product where supplierId='.$supplierId.' and baseProductId in (select masterDeviceId from devices where online=1)') as $online_line) {
            $online_sku[$online_line['supplierSku']] = $online_line['baseProductId'];
        }
        foreach ($db->query('select supplierSku, baseProductId from supplier_product where supplierId='.$supplierId.' and baseProductId in (select skuId from dealer_sku where online=1)') as $online_line) {
            $online_sku[$online_line['supplierSku']] = $online_line['baseProductId'];
        }

        $st1 = $db->prepare('delete from supplier_product where supplierSku=? and supplierId='.$supplierId);
        $st2 = $db->prepare('delete from supplier_price where supplierSku=? and dealerId='.$dealerId.' and supplierId='.$supplierId);
        $c = count($this->remaining_product);
        foreach ($this->remaining_product as $supplierSku=>$md5) {

            if (isset($online_sku[$supplierSku])) {
                $baseProductId = intval($online_sku[$supplierSku]);
                $base_product = $db->query('select * from base_product where id='.$baseProductId)->fetch();
                $base_product_mfg = $db->query('select fullname from manufacturers where id='.intval($base_product['manufacturerId']))->fetchColumn(0);
                $affected_dealers = [];
                foreach ($db->query('select dealerName from dealers where id in (select dealerId from dealer_suppliers where supplierId='.$supplierId.') and id in (select dealerId from devices where online=1 and masterDeviceId='.$baseProductId.')') as $dealer_line) $affected_dealers[$dealer_line['dealerName']] = $dealer_line['dealerName'];
                foreach ($db->query('select dealerName from dealers where id in (select dealerId from dealer_suppliers where supplierId='.$supplierId.') and id in (select dealerId from dealer_sku where online=1 and skuId='.$baseProductId.')') as $dealer_line) $affected_dealers[$dealer_line['dealerName']] = $dealer_line['dealerName'];
                if (!empty($affected_dealers)) {
                    $msg = "
This product has been deleted by a distributor but is currently online:
Manufacturer: {$base_product_mfg}
Modelname: {$base_product['name']}
Dealers: " . implode(', ', $affected_dealers) . "
";
                    mail(
                        'root@tangentmtw.com',
                        'Online product deletion',
                        $msg
                    );
                }
            }

            $st1->execute([$supplierSku]);
            $st2->execute([$supplierSku]);
        }
        echo "{$c} products deleted\n";
        $this->total_result['products_deleted'] = $c;
        $this->total_result['prices_deleted'] = $c;
    }

    private function updateAcm($dealerSupplier) {
        $dealerId = intval($dealerSupplier['dealerId']);

        $db = \Zend_Db_Table::getDefaultAdapter();
        $filename = APPLICATION_BASE_PATH . '/data/cache/' . sprintf('acm-%s.txt', $dealerId);
        try {
            if (!file_exists($filename) || (filemtime($filename) < strtotime('-6 DAY'))) {
                $ftp = $this->getFtpClient();
                $ftp->get($dealerSupplier['url'].'/ACM_2_Customer/ProductList_'.date('Ymd').'.txt', $dealerSupplier['user'], $dealerSupplier['pass'], $filename);
            }
        } catch (\Exception $ex) {
            print_r($ex);
            Logger::logException($ex);
            return false;
        }

        echo "processing {$filename} for dealer {$dealerId}\n";
        $fp = fopen($filename, 'rb');

        gc_collect_cycles();
        echo "1. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $manufacturers = [];
        foreach ($db->query('select * from manufacturers order by fullname')->fetchAll() as $line) {
            $n = strtoupper($line['fullname']);
            $manufacturers[$n] = $line['id'];
            if ($n=='COPYSTAR') $manufacturers['ROYAL COPYSTAR'] = $line['id'];
            if ($n=='HEWLETT-PACKARD') $manufacturers['HEWLETT PACKARD'] = $line['id'];
            if ($n=='OKI') $manufacturers['OKIDATA'] = $line['id'];
        }

        //Brand	Product Group	Product Type	ACM#	OEM#	Product Description	Model	Branch 110 Inventory	Branch 130 Inventory	Branch 150 Inventory	Branch 160 Inventory
        //Total Inventory	Price	UOM	CustNo
        $columns = fgetcsv($fp, null, "\t");

        $oem_cartridges = [];

        $skus = [];
        foreach ($db->query('select * from base_product p join base_printer_consumable c using(id) left join base_printer_cartridge a using(id)')->fetchAll() as $line) {
            $sku = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$line['sku']));
            $skus[$line['manufacturerId']][$sku] = $line;
        }

        while ($line = fgetcsv($fp, null, "\t")) {
            while (count($line) > count($columns)) {
                array_pop($line);
            }
            $line = array_combine($columns, $line);

            if ($line['UOM']!='EA') {
                #echo "???? {$line['UOM']} \n";
                #var_dump($line);
                #continue;
            }

            $supplierSku = trim($line['ACM#']);

            if (strpos($line['Product Group'], 'ECOPlus')===0) {
                $line['CompatibleSkus'] = $line['OEM#'];
                $line['CompatibleBrand'] = strtoupper(preg_replace('#,.*$#','',$line['Brand']));
                $line['Product Description'] = $line['Brand'].' '.trim($line['OEM#']).' | '.trim($line['Product Description']);
                $line['Brand'] = 'ECOPlus';
                $line['OEM#'] = $supplierSku;
            } else if (substr($line['Product Description'],0,4)!='OEM ') {
                $line['CompatibleSkus'] = $line['OEM#'];
                $line['CompatibleBrand'] = strtoupper(preg_replace('#,.*$#','',$line['Brand']));
                $line['Product Description'] = $line['Brand'].' '.trim($line['OEM#']).' | '.trim($line['Product Description']);
                $line['Brand'] = 'ACM Technologies';
                $line['OEM#'] = $supplierSku;
            }

            $manufacturerId = null;
            $brand = strtoupper(preg_replace('#,.*$#','',$line['Brand']));
            if (isset($manufacturers[$brand])) $manufacturerId = $manufacturers[$brand];
            else {
                switch ($line['Brand']) {
                    case 'ECOPlus' : break;
                    case 'ACM' : break;
                    default : {
                        error_log($line['Brand']);
                        continue;
                    }
                }
            }

            $vpn = $line['OEM#'];
            $name = '';
            $e = explode(',', $vpn);
            if (count($e)>1) {
                $vpn = trim($e[0]);
                $name = trim(array_pop($e));
                #echo "{$vpn} : {$name}\n";
                if ((strpos($vpn, '-')!==false) || (stripos($vpn, 'type')!==false) || ((strlen($name)>strlen($vpn)) && (stripos($name, 'type')===false))) {
                    $vpn = $name;
                    $name = trim($e[0]);
                    #echo ">>> {$vpn} : {$name}\n";
                }

            }

            $product_data = [
                'supplierSku'=>$supplierSku,
                'manufacturer'=>$line['Brand'],
                'manufacturerId'=>$manufacturerId,
                'vpn'=>$vpn,
                'name'=>$name,
                'msrp'=>null,
                'weight'=>null,
                'length'=>null,
                'width'=>null,
                'height'=>null,
                'upc'=>null,
                'description'=>trim($line['Product Description']).'; Models: '.trim($line['Model']),
                'package'=>null,
                'isStock'=>intval($line['Total Inventory'])>0?1:0,
                'qty'=>json_encode([
                    'Branch 110 Inventory'=>$line['Branch 110 Inventory'],
                    'Branch 130 Inventory'=>$line['Branch 130 Inventory'],
                    'Branch 150 Inventory'=>$line['Branch 150 Inventory'],
                    'Branch 160 Inventory'=>$line['Branch 160 Inventory'],
                    'Total Inventory'=>$line['Total Inventory']]),
                'category'=>trim($line['Product Group']).' | '.trim($line['Product Type']),
                'categoryId'=>null,
                'dateCreated'=>null,
            ];

            $price_data = [
                'supplierSku'=>$supplierSku,
                'dealerId'=>$dealerId,
                'price'=>trim($line['Price']),
                'promotion'=>null,
            ];

            try {
                $this->populate($product_data, $price_data);
            } catch (\Exception $ex) {
                var_dump($product_data);
                error_log('xxx '.$ex->getMessage());
            }

            if ((substr($line['Product Description'],0,4)=='OEM ') && $manufacturerId) {
                $oem_cartridges[$manufacturerId][$vpn] = $supplierSku;
            }

            #-----
            if (!empty($line['CompatibleSkus']) && !empty($line['CompatibleBrand'])) {
                if (!preg_match('#, ([^,]*) YIELD#i', $line['Product Description'], $match)) {
                    continue;
                }
                $yield = trim(str_replace([
                    'ULTRA',
                    'SUPER',
                    'HIGH',
                    'EXTRA',
                    'FULL',
                    'STARTER CARTRIDGE',
                    'K='
                ],[
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ],$match[1]));
                $e=explode('/',$yield);
                if (count($e)>1) $yield=array_pop($e);
                $yield=$this->expandYield($yield); //trim(str_replace(['.','K'],['','000'],$yield));
                if (!$yield) continue;

                $brand = $line['CompatibleBrand'];
                if (isset($manufacturers[$brand])) $oem_mfg_id = $manufacturers[$brand];
                else {
                    error_log('unknown oem brand: ' . $line['CompatibleBrand']);
                    die();
                    continue;
                }

                $oem_lines = [];
                foreach (explode(',', $line['CompatibleSkus']) as $n) {
                    $n = str_replace('-', '', trim($n));
                    if (empty($n)) continue;
                    if (isset($skus[$oem_mfg_id][$n])) {
                        $oem_lines[] = $skus[$oem_mfg_id][$n];
                    }
                }
                if (empty($oem_lines)) continue;

                if (!isset($manufacturers[strtoupper($line['Brand'])])) {
                    error_log('unknown compatible brand: '.$line['Brand']);
                    continue;
                }
                $comp_mfg_id = $manufacturers[strtoupper($line['Brand'])];

                $imgUrl = 'http://www.acmtech.com/Pictures/Pic_Small/'.substr($supplierSku,0,2).'/'.$supplierSku.'.jpg';

                $this->populateCompatible($db, $skus, $comp_mfg_id, $supplierSku, $imgUrl, $name, null, null, $line['Price'], $yield, $oem_lines, $dealerId);
            }
        }

        gc_collect_cycles();
        echo "2. ".round(memory_get_usage()/(1024*1024))." MB\n";


        $cursor = $db->query("select base_product.id, base_product.manufacturerId, sku from base_product join base_printer_consumable using (id)");
        while ($line = $cursor->fetch(\PDO::FETCH_ASSOC)) {
            $sku = $line['sku'];
            $manufacturerId = $line['manufacturerId'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($oem_cartridges[$manufacturerId][$sku])) {
                //update supplier_product set baseProductId=? where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $oem_cartridges[$manufacturerId][$sku]]);
            }
        }
        $cursor->closeCursor();

        gc_collect_cycles();
        echo "3. ".round(memory_get_usage()/(1024*1024))." MB\n";

        echo time() - $this->timerStart."s\n";
        return true;
    }

    private function populateCompatible($db, $skus, $comp_mfg_id, $supplierSku, $imgUrl, $name, $weight, $upc, $price, $yield, $oem_lines, $dealerId) {
        if (empty($this->compatibleStatements)) {
            $this->compatibleStatements['st1'] = $db->prepare("REPLACE INTO base_product SET userId=1, dateCreated=now(), isSystemProduct=1, imageUrl=?, base_type=?, manufacturerId=?, sku=?, name=?, weight=?, UPC=?");
            $this->compatibleStatements['st2'] = $db->prepare("REPLACE INTO base_printer_consumable SET id=?, cost=?, pageYield=?, quantity=?, type=?");
            $this->compatibleStatements['st3'] = $db->prepare("REPLACE INTO base_printer_cartridge SET id=?, colorId=?");
            $this->compatibleStatements['st4'] = $db->prepare("REPLACE INTO compatible_printer_consumable SET oem=?, compatible=?");
            $this->compatibleStatements['st5'] = $db->prepare("REPLACE INTO dealer_toner_attributes SET tonerId=?, dealerId=?, cost=?, dealerSku=?");
        }

        if (!$comp_mfg_id) die('comp_mfg_id?');

        if (!isset($skus[$comp_mfg_id][$supplierSku])) {
            $oem_line = current($oem_lines);
            $this->compatibleStatements['st1']->execute([$imgUrl, $oem_line['base_type'], $comp_mfg_id, $supplierSku, $name, $weight, $upc]);
            $base_id = $db->lastInsertId();
            $this->compatibleStatements['st2']->execute([$base_id, $price, $yield, $oem_line['quantity'], $oem_line['type']]);
            if ($oem_line['colorId']) $this->compatibleStatements['st3']->execute([$base_id, $oem_line['colorId']]);
        } else {
            $base_id = $skus[$comp_mfg_id][$supplierSku]['id'];
        }

        foreach ($oem_lines as $oem_line) {
            echo "{$oem_line['id']} > {$base_id} \n";
            $this->compatibleStatements['st4']->execute([$oem_line['id'], $base_id]);
        }

        $this->compatibleStatements['st5']->execute([$base_id, $dealerId, $price, $supplierSku]);
    }

    private function updateDH($dealerSupplier) {

    }
    private function updateClover($dealerSupplier) {

        $comp_mfg_id = 63; //Clover Technologies

        $csv_filename = APPLICATION_BASE_PATH . '/data/cache/'.$dealerSupplier['url'];
        if (!file_exists($csv_filename)) {
            error_log('file not found: '.$csv_filename);
            return;
        }

        echo "processing {$csv_filename} for dealer {$dealerSupplier['dealerId']}\n";

        $db = \Zend_Db_Table::getDefaultAdapter();
        $manufacturers = [];
        foreach ($db->query('select * from manufacturers order by displayname')->fetchAll() as $line) {
            $n = strtoupper($line['fullname']);
            $manufacturers[$n] = $line['id'];
            $n = strtoupper($line['displayname']);
            $manufacturers[$n] = $line['id'];
            if ($n=='FUJI XEROX') $manufacturers['FUJIFILM'] = $line['id'];
        }

        $skus = [];
        foreach ($db->query('select * from base_product p join base_printer_consumable c using(id) left join base_printer_cartridge a using(id)')->fetchAll() as $line) {
            $sku = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$line['sku']));
            $skus[$line['manufacturerId']][$sku] = $line;
        }

        $fp = fopen($csv_filename,'rb');
        $cols = fgetcsv($fp, null, ';', '"', '""');

        $i = 0;
        while($line = fgetcsv($fp, null, ';', '"', '""')) {
            $line = array_combine($cols, $line);

            if (empty($line['Page Yield']) || ($line['Page Yield']==' - ')) {
                continue;
            }

            $ignore = false;
            switch (trim($line['OEM'])) {
                case '':
                case 'PITNEY BOW':
                case 'PB/IMAG':
                case 'OTHER':
                case 'Olivetti':
                case 'Muratec':
                case 'LENOVO':
                case 'Kodak':
                case 'ZEBRA':
                case 'XER-KM-OKI':
                case 'SOURCETECH':
                    $ignore = true;
            }
            switch (trim($line['Type'])) {
                case 'Ink Postage':
                case 'Switch/Router':
                case 'Hard Drive':
                case 'Belt':
                case 'Sensor':
                case 'Processor':
                case 'Cable':
                case '3D Filament':
                case 'Memory':
                case 'Ribbon':
                    $ignore = true;
            }
            if ($ignore) continue;

            $line['Page Yield'] = $this->expandYield($line['Page Yield']); //str_replace(',','',$line['Page Yield']);
            if (!$line['Page Yield']) {
                error_log("{$line['Page Yield']} ???");
                continue;
            }

            if (!isset($manufacturers[strtoupper($line['OEM'])])) {
                #var_dump($line);
                #die($line['brand']);
                error_log('unknown brand: '.$line['OEM']);
                continue;
            }
            $oem_mfg_id = $manufacturers[strtoupper($line['OEM'])];

            $oem_lines=[];
            foreach (explode(',', $line['OEM Part #']) as $n) {
                $n = str_replace('-','',trim($n));
                if (empty($n)) continue;
                if (preg_match('#^(.+)\([A-Z]\)$#', $n, $match)) {
                    $n = $match[1];
                }
                if (isset($skus[$oem_mfg_id][$n])) {
                    $oem_lines[] = $skus[$oem_mfg_id][$n];
                }
            }
            if (empty($oem_lines)) {
                //error_log("unknown OEM part: {$line['OEM']} {$line['OEM Part #']}");
                continue;
            }

            $imgUrl = $line['Image URL'];
            $supplierSku = str_replace('-','',trim($line['Item #']));

            $name = '';
            $weight = $line['Weight'];
            $upc = $line['UPC Code'];
            $price = $line['Price'];
            $yield = $line['Page Yield'];
            $dealerId = $dealerSupplier['dealerId'];
            $this->populateCompatible($db, $skus, $comp_mfg_id, $supplierSku, $imgUrl, $name, $weight, $upc, $price, $yield, $oem_lines, $dealerId);

            $i++;
        }

        gc_collect_cycles();
        echo "{$i} lines processed\n";

        $this->total_result['products_total'] = $i;
    }
    private function updateGenuine($dealerSupplier) {
        $base_uri = trim($dealerSupplier['url'], '/');

        require_once(APPLICATION_BASE_PATH.'/library/My/MyCookieJar.php');
        $jar = new \MyCookieJar();

        $client = new Client([
            //'debug' => true,
            'verify' => APPLICATION_BASE_PATH.'/docs/cacert.pem',
            'cookies' => $jar,
            'headers' => [
                'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language'=>'en-US,en;q=0.5',
                'Accept-Encoding'=>'gzip, deflate',
                'Connection'=>'keep-alive',
                'User-Agent'=>'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0',
            ]
        ]);

        try {

            /**/
            $r = $client->get($base_uri.'/landing.asp?autopage=/Default.asp');
            if ($r->getStatusCode() != 200) {
                error_log($dealerSupplier['url'] . ' > ' . $r->getStatusCode());
                return;
            }

            $r = $client->post(
                $base_uri.'/security_logonScript_siteFront.asp?' . http_build_query([
                    'action' => 'logon',
                    'pageredir' => '/default.asp',
                    'parent_c_id' => '',
                    'returnpage' => 'landing.asp?'
                ]),
                array(
                    'allow_redirects' => false,
                    'headers' => [
                        'Referer'=>$base_uri.'/landing.asp?autopage=/Default.asp',
                    ],
                    'form_params' => [
                            'username' => $dealerSupplier['user'],
                            'password' => $dealerSupplier['pass'],
                            'B1' => 'GO!',
                            'logontype' => 'customer',
                    ]
                )
            );
            if ($r->getStatusCode() != 302) {
                error_log($dealerSupplier['url'] . '/security_logonScript_siteFront.asp > ' . $r->getStatusCode());
                return;
            }

            $redirect = $r->getHeaderLine('Location');
            $r = $client->get($redirect, [
                'allow_redirects' => false,
                'headers' => [
                ],
            ]);
            if ($r->getStatusCode() != 200) {
                error_log($redirect. ' > ' . $r->getStatusCode());
                return;
            }

            $r = $client->get(str_replace('https','http',$base_uri).'/product_list.asp?' . http_build_query(['downloadasfile' => '1', '' => '']), [
                'allow_redirects' => true,
                'headers' => [
                ],
            ]);
            if ($r->getStatusCode() != 200) {
                error_log($dealerSupplier['url'] . '/product_list.asp > ' . $r->getStatusCode());
                return;
            }

            if ($r->getHeaderLine('Content-Type')!='application/csv; charset=utf-8; Charset=utf-8') {
                error_log($dealerSupplier['url'] . '/product_list.asp > ' . $r->getHeaderLine('Content-Type'));
                return;
            }

            $str = $r->getBody()->getContents();

            $fp = fopen('php://memory', 'r+');
            fwrite($fp, $str);
            rewind($fp);

            echo "processing {$dealerSupplier['url']}/product_list.asp for dealer {$dealerSupplier['dealerId']}\n";

            /**/
            //$fp = fopen(APPLICATION_BASE_PATH.'/data/cache/Product_Listing.csv','rb');
            /**/
            #===============================================

            $db = \Zend_Db_Table::getDefaultAdapter();
            $manufacturers = [];
            foreach ($db->query('select * from manufacturers order by displayname')->fetchAll() as $line) {
                $n = strtoupper($line['displayname']);
                $manufacturers[$n] = $line['id'];
                if ($n=='KONICA') $manufacturers['KONICA/MINOLTA'] = $line['id'];
                if ($n=='OKI') $manufacturers['OKIDATA'] = $line['id'];
            }

            $skus = [];
            foreach ($db->query('select * from base_product p join base_printer_consumable c using(id) left join base_printer_cartridge a using(id)')->fetchAll() as $line) {
                $sku = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$line['sku']));
                $skus[$line['manufacturerId']][$sku] = $line;
            }

            $cols = fgetcsv($fp);

            $i = 0;
            while($line = fgetcsv($fp)) {
                $line = array_combine($cols, $line);
                $line['brand'] = strip_tags($line['brand']);
                if ($line['category']=='reset-chips') continue;
                if ($line['type']=='ORIGINAL') continue;
                if ($line['brand']=='Generic') continue;

                if (empty($line['yield']) || (strtolower($line['yield'])=='n/a')) continue;
                if (preg_match('#([\d,]+) page#i',$line['yield'], $match)) $line['yield'] = $match[1];
                $line['yield'] = $this->expandYield($line['yield']); //str_replace(',','',$line['yield']);
                if (!$line['yield']) continue;

                $pair = explode(' ', $line['nm'], 2);
                $brand = strtoupper($pair[0]);
                if ($brand=='FRANCOTYP') continue;
                if ($brand=='KM') continue;
                if ($brand=='KODAK') continue;
                if ($brand=='MITA') continue;
                if ($brand=='NEOPOST') continue;
                $oem_mfg_id = $manufacturers[$brand];

                $oem_lines=[];
                foreach (explode(',', $line['oem']) as $n) {
                    $n = str_replace('-','',trim($n));
                    if (empty($n)) continue;
                    if (isset($skus[$oem_mfg_id][$n])) {
                        $oem_lines[] = $skus[$oem_mfg_id][$n];
                    }
                }
                if (empty($oem_lines)) continue;

                if (!isset($manufacturers[strtoupper($line['brand'])])) {
                    #var_dump($line);
                    #die($line['brand']);
                    error_log('unknown brand: '.$line['brand']);
                    continue;
                }
                $comp_mfg_id = $manufacturers[strtoupper($line['brand'])];

                $imgUrl = 'http://store.genuinesupply.ca/images/'.$line['lg_pic'];
                $supplierSku = str_replace('-','',trim($line['sku']));

                $name = '';
                $weight = $line['weight'];
                $upc = $line['upc'];
                $price = $line['cust_price'];
                $yield = $line['yield'];
                $dealerId = $dealerSupplier['dealerId'];
                $this->populateCompatible($db, $skus, $comp_mfg_id, $supplierSku, $imgUrl, $name, $weight, $upc, $price, $yield, $oem_lines, $dealerId);

                $i++;
            }

            gc_collect_cycles();
            echo "{$i} lines processed\n";

            $this->total_result['products_total'] = $i;

        } catch (RequestException $ex) {
            error_log($ex->getMessage());
        }
    }

    private function updateTechData($dealerSupplier) {
        if (strpos($dealerSupplier['url'], 'https://www.techdata.ca') === 0) {
            return $this->updateTechDataNA($dealerSupplier);
        }
        return $this->updateTechDataSA($dealerSupplier);
    }
    private function updateTechDataNA($dealerSupplier) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/cache/' . sprintf('techdata-%s.zip', $dealerSupplier['dealerId']);
        echo "starting with zip file {$zip_filename} for dealer {$dealerSupplier['dealerId']}\n";

        try {
            if (!file_exists($zip_filename) || (filemtime($zip_filename) < strtotime('-6 DAY'))) {
                echo "downloading zip file {$zip_filename} for dealer {$dealerSupplier['dealerId']}\n";
                $url = $dealerSupplier['url'].'?Auto=Y&UserID='.$dealerSupplier['user'].'&Password='.$dealerSupplier['pass'].'&Type=Price&Name=ProdCod.zip';
                $client = new Client(['verify'=>false]);
                $response = $client->get($url);
                file_put_contents($zip_filename, $response->getBody());
            }

            if (!file_exists($zip_filename) || (filesize($zip_filename)==0)) {
                error_log('zip download failed');
                return false;
            }

            echo "extracting zip file {$zip_filename} for dealer {$dealerSupplier['dealerId']}\n";
            $zip = $this->getZipAdapter();
            $zip->open($zip_filename);
            #$finfo = $zip->statIndex(0);
            $txt_filename = dirname($zip_filename).'/products.dbf'; //$finfo['name'];
            $zip_result = $zip->extractTo(dirname($zip_filename));
            $zip->close();

            if (!$zip_result) {
                throw new \Exception('Zip extract failed: '.$zip_filename.' >> '.$txt_filename);
            }

            $zip = null;
            $ftp = null;

        } catch (\Exception $ex) {
            print_r($ex);
            Logger::logException($ex);
            return false;
        }

        if (!is_file($txt_filename) || !file_exists($txt_filename) || (filesize(dirname($zip_filename)==0))) {
            return false;
        }

        gc_collect_cycles();
        echo "1. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $comcat = [];
        $table = new Table(dirname($zip_filename).'/comcat.dbf');
        while($record = $table->nextRecord()) {
            $grp = $record->forceGetString('grp');
            $cat = $record->forceGetString('cat');
            $comcat[$grp][$cat] = $record->forceGetString('descr');
        }

        $manuf = [];
        $table = new Table(dirname($zip_filename).'/manuf.dbf');
        while($record = $table->nextRecord()) {
            $vend_code = $record->forceGetString('vend_code');
            $manuf[$vend_code] = $record->forceGetString('vend_name');
        }

        $manufacturers=[];
        foreach ($db->query('select * from techdata_manufacturer') as $line) {
            $manufacturers[$line['name']] = $line['manufacturerId'];
        }
        foreach ($db->query('select * from manufacturers') as $line) {
            $manufacturers[$line['fullname'].'%'] = $line['id'];
            $manufacturers[$line['displayname'].'%'] = $line['id'];
        }
        $manufacturer_lookup = [];

        $i=0;$j=0;
        $table = new Table($txt_filename);
        while($record = $table->nextRecord()) {
            if (++$i % 50000 == 0) {
                echo "{$j}/{$i} >> ".round(memory_get_usage()/(1024*1024))." MB\n";
            }
            $manufacturerId = null;
            $manufacturerName = $vend_code = $record->forceGetString('vend_code');
            if (isset($manuf[$vend_code])) $manufacturerName = $manuf[$vend_code];

            if (isset($manufacturer_lookup[$vend_code])) {
                $manufacturerId = $manufacturer_lookup[$vend_code];
            } else {

                foreach ($manufacturers as $mfg_name => $mfg_id) {
                    if (preg_match('#' . str_replace('%', '.*', $mfg_name) . '#i', $manufacturerName)) {
                        $manufacturerId = $mfg_id;
                    }
                }
                $manufacturer_lookup[$vend_code] = $manufacturerId;
            }

            $vpn = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$record->forceGetString('manu_part')));

            $grp = $record->forceGetString('grp');
            $cat = $record->forceGetString('cat');
            $part_num = $record->forceGetString('part_num');

            if (is_numeric($grp)) continue;
            //if ($grp=='CK') continue; //	Computer- Chromebook
            //if ($grp=='F5') continue; //	Periph- Graphic Tablets
            if ($grp=='G5') continue; //	Storage- Drive Enclosure
            if ($grp=='I2') continue; //	Periph- Power Equipment
            if ($grp=='J5') continue; //	Periph- Audio Devices
            if ($grp=='J8') continue; //	Component- I/O Adapters
            //if ($grp=='MD') continue; //	Computer- Mini Desktop PC
            if ($grp=='N2') continue; //	Network- LAN/WAN Bus Adapters
            if ($grp=='N4') continue; //	Network- Transceivers/Converters
            if ($grp=='O5') continue; //	Network- Wiring Closet/Rack
            if ($grp=='O7') continue; //	Network- Modules
            if ($grp=='S9') continue; //	S/W- Inter/Intra/Extranet
            //if ($grp=='T4') continue; //	Computer- Hybrid Tablets
            //if ($grp=='B4') continue; //	Computer- Bare Bones Desktop
            //if ($grp=='C2') continue; //	Periph- Monitors/Displays
            if ($grp=='E1') continue; //	Misc- Education
            if ($grp=='F6') continue; //	Periph- Mounting Kits
            if ($grp=='G7') continue; //	Storage- Floppy/ZIP Drives
            if ($grp=='G8') continue; //	Storage- Hard Drives
            if ($grp=='I3') continue; //	Periph- UPS Systems
            //if ($grp=='M3') continue; //	Computer- Workstation PC
            if ($grp=='N3') continue; //	Network- Serial I/O
            if ($grp=='O2') continue; //	Cables/Testing/Tools
            if ($grp=='O3') continue; //	Modems
            if ($grp=='P4') continue; //	Component- Sound Cards/Kits
            if ($grp=='S7') continue; //	S/W- Developer Tools
            if ($grp=='S8') continue; //	S/W- Communication
            if ($grp=='T1') continue; //	Security/Surveillance
            //if ($grp=='G6') continue; //	Storage- DVD Drives
            if ($grp=='G9') continue; //	Storage- Tape Drives
            //if ($grp=='H2') continue; //	Periph- Multifunction Device
            //if ($grp=='J2') continue; //	Storage- Portable Flash Memory Drives
            if ($grp=='J3') continue; //	Component- Memory
            if ($grp=='J4') continue; //	Storage- Adapters & I/F
            //if ($grp=='J9') continue; //	Storage- Media
            //if ($grp=='L1') continue; //	Periph- Projectors
            //if ($grp=='M4') continue; //	Computer- Tower Server
            //if ($grp=='M6') continue; //	Computer- Server Rackmount
            //if ($grp=='MP') continue; //	Computer- Blade PC
            if ($grp=='O1') continue; //	Network- Telephony
            //if ($grp=='P3') continue; //	Storage- Servers
            if ($grp=='P5') continue; //	Periph- Switch Boxes
            //if ($grp=='S1') continue; //	S/W- O/S & Enhancements
            if ($grp=='S2') continue; //	S/W- Networking
            //if ($grp=='Y1') continue; //	Office Equipment
            //if ($grp=='Z1') continue; //	Canada Only - Floor Standing Printers
            if ($grp=='B2') continue; //	Computer- Mainboards
            if ($grp=='C1') continue; //	Periph- Control Devices
            if ($grp=='C3') continue; //	Component- Video Adapters
            if ($grp=='C4') continue; //	Consumer Electronics
            //if ($grp=='CX') continue; //	Computer- Chromebox
            if ($grp=='F7') continue; //	Periph- Cellular Accessory
            if ($grp=='G1') continue; //	Storage- Blu-ray Drives
            //if ($grp=='H3') continue; //	Periph- Copiers
            if ($grp=='J6') continue; //	Periph- Cameras
            //if ($grp=='M9') continue; //	Computer- Thin Client/Terminal
            //if ($grp=='MX') continue; //	Computer- AIO Desktop PC
            if ($grp=='N9') continue; //	Network- Host Connectivity
            if ($grp=='O8') continue; //	Network- Multislot Chassis
            if ($grp=='O9') continue; //	Network- PS/Components/Device SW
            if ($grp=='P6') continue; //	(POS) Point of Sale/Data Capture
            if ($grp=='Q1') continue; //	Books - Printed Material
            //if ($grp=='S0') continue; //	S/W- Business
            //if ($grp=='T2') continue; //	Computer- Convertible Tablets
            if ($grp=='C5') continue; //	Mobile- Cellular Phones
            //if ($grp=='F2') continue; //	Periph- Scanners & Microfilm Devices
            //if ($grp=='F3') continue; //	Periph- Keyboards/Keypads
            //if ($grp=='J7') continue; //	Periph- Desk Accessories
            //if ($grp=='M0') continue; //	Computer- Server RM Multiprocessor
            //if ($grp=='M1') continue; //	Computer- Desktop PC
            //if ($grp=='M2') continue; //	Computer- Desktop Tower
            //if ($grp=='M5') continue; //	Computer- Tower Server Multiprocessor
            //if ($grp=='M7') continue; //	Computer- Notebook
            //if ($grp=='MB') continue; //	Computer- BladeServer
            if ($grp=='N5') continue; //	Network- Infrastructure
            if ($grp=='S4') { //	S/W- Graphics/Desktop Publish
                if ($cat=='B4') continue;
            }
            if ($grp=='S6') continue; //	S/W- Games
            //if ($grp=='SA') continue; //	S/W- AntiVirus/SPAM
            if ($grp=='SV') continue; //	S/W- Virtualization
            //if ($grp=='T3') continue; //	Computer- Slate Tablets
            if ($grp=='V7') continue; //	Mobile- Accessories
            if ($grp=='B3') continue; //	Computer- Chassis
            //if ($grp=='C6') continue; //	Mobile- Smart Phones
            if ($grp=='E2') continue; //	Misc- Other
            //if ($grp=='F4') continue; //	Periph- Pointing Devices
            //if ($grp=='G3') continue; //	Storage- CD-ROM/Optical Drives
            //if ($grp=='G4') continue; //	Storage- Disk Array Systems
            //if ($grp=='H4') continue; //	Periph- FAX Machines
            //if ($grp=='H5') continue; //	Periph- Printer Options
            //if ($grp=='H6') continue; //	Periph- Printers
            //if ($grp=='K1') continue; //	Misc- Services & Agreements
            if ($grp=='O6') continue; //	Network- LAN/WAN/INET Hub Devices
            //if ($grp=='P1') continue; //	Periph- Notebook/Tablet/PDA Options
            if ($grp=='P2') continue; //	Component- Processors
            if ($grp=='S3') continue; //	S/W- Home/Education
            //if ($grp=='S5') continue; //	S/W- Utilities

            $product_data = [
                'supplierSku'=>$part_num,
                'manufacturer'=>$manufacturerName,
                'manufacturerId'=>$manufacturerId,
                'vpn'=>$vpn,
                'name'=>$record->forceGetString('descr'),
                'msrp'=>$record->getFloat('retail'),
                'weight'=>0,
                'length'=>0,
                'width'=>0,
                'height'=>0,
                'upc'=>$record->forceGetString('upc'),
                'description'=>$record->forceGetString('descr'),
                'package'=>null,
                'isStock'=>intval($record->forceGetString('qty'))>0?1:0,
                'qty'=>json_encode([
                    'qty'=>$record->forceGetString('qty'),
                    'columbus'=>$record->forceGetString('columbus'),
                    'vancouver'=>$record->forceGetString('vancouver'),
                    'orbitor'=>$record->forceGetString('orbitor'),
                ]),
                'category'=>$comcat[$grp][$cat],
                'categoryId'=>"{$grp}/{$cat}",
                'dateCreated'=>null,
            ];

            $price_data = [
                'supplierSku'=>$part_num,
                'dealerId'=>$dealerSupplier['dealerId'],
                'price'=>trim($record->getString('list_price')),
                'promotion'=>0,
            ];

            try {
                $this->populate($product_data, $price_data);
                $j++;
            } catch (\Exception $ex) {
                var_dump($product_data);
                die ('xxx '.$ex->getMessage());
            }

            $skus[$manufacturerId][$vpn] = $part_num;
            unset($this->remaining_product[$part_num]);
        }

        #==
        $this->deleteRemainingProducts($dealerSupplier['supplierId'], $dealerSupplier['dealerId']);
        #==

        gc_collect_cycles();
        echo "5. ".round(memory_get_usage()/(1024*1024))." MB\n";

        //xxx
        $cursor = $db->query("select id, manufacturerId, sku, weight, UPC from base_product");
        while ($line=$cursor->fetch(\PDO::FETCH_ASSOC)) {
            $manufacturerId = $line['manufacturerId'];
            $sku = $line['sku'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($skus[$manufacturerId][$sku])) {
                $supplierSku = $skus[$manufacturerId][$sku];
                //update supplier_product set baseProductId=? where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $supplierSku]);
                #if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                    //update base_product set sku=(select vpn from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?) where id=?
                #    $this->sku_statement->execute([$supplierSku, $supplierSku, $supplierSku, $line['id']]);
                #}
            }
        }
        $cursor->closeCursor();

        gc_collect_cycles();
        echo "6. ".round(memory_get_usage()/(1024*1024))." MB\n";

        echo time() - $this->timerStart."s\n";
        return true;
    }

    private function updateTechDataSA($dealerSupplier) {

        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/temp/'.$dealerSupplier['url'];

        $product_file='';
        $price_file='';
        $stock_file='';

        try {
            if (!file_exists($zip_filename) || (filemtime($zip_filename) < strtotime('-6 DAY'))) {
                #$ftp = $this->getFtpClient();
                #$ftp->get($dealerSupplier['url'], $dealerSupplier['user'], $dealerSupplier['pass'], $zip_filename);
                echo "file not found: {$zip_filename}\n";
                return null;
            }

            $zip = $this->getZipAdapter();
            $zip->open($zip_filename);
            for ($i=0;$i<$zip->numFiles;$i++) {
                $finfo = $zip->statIndex($i);
                if (preg_match('#__MACOSX#',$finfo['name'])) continue;
                if (preg_match('#Material.txt#',$finfo['name'])) $product_file = $finfo['name'];
                if (preg_match('#Price.txt#',$finfo['name'])) $price_file = $finfo['name'];
                if (preg_match('#Availability.txt#',$finfo['name'])) $stock_file = $finfo['name'];
                $subdir = dirname($finfo['name']);
                if (!file_exists(dirname($zip_filename).'/'.$subdir)) mkdir(dirname($zip_filename).'/'.$subdir);
                $zip->extractTo(dirname($zip_filename));
            }
            $zip->close();

        } catch (\Exception $ex) {
            print_r($ex);
            Logger::logException($ex);
            return false;
        }

        if (empty($product_file)) { echo "product_file?\n"; return null; }
        if (empty($price_file)) { echo "price_file?\n"; return null; }
        if (empty($stock_file)) { echo "stock_file?\n"; return null; }

        gc_collect_cycles();
        echo "1. ".round(memory_get_usage()/(1024*1024))." MB\n";

        #----
        $columns = [
            'Matnr',
            'ManufPartNo',
            'GTIN',
            'Qty',
        ];
        $qty = [];
        $filename=dirname($zip_filename).'/'.$stock_file;
        echo "{$filename}\n";
        $fp = fopen($filename, 'rb');

        $hdr = fgetcsv($fp, null, "\t");
        while ($line = fgetcsv($fp, null, "\t")) {
            while (count($line) > count($columns)) {
                array_pop($line);
            }
            $line = array_combine($columns, $line);
            $qty[$line['Matnr']] = $line['Qty'];
        }
        fclose($fp);

        gc_collect_cycles();
        echo "2. ".round(memory_get_usage()/(1024*1024))." MB\n";

        #----
        $filename=dirname($zip_filename).'/'.$product_file;
        echo "{$filename}\n";
        $fp = fopen($filename, 'rb');

        $category_mattnr = [];

        $manufacturers=[];
        foreach ($db->query('select * from techdata_manufacturer') as $line) {
            $reg = '#^'.str_replace('%','.*',strtoupper($line['name'])).'$#i';
            $manufacturers[$reg] = $line['manufacturerId'];
        }

        //Matnr	ShortDescription	LongDescription	ManufPartNo	Manufacturer	ManufacturerGlobalDescr	GTIN	ProdFamilyID	ProdFamily	ProdClassID	ProdClass	ProdSubClassID	ProdSubClass
        //ArticleCreationDate	CNETavailable	CNETid	ListPrice	Weight	Length	Width	Heigth	NoReturn	MayRequireAuthorization	EndUserInformation	FreightPolicyException
        $columns = fgetcsv($fp, null, "\t");
        $matnrs=[];
        while ($line = fgetcsv($fp, null, "\t")) {
            while (count($line)>count($columns)) {
                array_pop($line);
            }
            $line = array_combine($columns, $line);

            $is_relevant=false;
            switch ($line['ProdClass']) {
                case 'Peripherals' :
                case 'Servers' :
                case 'Printers' :
                case 'Monitors & Displays' :
                case 'Services/Support Printers' :
                case 'Operating Systems' :
                case 'Notebooks & Tablet PCs' :
                case 'Desktops & Workstations' :
                case 'Hard Drives' :
                {
                    $is_relevant=true;
                    break;
                }
            }
            if (!$is_relevant) continue;

            $matnrs[$line['Matnr']] = true;

            $manufPartNo = preg_replace('/#\w\w\w$/', '', preg_replace('#\?\w+$#', '', $line['ManufPartNo']));
            $category_mattnr[$line['ProdFamilyID']][$line['ProdClassID']][$manufPartNo] =  $line['Matnr'];
            $qty = isset($qty[$line['Matnr']]) ? $qty[$line['Matnr']] : 0;
            $price = isset($price[$line['Matnr']]) ? $price[$line['Matnr']] : ['CustBestPrice'=>'0','Promotion'=>null];

            $manufacturerId = null;
            foreach ($manufacturers as $mfg_reg=>$mfg_id) {
                if (preg_match($mfg_reg, $line['Manufacturer'])) {
                    $manufacturerId = $mfg_id;
                }
            }

            $product_data = [
                'supplierSku'=>trim($line['Matnr']),
                'manufacturer'=>$line['Manufacturer'],
                'manufacturerId'=>$manufacturerId,
                'vpn'=>$manufPartNo,
                'name'=>$line['ShortDescription'],
                'msrp'=>$line['ListPrice'],
                'weight'=>$line['Weight'] ? $line['Weight']*0.453592 : null,
                'length'=>$line['Length'] ? $line['Length']*2.54/100 : null,
                'width'=>$line['Width'] ? $line['Width']*2.54/100 : null,
                'height'=>$line['Heigth'] ? $line['Heigth']*2.54/100 : null,
                'upc'=>$line['GTIN'],
                'package'=>null,
                'description'=>trim($line['LongDescription']),
                'isStock'=>intval($qty)>0?1:0,
                'qty'=>$qty,
                'category'=>$line['ProdFamily'].' | '.$line['ProdClass'].' | '.$line['ProdSubClass'],
                'categoryId'=>$line['ProdSubClassID'],
                'dateCreated'=>$line['ArticleCreationDate'],
            ];

            try {
                $this->populate($product_data, null);
            } catch (\Exception $ex) {
                var_dump($product_data);
                die ('xxx '.$ex->getMessage());
            }

            if ($manufacturerId) {
                $category_mattnr[$line['ProdFamilyID']][$line['ProdClassID']][$manufacturerId][$manufPartNo] = $line['Matnr'];
            }

            unset($this->remaining_product[$line['Matnr']]);
        }
        fclose($fp);

        gc_collect_cycles();
        echo "3. ".round(memory_get_usage()/(1024*1024))." MB\n";

        #----
        $columns = [
            'Matnr',
            'CustBestPrice',
            'Promotion',
        ];
        $filename=dirname($zip_filename).'/'.$price_file;
        echo "{$filename}\n";
        $fp = fopen($filename, 'rb');

        $hdr = fgetcsv($fp, null, "\t");
        while ($line = fgetcsv($fp, null, "\t")) {
            while (count($line) > count($columns)) {
                array_pop($line);
            }
            $line = array_combine($columns, $line);

            if (!isset($matnrs[$line['Matnr']])) continue;

            $price_data = [
                'supplierSku'=>$line['Matnr'],
                'dealerId'=>$dealerSupplier['dealerId'],
                'price'=>$line['CustBestPrice'],
                'promotion'=>$line['Promotion']=='N'?0:1,
            ];

            try {
                $this->populate(null, $price_data);
            } catch (\Exception $ex) {
                var_dump($price_data);
                die ('xxx '.$ex->getMessage());
            }
        }
        fclose($fp);

        gc_collect_cycles();
        echo "4. ".round(memory_get_usage()/(1024*1024))." MB\n";

        #--
        $this->deleteRemainingProducts($dealerSupplier['supplierId'], $dealerSupplier['dealerId']);

        #--

        $toner_count = 0;
        $weight_count = 0;
        $cursor = $db->query("select base_product.id, manufacturerId, sku, weight, UPC from base_product join base_printer_consumable using (id)");
        while ($line=$cursor->fetch(\PDO::FETCH_ASSOC)) {
            $sku = $line['sku'];
            $manufacturerId = $line['manufacturerId'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($category_mattnr['SUPPLIESA']['PERIPHERA'][$manufacturerId][$sku])) {
                $matnr = $category_mattnr['SUPPLIESA']['PERIPHERA'][$manufacturerId][$sku];
                //update supplier_product set baseProductId=? where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $matnr]);
                $toner_count += $this->base_product_statement->rowCount();
                if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                    //update base_product set sku=(select vpn from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?) where id=?
                    $this->sku_statement->execute([$matnr, $matnr, $matnr, $line['id']]);
                    $weight_count += $this->sku_statement->rowCount();
                }
            }
        }
        $cursor->closeCursor();
        echo " toner_count:{$toner_count} --- weight_count:{$weight_count}\n";

        $cursor = $db->query("select * from base_product where base_type='printer'");
        while ($line=$cursor->fetch(\PDO::FETCH_ASSOC)) {
            $sku = $line['sku'];
            if (!$sku) continue;
            $manufacturerId = $line['manufacturerId'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($category_mattnr['PERIPHERA']['PRINTERSP'][$manufacturerId][$sku])) {
                $matnr = $category_mattnr['PERIPHERA']['PRINTERSP'][$manufacturerId][$sku];
                $this->base_product_statement->execute([$line['id'], $matnr]);
                if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                    $this->sku_statement->execute([$matnr, $matnr, $matnr, $line['id']]);
                }
            }
        }
        $cursor->closeCursor();
        #--

        gc_collect_cycles();
        echo "6. ".round(memory_get_usage()/(1024*1024))." MB\n";
        echo time() - $this->timerStart."s\n";
        return true;
    }

    private function updateSynnex($dealerSupplier)
    {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/cache/' . sprintf('synnex-%s.zip', $dealerSupplier['dealerId']);
        echo "starting with zip file {$zip_filename} for dealer {$dealerSupplier['dealerId']}\n";

        try {
            if (!file_exists($zip_filename) || (filemtime($zip_filename) < strtotime('-6 DAY'))) {
                echo "downloading zip file {$zip_filename} for dealer {$dealerSupplier['dealerId']}\n";
                $ftp = $this->getFtpClient();
                $ftp->get($dealerSupplier['url'], $dealerSupplier['user'], $dealerSupplier['pass'], $zip_filename);
            }

            if (!file_exists($zip_filename)) {
                error_log('zip download failed');
                return false;
            }

            $zip = $this->getZipAdapter();
            $zip->open($zip_filename);
            $finfo = $zip->statIndex(0);
            $txt_filename = $finfo['name'];
            $zip_result = ($txt_filename!='') && $zip->extractTo(dirname($zip_filename));
            $zip->close();

            if (!$zip_result) {
                throw new \Exception('Zip extract failed: '.$zip_filename.' >> '.$txt_filename);
            }

            $zip = null;
            $ftp = null;

        } catch (\Exception $ex) {
            print_r($ex);
            Logger::logException($ex);
            return false;
        }

        $columns = [
            'Trading_Partner_Code',
            'Detail_Record_ID',
            'Manufacturer_Part',
            'SYNNEX_Internal_Use_1',
            'SYNNEX_SKU',
            'Status_Code',
            'Part_Description',
            'Manufacturer_Name',
            'SYNNEX_Internal_Use_2',
            'Qty_on_Hand',
            'SYNNEX_Internal_Use_3',
            'SYNNEX_Internal_Use_4',
            'Contract_Price',
            'MSRP',
            'Warehouse_Qty_on_Hand_1',
            'Warehouse_Qty_on_Hand_2',
            'Returnable_Flag',
            'Warehouse_Qty_on_Hand_3',
            'Parcel_Shippable',
            'Warehouse_Qty_on_Hand_4',
            'Unit_Cost',
            'Warehouse_Qty_on_Hand_5',
            'Media_Type',
            'Warehouse_Qty_on_Hand_6',
            'SYNNEX_CAT_Code',
            'Warehouse_Qty_on_Hand_7',
            'SYNNEX_Internal_Use_5',
            'Ship_Weight',
            'Serialized_Flag',
            'Warehouse_Qty_on_Hand_8',
            'Warehouse_Qty_on_Hand_9',
            'Warehouse_Qty_on_Hand_10',
            'SYNNEX_Reserved_Use_1',
            'UPC_Code',
            'UNSPSC_Code',
            'SYNNEX_Internal_Use_6',
            'SKU_Created_Date',
            'One_Source_Flag',
            'ETA_Date',
            'ABC_Code',
            'Kit_Stand_Alone_Flag',
            'State_GOV_Price',
            'Federal_GOV_Price',
            'EDUcational_Price',
            'TAA_Flag',
            'GSA_Pricing',
            'Promotion_Flag',
            'Promotion_Comment',
            'Promotion_Expiration_Date',
            'Long_Description_1',
            'Long_Description_2',
            'Long_Description_3',
            'Length',
            'Width',
            'Height',
            'Warehouse_Qty_on_Hand_11',
            'GSA_NTE_Price',
            'Platform_Type',
            'Product_Description_FR',
            'SYNNEX_Reserved_Use_2',
            'Warehouse_Qty_on_Hand_12',
            'Warehouse_Qty_on_Hand_13',
            'Warehouse_Qty_on_Hand_14',
            'Warehouse_Qty_on_Hand_15',
            'Replacement_Sku',
            'Minimum_Order_Qty',
            'Purchasing_Requirements',
            'Gov_Class',
            'Warehouse_Qty_on_Hand_16',
            'MFG_Drop_Ship_Warehouse_QTY',
        ];

        $filename=dirname($zip_filename).'/'.$txt_filename;
        if (!$txt_filename || !is_file($filename) || !file_exists($filename) || (filesize($filename)==0)) {
            error_log("txt file not found: {$filename}\n");
            return false;
        }

        echo "processing {$filename} for dealer {$dealerSupplier['dealerId']}\n";
        $fp = fopen($filename, 'rb');

        gc_collect_cycles();
        echo "1. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $manufacturers=[];
        foreach ($db->query('select * from synnex_manufacturer') as $line) {
            $manufacturers[$line['name']] = $line['manufacturerId'];
        }

        $skus = [];

        //ignore header line
        fgetcsv($fp, null, '~');

        while ($line = fgetcsv($fp, null, '~')) {
            while (count($line) > count($columns)) {
                array_pop($line);
            }
            $line = array_combine($columns, $line);

            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '001') continue; //Accessories / Cables
            //if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '002') continue; //Computers  / Portables
            //if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '003') continue; //Digital Cameras / Keyboards / Input Device
            //if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '005') continue; //Monitor / Display / Projector
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '006') continue; //Network Hardware
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '007') continue; //Audio / Video / Output Devices
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '008') continue; //Power Equipment
            //if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '009') continue; //Printers
            //if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '010') continue; //Service / Support
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '011') continue; //Software
            //if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '012') continue; //Storage Devices
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '013') continue; //Computer Components
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '311') continue; //To Be Determined
            //if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '350') continue; //Office Machines & Supplies
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '544') continue; //TV & Video
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '545') continue; //Home Audio
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '546') continue; //Portable Electronics
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '547') continue; //Projectors
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '548') continue; //Appliances
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '549') continue; //Photo
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '550') continue; //Telecom
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '551') continue; //Digital Signage
            if (substr($line['SYNNEX_CAT_Code'], 0, 3) == '552') continue; //Security

            $manufacturerId = null;
            if (isset($manufacturers[$line['Manufacturer_Name']])) $manufacturerId = $manufacturers[$line['Manufacturer_Name']];

            $vpn = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$line['Manufacturer_Part']));

            $sku_Created_Date = \DateTime::createFromFormat('Ymd', $line['SKU_Created_Date']);

            $product_data = [
                'supplierSku'=>$line['SYNNEX_SKU'],
                'manufacturer'=>$line['Manufacturer_Name'],
                'manufacturerId'=>$manufacturerId,
                'vpn'=>$vpn,
                'name'=>$line['Part_Description'],
                'msrp'=>$line['MSRP'],
                'weight'=>0.453592 * floatval($line['Ship_Weight']),
                'length'=>$line['Length'] * 2.54 / 100,
                'width'=>$line['Width'] * 2.54 / 100,
                'height'=>$line['Height'] * 2.54 / 100,
                'upc'=>$line['UPC_Code'],
                'description'=>trim($line['Part_Description']),
                'package'=>$line['Kit_Stand_Alone_Flag'],
                'isStock'=>intval($line['Qty_on_Hand'])>0?1:0,
                'qty'=>json_encode([
                    'Warehouse_Qty_on_Hand_1'=>$line['Warehouse_Qty_on_Hand_1'],
                    'Warehouse_Qty_on_Hand_2'=>$line['Warehouse_Qty_on_Hand_2'],
                    'Warehouse_Qty_on_Hand_3'=>$line['Warehouse_Qty_on_Hand_3'],
                    'Warehouse_Qty_on_Hand_4'=>$line['Warehouse_Qty_on_Hand_4'],
                    'Warehouse_Qty_on_Hand_5'=>$line['Warehouse_Qty_on_Hand_5'],
                    'Warehouse_Qty_on_Hand_6'=>$line['Warehouse_Qty_on_Hand_6'],
                    'Warehouse_Qty_on_Hand_7'=>$line['Warehouse_Qty_on_Hand_7'],
                    'Warehouse_Qty_on_Hand_8'=>$line['Warehouse_Qty_on_Hand_8'],
                    'Warehouse_Qty_on_Hand_9'=>$line['Warehouse_Qty_on_Hand_9'],
                    'Warehouse_Qty_on_Hand_10'=>$line['Warehouse_Qty_on_Hand_10'],
                    'Warehouse_Qty_on_Hand_11'=>$line['Warehouse_Qty_on_Hand_11'],
                    'Warehouse_Qty_on_Hand_12'=>$line['Warehouse_Qty_on_Hand_12'],
                    'Warehouse_Qty_on_Hand_13'=>$line['Warehouse_Qty_on_Hand_13'],
                    'Warehouse_Qty_on_Hand_14'=>$line['Warehouse_Qty_on_Hand_14'],
                    'Warehouse_Qty_on_Hand_15'=>$line['Warehouse_Qty_on_Hand_15'],
                    'Warehouse_Qty_on_Hand_16'=>$line['Warehouse_Qty_on_Hand_16'],
                    'MFG_Drop_Ship_Warehouse_QTY'=>$line['MFG_Drop_Ship_Warehouse_QTY'],
                    'Qty_on_Hand'=>$line['Qty_on_Hand']]),
                'category'=>$line['SYNNEX_CAT_Code'],
                'categoryId'=>$line['SYNNEX_CAT_Code'],
                'dateCreated'=>$sku_Created_Date?$sku_Created_Date->format('Y-m-d'):null,
            ];

            $price_data = [
                'supplierSku'=>$line['SYNNEX_SKU'],
                'dealerId'=>$dealerSupplier['dealerId'],
                'price'=>trim($line['Contract_Price']),
                'promotion'=>$line['Promotion_Flag']=='Y'?1:0,
            ];


            try {
                $this->populate($product_data, $price_data);
            } catch (\Exception $ex) {
                var_dump($product_data);
                die ('xxx '.$ex->getMessage());
            }

            #$cat = substr($line['SYNNEX_CAT_Code'], 0, 6);
            #$skus[$cat][$manufacturerId][$vpn] = $line['SYNNEX_SKU'];
            $skus[$manufacturerId][$vpn] = $line['SYNNEX_SKU'];

            unset($this->remaining_product[$line['SYNNEX_SKU']]);
        }

        #==
        $this->deleteRemainingProducts($dealerSupplier['supplierId'], $dealerSupplier['dealerId']);
        #==

        gc_collect_cycles();
        echo "5. ".round(memory_get_usage()/(1024*1024))." MB\n";

        //xxx
        $cursor = $db->query("select id, manufacturerId, sku, weight, UPC from base_product");
        while ($line=$cursor->fetch(\PDO::FETCH_ASSOC)) {
            $manufacturerId = $line['manufacturerId'];
            $sku = $line['sku'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($skus[$manufacturerId][$sku])) {
                $supplierSku = $skus[$manufacturerId][$sku];
                //update supplier_product set baseProductId=? where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $supplierSku]);
                if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                    //update base_product set sku=(select vpn from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?) where id=?
                    $this->sku_statement->execute([$supplierSku, $supplierSku, $supplierSku, $line['id']]);
                }
            }
        }
        $cursor->closeCursor();

        /**
        $cursor = $db->query("select * from base_product where base_type='printer'");
        while ($line=$cursor->fetch(\PDO::FETCH_ASSOC)) {
            $manufacturerId = $line['manufacturerId'];
            $sku = $line['sku'];
            if (!$sku) continue;

            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            foreach (['009053','009058','009059','009060'] as $n) {
                if (isset($skus[$n][$manufacturerId][$sku])) {
                    $supplierSku = $skus[$n][$manufacturerId][$sku];
                    $this->base_product_statement->execute([$line['id'], $supplierSku]);
                    if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                        $this->sku_statement->execute([$supplierSku, $supplierSku, $supplierSku, $line['id']]);
                    }
                }
            }
        }
        $cursor->closeCursor();
        **/

        gc_collect_cycles();
        echo "6. ".round(memory_get_usage()/(1024*1024))." MB\n";

        echo time() - $this->timerStart."s\n";
        return true;
    }

    private function updateIngram($dealerSupplier) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/cache/'.sprintf('ingram-%s.zip', $dealerSupplier['dealerId']);
        $price_txt = dirname($zip_filename).'/PRICE.TXT';
        if (file_exists($price_txt)) {
            unlink($price_txt);
        }
        try {
            if (!file_exists($zip_filename) || (filemtime($zip_filename)<strtotime('-6 DAY'))) {
                $ftp = $this->getFtpClient();
                $ftp->get("{$dealerSupplier['url']}/PRICE.ZIP", $dealerSupplier['user'], $dealerSupplier['pass'], $zip_filename);
            }

            if (!file_exists($zip_filename)) {
                error_log('zip file not downloaded');
                return false;
            }

            $zip = $this->getZipAdapter();
            $zip->open($zip_filename);
            $zip->extractTo(dirname($zip_filename));
            $zip->close();

            $zip = null;
            $ftp = null;

        } catch (\Exception $ex) {
            print_r($ex);
            Logger::logException($ex);
            return false;
        }

        $manufacturers=[];
        foreach ($db->query('select * from ingram_manufacturer') as $line) {
            $reg = '#^'.str_replace('%','.*',strtoupper($line['name'])).'$#i';
            $manufacturers[$reg] = $line['manufacturerId'];
        }

        $columns = [
'ACTION_INDICATOR', // - A=ADD (New Record); C=CHANGE (Record Changed); D=DELETE (Discontinued)
'INGRAM_PART_NUMBER', // (CURRENT) - 7 Positions, Alphanumeric, followed by 5 spaces
'VENDOR_NUMBER', // - Ingram Micro's Internal Vendor Number; NOT a standard Vendor Number  (X-Ref File Available)
'VENDOR_NAME', //
'INGRAM_PART_DESCRIPTION_LINE_1', //
'INGRAM_PART_DESCRIPTION_LINE_2', //
'RETAIL_PRICE', // - Formatted as 9999999.99; Zero packed
'VENDOR_PART_NUMBER', //
'WEIGHT', // - Formatted as 999.99; Zero packed
'UPC_CODE', //
'LENGTH', // - Formatted as 999.99; Zero packed
'WIDTH', // - Formatted as 999.99; Zero packed
'HEIGHT', // - Formatted as 999.99; Zero packed
'PRICE_CHANGE_FLAG', // - Y=Customer Price has changed; BLANK =No change in price
'CUSTOMER_PRICE', // - Formatted as 9999999.99; Zero packed
'SPECIAL_PRICE_FLAG', // - * =Special Price Used; (Blank Field)=Regular Pricing Used. Indicates if customer price was based on a current Ingram Micro special.
'AVAILABILITY_FLAG', // - Y=Item in Stock; N=No Item in Stock.  Field indicates if stock is available in at least one warehouse.
'STATUS', // - * =Discontinued; N=To be Discontinued; V=Deleted by Vendor
'CPU_CODE', //
'MEDIA_TYPE', // - Indicates DSK3 (DISK 3 1/2), ETC.
'INGRAM_MICRO_CATEGORY', // / SUBCATEGORY
'NEW_ITEM_RECEIPT_FLAG', // - Y=Stock has been received; (BLANK FIELD)=Stock has not been received.  Indicates if first stock has been received.
'SUBSTITUTE_PART_NUMBER', // - 7 Positions, Alphanumeric, followed by 5 spaces; (Blank Field)=No substitute part number
        ];

        $skus = [];

        gc_collect_cycles();
        echo "1. ".round(memory_get_usage()/(1024*1024))." MB\n";

        if (!file_exists($price_txt)) {
            error_log('price.txt not found');
            return false;
        }
        $fp = fopen($price_txt, 'rb');
        while ($line = fgetcsv($fp)) {
            $line = array_combine($columns, $line);

            if (
                ($line['INGRAM_MICRO_CATEGORY'] != '9045') && // monitor
                ($line['INGRAM_MICRO_CATEGORY'] != '0011') && // notebook
                ($line['INGRAM_MICRO_CATEGORY'] != '1221') && // service
                ($line['INGRAM_MICRO_CATEGORY'] != '1010') && // ink & toner
                ($line['INGRAM_MICRO_CATEGORY'] != '0701') && // printers
                ($line['INGRAM_MICRO_CATEGORY'] != '0733')    // printers
            ) continue;

            foreach ($line as $k=>$v) $line[$k] = trim($v);

            switch ($line['ACTION_INDICATOR']) {
                case 'A':
                case 'C':
                {
                    $vpn = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$line['VENDOR_PART_NUMBER']));

                    $manufacturerId = null;
                    foreach ($manufacturers as $reg=>$id) {
                        if (preg_match($reg, $line['VENDOR_NAME'])) {
                            $manufacturerId = $id;
                        }
                    }

                    $product_data = [
                        'supplierSku'=>$line['INGRAM_PART_NUMBER'],
                        'manufacturer'=>$line['VENDOR_NAME'],
                        'manufacturerId'=>$manufacturerId,
                        'vpn'=>$vpn,
                        'name'=>null,
                        'msrp'=>$line['RETAIL_PRICE'],
                        'weight'=>0.453592 * floatval($line['WEIGHT']),
                        'length'=>$line['LENGTH'] * 2.54 / 100,
                        'width'=>$line['WIDTH'] * 2.54 / 100,
                        'height'=>$line['HEIGHT'] * 2.54 / 100,
                        'upc'=>$line['UPC_CODE'],
                        'description'=>trim($line['INGRAM_PART_DESCRIPTION_LINE_1'].' '.$line['INGRAM_PART_DESCRIPTION_LINE_2']),
                        'package'=>$line['MEDIA_TYPE'],
                        'isStock'=>$line['AVAILABILITY_FLAG']=='Y'?1:0,
                        'qty'=>null,
                        'category'=>$line['INGRAM_MICRO_CATEGORY'],
                        'categoryId'=>$line['INGRAM_MICRO_CATEGORY'],
                        'dateCreated'=>null,
                    ];

                    $price_data = [
                        'supplierSku'=>$line['INGRAM_PART_NUMBER'],
                        'dealerId'=>$dealerSupplier['dealerId'],
                        'price'=>trim($line['CUSTOMER_PRICE']),
                        'promotion'=>$line['SPECIAL_PRICE_FLAG']=='*'?1:0,
                    ];


                    try {
                        $this->populate($product_data, $price_data);
                    } catch (\Exception $ex) {
                        var_dump($product_data);
                        die ('xxx '.$ex->getMessage());
                    }

                    $skus[$line['INGRAM_MICRO_CATEGORY']][$manufacturerId][$vpn] = $line['INGRAM_PART_NUMBER'];

                    unset($this->remaining_product[$line['INGRAM_PART_NUMBER']]);
                    break;
                }
            }
        }

        gc_collect_cycles();
        echo "2. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $this->deleteRemainingProducts($dealerSupplier['supplierId'], $dealerSupplier['dealerId']);

        $cursor = $db->query("select base_product.id, base_product.manufacturerId, sku, weight, UPC from base_product join base_printer_consumable using (id)");
        while ($line=$cursor->fetch(\PDO::FETCH_ASSOC)) {
            $manufacturerId = $line['manufacturerId'];
            $sku = $line['sku'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($skus['1010'][$manufacturerId][$sku])) {
                $supplierSku = $skus['1010'][$manufacturerId][$sku];
                //update supplier_product set baseProductId=? where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $supplierSku]);
                if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                    //update base_product set sku=(select vpn from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$dealerSupplier['supplierId'].' and `supplierSku`=?) where id=?
                    $this->sku_statement->execute([$supplierSku, $supplierSku, $supplierSku, $line['id']]);
                }
            }
        }
        $cursor->closeCursor();

        $cursor = $db->query("select * from base_product where base_type='printer'");
        while ($line=$cursor->fetch(\PDO::FETCH_ASSOC)) {
            $manufacturerId = $line['manufacturerId'];
            $sku = $line['sku'];
            if (empty($sku)) continue;

            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            foreach (['0701', '0733'] as $catId) {
                if (isset($skus[$catId][$manufacturerId][$sku])) {
                    $supplierSku = $skus[$catId][$manufacturerId][$sku];
                    $this->base_product_statement->execute([$line['id'], $supplierSku]);
                    if (empty($line['sku']) || empty($line['weight']) || empty($line['upc'])) {
                        $this->sku_statement->execute([$supplierSku, $supplierSku, $supplierSku, $line['id']]);
                    }
                }
            }
        }
        $cursor->closeCursor();


        gc_collect_cycles();
        echo "3. ".round(memory_get_usage()/(1024*1024))." MB\n";

        echo time() - $this->timerStart."s\n";
        return true;
    }

}