<?php

use MPSToolbox\Legacy\Models\UserModel;

require 'IndexController.php';

/**
 * Class Api_IndexController
 */
class Dealerapi_AuthController extends Dealerapi_IndexController
{
    public function indexAction() {
        $key = $this->getParam('key');
        $secret = $this->getParam('secret');
        if ($key && $secret) {
            $mapper = MPSToolbox\Legacy\Mappers\DealerMapper::getInstance();
            $dealer = $mapper->fetch([
                "api_key = ?" => $key
            ]);
            if (!$dealer) {
                $this->outputJson(['error'=>'invalid key']);
                return;
            }
            if ($dealer->getApiSecret()!=$secret) {
                $this->outputJson(['error'=>'invalid secret']);
                return;
            }
            $auth   = Zend_Auth::getInstance();
            $user = new UserModel([
                'id'=>null,
                'eulaAccepted'=>true,
                'firstname'=>$dealer->dealerName,
                'dealerId'=>$dealer->id,
            ]);
            $auth->getStorage()->write($user);
            $this->outputJson(['ok'=>'welcome '.$dealer->dealerName]);
            return;
        }
        $auth   = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
            $this->outputJson(['ok'=>'welcome '.$user->firstname]);
        } else {
            $this->outputJson(['error'=>'not authenticated']);
        }
    }
}