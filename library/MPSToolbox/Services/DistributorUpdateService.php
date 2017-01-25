<?php

namespace MPSToolbox\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Uri;
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

    private $dealerSupplier;
    private $supplierId;
    private $dealerId;

    private $remaining_product = [];
    private $supplier_product = [];
    private $supplier_price = [];
    private $dealer_toner_attributes = [];

    private $compatibleStatements = [];
    private $supplierConsumableStatements = [];

    private $total_result = [];

    const SUPPLIER_INGRAM   = 1;
    const SUPPLIER_SYNNEX   = 2;
    const SUPPLIER_TECHDATA = 3;
    const SUPPLIER_GENUINE = 4;
    const SUPPLIER_ACM = 5;
    const SUPPLIER_DH = 6;
    const SUPPLIER_CLOVER = 7;
    const SUPPLIER_ARLI = 8;
    const SUPPLIER_TECHDATA_SA = 9;
    const SUPPLIER_QCFL = 10;

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
        if (preg_match('#^(.+)m$#i', $str, $match)) {
            $f = floatval($match[1]);
            return round(1000000 * $f);
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

        $this->dealerSupplier = $dealerSupplier;
        $this->supplierId = $this->dealerSupplier['supplierId'];
        $this->dealerId = intval($this->dealerSupplier['dealerId']);

        $db = \Zend_Db_Table::getDefaultAdapter();

        #--
        $this->insert_product = $db->prepare("
                  insert into supplier_product SET
supplierId={$this->supplierId},
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
supplierId={$this->supplierId},
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
                  WHERE supplierSku=:supplierSku and supplierId={$this->supplierId}");

        $this->update_price = $db->prepare("
                  UPDATE supplier_price SET
price=:price,
promotion=:promotion,
_md5=:_md5
                    WHERE dealerId=:dealerId and supplierSku=:supplierSku and supplierId={$this->supplierId}");


        $this->base_product_statement = $db->prepare('update supplier_product set baseProductId=? where `supplierId`='.$this->supplierId.' and `supplierSku`=?');
        $this->sku_statement = $db->prepare('update base_product set sku=(select vpn from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?) where id=?');

        #--
        $this->supplier_product = [];
        $cursor = $db->query('select supplierSku, _md5 from supplier_product where supplierId='.$this->supplierId);
        while($line = $cursor->fetch()) {
            $this->supplier_product[$line['supplierSku']] = $line['_md5'] ? $line['_md5'] : 'x';
        }
        $cursor->closeCursor();

        $this->remaining_product = [];
        $cursor = $db->query('select supplierSku, _md5 from supplier_product where supplierId='.$this->supplierId.' and supplierSku in (select supplierSku from supplier_price where dealerId='.$this->dealerId.')');
        while($line = $cursor->fetch()) {
            $this->remaining_product[$line['supplierSku']] = $line['_md5'] ? $line['_md5'] : 'x';
        }
        $cursor->closeCursor();

        $this->supplier_price = [];
        $cursor = $db->query('select supplierSku, _md5 from supplier_price where dealerId='.$this->dealerId.' and supplierId='.$this->supplierId);
        while($line = $cursor->fetch()) {
            $this->supplier_price[$line['supplierSku']] = $line['_md5'] ? $line['_md5'] : 'x';
        }
        $cursor->closeCursor();

        $this->dealer_toner_attributes = [];
        $cursor = $db->query('select tonerId, cost, dealerSku, sellPrice from dealer_toner_attributes where dealerId='.$this->dealerId);
        while($line = $cursor->fetch()) {
            $this->dealer_toner_attributes[$line['tonerId']] = $line;
        }
        $cursor->closeCursor();


        $cursor = null;
        gc_collect_cycles();

        echo "dealer: {$this->dealerId}\n";
        echo "supplier: {$this->supplierId}\n";
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

        switch($this->supplierId) {
            case self::SUPPLIER_INGRAM : {
                $this->updateIngram();
                break;
            }
            case self::SUPPLIER_SYNNEX : {
                $this->updateSynnex();
                break;
            }
            case self::SUPPLIER_TECHDATA : {
                $this->updateTechDataNA();
                break;
            }
            case self::SUPPLIER_GENUINE : {
                $this->updateGenuine();
                break;
            }
            case self::SUPPLIER_ACM : {
                $this->updateAcm();
                break;
            }
            case self::SUPPLIER_DH : {
                $this->updateDH();
                break;
            }
            case self::SUPPLIER_CLOVER : {
                $this->updateClover();
                break;
            }
            case self::SUPPLIER_ARLI : {
                $this->updateArli();
                break;
            }
            case self::SUPPLIER_TECHDATA_SA : {
                $this->updateTechDataSA();
                break;
            }
            case self::SUPPLIER_QCFL : {
                $this->updateQcfl();
                break;
            }
        }
        gc_collect_cycles();

        if ($this->total_result['products_total']==0) {
            $this->total_result['products_total'] = $db->query('SELECT count(*) FROM supplier_product WHERE supplierId=' . intval($this->supplierId))->fetchColumn(0);
            $this->total_result['prices_total'] = $db->query('SELECT count(*) FROM supplier_price WHERE dealerId=' . intval($this->dealerId) . ' AND supplierId=' . intval($this->supplierId))->fetchColumn(0);
        }

        $db->query("
          insert into distributor_import_result set
              `dealerId`={$this->dealerId},
              `supplierId`={$this->supplierId},
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

    private function deleteRemainingProducts() {
        $db = \Zend_Db_Table::getDefaultAdapter();

        $online_sku=[];
        foreach ($db->query('select supplierSku, baseProductId from supplier_product where supplierId='.$this->supplierId.' and baseProductId in (select masterDeviceId from devices where online=1)') as $online_line) {
            $online_sku[$online_line['supplierSku']] = $online_line['baseProductId'];
        }
        foreach ($db->query('select supplierSku, baseProductId from supplier_product where supplierId='.$this->supplierId.' and baseProductId in (select skuId from dealer_sku where online=1)') as $online_line) {
            $online_sku[$online_line['supplierSku']] = $online_line['baseProductId'];
        }

        $st1 = $db->prepare('delete from supplier_product where supplierSku=? and supplierId='.$this->supplierId);
        $st2 = $db->prepare('delete from supplier_price where supplierSku=? and dealerId='.$this->dealerId.' and supplierId='.$this->supplierId);
        $c = count($this->remaining_product);
        foreach ($this->remaining_product as $supplierSku=>$md5) {

            if (isset($online_sku[$supplierSku])) {
                $baseProductId = intval($online_sku[$supplierSku]);
                $base_product = $db->query('select * from base_product where id='.$baseProductId)->fetch();
                $base_product_mfg = $db->query('select fullname from manufacturers where id='.intval($base_product['manufacturerId']))->fetchColumn(0);
                $affected_dealers = [];
                foreach ($db->query('select dealerName from dealers where id in (select dealerId from dealer_suppliers where supplierId='.$this->supplierId.') and id in (select dealerId from devices where online=1 and masterDeviceId='.$baseProductId.')') as $dealer_line) $affected_dealers[$dealer_line['dealerName']] = $dealer_line['dealerName'];
                foreach ($db->query('select dealerName from dealers where id in (select dealerId from dealer_suppliers where supplierId='.$this->supplierId.') and id in (select dealerId from dealer_sku where online=1 and skuId='.$baseProductId.')') as $dealer_line) $affected_dealers[$dealer_line['dealerName']] = $dealer_line['dealerName'];
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

    private function updateAcm() {
        $db = \Zend_Db_Table::getDefaultAdapter();

        #--
        $product_model = [];
        $product_oem = [];
        $product_price = [];

        $fp = fopen(APPLICATION_BASE_PATH . '/data/cache/' . 'Product Model.txt','rb');
        $col=false;
        while ($line=fgetcsv($fp, null, "\t")) {
            foreach ($line as $i=>$s) $line[$i] = trim($s);
            if (!$col) {
                $col = ['ACM#','MODEL','BRAND'];
            } else {
                $line = array_combine($col, $line);
                $product_model[$line['ACM#']][] = $line;
            }
        }

        $fp = fopen(APPLICATION_BASE_PATH . '/data/cache/' . 'Product OEM.TXT','rb');
        $col=false;
        while ($line=fgetcsv($fp, null, "\t")) {
            foreach ($line as $i=>$s) $line[$i] = trim($s);
            if (!$col) {
                $col = ['ACM#','OEM#'];
            } else {
                $product_oem[$line[0]][] = $line[1];
            }
        }

        $fp = fopen(APPLICATION_BASE_PATH . '/data/cache/' . 'Product Price.txt','rb');
        $col=false;
        while ($line=fgetcsv($fp, null, "\t")) {
            foreach ($line as $i=>$s) $line[$i] = trim($s);
            if (!$col) {
                $col = ['ACM#','ATTRIBUTE1','PRODUCT TYPE','DESCRIPTION','YIELD','UOM','PRICE','WEIGHT'];
            } else {
                $product_price[$line[0]] = array_combine($col, $line);
            }
        }
        #--

        $filename = APPLICATION_BASE_PATH . '/data/cache/' . sprintf('acm-%s.txt', $this->dealerId);
        try {
            if (!file_exists($filename) || (filemtime($filename) < strtotime('-6 DAY'))) {
                $ftp = $this->getFtpClient();
                $ftp->get($this->dealerSupplier['url'].'/ACM_2_Customer/ProductList_'.date('Ymd').'.txt', $this->dealerSupplier['user'], $this->dealerSupplier['pass'], $filename);
            }
        } catch (\Exception $ex) {
            print_r($ex);
            Logger::logException($ex);
            return false;
        }

        echo "processing {$filename} for dealer {$this->dealerId}\n";
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

        $colors = [];
        foreach ($db->query('select * from toner_colors') as $line) {
            $colors[$line['name']] = $line['id'];
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

        $rename_statement = $db->prepare('update base_product set name=?, sku=? where id=?');
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

            //if ($supplierSku!='EPQ6000A') continue;

            $is_consumable = false;
            switch ($line['Product Group']) {
                case 'Toner/Cartridges':
                case 'Toner Disposal Collect Units':
                case 'Clean/Maint/PM Kit':
                case 'Developer':
                case 'Inkjet':
                case 'MICR Toner':
                case 'Fuser Oil':
                case 'ECOPlus':
                case 'Oil Supply Roller/Pad':
                case 'Drum/Drum Kit':
                    $is_consumable = true;
                    break;
            }

            $manufacturerId = null;
            $brand = strtoupper(preg_replace('#,.*$#','',$line['Brand']));
            if (isset($manufacturers[$brand])) $manufacturerId = $manufacturers[$brand];
            else {
                switch ($line['Brand']) {
                    case 'Star Micronics' :
                    case 'Dymo' :
                    case 'Miscellaneous' :
                        continue;
                    default : {
                        error_log('Brand? '.$line['Brand']);
                        continue;
                    }
                }
            }

            $status = 'Compatible';
            if (strpos($line['Product Type'], 'Reman ')===0) $status='Remanufactured';
            if (strpos($line['Product Description'], 'REMAN ')===0) $status='Remanufactured';
            if (strpos($line['Product Description'], 'ECOPLUS REMAN ')===0) $status='Compatible'; //'Remanufactured';
            if (strpos($line['Product Type'], 'OEM ')===0) $status='OEM';
            if (strpos($line['Product Description'], 'OEM ')===0) $status='OEM';
            if (strpos($line['Product Description'], 'COMPATIBLE ')===0) $status='Compatible';

            $consumableManufacturer = $line['Brand'];
            $consumableManufacturerId = $manufacturerId;
            if (($line['Product Group']=='ECOPlus') || (strpos($line['Product Description'], 'ECOPLUS ')===0)) {
                $status='Compatible';
                $consumableManufacturer = 'ECOPlus';
                $consumableManufacturerId = $manufacturers['ECOPLUS'];
            }
            if (($status=='Compatible') || ($status=='Remanufactured')) {
                if (($line['Product Group']=='ECOPlus') || (strpos($line['Product Description'], 'ECOPLUS ')===0)) {
                    $consumableManufacturer = 'ECOPlus';
                    $consumableManufacturerId = $manufacturers['ECOPLUS'];
                } else {
                    $consumableManufacturer = 'ACM Technologies';
                    $consumableManufacturerId = $manufacturers['ACM TECHNOLOGIES'];
                }
            }

            if (isset($product_oem[$supplierSku])) {
                $e = $product_oem[$supplierSku];
                $name = '';
                $vpn = implode(', ', $e);
            } else {
                $vpn = $line['OEM#'];
                $name = '';
                $e = explode(',', $vpn);
            }
            if (count($e)>1) {
                $vpn_length = 0;
                foreach ($e as $i=>$n) {
                    $e[$i] = trim($n);
                }
                foreach ($e as $n) {
                    if (!preg_match('#(^[A-Z][A-Z]-?\d\d\d|-|TYPE)#i', $n)) {
                        $vpn_length = max($vpn_length, strlen($n));
                    }
                }
                $vpn_arr = [];
                $name_arr = [];
                foreach ($e as $n) {
                    if (!preg_match('#(^[A-Z][A-Z]-?\d\d\d|-|TYPE)#i', $n)) {
                        if (strlen($n) == $vpn_length) {
                            $vpn_arr[] = $n;
                        } else {
                            $name_arr [] = $n;
                        }
                    } else {
                        $name_arr [] = $n;
                    }
                }

                $vpn = implode(', ', $vpn_arr);
                $name = implode(', ', $name_arr);
                if ($name && !$vpn) {
                    $vpn = $name;
                    $name = '';
                }
            }

            $weight = null;
            if (isset($product_price[$supplierSku])) {
                $weight = $product_price[$supplierSku]['WEIGHT'] * 0.453592;
            }

            $product_data = [
                'supplierSku'=>$supplierSku,
                'manufacturer'=>$consumableManufacturer,
                'manufacturerId'=>$consumableManufacturerId,
                'vpn'=>$vpn,
                'name'=>$name,
                'msrp'=>trim($line['Price']),
                'weight'=>$weight,
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
                'dealerId'=>$this->dealerId,
                'price'=>trim($line['Price']),
                'promotion'=>null,
            ];

            try {
                $this->populate($product_data, $price_data);
            } catch (\Exception $ex) {
                var_dump($product_data);
                error_log('xxx '.$ex->getMessage());
            }

            #-----
            $pageYield = 0;
            $mlYield = 0;

            if (isset($product_price[$supplierSku])) {
                $part_yield = $product_price[$supplierSku]['YIELD'];
            } else {
                $pair = explode(' | ', $line['Product Description']);
                $d = array_pop($pair);
                $parts = explode(', ', $d);
                $part_type = $parts[0];
                $part_color = isset($parts[1]) ? $parts[1] : '';
                $part_yield = isset($parts[2]) ? $parts[2] : '';
                $part_memo = isset($parts[3]) ? $parts[3] : '';

                if (preg_match('#^(.*) YIELD#i', $part_color)) {
                    $part_yield = $part_color;
                    $part_color = '';
                }
            }

            if (preg_match('#^(.*)ML#i', $part_yield, $match)) {
                $mlYield = floatval($match[1]);
            } else if (preg_match('#^(.*) YIELD#i', $part_yield, $match)) {
                $str = trim(str_replace([
                    'ULTRA',
                    'SUPER',
                    'HIGH',
                    'EXTRA',
                    'FULL',
                    'STARTER CARTRIDGE'
                ], [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ], $match[1]));
                $e = explode('/', $str);
                if (count($e) > 1) $str = array_pop($e);
                if (preg_match('#([0-9.,]*) ?(K|M)#', $str, $match2)) {
                    $str = $match2[1].$match2[2];
                }
                $pageYield = $this->expandYield($str);
                if (!$pageYield) {
                    $pageYield = 0;
                }
            } else {
                //error_log('no yield? '.$d);
            }

            #-----
            if ($is_consumable && substr($line['Product Description'],0,4)=='OEM ') {
                $e = explode(', ',strtoupper($line['Brand']));
                if (!isset($manufacturers[$e[0]])) {
                    error_log('???? '.$e[0]);
                }
                $oem_mfg_id = $manufacturers[$e[0]];

                $e = explode(',',$line['OEM#']);
                $found=false;
                foreach ($e as $n) {
                    $n = trim($n);
                    if (isset($skus[$oem_mfg_id][$n]) && ($skus[$oem_mfg_id][$n]['yield']==$pageYield)) {
                        $found=true;
                        if ($n!=$vpn) {
                            echo "nnnnnnnnn {$n} : {$vpn} : {$skus[$oem_mfg_id][$n]['id']}\n";
                            $rename_statement->execute([$name, $vpn, $skus[$oem_mfg_id][$n]['id']]);
                        }
                    }
                }
            }

            $imgUrl = 'http://www.acmtech.com/Pictures/Pic_Small/'.substr($supplierSku,0,2).'/'.$supplierSku.'.jpg';
            $title = $line['Product Description'];
            $type = $line['Product Type'];
            $color = '';
            if (isset($product_price[$supplierSku])) {
                $title = $product_price[$supplierSku]['DESCRIPTION'];
                $type = $product_price[$supplierSku]['PRODUCT TYPE'];
                $color = $product_price[$supplierSku]['ATTRIBUTE1'];
                if ($color=='NULL') $color='';
            }

            $compatible = [];
            if (isset($product_model[$supplierSku])) {
                foreach ($product_model[$supplierSku] as $pm) {
                    $n = strtoupper($pm['BRAND']);
                    if ($n!='NULL') {
                        if (!isset($manufacturers[$n])) {
                            $oem_mfg_id = null;
                        } else {
                            $oem_mfg_id = $manufacturers[$n];
                        }
                        $compatible[] = ['brand' => $pm['BRAND'], 'manufacturerId' => $oem_mfg_id, 'model' => $pm['MODEL']];
                    }
                }
            } else {
                $e = explode(', ', $line['Model']);
                foreach ($e as $model) {
                    $compatible[] = ['brand'=>$line['Brand'], 'manufacturerId'=>$manufacturerId, 'model'=>trim($model)];
                }
            }

            //$db, $supplierSku,$status,$oemManufacturer,$oemManufacturerId,$consumableManufacturer,$consumableManufacturerId,$oemSku,$title,$type,$color,$yield,$upc,$imageUrl,$compatible
            $this->populateSupplierConsumable(
                $db,
                $supplierSku,
                $status,
                $line['Brand'],
                $manufacturerId,
                $consumableManufacturer,
                $consumableManufacturerId,
                $line['OEM#'],
                $title,
                $type,
                $color,
                $part_yield,
                null,
                $imgUrl,
                $compatible
            );

            #-----
            if (($status!='OEM') && $is_consumable) {
                $oem_lines = [];
                if (isset($product_oem[$supplierSku])) {
                    $e = $product_oem[$supplierSku];
                } else {
                    $e = explode(', ', $line['OEM#']);
                }
                foreach ($e as $n) {
                    $n = str_replace('-', '', trim($n));
                    if (empty($n)) continue;
                    if (isset($skus[$manufacturerId][$n])) {
                        $oem_lines[] = $skus[$manufacturerId][$n];
                    }
                }

                /**/
                if (empty($oem_lines)) {
                    //error_log('oem not found for compatible: '.print_r($line,true));
                    //continue;
                    $oem_line_colorId=null;
                    if (!empty($color)) {
                        if (isset($colors[strtoupper($color)])) $oem_line_colorId = $colors[strtoupper($color)];
                        else $oem_line_colorId = 7; //COLOR
                    }

                    $oem_lines = [[
                        'base_type'=>$oem_line_colorId ? 'printer_cartridge':'printer_consumable',
                        'weight'=>$weight,
                        'quantity'=>1,
                        'type'=>$line['Product Type'],
                        'colorId'=>$oem_line_colorId,
                    ]];
                }
                /* xxxx */

                $this->populateCompatible($db, $skus, $consumableManufacturerId, $supplierSku, $imgUrl, $name, $weight, null, $line['Price'], $pageYield, $oem_lines, $mlYield, $color);
            }
            #---
            if (($status=='OEM') && $is_consumable) {
                $oem_cartridges[$manufacturerId][$vpn] = $supplierSku;
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
                //update supplier_product set baseProductId=? where `supplierId`='.$this->supplierId.' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $oem_cartridges[$manufacturerId][$sku]]);
            }
        }
        $cursor->closeCursor();

        gc_collect_cycles();
        echo "3. ".round(memory_get_usage()/(1024*1024))." MB\n";

        echo time() - $this->timerStart."s\n";
        return true;
    }

    private function populateSupplierConsumable(\Zend_Db_Adapter_Abstract $db, $supplierSku,$status,$oemManufacturer,$oemManufacturerId,$consumableManufacturer,$consumableManufacturerId,$oemSku,$title,$type,$color,$yield,$upc,$imageUrl,$compatible) {
        if (empty($this->supplierConsumableStatements)) {
            $this->supplierConsumableStatements['st1'] = $db->prepare('replace into supplier_consumable set supplierId=?,supplierSku=?,status=?,oemManufacturer=?,oemManufacturerId=?,consumableManufacturer=?,consumableManufacturerId=?,oemSku=?,title=?,type=?,color=?,yield=?,upc=?,imageUrl=?');
            $this->supplierConsumableStatements['st2a'] = $db->prepare('delete from supplier_consumable_compatible where supplierId=? and supplierSku=?');
            $this->supplierConsumableStatements['st2'] = $db->prepare('replace into supplier_consumable_compatible set supplierId=?,supplierSku=?,brand=?,manufacturerId=?,model=?');
        }
        $this->supplierConsumableStatements['st1']->execute([$this->supplierId,$supplierSku,$status,$oemManufacturer,$oemManufacturerId,$consumableManufacturer,$consumableManufacturerId,$oemSku,$title,$type,$color,$yield,$upc,$imageUrl]);
        $this->supplierConsumableStatements['st2a']->execute([$this->supplierId,$supplierSku]);
        foreach ($compatible as $line) {
            $this->supplierConsumableStatements['st2']->execute([$this->supplierId,$supplierSku,$line['brand'],$line['manufacturerId'],$line['model']]);
        }
    }

    private function populateCompatible(\Zend_Db_Adapter_Abstract $db, $skus, $comp_mfg_id, $supplierSku, $imgUrl, $name, $weight, $upc, $price, $pageYield, $oem_lines, $mlYield=null, $colorStr=null, $sellPrice=null) {
        if (empty($this->compatibleStatements)) {
            $this->compatibleStatements['st1'] = $db->prepare("REPLACE INTO base_product SET userId=1, dateCreated=now(), isSystemProduct=1, imageUrl=?, base_type=?, manufacturerId=?, sku=?, name=?, weight=?, UPC=?");
            $this->compatibleStatements['st1a'] = $db->prepare("update base_product SET imageUrl=?, base_type=?, manufacturerId=?, sku=?, name=?, weight=?, UPC=? where id=?");
            $this->compatibleStatements['st2'] = $db->prepare("REPLACE INTO base_printer_consumable SET id=?, cost=?, pageYield=?, quantity=?, type=?");
            $this->compatibleStatements['st3'] = $db->prepare("REPLACE INTO base_printer_cartridge SET id=?, colorId=?, colorStr=?, mlYield=?");
            $this->compatibleStatements['st3a'] = $db->prepare("delete from base_printer_cartridge where id=?");
            $this->compatibleStatements['st4'] = $db->prepare("REPLACE INTO compatible_printer_consumable SET oem=?, compatible=?");
            $this->compatibleStatements['st5'] = $db->prepare("insert INTO dealer_toner_attributes SET tonerId=?, dealerId=?, cost=?, dealerSku=?, sellPrice=?");
            $this->compatibleStatements['st5a'] = $db->prepare("update dealer_toner_attributes SET cost=?, dealerSku=?, sellPrice=? where tonerId=? and dealerId=?");
            $this->compatibleStatements['st6'] = $db->prepare("update supplier_product set baseProductId=? where supplierId=? and supplierSku=?");
        }

        if (!$comp_mfg_id) {
            error_log('comp_mfg_id?');
            return false;
        }

        $base_id = false;
        $str = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$supplierSku));
        if (isset($skus[$comp_mfg_id][$supplierSku])) {
            $base_id = $skus[$comp_mfg_id][$supplierSku]['id'];
        } else if (isset($skus[$comp_mfg_id][$str])) {
            $base_id = $skus[$comp_mfg_id][$str]['id'];
        }

        if (!$base_id) {
            $oem_line = current($oem_lines);
            if (!$weight) $weight = $oem_line['weight'];
            $this->compatibleStatements['st1']->execute([$imgUrl, $oem_line['base_type'], $comp_mfg_id, $supplierSku, $name, $weight, $upc]);
            $base_id = $db->lastInsertId();
            $this->compatibleStatements['st2']->execute([$base_id, $price, $pageYield, $oem_line['quantity'], $oem_line['type']]);
            if ($oem_line['colorId']) {
                $this->compatibleStatements['st3']->execute([$base_id, $oem_line['colorId'], $colorStr, $mlYield]);
            }
        } else {
            $oem_line = current($oem_lines);
            if (!$weight) $weight = $oem_line['weight'];
            $this->compatibleStatements['st1a']->execute([$imgUrl, $oem_line['base_type'], $comp_mfg_id, $supplierSku, $name, $weight, $upc, $base_id]);
            $this->compatibleStatements['st2']->execute([$base_id, $price, $pageYield, $oem_line['quantity'], $oem_line['type']]);
            if ($oem_line['colorId']) {
                $this->compatibleStatements['st3']->execute([$base_id, $oem_line['colorId'], $colorStr, $mlYield]);
            } else {
                $this->compatibleStatements['st3a']->execute([$base_id]);
            }
        }

        foreach ($oem_lines as $oem_line) if (!empty($oem_line['id'])) {
            //echo "{$oem_line['id']} > {$base_id} \n";
            $this->compatibleStatements['st4']->execute([$oem_line['id'], $base_id]);
        }

        if (isset($this->dealer_toner_attributes[$base_id])) {
            $this->compatibleStatements['st5a']->execute([$price, $supplierSku, $sellPrice, $base_id, $this->dealerId]);
        } else {
            $this->compatibleStatements['st5']->execute([$base_id, $this->dealerId, $price, $supplierSku, $sellPrice]);
            $this->dealer_toner_attributes[$base_id] = ['tonerId'=>$base_id, 'sellPrice'=>$sellPrice];
        }
        $this->compatibleStatements['st6']->execute([$base_id, $this->supplierId, $supplierSku]);
        return $base_id;
    }

    private function updateDH() {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $price_txt = APPLICATION_BASE_PATH . '/data/cache/ITEMLIST-'.$this->dealerId;
        if (file_exists($price_txt)) {
            unlink($price_txt);
        }
        try {
            echo "downloading {$this->dealerSupplier['url']}/ITEMLIST for dealer {$this->dealerId}\n";
            $ftp = $this->getFtpClient();
            $parts = parse_url($this->dealerSupplier['url']);
            $ftp->ext_get($parts['host'], 'ITEMLIST', $this->dealerSupplier['user'], $this->dealerSupplier['pass'], $price_txt);

            if (!file_exists($price_txt) || (filesize($price_txt)==0)) {
                error_log('price file not downloaded');
                return false;
            }
        } catch (\Exception $ex) {
            print_r($ex);
            Logger::logException($ex);
            return false;
        }

        $manufacturers=[];
        /**
        foreach ($db->query('select * from ingram_manufacturer') as $line) {
            $reg = '#^'.str_replace('%','.*',strtoupper($line['name'])).'$#i';
            $manufacturers[$reg] = $line['manufacturerId'];
        }
        **/
        foreach ($db->query('select * from manufacturers') as $line) {
            $reg = '#^'.strtoupper($line['fullname']).'.*$#i';
            $manufacturers[$reg] = $line['id'];
            $reg = '#^'.strtoupper($line['displayname']).'.*$#i';
            $manufacturers[$reg] = $line['id'];
        }

        $columns = [
            'Stock Status', // - I=Instock, O=Out of stock
            'Qty Avail All Branches', // Max 999
            'Rebate Flag', // Yes when exists; ? otherwise
            'Rebate End Date', // 99/99/99
            'D&H Item Number', // D&H part number; potentially the same as manufacturer's part number. No special characters or punctuation allowed
            'Manuf.Item Number', // Manufacturer’s part number.
            'UPC', // Universal Product Code
            'Subcategory Code', // D&H Product category code
            'Vendor Name', // Manufacturer
            'Unit Cost', // 5.2 Example: 00010.00 = $10.00
            'Rebate Amount', // Zeroes when rebate is not applied. Example: 00010.00 = $10.00
            'Handling charge', // 5.2 Example: 00010.00 = $10.00
            'Freight', // 5.2 Based on UPS zone 5
            'Ship Via', // Ups
            'Weight', // Example: 0003 = 3 pounds.
            'Short Description', // Brief item description.
            'Long Description', // Detailed item description. Same as short description if detailed description not available.
        ];

        $skus = [];

        gc_collect_cycles();
        echo "1. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $fp = fopen($price_txt, 'rb');
        while ($line = fgetcsv($fp, null, '|')) {
            $line = array_combine($columns, $line);

/**
            if (
                ($line['Subcategory Code'] != '7566') && // toner
                ($line['Subcategory Code'] != '0733')    // xx
            ) {
                var_dump($line);
                die();
            }
**/

            foreach ($line as $k=>$v) $line[$k] = trim($v);

            $vpn = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$line['Manuf.Item Number']));

            $manufacturerId = null;
            foreach ($manufacturers as $reg=>$id) {
                if (preg_match($reg, $line['Vendor Name'])) {
                    $manufacturerId = $id;
                }
            }

            $product_data = [
                'supplierSku'=>$line['D&H Item Number'],
                'manufacturer'=>$line['Vendor Name'],
                'manufacturerId'=>$manufacturerId,
                'vpn'=>$vpn,
                'name'=>$line['Short Description'],
                'msrp'=>null,
                'weight'=>0.453592 * floatval($line['Weight']),
                'length'=>null,
                'width'=>null,
                'height'=>null,
                'upc'=>$line['UPC'],
                'description'=>$line['Long Description'],
                'package'=>null,
                'isStock'=>$line['Stock Status']=='I'?1:0,
                'qty'=>json_encode([
                    'All Branches'=>$line['Qty Avail All Branches']
                ]),
                'category'=>$line['Subcategory Code'],
                'categoryId'=>$line['Subcategory Code'],
                'dateCreated'=>null,
            ];

            $price_data = [
                'supplierSku'=>$line['D&H Item Number'],
                'dealerId'=>$this->dealerId,
                'price'=>trim($line['Unit Cost']),
                'promotion'=>$line['Rebate Flag']=='y'?1:0,
            ];

            try {
                $this->populate($product_data, $price_data);
            } catch (\Exception $ex) {
                var_dump($product_data);
                die ('xxx '.$ex->getMessage());
            }

            $skus[$manufacturerId][$vpn] = $line['D&H Item Number'];
            unset($this->remaining_product[$line['D&H Item Number']]);
        }

        gc_collect_cycles();
        echo "2. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $this->deleteRemainingProducts();

        $cursor = $db->query("select base_product.id, base_product.manufacturerId, sku, weight, UPC from base_product");
        while ($line=$cursor->fetch(\PDO::FETCH_ASSOC)) {
            $manufacturerId = $line['manufacturerId'];
            $sku = $line['sku'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($skus[$manufacturerId][$sku])) {
                $supplierSku = $skus[$manufacturerId][$sku];
                //update supplier_product set baseProductId=? where `supplierId`='.$this->supplierId.' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $supplierSku]);
                if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                    //update base_product set sku=(select vpn from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?) where id=?
                    $this->sku_statement->execute([$supplierSku, $supplierSku, $supplierSku, $line['id']]);
                }
            }
        }
        $cursor->closeCursor();

        gc_collect_cycles();
        echo "3. ".round(memory_get_usage()/(1024*1024))." MB\n";

        echo time() - $this->timerStart."s\n";
        return true;

    }

    private function updateArli() {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $xls_file = APPLICATION_BASE_PATH . '/data/cache/arli-'.$this->dealerId.'.xlsx';
        $csv_file = APPLICATION_BASE_PATH . '/data/cache/arli-'.$this->dealerId.'.csv';
        if (file_exists($xls_file)) {
            unlink($xls_file);
        }
        if (!file_exists($csv_file) || (filemtime($csv_file)<time()-(60*60*24*6))) try {
            echo "downloading {$this->dealerSupplier['url']} for dealer {$this->dealerId}\n";
            $ftp = $this->getFtpClient();
            $ftp->get($this->dealerSupplier['url'], $this->dealerSupplier['user'], $this->dealerSupplier['pass'], $xls_file);

            if (!file_exists($xls_file) || (filesize($xls_file)==0)) {
                error_log('xls file not downloaded');
                return false;
            }

            echo "converting xls to csv...\n";
            if (file_exists('c:/')) exec('xlsx2csv.py '.$xls_file.' '.$csv_file);
            else exec('xlsx2csv '.$xls_file.' '.$csv_file);

            if (!file_exists($csv_file) || (filesize($csv_file)==0)) {
                error_log('csv file not downloaded');
                return false;
            }
        } catch (\Exception $ex) {
            print_r($ex);
            Logger::logException($ex);
            return false;
        }
        echo "processing {$csv_file} for dealer {$this->dealerId}\n";

        $manufacturers=[];
        $prefixes=[];
        foreach ($db->query('select * from arli_manufacturer') as $line) {
            $manufacturers[$line['name']] = $line['manufacturerId'];
            $prefixes[$line['prefix']] = $line['manufacturerId'];
        }

        $skus = [];

        gc_collect_cycles();
        echo "1. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $fp = fopen($csv_file, 'rb');

        $columns = fgetcsv($fp);
        while ($line = fgetcsv($fp)) {
            if (empty($line) || empty($line[0])) continue;

            $line = array_combine($columns, $line);
            foreach ($line as $k=>$v) $line[$k] = trim($v);

            $manufacturerId = null;

            $vpn = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$line['Item Number']));
            foreach ($prefixes as $prefix=>$mfg) {
                if (strpos($vpn, $prefix)===0) {
                    $vpn = substr($vpn,3);
                    $manufacturerId = $mfg;
                }
            }
            if (isset($manufacturers[$line['prodline']])) {
                $manufacturerId = $manufacturers[$line['prodline']];
            }

            $product_data = [
                'supplierSku'=>$line['Item Number'],
                'manufacturer'=>$line['prodline'],
                'manufacturerId'=>$manufacturerId,
                'vpn'=>$vpn,
                'name'=>$line['Product Description'],
                'msrp'=>$line['SRP-List'],
                'weight'=>0.453592 * floatval($line['Weight']),
                'length'=>$line['Length(in)'] ? $line['Length(in)']*2.54/100 : null,
                'width'=>$line['width(in)'] ? $line['width(in)']*2.54/100 : null,
                'height'=>$line['height(in)'] ? $line['height(in)']*2.54/100 : null,
                'upc'=>$line['UPC'],
                'description'=>$line['Extended Desc'],
                'package'=>null,
                'isStock'=>$line['QtyAvail']>0?1:0,
                'qty'=>json_encode([
                    'QtyAvail'=>$line['QtyAvail']
                ]),
                'category'=>$line['MainCategory'].'/'.$line['SubCategory'],
                'categoryId'=>$line['SubCategory'],
                'dateCreated'=>null,
            ];

            $price_data = [
                'supplierSku'=>$line['Item Number'],
                'dealerId'=>$this->dealerId,
                'price'=>trim($line['Price']),
                'promotion'=>0,
            ];

            try {
                $this->populate($product_data, $price_data);
            } catch (\Exception $ex) {
                var_dump($product_data);
                die ('xxx '.$ex->getMessage());
            }

            $skus[$manufacturerId][$vpn] = $line['Item Number'];
            unset($this->remaining_product[$line['Item Number']]);
        }

        /**/
        gc_collect_cycles();
        echo "2. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $this->deleteRemainingProducts();

        $cursor = $db->query("select base_product.id, base_product.manufacturerId, sku, weight, UPC from base_product");
        while ($line=$cursor->fetch(\PDO::FETCH_ASSOC)) {
            $manufacturerId = $line['manufacturerId'];
            $sku = $line['sku'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($skus[$manufacturerId][$sku])) {
                $supplierSku = $skus[$manufacturerId][$sku];
                //update supplier_product set baseProductId=? where `supplierId`='.$this->supplierId.' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $supplierSku]);
                if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                    //update base_product set sku=(select vpn from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?) where id=?
                    $this->sku_statement->execute([$supplierSku, $supplierSku, $supplierSku, $line['id']]);
                }
            }
        }
        $cursor->closeCursor();

        gc_collect_cycles();
        echo "3. ".round(memory_get_usage()/(1024*1024))." MB\n";

        echo time() - $this->timerStart."s\n";
        return true;
    }

    private function updateClover() {

        $csv_filename = APPLICATION_BASE_PATH . '/data/cache/'.$this->dealerSupplier['url'];
        if (!file_exists($csv_filename)) {
            error_log('file not found: '.$csv_filename);
            return;
        }

        echo "processing {$csv_filename} for dealer {$this->dealerId}\n";

        $db = \Zend_Db_Table::getDefaultAdapter();
        $manufacturers = [];
        foreach ($db->query('select * from manufacturers order by displayname')->fetchAll() as $line) {
            $n = strtoupper($line['fullname']);
            if ($n=='DATAPRODUCTS') $manufacturers['DATAPRODUCTS CANADA'] = $line['id'];
            if ($n=='DATAPRODUCTS') $manufacturers['DP'] = $line['id'];
            if ($n=='CLOVER TECHNOLOGIES') $manufacturers['CIG'] = $line['id'];
            $manufacturers[$n] = $line['id'];
            $n = strtoupper($line['displayname']);
            $manufacturers[$n] = $line['id'];
            if ($n=='FUJI XEROX') $manufacturers['FUJIFILM'] = $line['id'];
            if ($n=='OKI') $manufacturers['OKIDATA'] = $line['id'];
            if ($n=='ITOCHU') $manufacturers['C. ITOH'] = $line['id'];
            if ($n=='ITOCHU') $manufacturers['C.ITOH'] = $line['id'];
            if ($n=='ITOCHU') $manufacturers['C ITOH'] = $line['id'];
        }

        $skus = [];
        foreach ($db->query('select * from base_product p join base_printer_consumable c using(id) left join base_printer_cartridge a using(id)')->fetchAll() as $line) {
            $sku = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$line['sku']));
            $skus[$line['manufacturerId']][$sku] = $line;
        }

        $fp = fopen($csv_filename,'rb');
        $cols = fgetcsv($fp, null, ';', '"', '""');
        foreach ($cols as $i=>$n) {
            if ($n=='Colour') $cols[$i] = 'Color';
        }

        $i = 0;
        while($line = fgetcsv($fp, null, ';', '"', '""')) {
            $line = array_combine($cols, $line);

            //if ($line['Item #']!='EPC99620') continue;

            $ignore = false;
            /**
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
            **/
            switch (trim($line['Type'])) {
                //case 'Ink Postage':
                case 'Switch/Router':
                case 'Hard Drive':
                //case 'Belt':
                case 'Sensor':
                case 'Processor':
                case 'Cable':
                case '3D Filament':
                case 'Memory':
                //case 'Ribbon':
                    $ignore = true;
            }
            if ($ignore) continue;

            if (strpos($line['Title'], 'OLD - ')===0) {
                continue;
            }

            $comp_mfg_id = false;
            $brand = '';
            foreach ($manufacturers as $name=>$id) {
                if (preg_match('#^('.preg_quote($name).') (.+)$#i', $line['Title'], $match)) {
                    $brand = $match[1];
                    $comp_mfg_id = $id;
                    $line['Title'] = $match[2];
                    break;
                }
            }
            if (!$comp_mfg_id) {
                //die($line['Title']);
                error_log('Manufacturer not recognized: '.$line['Title']);
                continue;
            }

            $status = 'Compatible';
            if (preg_match('#^(Remanufactured|Non-OEM|OEM) (.+)$#i', $line['Title'], $match)) {
                $status = $match[1];
                if ($status=='Non-OEM') $status = 'Compatible';
                $line['Title'] = $match[2];
            }
            if ($status == 'Remanufactured') {
                switch ($brand) {
                    case 'Dataproducts':
                    case 'Dataproducts Canada':
                    case 'MSE':
                    case 'CIG':
                    case 'Depot International':
                    case 'ecoPost':
                        break;
                    default : {
                        $brand = 'CIG';
                        $comp_mfg_id = $manufacturers['CIG'];
                    }
                }
            }

            if (preg_match('#^'.preg_quote($line['OEM']).' (.+)$#i', $line['Title'], $match)) {
                $line['Title'] = $match[1];
                if (preg_match('#^New (.+)$#', $line['Title'], $match)) {
                    $line['Title'] = $match[1];
                }
            }

            //echo "{$line['Title']}\n";

            #--
            $product_data = [
                'supplierSku'=>$line['Item #'],
                'manufacturer'=>$line['OEM'],
                'manufacturerId'=>$comp_mfg_id,
                'vpn'=>$line['Item #'],
                'name'=>$line['Title'],
                'msrp'=>null,
                'weight'=>0.453592 * floatval($line['Weight']),
                'length'=>$line['Length']*2.54/100,
                'width'=>$line['Width']*2.54/100,
                'height'=>$line['Height']*2.54/100,
                'upc'=>$line['UPC Code'],
                'description'=>$line['Title'],
                'package'=>$line['Unit of Measure'],
                'isStock'=>1,
                'qty'=>json_encode([]),
                'category'=>$line['Type'],
                'categoryId'=>$line['Type'],
                'dateCreated'=>null,
            ];

            $price_data = [
                'supplierSku'=>$line['Item #'],
                'dealerId'=>$this->dealerId,
                'price'=>trim($line['Price']),
                'promotion'=>0,
            ];

            try {
                $this->populate($product_data, $price_data);
            } catch (\Exception $ex) {
                var_dump($product_data);
                die ('xxx '.$ex->getMessage());
            }
            #--

            //xxxx

            $oem_mfg_id = null;
            if (isset($manufacturers[strtoupper($line['OEM'])])) {
                $oem_mfg_id = $manufacturers[strtoupper($line['OEM'])];
            }

            $compatible = [];
            foreach (explode(',', $line['Compatibility']) as $str) {
                $printer_brands = [];
                $str = trim($str);
                if (!empty($str)) {
                    while(true) {
                        //echo "$str\n";
                        if (preg_match('#^' . preg_quote($line['OEM']) . ' (.+)$#', $str, $match)) {
                            //$compatible[] = ['brand'=>$line['OEM'], 'manufacturerId'=>$oem_mfg_id, 'model'=>$match[1]];
                            $printer_brands[$oem_mfg_id] = $line['OEM'];
                            $str = $match[1];
                            continue;
                        }
                        if (preg_match('#^' . preg_quote($brand) . ' (.+)$#', $str, $match)) {
                            //$compatible[] = ['brand'=>$brand, 'manufacturerId'=>$comp_mfg_id, 'model'=>$match[1]];
                            $printer_brands[$comp_mfg_id] = $brand;
                            $str = $match[1];
                            continue;
                        }
                        foreach ($manufacturers as $name => $id) {
                            if (preg_match('#^(' . preg_quote($name) . ') (.+)$#i', $str, $match)) {
                                //$compatible[] = ['brand'=>$match[1], 'manufacturerId'=>$id, 'model'=>$match[2]];
                                $printer_brands[$id] = $match[1];
                                $str = $match[2];
                                continue 2;
                            }
                        }
                        if (empty($printer_brands)) {
                            $e = explode(' ', $str);
                            if (count($e)>1) {
                                $printer_brand = array_shift($e);
                                $compatible[] = ['brand'=>$printer_brand, 'manufacturerId'=>null, 'model'=>implode(' ', $e)];
                            }
                        } else {
                            foreach ($printer_brands as $id=>$name) {
                                $compatible[] = ['brand'=>$name, 'manufacturerId'=>$id, 'model'=>$str];
                            }
                        }
                        break;
                    }
                }
            }

            //$db, $supplierSku,$status,$oemManufacturer,$oemManufacturerId,$consumableManufacturer,$consumableManufacturerId,$oemSku,$title,$type,$color,$yield,$upc,$imageUrl,$compatible
            $this->populateSupplierConsumable(
                $db,
                $line['Item #'],
                $status,
                $line['OEM'],
                $oem_mfg_id,
                $brand,
                $comp_mfg_id,
                $line['OEM Part #'],
                $line['Title'],
                $line['Type'],
                $line['Color'],
                $line['Page Yield'],
                $line['UPC Code'],
                $line['Image URL'],
                $compatible
            );

            if (empty($line['Page Yield']) || ($line['Page Yield']==' - ')) {
                $line['Page Yield'] = 0;
            } else {
                $line['Page Yield'] = $this->expandYield($line['Page Yield']); //str_replace(',','',$line['Page Yield']);
                if (!$line['Page Yield']) {
                    error_log("{$line['Page Yield']} ???");
                    continue;
                }
            }

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
            $this->populateCompatible($db, $skus, $comp_mfg_id, $supplierSku, $imgUrl, $name, $weight, $upc, $price, $yield, $oem_lines);

            $i++;
        }

        gc_collect_cycles();
        echo "{$i} lines processed\n";

        $this->total_result['products_total'] = $i;
    }
    private function updateGenuine() {
        $base_uri = trim($this->dealerSupplier['url'], '/');

        require_once(APPLICATION_BASE_PATH.'/library/My/MyCookieJar.php');
        $jar = new \MyCookieJar();

        $client = new Client([
            //'debug' => true,
            'base_uri' => $base_uri,
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
            if (isset($_GET['test'])) {
                echo "TEST: Product_Listing.csv\n";
                $fp = fopen(APPLICATION_BASE_PATH.'/data/cache/Product_Listing.csv','rb');
            } else {
                /**/
                $uri = new Uri($base_uri . '/landing.asp?autopage=/Default.asp');
                $r = $client->get($uri, ['allow_redirects' => false]);
                if ($r->getStatusCode() != 200) {
                    error_log($this->dealerSupplier['url'] . ' > ' . $r->getStatusCode());
                    return;
                }

                echo "landing.asp ok\n";

                $uri = new Uri($base_uri . '/security_logonScript_siteFront.asp?' . http_build_query([
                        'action' => 'logon',
                        'pageredir' => '/default.asp',
                        'parent_c_id' => '',
                        'returnpage' => 'landing.asp?'
                    ]));

                $r = $client->post($uri,
                    array(
                        'allow_redirects' => false,
                        'headers' => [
                            'Referer' => $base_uri . '/landing.asp?autopage=/Default.asp',
                        ],
                        'form_params' => [
                            'username' => $this->dealerSupplier['user'],
                            'password' => $this->dealerSupplier['pass'],
                            'B1' => 'GO!',
                            'logontype' => 'customer',
                        ]
                    )
                );
                if ($r->getStatusCode() != 302) {
                    error_log($this->dealerSupplier['url'] . '/security_logonScript_siteFront.asp > ' . $r->getStatusCode());
                    return;
                }

                echo "security_logonScript_siteFront.asp ok\n";

                $redirect = $r->getHeaderLine('Location');
                if (strpos($redirect, 'err=1') !== false) {
                    error_log("LOGIN FAILED");
                    return;
                }

                if ($redirect=='http://store.genuinesupply.ca//default.asp') {
                    $redirect = 'http://store.genuinesupply.ca/';
                }
                $r = $client->get($redirect, [
                    'allow_redirects' => false,
                    'headers' => [
                    ],
                ]);
                if ($r->getStatusCode() != 200) {
                    error_log($redirect . ' > ' . $r->getStatusCode());
                    return;
                }

                echo "{$redirect} ok\n";

                $r = $client->get(str_replace('https', 'http', $base_uri) . '/product_list.asp?' . http_build_query(['downloadasfile' => '1', '' => '']), [
                    'allow_redirects' => true,
                    'headers' => [
                    ],
                ]);
                if ($r->getStatusCode() != 200) {
                    error_log($this->dealerSupplier['url'] . '/product_list.asp > ' . $r->getStatusCode());
                    return;
                }

                if ($r->getHeaderLine('Content-Type') != 'application/csv; charset=utf-8; Charset=utf-8') {
                    error_log($this->dealerSupplier['url'] . '/product_list.asp > Content-Type!=csv > ' . $r->getHeaderLine('Content-Type'));
                    return;
                }

                $str = $r->getBody()->getContents();

                $fp = fopen('php://memory', 'r+');
                fwrite($fp, $str);
                rewind($fp);

                echo "processing {$this->dealerSupplier['url']}/product_list.asp for dealer {$this->dealerId}\n";
            }
            #===============================================

            $db = \Zend_Db_Table::getDefaultAdapter();

            $manufacturers = [];
            foreach ($db->query('select * from manufacturers order by displayname')->fetchAll() as $line) {
                $n = strtoupper($line['fullname']);
                if ($n=='KONICA') $manufacturers['KONICA/MINOLTA'] = $line['id'];
                if ($n=='OKI') $manufacturers['OKIDATA'] = $line['id'];
                if ($n=='LANIER') $manufacturers['LANIER WORLDWIDE'] = $line['id'];
                if ($n=='KODAK') $manufacturers['DANKA OFFICE IMAGING (KODAK)'] = $line['id'];
                if ($n=='PITNEY BOWES') $manufacturers['IMAGISTICS-PITNEY BOWES'] = $line['id'];
                if ($n=='KONICA MINOLTA') $manufacturers['KM'] = $line['id'];
                if ($n=='PANSONIC') $manufacturers['PANASONIC'] = $line['id'];
                $manufacturers[$n] = $line['id'];
                $n = strtoupper($line['displayname']);
                if ($n=='HP') $manufacturers['HEWLETT PACKARD'] = $line['id'];
                if ($n=='KONICA') $manufacturers['KONICA/MINOLTA'] = $line['id'];
                if ($n=='OKI') $manufacturers['OKIDATA'] = $line['id'];
                if ($n=='LANIER') $manufacturers['LANIER WORLDWIDE'] = $line['id'];
                if ($n=='KODAK') $manufacturers['DANKA OFFICE IMAGING (KODAK)'] = $line['id'];
                if ($n=='PITNEY BOWES') $manufacturers['IMAGISTICS-PITNEY BOWES'] = $line['id'];
                if ($n=='KONICA MINOLTA') $manufacturers['KM'] = $line['id'];
                if ($n=='PANSONIC') $manufacturers['PANASONIC'] = $line['id'];
                $manufacturers[$n] = $line['id'];
            }

            $colors = [];
            foreach ($db->query('select * from toner_colors') as $line) {
                $colors[$line['name']] = $line['id'];
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
                foreach ($line as $k=>$v) $line[$k] = trim($v);
                $line['brand'] = strip_tags($line['brand']);
                #--

                $imgUrl = 'http://store.genuinesupply.ca/images/'.$line['lg_pic'];

                $comp_mfg_id = null;
                if (isset($manufacturers[strtoupper($line['brand'])])) $comp_mfg_id = $manufacturers[strtoupper($line['brand'])];

                $dmatch=null;
                preg_match('#^(\d+)mm x (\d+)mm x (\d+)mm$#',$line['dimension'], $dmatch);

                $product_data = [
                    'supplierSku'=>$line['sku'],
                    'manufacturer'=>$line['brand'],
                    'manufacturerId'=>$comp_mfg_id,
                    'vpn'=>$line['sku'],
                    'name'=>$line['ds'],
                    'msrp'=>$line['msrp'],
                    'weight'=>0.453592 * floatval($line['weight']),
                    'length'=>$dmatch?intval($dmatch[1])/1000:null,
                    'width'=>$dmatch?intval($dmatch[2])/1000:null,
                    'height'=>$dmatch?intval($dmatch[3])/1000:null,
                    'upc'=>$line['upc'],
                    'description'=>$line['ds'],
                    'package'=>$line['contents'],
                    'isStock'=>preg_match('#In Stock#', $line['calc_inv_message'])?1:0,
                    'qty'=>json_encode([]),
                    'category'=>$line['category'],
                    'categoryId'=>$line['category'],
                    'dateCreated'=>null,
                ];

                $price_data = [
                    'supplierSku'=>$line['sku'],
                    'dealerId'=>$this->dealerId,
                    'price'=>trim($line['cust_price']),
                    'promotion'=>0,
                ];

                try {
                    $this->populate($product_data, $price_data);
                } catch (\Exception $ex) {
                    var_dump($product_data);
                    die ('xxxx '.$ex->getMessage());
                }
                #--

                if ($line['type']=='New') continue;
                if ($line['type']=='ORIGINAL') continue;
                if ($line['brand']=='Generic') continue;

                switch ($line['category']) {
                    case 'laser-toner-cartridges' :
                    case 'laser-drum-units' :
                    case 'Ink Cartridges' :
                    case 'drums,laser-drum-units' :
                    case 'thermal-films' :
                    case 'drums' :
                    case 'drums,kits' :
                    case 'kits' :
                    case 'fuser-heat-rollers' :
                    case 'fuser-pressure-rollers' :
                    case 'feed-delivery-components' :
                    case 'waste-toner-receptacles' :
                    case 'copier-toner-cartridges' :
                    case 'ribbons' :
                    case 'developer' :
                    case 'fuser-cleaning-components' :
                    case 'kits,fuser-cleaning-components' :
                    case 'kits,fuser-pressure-rollers' :
                    case 'solid-ink' :
                    {
                        break;
                    }
                    default: {
                        echo "ignoring: {$line['category']}\n";
                        continue;
                    }
                }

                $brand = false;
                $oem_mfg_id = false;
                foreach ($manufacturers as $mfg_name=>$mfg_id) {
                    if (preg_match('#^('.preg_quote($mfg_name).')#i', $line['nm'], $brand_match)) {
                        $brand = $brand_match[1];
                        $oem_mfg_id = $mfg_id;
                    }
                }
                if (!$oem_mfg_id) {
                    echo "brand? {$line['nm']}\n";
                    continue;
                }

                $status = 'Compatible';
                if ($line['type']=='New') $status='OEM';
                if ($line['type']=='Remanufactured') $status='Remanufactured';

                $compatible = [];
                $str = $line['ds'];
                if (preg_match('#^(.+)- [\w ]+$#i', $str, $match)) {
                    $str = trim($match[1]);
                }
                //echo "$str\n";
                $e = explode(',', $str);
                $printer_brand = $brand;
                $printer_mfg_id = $oem_mfg_id;
                foreach ($e as $s) {
                    $s = trim($s);
                    $ee = explode(' ', $s);
                    if (count($ee)==1) {
                        if (!empty($s)) {
                            $compatible[] = ['brand'=>$printer_brand, 'manufacturerId'=>$printer_mfg_id, 'model'=>$s];
                        }
                    } else {
                        foreach ($manufacturers as $mfg_name=>$mfg_id) {
                            if (preg_match('#^('.preg_quote($mfg_name).') (.+)$#i', $s, $brand_match)) {
                                $printer_brand = $brand_match[1];
                                $printer_mfg_id = $mfg_id;
                                $s = trim($brand_match[2]);
                                break;
                            }
                        }
                        if (!empty($s)) {
                            $compatible[] = ['brand' => $printer_brand, 'manufacturerId' => $printer_mfg_id, 'model' => $s];
                        }
                    }
                }

                //$db, $supplierSku,$status,$oemManufacturer,$oemManufacturerId,$consumableManufacturer,$consumableManufacturerId,$oemSku,$title,$type,$color,$yield,$upc,$imageUrl,$compatible
                $this->populateSupplierConsumable(
                    $db,
                    $line['sku'],
                    $status,
                    $brand,
                    $oem_mfg_id,
                    $line['brand'],
                    $comp_mfg_id,
                    $line['oem'],
                    $line['ds'],
                    $line['category'],
                    $line['colour'],
                    $line['yield'],
                    $line['upc'],
                    $imgUrl,
                    $compatible
                );

                #--

                if (!empty($line['yield']) && (strtolower($line['yield'])!='n/a')) {
                    if (preg_match('#([\d,]+) page#i', $line['yield'], $match)) $line['yield'] = $match[1];
                    $line['yield'] = $this->expandYield($line['yield']); //str_replace(',','',$line['yield']);
                } else {
                    $line['yield'] = 0;
                }

                $oem_lines=[];
                foreach (explode(',', $line['oem']) as $n) {
                    $n = str_replace('-','',trim($n));
                    if (empty($n)) continue;
                    if (isset($skus[$oem_mfg_id][$n])) {
                        $oem_lines[] = $skus[$oem_mfg_id][$n];
                    }
                }

                if (!isset($manufacturers[strtoupper($line['brand'])])) {
                    #var_dump($line);
                    #die($line['brand']);
                    error_log('unknown brand: '.$line['brand']);
                    continue;
                }
                $comp_mfg_id = $manufacturers[strtoupper($line['brand'])];

                if (empty($oem_lines)) {
                    //continue;
                    $oem_line_colorId=null;
                    if (!empty($line['colour'])) {
                        if (isset($colors[strtoupper($line['colour'])])) $oem_line_colorId = $colors[strtoupper($line['colour'])];
                        else $oem_line_colorId = 7; //COLOR
                    }

                    $oem_lines = [[
                        'base_type'=>$oem_line_colorId ? 'printer_cartridge':'printer_consumable',
                        'weight'=>0.453592 * floatval($line['weight']),
                        'quantity'=>1,
                        'type'=>$line['category'],
                        'colorId'=>$oem_line_colorId,
                    ]];
                }

                $name = '';
                $weight = $line['weight'];
                $upc = $line['upc'];
                $price = $line['cust_price'];
                $yield = $line['yield'];
                $this->populateCompatible($db, $skus, $comp_mfg_id, $line['sku'], $imgUrl, $name, $weight, $upc, $price, $yield, $oem_lines, null, $line['colour']);

                $i++;
            }

            gc_collect_cycles();
            echo "{$i} lines processed\n";

            $this->total_result['products_total'] = $i;

        } catch (RequestException $ex) {
            error_log($ex->getMessage());
            die();
        }
    }

    private function updateTechDataNA() {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/cache/' . sprintf('techdata-%s.zip', $this->dealerId);
        echo "starting with zip file {$zip_filename} for dealer {$this->dealerId}\n";

        try {
            if (!file_exists($zip_filename) || (filemtime($zip_filename) < strtotime('-6 DAY'))) {
                echo "downloading zip file {$zip_filename} for dealer {$this->dealerId}\n";
                $url = $this->dealerSupplier['url'].'?Auto=Y&UserID='.$this->dealerSupplier['user'].'&Password='.$this->dealerSupplier['pass'].'&Type=Price&Name=ProdCod.zip';
                $client = new Client(['verify'=>false]);
                $response = $client->get($url);
                file_put_contents($zip_filename, $response->getBody());
            }

            if (!file_exists($zip_filename) || (filesize($zip_filename)==0)) {
                error_log('zip download failed');
                return false;
            }

            echo "extracting zip file {$zip_filename} for dealer {$this->dealerId}\n";
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
                'dealerId'=>$this->dealerId,
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
        $this->deleteRemainingProducts();
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
                //update supplier_product set baseProductId=? where `supplierId`='.$this->supplierId.' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $supplierSku]);
                #if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                    //update base_product set sku=(select vpn from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?) where id=?
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

    private function updateTechDataSA() {

        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/temp/'.$this->dealerSupplier['url'];

        $product_file='';
        $price_file='';
        $stock_file='';

        try {
            if (!file_exists($zip_filename) || (filemtime($zip_filename) < strtotime('-6 DAY'))) {
                #$ftp = $this->getFtpClient();
                #$ftp->get($this->dealerSupplier['url'], $this->dealerSupplier['user'], $this->dealerSupplier['pass'], $zip_filename);
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
        $currency_exchange = 1;
        $dealer_currency = $db->query('select currency from dealers where id=?', [$this->dealerId])->fetchColumn(0);
        if ($dealer_currency!='USD') {
            echo "dealer currency: {$dealer_currency}\n";
            $currency_exchange = floatval($db->query('select rate from currency_exchange where currency=?', [$dealer_currency])->fetchColumn(0));
            echo "dealer currency exchange: {$currency_exchange}\n";
        }

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
                'dealerId'=>$this->dealerId,
                'price'=>$currency_exchange * $line['CustBestPrice'],
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
        $this->deleteRemainingProducts();

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
                //update supplier_product set baseProductId=? where `supplierId`='.$this->supplierId.' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $matnr]);
                $toner_count += $this->base_product_statement->rowCount();
                if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                    //update base_product set sku=(select vpn from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?) where id=?
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

    private function updateSynnex()
    {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/cache/' . sprintf('synnex-%s.zip', $this->dealerId);
        echo "starting with zip file {$zip_filename} for dealer {$this->dealerId}\n";

        try {
            if (!file_exists($zip_filename) || (filemtime($zip_filename) < strtotime('-6 DAY'))) {
                echo "downloading zip file {$zip_filename} for dealer {$this->dealerId}\n";
                $ftp = $this->getFtpClient();
                $ftp->get($this->dealerSupplier['url'], $this->dealerSupplier['user'], $this->dealerSupplier['pass'], $zip_filename);
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

        echo "processing {$filename} for dealer {$this->dealerId}\n";
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
                'dealerId'=>$this->dealerId,
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
        $this->deleteRemainingProducts();
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
                //update supplier_product set baseProductId=? where `supplierId`='.$this->supplierId.' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $supplierSku]);
                if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                    //update base_product set sku=(select vpn from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?) where id=?
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

    private function updateIngram() {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/cache/'.sprintf('ingram-%s.zip', $this->dealerId);
        $price_txt = dirname($zip_filename).'/PRICE.TXT';
        if (file_exists($price_txt)) {
            unlink($price_txt);
        }
        try {
            if (!file_exists($zip_filename) || (filemtime($zip_filename)<strtotime('-6 DAY'))) {
                $ftp = $this->getFtpClient();
                $ftp->get("{$this->dealerSupplier['url']}/PRICE.ZIP", $this->dealerSupplier['user'], $this->dealerSupplier['pass'], $zip_filename);
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
        foreach ($db->query('select * from manufacturers') as $line) {
            $reg = '#^'.strtoupper($line['fullname']).'.*$#i';
            $manufacturers[$reg] = $line['id'];
            $reg = '#^'.strtoupper($line['displayname']).'.*$#i';
            $manufacturers[$reg] = $line['id'];
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
                ($line['INGRAM_MICRO_CATEGORY'] != '0001') && // desktop
                ($line['INGRAM_MICRO_CATEGORY'] != '0303') && // monitor
                ($line['INGRAM_MICRO_CATEGORY'] != '9920') && // cases
                ($line['INGRAM_MICRO_CATEGORY'] != '9045') && // monitor
                ($line['INGRAM_MICRO_CATEGORY'] != '0011') && // notebook
                ($line['INGRAM_MICRO_CATEGORY'] != '1241') && // service
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
                        'dealerId'=>$this->dealerId,
                        'price'=>trim($line['CUSTOMER_PRICE']),
                        'promotion'=>$line['SPECIAL_PRICE_FLAG']=='*'?1:0,
                    ];


                    try {
                        $this->populate($product_data, $price_data);
                    } catch (\Exception $ex) {
                        var_dump($product_data);
                        die ('xxx '.$ex->getMessage());
                    }

                    $skus[$manufacturerId][$vpn] = $line['INGRAM_PART_NUMBER'];

                    unset($this->remaining_product[$line['INGRAM_PART_NUMBER']]);
                    break;
                }
            }
        }

        gc_collect_cycles();
        echo "2. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $this->deleteRemainingProducts();

        $cursor = $db->query("select id, manufacturerId, sku, weight, UPC from base_product");
        while ($line=$cursor->fetch(\PDO::FETCH_ASSOC)) {
            $manufacturerId = $line['manufacturerId'];
            $sku = $line['sku'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($skus[$manufacturerId][$sku])) {
                $supplierSku = $skus[$manufacturerId][$sku];
                //update supplier_product set baseProductId=? where `supplierId`='.$this->supplierId.' and `supplierSku`=?
                $this->base_product_statement->execute([$line['id'], $supplierSku]);
                if (empty($line['sku']) || empty($line['weight']) || empty($line['UPC'])) {
                    //update base_product set sku=(select vpn from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), upc=(select upc from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?), weight=(select weight from supplier_product where `supplierId`='.$this->supplierId.' and `supplierSku`=?) where id=?
                    $this->sku_statement->execute([$supplierSku, $supplierSku, $supplierSku, $line['id']]);
                }
            }
        }
        $cursor->closeCursor();


        gc_collect_cycles();
        echo "3. ".round(memory_get_usage()/(1024*1024))." MB\n";

        echo time() - $this->timerStart."s\n";
        return true;
    }

    public function updateQcfl() {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $fp = fopen(APPLICATION_BASE_PATH . '/data/cache/'.$this->dealerSupplier['url'], 'rb');
        if (!$fp) {
            error_log('Cannot open '.APPLICATION_BASE_PATH . '/data/cache/'.$this->dealerSupplier['url']);
            return;
        }

        $manufacturers = [];
        foreach ($db->query('select * from manufacturers') as $line) {
            $str = strtoupper($line['fullname']);
            $manufacturers[$str] = $line['id'];
            $str = strtoupper($line['displayname']);
            $manufacturers[$str] = $line['id'];
            if ($line['fullname']=='Imagistics') {
                $manufacturers['PB/IMAG'] = $line['id'];
            }
            if ($line['fullname']=='PITNEY BOWES') {
                $manufacturers['PITNEY BOW'] = $line['id'];
            }
            if ($line['fullname']=='Source Technologies') {
                $manufacturers['SOURCETECH'] = $line['id'];
            }
        }

        $colors = [];
        foreach ($db->query('select * from toner_colors') as $line) {
            $colors[$line['name']] = $line['id'];
        }

        $skus = [];
        foreach ($db->query('select * from base_product p join base_printer_consumable c using(id) left join base_printer_cartridge a using(id)')->fetchAll() as $line) {
            $sku = str_replace('-','',preg_replace('/[#\/]\w\w\w/','',$line['sku']));
            $skus[$line['manufacturerId']][$sku] = $line;
        }

        $line = fgetcsv($fp);
        $line = fgetcsv($fp);
        $line = fgetcsv($fp);
        $line = fgetcsv($fp);
        $line = fgetcsv($fp);
        $columns = fgetcsv($fp);

        while($line = fgetcsv($fp)) {
            $line = array_combine($columns, $line);
            foreach ($line as $k=>$v) $line[$k] = trim($v);
            $str = strtoupper($line['OEM']);
            if (!isset($manufacturers[$str])) {
                error_log('Mfg unknown: '.$line['OEM']);
                continue;
            }
            $manufacturerId = $manufacturers[$str];

            $sell_price = trim($line['Price'],'$');
            $price = 0.8 * $sell_price;

            $type = $line['Product Group'];
            if ($type=='Inkjet') $type='Inkjet Cartridge';
            if ($type=='Laser') $type='Laser Cartridge';
            if ($type=='Color Laser') $type='Laser Cartridge';

            $product_data = [
                'supplierSku'=>$line['Item No.'],
                'manufacturer'=>$line['OEM'],
                'manufacturerId'=>$manufacturerId,
                'vpn'=>$line['OEM Item No.'],
                'name'=>null,
                'msrp'=>trim($line['MSRP'],'$'),
                'weight'=>null,
                'length'=>null,
                'width'=>null,
                'height'=>null,
                'upc'=>null,
                'description'=>$line['Machine Compatibility/Description'],
                'package'=>null,
                'isStock'=>1,
                'qty'=>null,
                'category'=>null,
                'categoryId'=>null,
                'dateCreated'=>null,
            ];

            $price_data = [
                'supplierSku'=>$line['Item No.'],
                'dealerId'=>$this->dealerId,
                'price'=>$price,
                'promotion'=>0,
            ];

            try {
                $this->populate($product_data, $price_data);
            } catch (\Exception $ex) {
                var_dump($product_data);
                die ('xxx '.$ex->getMessage());
            }

            $imgUrl = 'https://cdn.shopify.com/s/files/1/1603/0113/files/QCFL_lg_png24_1.png';

            $this->populateSupplierConsumable(
                $db,
                $line['Item No.'],
                'Compatible',
                $line['OEM'],
                $manufacturerId,
                'QCFL',
                137,
                $line['OEM Item No.'],
                $line['OEM Item No.'],
                $type,
                $line['Color'],
                $line['Page Yield'],
                null,
                $imgUrl,
                []
            );

            #--
            $oem_lines = [];
            $e = explode(',', $line['OEM Item No.']);
            foreach ($e as $n) {
                $n = str_replace('-', '', trim($n));
                $n = preg_replace('#\((M|J)\)$#','',$n);
                if (empty($n)) continue;
                if (isset($skus[$manufacturerId][$n])) {
                    $oem_lines[] = $skus[$manufacturerId][$n];
                }
            }

            /**/
            if (empty($oem_lines)) {
//                error_log('oem not found for compatible: '.print_r($line,true));
                /**/
                $oem_line_colorId=null;
                $color = str_replace('DRUM, ','',$line['Color']);
                if ($color=='DRUM') $color='';
                if ($color=='Black Drum') $color='BLACK';
                if ($color=='Tri-Color') $color='3 COLOR';
                if ($color=='Photo Black') $color='BLACK';
                if ($color=='MICR Black') $color='BLACK';
                if (!empty($color)) {
                    if (isset($colors[strtoupper($color)])) $oem_line_colorId = $colors[strtoupper($color)];
                    else {
                        echo "{$color}\n";
                        $oem_line_colorId = 7;
                    } //COLOR
                }

                $oem_lines = [[
                    'base_type'=>$oem_line_colorId ? 'printer_cartridge':'printer_consumable',
                    'weight'=>0,
                    'quantity'=>1,
                    'type'=>$type,
                    'colorId'=>$oem_line_colorId,
                ]];
                /**/
            }
            /* xxxx */

            $base_product_id = $this->populateCompatible($db, $skus, 137, $line['Item No.'], $imgUrl, '', null, null, $price, str_replace(',','', $line['Page Yield']), $oem_lines, 0, $line['Color'], $sell_price);
            #--

        }

        fclose($fp);
    }

}