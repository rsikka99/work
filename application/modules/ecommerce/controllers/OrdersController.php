<?php

use Tangent\Controller\Action;

class Order extends \ActiveResource\Base {}

class Ecommerce_OrdersController extends Action {

    private function shopifyConnect() {
        $settings = \MPSToolbox\Settings\Entities\DealerSettingsEntity::getDealerSettings();
        if (empty($settings->shopSettings->shopifyName)) return false;

        /** @var Zend_Db_Adapter_Pdo_Mysql $db */
        $db = Zend_Db_Table::getDefaultAdapter();

        try {
            $str = file_get_contents(APPLICATION_PATH.'/configs/shopify.json');
            $config = json_decode($str);
            $config->shopname = $settings->shopSettings->shopifyName;
            $storage = new \cdyweb\Shopify\OAuth\PDOTokenStorage($db->getConnection(), 'shopify_');
            $shopifyClient = new \cdyweb\Shopify\Shopify($config, $storage);
            \ActiveResource\Base::setDefaultConnection($shopifyClient->getConnection());
        } catch (\Exception $ex) {
            var_dump($ex);
            return false;
        }
        return true;
    }

    public function indexAction() {
        $dealerId = \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId();
        if ($this->getRequest()->getMethod()=='POST') {
            $result = ['number'=>'','general'=>[], 'customer'=>[], 'products'=>[]];
            $id = intval($this->getRequest()->getParam('id'));
            if ($id && $this->shopifyConnect()) {
                $order = Order::find($id);
                if ($order) {
                    $result['number'] = $order->order_number;
                    $result['general'][] = ['Order number', $order->order_number];
                    $result['general'][] = ['Order date', date('Y-m-d H:i',strtotime($order->created_at))];
                    $result['general'][] = ['Customer name', $order->customer['last_name'].', '.$order->customer['first_name']];
                    $result['general'][] = ['Subtotal', $order->subtotal_price];
                    $result['general'][] = ['Status', $order->financial_status];

                    $result['customer'][] = ['Register date', $order->customer['created_at']];
                    $result['customer'][] = ['Number of orders', $order->customer['orders_count']];
                    $result['customer'][] = ['First name', $order->customer['first_name']];
                    $result['customer'][] = ['Last name', $order->customer['last_name']];
                    $result['customer'][] = ['Company', $order->shipping_address['company']];
                    $result['customer'][] = ['Email', "<a href='{$order->customer['email']}'>{$order->customer['email']}</a>"];
                    $result['customer'][] = ['Phone', $order->shipping_address['phone']];
                    $result['customer'][] = ['&nbsp;','&nbsp;'];
                    $result['customer'][] = ['Shipping address', trim($order->shipping_address['address1'].', '.$order->shipping_address['address2'],', ')];
                    $result['customer'][] = ['City', $order->shipping_address['city']];
                    $result['customer'][] = ['Region', $order->shipping_address['province']];
                    $result['customer'][] = ['Country', $order->shipping_address['country']];
                    $result['customer'][] = ['Postal code', $order->shipping_address['zip']];

                    $db = Zend_Db_Table::getDefaultAdapter();

                    foreach ($order->line_items as $item) {

                        $tonerId = $db->query('select tonerId from dealer_toner_attributes where webId='.$item['id'])->fetchColumn(0);
                        $masterDeviceId = $db->query('select masterDeviceId from devices where webId='.$item['id'])->fetchColumn(0);
                        $computerId = $db->query('select ext_hardware.id from ext_hardware join ext_computer on ext_hardware.id = ext_computer.id join ext_dealer_hardware on ext_hardware.id=ext_dealer_hardware.id and webId='.$item['id'])->fetchColumn(0);
                        $peripheralId = $db->query('select ext_hardware.id from ext_hardware join ext_peripheral on ext_hardware.id = ext_peripheral.id join ext_dealer_hardware on ext_hardware.id=ext_dealer_hardware.id and webId='.$item['id'])->fetchColumn(0);

                        $ingram = null;
                        $synnex = null;
                        $techdata = null;
                        if ($tonerId) {
                            $ingram = $db->query("select 'Ingram' as distributor, ingram_products.ingram_part_number as partnumber, availability_flag as available, customer_price as price from ingram_products join ingram_prices on ingram_products.ingram_part_number = ingram_prices.ingram_part_number where dealerId={$dealerId} and tonerId={$tonerId}")->fetch();
                            $synnex = $db->query("select 'Synnex' as distributor, synnex_products.SYNNEX_SKU as partnumber, if (Qty_on_Hand>0,'Y','N') as available, Unit_Cost as price from synnex_products join synnex_prices on synnex_products.SYNNEX_SKU = synnex_prices.SYNNEX_SKU where dealerId={$dealerId} and tonerId={$tonerId}")->fetch();
                            $rate = \MPSToolbox\Services\CurrencyService::getInstance()->getRate();
                            $techdata = $db->query("select 'Tech Data' as distributor, techdata_products.Matnr as partnumber, if (Qty>0,'Y','N') as available, {$rate} * CustBestPrice as price from techdata_products join techdata_prices on techdata_products.Matnr = techdata_prices.Matnr where dealerId={$dealerId} and tonerId={$tonerId}")->fetch();
                        }

                        $the_distributor = null;

                        $found = [];
                        if ($ingram) $found[] = $ingram;
                        if ($synnex) $found[] = $synnex;
                        if ($techdata) $found[] = $techdata;
                        if (count($found)==0) {
                            //noop
                        } else if (count($found)==1) {
                            $the_distributor = $found[0];
                        } else {
                            $in_stock = [];
                            foreach ($found as $line) {
                                if ($line['available']=='Y') $in_stock = $line;
                            }

                            if (count($in_stock) == 1) {
                                $the_distributor = $in_stock[0];
                            } else {
                                $by_price = [];
                                foreach ($found as $line) {
                                    $by_price[$line['price']] = $line;
                                }
                                ksort($by_price);
                                $the_distributor = current($by_price);
                            }
                        }


                        $result['products'][] = [
                            $item['name'],
                            $item['sku'],
                            '$'.$item['price'],
                            $the_distributor?$the_distributor['distributor']:'',
                            $the_distributor?$the_distributor['partnumber']:'',
                            $the_distributor?'$'.$the_distributor['price']:'',
                        ];
                    }
                }
            }
            $this->sendJson($result);
            return;
        }
        $this->_pageTitle = ['E-commerce - Orders'];
        if ($this->shopifyConnect()) {
            $this->view->orders = Order::find('all');
        } else {
            $this->view->shopifyError=true;
        }
    }

}