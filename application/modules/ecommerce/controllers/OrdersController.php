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
                    $result['customer'][] = ['First name', trim($order->customer['first_name'])];
                    $result['customer'][] = ['Last name', trim($order->customer['last_name'])];
                    $result['customer'][] = ['Email', "<a href='{$order->customer['email']}'>{$order->customer['email']}</a>"];
                    $result['customer'][] = ['Phone', trim($order->billing_address['phone'])];
                    $result['customer'][] = ['Billing address', trim($order->billing_address['address1'].', '.$order->billing_address['address2'],', ')];
                    $result['customer'][] = ['Company', trim($order->billing_address['company'])];
                    $result['customer'][] = ['City', trim($order->billing_address['city'])];
                    $result['customer'][] = ['Region', trim($order->billing_address['province'])];
                    $result['customer'][] = ['Postal code', trim($order->billing_address['zip'])];
                    $result['customer'][] = ['Country', trim($order->billing_address['country'])];
                    if ($order->shipping_address['address1']) {
                        $result['customer'][] = ['&nbsp;', '&nbsp;'];
                        $result['customer'][] = ['Shipping address', trim($order->shipping_address['address1'] . ', ' . $order->shipping_address['address2'], ', ')];
                        $result['customer'][] = ['Company', trim($order->shipping_address['company'])];
                        $result['customer'][] = ['City', trim($order->shipping_address['city'])];
                        $result['customer'][] = ['Region', trim($order->shipping_address['province'])];
                        $result['customer'][] = ['Postal code', trim($order->shipping_address['zip'])];
                        $result['customer'][] = ['Country', trim($order->shipping_address['country'])];
                    }

                    $db = Zend_Db_Table::getDefaultAdapter();

                    foreach ($order->line_items as $item) {
                        $id = $db->query('select tonerId from dealer_toner_attributes where webId='.$item['product_id'])->fetchColumn(0);
                        if (!$id) $id = $db->query('select masterDeviceId from devices where webId='.$item['product_id'])->fetchColumn(0);
                        if (!$id) $id = $db->query('select skuId from dealer_sku where webId='.$item['product_id'])->fetchColumn(0);
                        if ($id) {
                            $products = $db->query("select vpn, price, isStock, suppliers.name as sname from supplier_product join supplier_price using (supplierId,supplierSku) join suppliers on supplier_product.supplierId=suppliers.id where dealerId={$dealerId} and baseProductId={$id}")->fetchAll();
                        }

                        $the_product = null;
                        if (count($products)==0) {
                            //noop
                        } else if (count($products)==1) {
                            $the_product = $products[0];
                        } else {
                            $in_stock = [];
                            foreach ($products as $line) {
                                if ($line['isStock']) $in_stock[] = $line;
                            }

                            if (count($in_stock) == 1) {
                                $the_product = $in_stock[0];
                            } else if (count($in_stock) > 1) {
                                $by_price = [];
                                foreach ($in_stock as $line) {
                                    $by_price[$line['price']] = $line;
                                }
                                ksort($by_price);
                                $the_product = array_shift($by_price);
                            } else {
                                $by_price = [];
                                foreach ($products as $line) {
                                    $by_price[$line['price']] = $line;
                                }
                                ksort($by_price);
                                $the_product = array_shift($by_price);
                            }
                        }

                        $result['products'][] = [
                            $item['name'],
                            $item['sku'],
                            '$'.$item['price'],
                            $the_product?$the_product['sname']:'',
                            $the_product?$the_product['vpn']:'('.$item['product_id'].')',
                            $the_product?'$'.$the_product['price']:'',
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