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
     * @return \Zend_Filter_Compress_Zip
     */
    public function getZipAdapter()
    {
        if (!$this->zipAdapter) {
            $this->zipAdapter = new \Zend_Filter_Compress_Zip();
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
            }
            case self::SUPPLIER_SYNNEX : {
                $this->updateSynnex($dealerSupplier);
            }
        }
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
            $zip->setTarget(dirname($zip_filename));
            $zip->decompress($zip_filename);

        } catch (\Exception $ex) {
            print_r($ex);
            Logger::logException($ex);
            return false;
        }

        $columns = [];
        $fp = fopen(dirname($zip_filename).'/'.str_ireplace('.zip','.ap',basename($dealerSupplier['url'])), 'rb');
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

        $hdr = fgetcsv($fp, null, '~');

        while ($line = fgetcsv($fp, null, '~')) {
            $line = array_combine($columns, $line);

            #$price_data =false;
            #if (($line['ACTION_INDICATOR']=='A') || ($line['PRICE_CHANGE_FLAG']=='Y')) {
                $price_data = [
                    #'CUSTOMER_PRICE' => $line['CUSTOMER_PRICE'],
                    #'SPECIAL_PRICE_FLAG' => $line['SPECIAL_PRICE_FLAG'],
                    #'INGRAM_PART_NUMBER' => $line['INGRAM_PART_NUMBER'],
                    'dealerId' => $dealerSupplier['dealerId'],
                ];
            #}

            foreach (['ACTION_INDICATOR', 'PRICE_CHANGE_FLAG', 'CUSTOMER_PRICE', 'SPECIAL_PRICE_FLAG'] as $key) {
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
                $sql = 'REPLACE INTO synnex_prices SET CUSTOMER_PRICE=:CUSTOMER_PRICE, SPECIAL_PRICE_FLAG=:SPECIAL_PRICE_FLAG, INGRAM_PART_NUMBER=:INGRAM_PART_NUMBER, dealerId=:dealerId';
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
            $zip->setTarget(dirname($zip_filename));
            $zip->decompress($zip_filename);

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