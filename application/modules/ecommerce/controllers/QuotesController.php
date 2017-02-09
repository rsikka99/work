<?php

use Tangent\Controller\Action;

class Ecommerce_QuotesController extends Action {

    public function indexAction()
    {
        $this->_pageTitle = ['E-commerce - Quotes'];

        $settings = \MPSToolbox\Settings\Entities\DealerSettingsEntity::getDealerSettings();
        $shop = $settings->shopSettings->shopifyName;
        if (empty($shop)) {
            $this->view->content = "<div class='alert alert-danger'>Your account has not been connected to Shopify yet.</div>";
            return;
        }

        $client = new \GuzzleHttp\Client([]);

        $userId = $wu = \MPSToolbox\Legacy\Services\NavigationService::$userId;
        $roles = \MPSToolbox\Legacy\Modules\Admin\Mappers\UserRoleMapper::getInstance()->fetchAllRolesForUser($userId);
        foreach ($roles as $role) {
            if ($role->roleId==2) $userId=0; // Company Administrator
            if ($role->roleId==3) $userId=0; // Hardware & Pricing Administrator
        }
        $url = 'http://quote.tangentmtw.com/api/mpstoolbox.php?shop='.$shop.'.myshopify.com&wu='.$wu.'&user='.$userId.'&'.http_build_query($_GET);
        if ($this->getRequest()->getMethod()=='POST') {
            $response = $client->post($url, [ 'form_params'=>$_POST ]);
        } else {
            $response = $client->get($url);
        }
        $html = trim($response->getBody()->getContents());
        $contentType = $response->getHeaderLine('Content-Type');
        //$this->view->content = "content type: [{$contentType}]";
        //return;

        if ((substr($html,0,1)=='{') || (substr($html,0,1)=='[') || ($contentType=='application/json')) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_response->setHeader('Content-Type','application/json');
            echo $html;
            return;
        }

        $html = str_replace([
            '/index.php/quotes',
        ],[
            '/ecommerce/quotes',
        ],$html);

        $html = preg_replace('#'.preg_quote('<script>').'(.*)'.preg_quote('</script>').'#Usi', "<script>\nrequire(['jquery','datatables', 'datatables.bootstrap', 'jquery-datetimepicker'], function ($) {\n$1});\n</script>", $html);

        $this->view->headLink()->prependStylesheet('//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.min.css');

        $this->view->content = $html;

    }

}