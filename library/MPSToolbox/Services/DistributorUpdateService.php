<?php

namespace MPSToolbox\Services;

use Tangent\Ftp\NcFtp;
use Tangent\Logger\Logger;

class DistributorUpdateService {

    const SUPPLIER_INGRAM   = 1;
    const SUPPLIER_SYNNEX   = 2;
    const SUPPLIER_TECHDATA = 3;

    /** @var  \Zend_Filter_Compress_Zip */
    private $zipAdapter;

    /** @var  NcFtp */
    private $ftpClient;

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
        }
    }

    private function updateTechData($dealerSupplier) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/temp/'.$dealerSupplier['url'];

        $product_file='';
        $price_file='';
        $stock_file='';

        try {
            if (!file_exists($zip_filename) || (filemtime($zip_filename) < strtotime('-6 DAY'))) {
                #$ftp = $this->getFtpClient();
                #$ftp->get($dealerSupplier['url'], $dealerSupplier['user'], $dealerSupplier['pass'], $zip_filename);
                echo "file not found: {$zip_filename}<br>\n";
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

        if (empty($product_file)) { echo "product_file?<br>\n"; return null; }
        if (empty($price_file)) { echo "price_file?<br>\n"; return null; }
        if (empty($stock_file)) { echo "stock_file?<br>\n"; return null; }

        $columns = [
            'Matnr',
            'ManufPartNo',
            'GTIN',
            'Qty',
        ];
        $qty = [];
        $filename=dirname($zip_filename).'/'.$stock_file;
        echo "{$filename}<br>\n";
        $fp = fopen($filename, 'rb');

        $hdr = fgetcsv($fp, null, "\t");
        while ($line = fgetcsv($fp, null, "\t")) {
            while (count($line) > count($columns)) {
                array_pop($line);
            }
            $line = array_combine($columns, $line);
            $qty[$line['Matnr']] = $line['Qty'];
        }
        fclose($filename);

        $columns = [
            'Matnr',
            'ShortDescription',
            'LongDescription',
            'ManufPartNo',
            'Manufacturer',
            'ManufacturerGlobalDescr',
            'GTIN',
            'ProdFamilyID',
            'ProdFamily',
            'ProdClassID',
            'ProdClass',
            'ProdSubClassID',
            'ProdSubClass',
            'ArticleCreationDate',
            'CNETavailable',
            'CNETid',
            'ListPrice',
            'Weight',
            'Length',
            'Width',
            'Heigth',
            'NoReturn',
            'MayRequireAuthorization',
            'EndUserInformation',
            'FreightPolicyException'
        ];

        $filename=dirname($zip_filename).'/'.$product_file;
        echo "{$filename}<br>\n";
        $fp = fopen($filename, 'rb');

        $db->query('update techdata_products set Qty=0');

        $replace_statement = false;
        $toner_statement = false;
        $masterDevice_statement = false;
        $computer_statement = false;
        $peripheral_statement = false;
        $price_statement = false;
        $hp_toner_statement = false;
        $hp_device_statement = false;


        $hdr = fgetcsv($fp, null, "\t");
        while ($line = fgetcsv($fp, null, "\t")) {
            while (count($line)>count($columns)) {
                array_pop($line);
            }
            $line = array_combine($columns, $line);
            $line['Qty'] = isset($qty[$line['Matnr']]) ? $qty[$line['Matnr']] : 0;

            if (!$replace_statement) {
                $sql = [];
                foreach ($line as $key => $value) {
                    $sql[] = "`$key`=:{$key}";
                }
                $sql = 'REPLACE INTO techdata_products SET ' . implode(', ', $sql);
                $replace_statement = $db->prepare($sql);
            }
            $replace_statement->execute($line);
/**/
            if (!$toner_statement) {
                $sql = 'update `techdata_products` i set `tonerId`=(select id from toners where `manufacturerId` in (select manufacturerId from master_devices) and sku=i.`ManufPartNo` limit 1) where tonerId is null and Matnr=:Matnr';
                $toner_statement = $db->prepare($sql);
                $sql = 'update `techdata_products` i set `masterDeviceId`=(select masterDeviceId from devices where oemSku=i.`ManufPartNo` limit 1) where Matnr=:Matnr';
                $masterDevice_statement = $db->prepare($sql);
                $sql = 'update `techdata_products` i set `computerId`=(select id from ext_dealer_hardware where id in (select id from ext_computer) and oemSku=i.`ManufPartNo` limit 1) where Matnr=:Matnr';
                $computer_statement = $db->prepare($sql);
                $sql = 'update `techdata_products` i set `peripheralId`=(select id from ext_dealer_hardware where id in (select id from ext_peripheral) and oemSku=i.`ManufPartNo` limit 1) where Matnr=:Matnr';
                $peripheral_statement = $db->prepare($sql);

                $sql = "update `techdata_products` i set `tonerId`=(select id from toners where `manufacturerId` in (select manufacturerId from master_devices) and i.`ManufPartNo` like concat(sku,'#%') limit 1) where tonerId is null and Matnr=:Matnr";
                $hp_toner_statement = $db->prepare($sql);
                $sql = "update `techdata_products` i set `masterDeviceId`=(select masterDeviceId from devices where i.`ManufPartNo` like concat(oemSku,'#%') limit 1) where masterDeviceId is null and Matnr=:Matnr";
                $hp_device_statement = $db->prepare($sql);
            }

            if ($line['ProdSubClass']=='Printer Consumables') { //Printer Consumables
                $toner_statement->execute(['Matnr'=>$line['Matnr']]);
                $hp_toner_statement->execute(['Matnr'=>$line['Matnr']]);
            } else if (($line['ProdSubClass']=='Multifunction Office Equipment') || ($line['ProdSubClass']=='Laser Printers (Mono)') || ($line['ProdSubClass']=='Laser Printers (Color)')) {
                $masterDevice_statement->execute(['Matnr'=>$line['Matnr']]);
                $hp_device_statement->execute(['Matnr'=>$line['Matnr']]);
            } else {
                $computer_statement->execute(['Matnr'=>$line['Matnr']]);
                $peripheral_statement->execute(['Matnr'=>$line['Matnr']]);
            }
/**/
        }
        fclose($fp);

        $columns = [
            'Matnr',
            'CustBestPrice',
            'Promotion',
        ];
        $filename=dirname($zip_filename).'/'.$price_file;
        echo "{$filename}<br>\n";
        $fp = fopen($filename, 'rb');

        $hdr = fgetcsv($fp, null, "\t");
        while ($line = fgetcsv($fp, null, "\t")) {
            while (count($line) > count($columns)) {
                array_pop($line);
            }
            $line = array_combine($columns, $line);

            if (!$price_statement) {
                $sql = 'replace into techdata_prices set dealerId=?, Matnr=?, CustBestPrice=?, Promotion=?';
                $price_statement = $db->prepare($sql);
            }

            $price_statement->execute([
                $dealerSupplier['dealerId'],
                $line['Matnr'],
                $line['CustBestPrice'],
                $line['Promotion'],
            ]);

        }
        fclose($filename);



        echo 'done! '.time();
        return true;


    }

    private function updateSynnex($dealerSupplier)
    {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/cache/' . sprintf('synnex-%s.zip', $dealerSupplier['dealerId']);
        try {
            if (!file_exists($zip_filename) || (filemtime($zip_filename) < strtotime('-6 DAY'))) {
                $ftp = $this->getFtpClient();
                $ftp->get($dealerSupplier['url'], $dealerSupplier['user'], $dealerSupplier['pass'], $zip_filename);
            }

            $zip = $this->getZipAdapter();
            $zip->open($zip_filename);
            $finfo = $zip->statIndex(0);
            $txt_filename = $finfo['name'];
            $zip->extractTo(dirname($zip_filename));
            $zip->close();

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
        echo "processing {$filename} for dealer {$dealerSupplier['dealerId']}\n";
        $fp = fopen($filename, 'rb');

        $sql=['Qty_on_Hand=0'];
        for ($i=1;$i<=16;$i++) $sql[]="`Warehouse_Qty_on_Hand_{$i}`=0";
        $db->query('update synnex_products set '.implode(',', $sql));

        $update_products_statement = false;
        $insert_products_statement = false;
        $update_price_statement = false;
        $insert_price_statement = false;
        $toner_statement = false;
        $masterDevice_statement = false;
        $computer_statement = false;
        $peripheral_statement = false;
        $weight_statement = false;
        $upc_statement = false;

        echo "1. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $exists = [];
        $cursor = $db->query('select SYNNEX_SKU, _md5 from synnex_products');
        while($line = $cursor->fetch()) {
            $exists[$line['SYNNEX_SKU']] = $line['_md5'] ? $line['_md5'] : 'x';
        }
        $cursor->closeCursor();
        $cursor = null;
        gc_collect_cycles();

        echo "exists_products: ".count($exists)."\n";

        echo "2. ".round(memory_get_usage()/(1024*1024))." MB\n";

        try { $db->query('ALTER TABLE synnex_prices DROP foreign key synnex_prices_ibfk_1'); } catch (\Exception $ex) {}
        try { $db->query('ALTER TABLE synnex_prices DROP foreign key synnex_prices_ibfk_2'); } catch (\Exception $ex) {}
        try { $db->query('ALTER TABLE synnex_prices DROP INDEX SYNNEX_SKU'); } catch (\Exception $ex) {}
        try { $db->query('ALTER TABLE synnex_prices DROP PRIMARY KEY'); } catch (\Exception $ex) {}

        try { $db->query('ALTER TABLE synnex_products DROP INDEX tonerId'); } catch (\Exception $ex) {}
        try { $db->query('ALTER TABLE synnex_products DROP INDEX peripheralId'); } catch (\Exception $ex) {}
        try { $db->query('ALTER TABLE synnex_products DROP INDEX computerId'); } catch (\Exception $ex) {}
        try { $db->query('ALTER TABLE synnex_products DROP INDEX masterDeviceId'); } catch (\Exception $ex) {}

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

            $price_data = [
                'Contract_Price' => $line['Contract_Price'],
                'Unit_Cost' => $line['Unit_Cost'],
                'Promotion_Flag' => $line['Promotion_Flag'],
                'Promotion_Comment' => $line['Promotion_Comment'],
                'Promotion_Expiration_Date' => $line['Promotion_Expiration_Date'],
                'dealerId' => $dealerSupplier['dealerId'],
                'SYNNEX_SKU' => $line['SYNNEX_SKU'],
            ];

            foreach (['Contract_Price', 'Unit_Cost', 'Promotion_Flag', 'Promotion_Comment', 'Promotion_Expiration_Date'] as $key) {
                unset($line[$key]);
            }

            $_md5 = md5(implode(',', array_values($line)));
            $line['_md5'] = $_md5;

            if (!$update_products_statement) {
                $sql = [];
                foreach ($line as $key => $value) if ($key != 'SYNNEX_SKU') $sql[] = "`$key`=:{$key}";
                $update_products_statement = $db->prepare('UPDATE synnex_products SET ' . implode(', ', $sql) . ' WHERE SYNNEX_SKU=:SYNNEX_SKU');
            }
            if (!$insert_products_statement) {
                $sql = [];
                foreach ($line as $key => $value) $sql[] = "`$key`=:{$key}";
                $insert_products_statement = $db->prepare('INSERT INTO synnex_products SET ' . implode(', ', $sql));
            }

            if (isset($exists[$line['SYNNEX_SKU']])) {
                if ($exists[$line['SYNNEX_SKU']] != $_md5) {
                    $update_products_statement->execute($line);
                }
                unset($exists[$line['SYNNEX_SKU']]);
            } else {
                $insert_products_statement->execute($line);
            }

        }

        #==
        $delete_product_statement = $db->prepare('delete from synnex_products where SYNNEX_SKU=?');
        $delete_price_statement = $db->prepare('delete from synnex_products where SYNNEX_SKU=?');
        foreach ($exists as $id=>$hash) {
            $delete_product_statement->execute([$id]);
            $delete_price_statement->execute([$id]);
        }
        echo count($exists)." products deleted\n";
        #==


        echo "3. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $exists = [];
        $cursor = $db->query('select SYNNEX_SKU, _md5 from synnex_prices where dealerId='.intval($dealerSupplier['dealerId']));
        while($line = $cursor->fetch()) {
            $exists[$line['SYNNEX_SKU']] = $line['_md5'] ? $line['_md5'] : 'x';
        }
        $cursor->closeCursor();
        $cursor = null;
        gc_collect_cycles();

        echo "exists_prices {$dealerSupplier['dealerId']}: ".count($exists)."\n";

        echo "4. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $skus = [];

        fseek($fp, 0);
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

            $price_data = [
                'Contract_Price' => $line['Contract_Price'],
                'Unit_Cost' => $line['Unit_Cost'],
                'Promotion_Flag' => $line['Promotion_Flag'],
                'Promotion_Comment' => $line['Promotion_Comment'],
                'Promotion_Expiration_Date' => $line['Promotion_Expiration_Date'],
                'dealerId' => $dealerSupplier['dealerId'],
                'SYNNEX_SKU' => $line['SYNNEX_SKU'],
            ];

            if (!$update_price_statement) {
                $sql = 'UPDATE synnex_prices SET
                  `Contract_Price`=:Contract_Price,
                  `Unit_Cost`=:Unit_Cost,
                  `Promotion_Flag`=:Promotion_Flag,
                  `Promotion_Comment`=:Promotion_Comment,
                  `Promotion_Expiration_Date`=:Promotion_Expiration_Date,
                  `_md5`=:_md5
                 WHERE
                  `SYNNEX_SKU`=:SYNNEX_SKU AND dealerId=:dealerId';
                $update_price_statement = $db->prepare($sql);
            }
            if (!$insert_price_statement) {
                $sql = 'INSERT INTO synnex_prices SET
                  `Contract_Price`=:Contract_Price,
                  `Unit_Cost`=:Unit_Cost,
                  `Promotion_Flag`=:Promotion_Flag,
                  `Promotion_Comment`=:Promotion_Comment,
                  `Promotion_Expiration_Date`=:Promotion_Expiration_Date,
                  `_md5`=:_md5,
                  `SYNNEX_SKU`=:SYNNEX_SKU,
                  dealerId=:dealerId';
                $insert_price_statement = $db->prepare($sql);
            }

            $_md5 = md5(implode(',', array_values($price_data)));
            $price_data['_md5'] = $_md5;

            if (isset($exists[$line['SYNNEX_SKU']])) {
                if ($exists[$line['SYNNEX_SKU']] != $_md5) {
                    $update_price_statement->execute($price_data);
                }
            } else {
                $insert_price_statement->execute($price_data);
            }

            $sku = $line['Manufacturer_Part'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            $cat = substr($line['SYNNEX_CAT_Code'], 0, 6);
            $skus[$cat][$sku] = $line['SYNNEX_SKU'];

            /**
             * if (!$toner_statement) {
             * $sql = "update `synnex_products` set tonerId=(select id from base_product where manufacturerId in (select manufacturerId from oem_manufacturers) and (base_type='printer_cartridge' or base_type='printer_consumable') and sku=:sku) where SYNNEX_SKU=:SYNNEX_SKU";
             * $toner_statement = $db->prepare($sql);
             * $sql = "update `synnex_products` set `masterDeviceId`=(select masterDeviceId from devices where oemSku=:sku or oemSku like concat(:sku,'#%') limit 1) where SYNNEX_SKU=:SYNNEX_SKU";
             * $masterDevice_statement = $db->prepare($sql);
             * $sql = 'update `synnex_products` i set `computerId`=(select id from ext_dealer_hardware where id in (select id from ext_computer) and oemSku=i.`Manufacturer_Part` limit 1) where SYNNEX_SKU=:SYNNEX_SKU';
             * $computer_statement = $db->prepare($sql);
             * $sql = 'update `synnex_products` i set `peripheralId`=(select id from ext_dealer_hardware where id in (select id from ext_peripheral) and oemSku=i.`Manufacturer_Part` limit 1) where SYNNEX_SKU=:SYNNEX_SKU';
             * $peripheral_statement = $db->prepare($sql);
             *
             * $sql = 'update base_product t set weight=:weight where id=(select tonerId from `synnex_products` where SYNNEX_SKU=:SYNNEX_SKU)';
             * $weight_statement = $db->prepare($sql);
             * $sql = 'update base_product t set upc=:upc where id=(select tonerId from `synnex_products` where SYNNEX_SKU=:SYNNEX_SKU)';
             * $upc_statement = $db->prepare($sql);
             * }
             *
             * if (!empty($product_action)) {
             * $sku = $line['Manufacturer_Part'];
             * if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
             * $sku = $match[1];
             * }
             * if (substr($line['SYNNEX_CAT_Code'], 0, 6) == '009088') { //Printer Consumables
             * $toner_statement->execute(['SYNNEX_SKU' => $line['SYNNEX_SKU'], 'sku'=>$sku]);
             * if ($toner_statement->rowCount() > 0) {
             * $weight_statement->execute(['SYNNEX_SKU' => $line['SYNNEX_SKU'], 'weight' => 0.453592 * floatval($line['Ship_Weight'])]);
             * $upc_statement->execute(['SYNNEX_SKU' => $line['SYNNEX_SKU'], 'upc' => $line['UPC_Code']]);
             * }
             * } else if ((substr($line['SYNNEX_CAT_Code'], 0, 6) == '009053') || (substr($line['SYNNEX_CAT_Code'], 0, 6) == '009058') || (substr($line['SYNNEX_CAT_Code'], 0, 6) == '009059')) {
             * $masterDevice_statement->execute(['SYNNEX_SKU' => $line['SYNNEX_SKU'],'sku'=>$sku]);
             * } else {
             * $computer_statement->execute(['SYNNEX_SKU' => $line['SYNNEX_SKU']]);
             * $peripheral_statement->execute(['SYNNEX_SKU' => $line['SYNNEX_SKU']]);
             * }
             * }
             **/
        }

        echo "5. ".round(memory_get_usage()/(1024*1024))." MB\n";

        $toner_statement = $db->prepare('update synnex_products set tonerId=? where SYNNEX_SKU=?');
        $weight_statement = $db->prepare('update base_product set weight=(select 0.453592 * Ship_Weight from synnex_products where SYNNEX_SKU=?) where id=?');
        $upc_statement = $db->prepare('update base_product set upc=(select UPC_Code from synnex_products where SYNNEX_SKU=?) where id=?');
        foreach($db->query("select base_product.id, sku, weight, upc from base_product join base_printer_consumable using (id)")->fetchAll() as $line) {
            $sku = $line['sku'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($skus['009088'][$sku])) {
                $toner_statement->execute([$line['id'], $skus['009088'][$sku]]);
                if (empty($line['weight'])) $weight_statement->execute([$skus['009088'][$sku], $line['id']]);
                if (empty($line['upc'])) $upc_statement->execute([$skus['009088'][$sku], $line['id']]);
            }
        }

        $device_statement = $db->prepare('update synnex_products set masterDeviceId=? where SYNNEX_SKU=?');
        $sku_statement = $db->prepare('update base_product set sku=? where id=?');
        foreach($db->query('select base_product.id, oemSku, sku from base_product join devices on base_product.id=devices.masterDeviceId and devices.oemSku is not null group by base_product.id') as $line) {
            $sku = $line['sku'] ? $line['sku'] : $line['oemSku'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($skus['009053'][$sku])) {
                $device_statement->execute([$line['id'], $skus['009053'][$sku]]);
                if (empty($line['sku'])) {
                    $sku_statement->execute([$sku, $line['id']]);
                }
            }
        }

        echo "6. ".round(memory_get_usage()/(1024*1024))." MB\n";

        echo 'done! '.time();
        return true;
    }

    private function updateIngram($dealerSupplier) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/cache/'.sprintf('ingram-%s.zip', $dealerSupplier['dealerId']);
        try {
            if (!file_exists($zip_filename) || (filemtime($zip_filename)<strtotime('-6 DAY'))) {
                $ftp = $this->getFtpClient();
                $ftp->get("{$dealerSupplier['url']}/PRICE.ZIP", $dealerSupplier['user'], $dealerSupplier['pass'], $zip_filename);
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
        $fp = fopen(dirname($zip_filename).'/PRICE.TXT', 'rb');

        $db->query("update ingram_products set `status`='*', `AVAILABILITY_FLAG`='N'");

        $insert_product_statement = false;
        $update_product_statement = false;
        $insert_price_statement = false;
        $update_price_statement = false;
        $toner_statement = false;
        $hp_toner_statement = false;
        $masterDevice_statement = false;
        $hp_device_statement = false;
        $computer_statement = false;
        $peripheral_statement = false;
        $weight_statement = false;
        $upc_statement = false;

        $exists_products = [];
        $cursor = $db->query('select INGRAM_PART_NUMBER, _md5 from ingram_products');
        while($line = $cursor->fetch()) {
            $exists_products[$line['INGRAM_PART_NUMBER']] = $line['_md5'] ? $line['_md5'] : 'x';
        }
        echo "exists_products: ".count($exists_products)."\n";

        $exists_prices = [];
        $cursor = $db->query('select INGRAM_PART_NUMBER, _md5 from ingram_prices where dealerId='.intval($dealerSupplier['dealerId']));
        while($line = $cursor->fetch()) {
            $exists_prices[$line['INGRAM_PART_NUMBER']] = $line['_md5'] ? $line['_md5'] : 'x';
        }
        echo "exists_prices {$dealerSupplier['dealerId']}: ".count($exists_prices)."\n";

        $skus = [];
        while ($line = fgetcsv($fp)) {
            $line = array_combine($columns, $line);

            if (
                ($line['INGRAM_MICRO_CATEGORY'] != '1010') &&
                ($line['INGRAM_MICRO_CATEGORY'] != '0701') &&
                ($line['INGRAM_MICRO_CATEGORY'] != '0733')
            ) continue;

            foreach ($line as $k=>$v) $line[$k] = trim($v);

            switch ($line['ACTION_INDICATOR']) {
                case 'A':
                case 'C':
                {
                    $price_data =false;
                    if (($line['ACTION_INDICATOR']=='A') || ($line['PRICE_CHANGE_FLAG']=='Y')) {
                        $price_data = [
                            'CUSTOMER_PRICE' => $line['CUSTOMER_PRICE'],
                            'SPECIAL_PRICE_FLAG' => $line['SPECIAL_PRICE_FLAG'],
                            'INGRAM_PART_NUMBER' => $line['INGRAM_PART_NUMBER'],
                            'dealerId' => $dealerSupplier['dealerId'],
                        ];
                    }

                    foreach (['ACTION_INDICATOR', 'PRICE_CHANGE_FLAG', 'CUSTOMER_PRICE', 'SPECIAL_PRICE_FLAG'] as $key) {
                        unset($line[$key]);
                    }

                    $_md5 = md5(implode(',',array_values($line)));
                    $line['_md5'] = $_md5;

                    if (!$update_product_statement) {
                        $sql = [];
                        foreach ($line as $key => $value) if ($key!='INGRAM_PART_NUMBER') {
                            $sql[] = "`$key`=:{$key}";
                        }
                        $sql = 'update ingram_products SET ' . implode(', ', $sql).' where INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER';
                        $update_product_statement = $db->prepare($sql);
                    }
                    if (!$insert_product_statement) {
                        $sql = [];
                        foreach ($line as $key => $value) {
                            $sql[] = "`$key`=:{$key}";
                        }
                        $sql = 'insert INTO ingram_products SET ' . implode(', ', $sql);
                        $insert_product_statement = $db->prepare($sql);
                    }

                    if (isset($exists_products[$line['INGRAM_PART_NUMBER']])) {
                        if ($exists_products[$line['INGRAM_PART_NUMBER']] != $_md5) {
                            $update_product_statement->execute($line);
                        }
                        unset($exists_products[$line['INGRAM_PART_NUMBER']]);
                    } else {
                        $insert_product_statement->execute($line);
                    }

                    #---

                    $_md5 = md5(implode('',array_values($price_data)));
                    $price_data['_md5'] = $_md5;

                    if (!$insert_price_statement) {
                        $sql = 'insert INTO ingram_prices SET CUSTOMER_PRICE=:CUSTOMER_PRICE, SPECIAL_PRICE_FLAG=:SPECIAL_PRICE_FLAG, INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER, dealerId=:dealerId, _md5=:_md5';
                        $insert_price_statement = $db->prepare($sql);
                    }
                    if (!$update_price_statement) {
                        $sql = 'update ingram_prices SET CUSTOMER_PRICE=:CUSTOMER_PRICE, SPECIAL_PRICE_FLAG=:SPECIAL_PRICE_FLAG, _md5=:_md5 where INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER and dealerId=:dealerId';
                        $update_price_statement = $db->prepare($sql);
                    }

                    if (isset($exists_prices[$line['INGRAM_PART_NUMBER']])) {
                        if ($exists_prices[$line['INGRAM_PART_NUMBER']] != $_md5) {
                            $update_price_statement->execute($price_data);
                        }
                    } else {
                        $insert_price_statement->execute($price_data);
                    }

                    $sku = $line['VENDOR_PART_NUMBER'];
                    if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                        $sku = $match[1];
                    }
                    $skus[$line['INGRAM_MICRO_CATEGORY']][$sku] = $line['INGRAM_PART_NUMBER'];

                    /**
                    if (!$toner_statement) {
                        $sql = 'update `ingram_products` i set `tonerId`=(select id from toners where `manufacturerId` in (select manufacturerId from master_devices) and sku=i.`vendor_part_number` limit 1) where tonerId is null and INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER';
                        $toner_statement = $db->prepare($sql);
                        $sql = "update `ingram_products` i set `tonerId`=(select id from toners where `manufacturerId` in (select manufacturerId from master_devices) and i.`vendor_part_number` like concat(sku,'#%') limit 1) where tonerId is null and INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER";
                        $hp_toner_statement = $db->prepare($sql);
                        $sql = 'update `ingram_products` i set `masterDeviceId`=(select masterDeviceId from devices where oemSku=i.`vendor_part_number` limit 1) where INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER';
                        $masterDevice_statement = $db->prepare($sql);
                        $sql = "update `ingram_products` i set `masterDeviceId`=(select masterDeviceId from devices where i.`vendor_part_number` like concat(oemSku,'#%') limit 1) where masterDeviceId is null and INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER";
                        $hp_device_statement = $db->prepare($sql);
                        $sql = 'update `ingram_products` i set `computerId`=(select id from ext_dealer_hardware where id in (select id from ext_computer) and oemSku=i.`vendor_part_number` limit 1) where INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER';
                        $computer_statement = $db->prepare($sql);
                        $sql = 'update `ingram_products` i set `peripheralId`=(select id from ext_dealer_hardware where id in (select id from ext_peripheral) and oemSku=i.`vendor_part_number` limit 1) where INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER';
                        $peripheral_statement = $db->prepare($sql);
                        $sql = 'update toners t set weight = 0.453592 * (select weight from ingram_products where tonerId=t.id limit 1) where weight is null and id=(select tonerId from `ingram_products` where INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER)';
                        $weight_statement = $db->prepare($sql);
                        $sql = 'update toners t set upc = (select upc_code from ingram_products where tonerId=t.id limit 1) where upc is null and id=(select tonerId from `ingram_products` where INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER)';
                        $upc_statement = $db->prepare($sql);
                    }

                    if ($line['INGRAM_MICRO_CATEGORY']=='1010') { //toners
                        $toner_statement->execute(['INGRAM_PART_NUMBER'=>$line['INGRAM_PART_NUMBER']]);
                        $hp_toner_statement->execute(['INGRAM_PART_NUMBER'=>$line['INGRAM_PART_NUMBER']]);
                        $weight_statement->execute(['INGRAM_PART_NUMBER'=>$line['INGRAM_PART_NUMBER']]);
                        $upc_statement->execute(['INGRAM_PART_NUMBER'=>$line['INGRAM_PART_NUMBER']]);
                    } else if (($line['INGRAM_MICRO_CATEGORY']=='0701') || ($line['INGRAM_MICRO_CATEGORY']=='0733')) {
                        $masterDevice_statement->execute(['INGRAM_PART_NUMBER'=>$line['INGRAM_PART_NUMBER']]);
                        $hp_device_statement->execute(['INGRAM_PART_NUMBER'=>$line['INGRAM_PART_NUMBER']]);
                    } else {
                        $computer_statement->execute(['INGRAM_PART_NUMBER'=>$line['INGRAM_PART_NUMBER']]);
                        $peripheral_statement->execute(['INGRAM_PART_NUMBER'=>$line['INGRAM_PART_NUMBER']]);
                    }
                    **/

                    break;
                }

                case 'D':
                {
                    $db->prepare('DELETE FROM ingram_products WHERE INGRAM_PART_NUMBER=:pn')->execute(['pn'=>$line['INGRAM_PART_NUMBER']]);
                    break;
                }
            }
        }

        $delete_product_statement = $db->prepare('delete from ingram_products where ingram_part_number=?');
        $delete_price_statement = $db->prepare('delete from ingram_prices where ingram_part_number=?');
        foreach ($exists_products as $id=>$hash) {
            $delete_product_statement->execute([$id]);
            $delete_price_statement->execute([$id]);
        }

        $toner_statement = $db->prepare('update ingram_products set tonerId=? where INGRAM_PART_NUMBER=?');
        $weight_statement = $db->prepare('update base_product set weight=(select 0.453592 * WEIGHT from ingram_products where INGRAM_PART_NUMBER=?) where id=?');
        $upc_statement = $db->prepare('update base_product set upc=(select UPC_CODE from ingram_products where INGRAM_PART_NUMBER=?) where id=?');
        foreach($db->query("select base_product.id, sku, weight, upc from base_product join base_printer_consumable using (id)")->fetchAll() as $line) {
            $sku = $line['sku'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($skus['1010'][$sku])) {
                $toner_statement->execute([$line['id'], $skus['1010'][$sku]]);
                if (empty($line['weight'])) $weight_statement->execute([$skus['1010'][$sku], $line['id']]);
                if (empty($line['upc'])) $upc_statement->execute([$skus['1010'][$sku], $line['id']]);
            }
        }

        $device_statement = $db->prepare('update ingram_products set masterDeviceId=? where INGRAM_PART_NUMBER=?');
        $sku_statement = $db->prepare('update base_product set sku=? where id=?');
        foreach($db->query('select base_product.id, oemSku, sku from base_product join devices on base_product.id=devices.masterDeviceId and devices.oemSku is not null group by base_product.id') as $line) {
            $sku = $line['sku'] ? $line['sku'] : $line['oemSku'];
            if (preg_match('/^(.+)[#\/]\w\w\w$/', $sku, $match)) {
                $sku = $match[1];
            }
            if (isset($skus['0701'][$sku])) {
                $device_statement->execute([$line['id'], $skus['0701'][$sku]]);
                if (empty($line['sku'])) $sku_statement->execute([$sku, $line['id']]);
            }
            if (isset($skus['0733'][$sku])) {
                $device_statement->execute([$line['id'], $skus['0733'][$sku]]);
                if (empty($line['sku'])) $sku_statement->execute([$sku, $line['id']]);
            }
        }

        echo 'done! '.time();
        return true;
    }

}