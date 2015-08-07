<?php

class Default_AuthControllerTest extends My_ControllerTestCase
{

    public $fixtures = ['users'];

    public function tearDown() {
        parent::tearDown();
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable(null);
        \MPSToolbox\Legacy\Mappers\UserEventLogMapper::getInstance()->setDbTable(null);
    }

    public function test_loginAction() {
        $this->dispatch('login');
        $this->assertModule('default');
        $this->assertController('auth');
        $this->assertAction('login');
        $this->assertQueryCount('form', 1);
        $this->assertQueryCount('form input[@name="email"]', 1);
        $this->assertQueryCount('form input[@name="password"]', 1);
    }

    public function test_loginAction_post_invalid() {
        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable($mock);

        $request = $this->getRequest();
        $request->setMethod('POST');
        $request->setParams(['email'=>'email','password'=>'password']);
        $this->dispatch('login');
        $this->assertAction('login');
        $this->assertQueryCount('form', 1);

        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable(null);
    }

    public function test_loginAction_post_fail() {
        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable($mock);

        $request = $this->getRequest();
        $request->setMethod('POST');
        $request->setParams(['email'=>'root@tangentmtw.com','password'=>'password']);
        $this->dispatch('login');
        $this->assertAction('login');
        $this->assertQueryCount('form', 1);

        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable(null);
    }

    public function test_loginAction_post_success() {
        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\UserEventLogMapper::getInstance()->setDbTable($mock);

        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable($mock);

        $request = $this->getRequest();
        $request->setMethod('POST');
        $request->setParams(['email'=>'root@tangentmtw.com','password'=>'tmtwdev']);
        $this->dispatch('login');
        $this->assertAction('login');

        $this->assertRedirectTo('/');

        $identity = Zend_Auth::getInstance()->getIdentity();
        $this->assertInstanceOf('stdClass', $identity);
        $arr = get_object_vars($identity);
        $this->assertEquals('1', $arr['id']);

        $row = \MPSToolbox\Legacy\Mappers\UserSessionMapper::getInstance()->find(session_id());
        $this->assertInstanceOf('\MPSToolbox\Legacy\Models\UserSessionModel', $row);
        \MPSToolbox\Legacy\Mappers\UserSessionMapper::getInstance()->delete($row);

        \MPSToolbox\Legacy\Mappers\UserEventLogMapper::getInstance()->setDbTable(null);
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable(null);
    }

    public function test_logoutAction() {
        Zend_Auth::getInstance()->getStorage()->write(json_decode(json_encode(['id'=>1, 'resetPasswordOnNextLogin'=>false])));
        $identity = Zend_Auth::getInstance()->getIdentity();
        $this->assertInstanceOf('stdClass', $identity);

        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\UserEventLogMapper::getInstance()->setDbTable($mock);

        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable($mock);

        $this->dispatch('logout');
        $this->assertAction('logout');

        $this->assertRedirectTo('/login');
        $identity = Zend_Auth::getInstance()->getIdentity();
        $this->assertEquals(null, $identity);

        \MPSToolbox\Legacy\Mappers\UserEventLogMapper::getInstance()->setDbTable(null);
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable(null);
    }

    public function test_forgotPasswordAction() {
        $this->dispatch('forgot-password');
        $this->assertModule('default');
        $this->assertController('auth');
        $this->assertAction('forgot-password');
        $this->assertQueryCount('form', 1);
        $this->assertQueryCount('form input[@name="email"]', 1);
    }

    public function test_forgotPasswordAction_post() {
        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\UserEventLogMapper::getInstance()->setDbTable($mock);

        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable($mock);

        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\UserPasswordResetRequestMapper::getInstance()->setDbTable($mock);

        $mock = $this->getMock('Zend_Mail_Transport_Smtp');
        $mock
            ->expects($this->once())
            ->method('send')
            ->will($this->returnValue(null));
        \Tangent\Controller\Action::setMailTransport($mock);

        $request = $this->getRequest();
        $request->setMethod('POST');
        $request->setParams(['email'=>'standarduser@tangentmtw.com']);
        $this->dispatch('forgot-password');
        $this->assertAction('forgot-password');

        $this->assertRedirectTo('/login');

        \MPSToolbox\Legacy\Mappers\UserEventLogMapper::getInstance()->setDbTable(null);
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable(null);
        \MPSToolbox\Legacy\Mappers\UserPasswordResetRequestMapper::getInstance()->setDbTable(null);
        \Tangent\Controller\Action::setMailTransport(null);
    }

    public function test_changepasswordAction() {
        $user = \MPSToolbox\Legacy\Mappers\UserMapper::getInstance()->find(1);
        Zend_Auth::getInstance()->getStorage()->write($user);
        $identity = Zend_Auth::getInstance()->getIdentity();
        $this->assertInstanceOf('stdClass', $identity);

        $this->dispatch('login/change-password');
        $this->assertModule('default');
        $this->assertController('auth');
        $this->assertAction('changepassword');

        $this->assertQueryCount('form', 1);
        $this->assertQueryCount('form input[@name="current_password"]', 1);
        $this->assertQueryCount('form input[@name="password"]', 1);
        $this->assertQueryCount('form input[@name="password_confirm"]', 1);
    }

    public function test_changepasswordAction_post() {
        $user = \MPSToolbox\Legacy\Mappers\UserMapper::getInstance()->find(1);
        $user->lastSeen = date('Y-m-d H:i:s');
        Zend_Auth::getInstance()->getStorage()->write($user);
        $identity = Zend_Auth::getInstance()->getIdentity();
        $this->assertInstanceOf('stdClass', $identity);

        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\UserEventLogMapper::getInstance()->setDbTable($mock);

        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable($mock);

        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('update')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\UserMapper::getInstance()->setDbTable($mock);

        $request = $this->getRequest();
        $request->setMethod('POST');
        $request->setParams(['current_password'=>'tmtwdev','password'=>'123abc!','password_confirm'=>'123abc!','submit'=>'submit']);
        $this->dispatch('login/change-password');
        $this->assertAction('changepassword');
        $this->assertRedirectTo('/login');

        \MPSToolbox\Legacy\Mappers\UserEventLogMapper::getInstance()->setDbTable(null);
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable(null);
        \MPSToolbox\Legacy\Mappers\UserMapper::getInstance()->setDbTable(null);
        Zend_Auth::getInstance()->getStorage()->write(null);
    }

    public function test_forgotPasswordResetAction() {
        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('fetchRow')
            ->will($this->returnValue(new PasswordResetRequest_Row()));
        \MPSToolbox\Legacy\Mappers\UserPasswordResetRequestMapper::getInstance()->setDbTable($mock);

        $this->dispatch('forgot-password/reset/123');
        $this->assertModule('default');
        $this->assertController('auth');
        $this->assertAction('forgot-password-reset');

        $this->assertQueryCount('form', 1);
        $this->assertQueryCount('form input[@name="password"]', 1);
        $this->assertQueryCount('form input[@name="password_confirm"]', 1);

        \MPSToolbox\Legacy\Mappers\UserPasswordResetRequestMapper::getInstance()->setDbTable(null);
    }


    public function test_forgotPasswordResetAction_post() {
        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('fetchRow')
            ->will($this->returnValue(new PasswordResetRequest_Row()));
        \MPSToolbox\Legacy\Mappers\UserPasswordResetRequestMapper::getInstance()->setDbTable($mock);

        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\UserEventLogMapper::getInstance()->setDbTable($mock);

        $mock = $this->getMock('Zend_Db_Table_Abstract');
        $mock
            ->expects($this->once())
            ->method('insert')
            ->will($this->returnValue(1));
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable($mock);

        $request = $this->getRequest();
        $request->setMethod('POST');
        $request->setParams(['password'=>'123abc!','password_confirm'=>'123abc!','submit'=>'submit']);

        $this->dispatch('forgot-password/reset/123');
        $this->assertModule('default');
        $this->assertController('auth');
        $this->assertAction('forgot-password-reset');

        $this->assertRedirectTo('/login');

        \MPSToolbox\Legacy\Mappers\UserEventLogMapper::getInstance()->setDbTable(null);
        \MPSToolbox\Legacy\Mappers\EventLogMapper::getInstance()->setDbTable(null);
        \MPSToolbox\Legacy\Mappers\UserPasswordResetRequestMapper::getInstance()->setDbTable(null);
    }

}

class PasswordResetRequest_Row extends Zend_Db_Table_Row_Abstract {
    public function __construct() {
        $this->_data = ['id'=>1,'userId'=>1,'resetUsed'=>false,'ipAddress'=>'127.0.0.1','dateRequested'=>date('Y-m-d H:i:s')];
    }
}
