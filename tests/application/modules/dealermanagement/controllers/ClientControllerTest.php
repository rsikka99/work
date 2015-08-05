<?php

class Dealermanagement_ClientControllerTest extends My_ControllerTestCase
{

    public $fixtures = ['users','clients'];

    public function setUp() {
        parent::setUp();
        $user = \MPSToolbox\Legacy\Mappers\UserMapper::getInstance()->find(2);
        Zend_Auth::getInstance()->getStorage()->write($user);
    }

    public function tearDown() {
        Zend_Auth::getInstance()->getStorage()->write(null);
        parent::tearDown();
    }

    public function test_indexAction() {
        $this->dispatch('company/clients');
        $this->assertNotRedirect();
        $this->assertModule('dealermanagement');
        $this->assertController('client');
        $this->assertAction('index');
        $this->assertEquals(1,preg_match('#'.preg_quote('<table class="table table-striped table-condensed table-bordered">').'#',$this->getResponse()->getBody()));
    }

    public function test_createClientAction() {
        $this->dispatch('company/clients/create');
        $this->assertNotRedirect();
        $this->assertModule('dealermanagement');
        $this->assertController('client');
        $this->assertAction('create');
        $this->assertQueryCount('form[@class="form-horizontal"]',1); //<form class="form-horizontal" method="post" accept-charset="UTF-8" action="">
    }

    public function test_createClientAction_post() {
        \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->clearItemCache();
        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->setDbTable($mock);

        $request = $this->getRequest();
        $request->setParams([
            'Save'=>'1',
            'accountNumber'=>'1',
            'addressLine1'=>'1',
            'addressLine2'	=>'1',
            'city'=>'1',
            'companyName'=>'1',
            'countryId'=>'1',
            'employeeCount'	=>'1',
            'firstName'=>'1',
            'lastName'=>'1',
            'legalName'=>'1',
            'phoneNumber'	=>'1',
            'postCode'=>'1',
            'region'=>'1',
        ]);

        $_GET['select']=true;
        $request->setMethod('POST');
        $this->dispatch('company/clients/create');
        echo $this->getResponse()->getBody();
        $this->assertRedirectTo('/select-client?selectClient=1');
    }

    public function test_editClientAction() {
        \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->clearItemCache();
        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->exactly(1))
            ->method('find')
            ->will($this->returnValue(
                new Row_editClientAction_post(['data'=>[['id'=>5, 'dealerId'=>2]]])
            ));
        \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->setDbTable($mock);
        $this->dispatch('company/clients/edit/5');
        $this->assertNotRedirect();
        $this->assertModule('dealermanagement');
        $this->assertController('client');
        $this->assertAction('edit');
        $this->assertQueryCount('form[@class="form-horizontal"]',1); //<form class="form-horizontal" method="post" accept-charset="UTF-8" action="">
    }

    public function test_editClientAction_post() {
        \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->clearItemCache();
        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->exactly(1))
            ->method('find')
            ->will($this->returnValue(
                new Row_editClientAction_post(['data'=>[['id'=>5, 'dealerId'=>2]]])
            ));
        $mock
            ->expects($this->once())
            ->method('update')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper::getInstance()->setDbTable($mock);

        $request = $this->getRequest();
        $request->setParams([
            'Save'=>'1',
            'accountNumber'=>'1',
            'addressLine1'=>'1',
            'addressLine2'	=>'1',
            'city'=>'1',
            'companyName'=>'1',
            'countryId'=>'1',
            'employeeCount'	=>'1',
            'firstName'=>'1',
            'lastName'=>'1',
            'legalName'=>'1',
            'phoneNumber'	=>'1',
            'postCode'=>'1',
            'region'=>'1',
        ]);
        $request->setMethod('POST');
        $this->dispatch('company/clients/edit/5');
        $this->assertRedirectTo('/company/clients');
    }


}

class Row_editClientAction_post extends Zend_Db_Table_Rowset_Abstract { }

