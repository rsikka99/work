<?php

/**
class MockedMessage extends \Fetch\Message {
    public function __construct($subject) {
        $this->subject = $subject;
    }
}
/**/

class RmsUpdateServiceTest extends My_DatabaseTestCase {

    public function test_update() {
        $service = new \MPSToolbox\Services\RmsUpdateService();
        $service->update();
    }


    /**
    public function test_processMessage_fail_subject() {
        $service = new \MPSToolbox\Services\RmsUpdateService();
        $message = new MockedMessage('foo');
        $this->setExpectedException('RuntimeException');
        $service->processMessage($message);
    }
    public function test_processMessage_fail() {
        $service = new \MPSToolbox\Services\RmsUpdateService();
        $message = new MockedMessage('foo');
        $this->setExpectedException('RuntimeException');
        $service->processMessage($message);
    }
    /**/
}