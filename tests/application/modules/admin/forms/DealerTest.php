<?php

use MPSToolbox\Legacy\Modules\Admin\Forms\DealerForm;

class Default_Form_DealerTest extends Tangent_PHPUnit_Framework_ZendFormTestCase

{

    /**
     * @return DealerForm
     */
    public function getForm ()
    {
        $form = new DealerForm();
        $form->getElement('dealerLogoImage')->setTransferAdapter(
            new Zend_File_Transfer_Adapter_AbstractTest_MockAdapter()
        );
        return $form;
    }

    /**
     * @return array
     */
    public function getGoodData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/goodData_dealerTest.xml");
    }

    /**
     * @return array
     */
    public function getBadData ()
    {
        return $this->loadFromXmlFile(__DIR__ . "/_files/badData_dealerTest.xml");
    }

    public function testWillFailIncorrectKeys ()
    {
        $this->assertFalse($this->getForm()->isValid(array('testKey' => 'Dealer Name', 'userLicenses' => 16)));
    }

    public function testWillFailNoData ()
    {
        $this->assertFalse($this->getForm()->isValid(array()));
    }


}


class Zend_File_Transfer_Adapter_AbstractTest_MockAdapter extends Zend_File_Transfer_Adapter_Abstract
{
    public $received = false;
    public $_tmpDir;
    public function __construct()
    {
        $testfile = dirname(__FILE__) . '/_files/goodData_dealerTest.xml';
        $this->_files = array(
            'dealerLogoImage' => array(
                'name'      => 'dealerLogoImage.jpg',
                'type'      => 'image/jpeg',
                'size'      => 126976,
                'tmp_name'  => '/tmp/489127ba5c89c',
                'options'   => array('ignoreNoFile' => false, 'useByteString' => true, 'detectInfos' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ),
        );
    }
    public function send($options = null)
    {
        return;
    }
    public function receive($options = null)
    {
        $this->received = true;
        return;
    }
    public function isSent($file = null)
    {
        return false;
    }
    public function isReceived($file = null)
    {
        return $this->received;
    }
    public function isUploaded($files = null)
    {
        return true;
    }
    public function isFiltered($files = null)
    {
        return true;
    }
    public static function getProgress()
    {
        return;
    }
    public function getTmpDir()
    {
        $this->_tmpDir = parent::_getTmpDir();
    }
    public function isPathWriteable($path)
    {
        return parent::_isPathWriteable($path);
    }
    public function addInvalidFile()
    {
        $this->_files += array(
            'test' => array(
                'name'      => 'test.txt',
                'type'      => 'image/jpeg',
                'size'      => 0,
                'tmp_name'  => '',
                'options'   => array('ignoreNoFile' => true, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            )
        );
    }
}