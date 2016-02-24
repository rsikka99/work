<?php

namespace MPSToolbox\Services;

use Tangent\Ftp\NcFtp;
use Tangent\Logger\Logger;

class DistributorUpdateService {

    const SUPPLIER_INGRAM  = 1;
    const SUPPLIER_SYNNEX  = 2;

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
        }
    }

    private function updateSynnex($dealerSupplier)
    {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $zip_filename = APPLICATION_BASE_PATH . '/data/cache/' . sprintf('synnex-%s.zip', $dealerSupplier['dealerId']);
        $txt_filename = '';
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
        echo $filename;
        $fp = fopen($filename, 'rb');

        $sql=['Qty_on_Hand=0'];
        for ($i=1;$i<=16;$i++) $sql[]="`Warehouse_Qty_on_Hand_{$i}`=0";
        $db->query('update synnex_products set '.implode($sql));

        $replace_statement = false;
        $price_statement = false;
        $toner_statement = false;
        $masterDevice_statement = false;
        $computer_statement = false;
        $peripheral_statement = false;
        $weight_statement = false;
        $upc_statement = false;

        $hdr = fgetcsv($fp, null, '~');

        while ($line = fgetcsv($fp, null, '~')) {
            while (count($line)>count($columns)) {
                array_pop($line);
            }
            $line = array_combine($columns, $line);

            $price_data = [
                'Contract_Price' => $line['Contract_Price'],
                'Unit_Cost' => $line['Unit_Cost'],
                'Promotion_Flag' => $line['Promotion_Flag'],
                'Promotion_Comment' => $line['Promotion_Comment'],
                'Promotion_Expiration_Date' => $line['Promotion_Expiration_Date'],
                'dealerId' => $dealerSupplier['dealerId'],
                'SYNNEX_SKU' => $line['SYNNEX_SKU'],
            ];

            foreach (['Contract_Price', 'Unit_Cost', 'Promotion_Flag', 'Promotion_Comment','Promotion_Expiration_Date'] as $key) {
                unset($line[$key]);
            }

            if (!$replace_statement) {
                $sql = [];
                foreach ($line as $key => $value) {
                    $sql[] = "`$key`=:{$key}";
                }
                $sql = 'REPLACE INTO synnex_products SET ' . implode(', ', $sql);
                $replace_statement = $db->prepare($sql);
            }
            $replace_statement->execute($line);

            if (!$price_statement) {
                $sql = 'REPLACE INTO synnex_prices SET
                  `Contract_Price`=:Contract_Price,
                  `Unit_Cost`=:Unit_Cost,
                  `Promotion_Flag`=:Promotion_Flag,
                  `Promotion_Comment`=:Promotion_Comment,
                  `Promotion_Expiration_Date`=:Promotion_Expiration_Date,
                  `SYNNEX_SKU`=:SYNNEX_SKU,
                  dealerId=:dealerId';
                $price_statement = $db->prepare($sql);
            }

            if ($price_data) {
                $price_statement->execute($price_data);
            }

            if (!$toner_statement) {
                $sql = 'update `synnex_products` i set `tonerId`=(select id from toners where `manufacturerId` in (select manufacturerId from master_devices) and sku=i.`Manufacturer_Part` limit 1) where tonerId is null and SYNNEX_SKU=:SYNNEX_SKU';
                $toner_statement = $db->prepare($sql);
                $sql = 'update `synnex_products` i set `masterDeviceId`=(select masterDeviceId from devices where oemSku=i.`Manufacturer_Part` limit 1) where SYNNEX_SKU=:SYNNEX_SKU';
                $masterDevice_statement = $db->prepare($sql);
                $sql = 'update `synnex_products` i set `computerId`=(select id from ext_dealer_hardware where id in (select id from ext_computer) and oemSku=i.`Manufacturer_Part` limit 1) where SYNNEX_SKU=:SYNNEX_SKU';
                $computer_statement = $db->prepare($sql);
                $sql = 'update `synnex_products` i set `peripheralId`=(select id from ext_dealer_hardware where id in (select id from ext_peripheral) and oemSku=i.`Manufacturer_Part` limit 1) where SYNNEX_SKU=:SYNNEX_SKU';
                $peripheral_statement = $db->prepare($sql);
                $sql = 'update toners t set weight = 0.453592 * (select weight from synnex_products where tonerId=t.id limit 1) where weight is null and id=(select tonerId from `synnex_products` where SYNNEX_SKU=:SYNNEX_SKU)';
                $weight_statement = $db->prepare($sql);
                $sql = 'update toners t set upc = (select upc_code from synnex_products where tonerId=t.id limit 1) where upc is null and id=(select tonerId from `synnex_products` where SYNNEX_SKU=:SYNNEX_SKU)';
                $upc_statement = $db->prepare($sql);
            }

            if (substr($line['SYNNEX_CAT_Code'],0,6)=='009088') { //Printer Consumables
                $toner_statement->execute(['SYNNEX_SKU'=>$line['SYNNEX_SKU']]);
                $weight_statement->execute(['SYNNEX_SKU'=>$line['SYNNEX_SKU']]);
                $upc_statement->execute(['SYNNEX_SKU'=>$line['SYNNEX_SKU']]);
            } else if ((substr($line['SYNNEX_CAT_Code'],0,6)=='009053') || (substr($line['SYNNEX_CAT_Code'],0,6)=='009058') || (substr($line['SYNNEX_CAT_Code'],0,6)=='009059')) {
                $masterDevice_statement->execute(['SYNNEX_SKU'=>$line['SYNNEX_SKU']]);
            } else {
                $computer_statement->execute(['SYNNEX_SKU'=>$line['SYNNEX_SKU']]);
                $peripheral_statement->execute(['SYNNEX_SKU'=>$line['SYNNEX_SKU']]);
            }
        }
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

        $replace_statement = false;
        $price_statement = false;
        $toner_statement = false;
        $hp_toner_statement = false;
        $masterDevice_statement = false;
        $hp_device_statement = false;
        $computer_statement = false;
        $peripheral_statement = false;
        $weight_statement = false;
        $upc_statement = false;


        while ($line = fgetcsv($fp)) {
            $line = array_combine($columns, $line);

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
                    if (!$replace_statement) {
                        $sql = [];
                        foreach ($line as $key => $value) {
                            $sql[] = "`$key`=:{$key}";
                        }
                        $sql = 'REPLACE INTO ingram_products SET ' . implode(', ', $sql);
                        $replace_statement = $db->prepare($sql);
                    }
                    $replace_statement->execute($line);

                    if (!$price_statement) {
                        $sql = 'REPLACE INTO ingram_prices SET CUSTOMER_PRICE=:CUSTOMER_PRICE, SPECIAL_PRICE_FLAG=:SPECIAL_PRICE_FLAG, INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER, dealerId=:dealerId';
                        $price_statement = $db->prepare($sql);
                    }

                    if ($price_data) {
                        $price_statement->execute($price_data);
                    }

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

                    break;
                }

                case 'D':
                {
                    $db->prepare('DELETE FROM ingram_products WHERE INGRAM_PART_NUMBER=:pn')->execute(['pn'=>$line['INGRAM_PART_NUMBER']]);
                    break;
                }
            }

        }
        echo 'done! '.time();
        return true;
    }

}