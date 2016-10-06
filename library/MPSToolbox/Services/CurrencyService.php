<?php
namespace MPSToolbox\Services;

use MPSToolbox\Legacy\Entities\DealerEntity;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerModel;

class CurrencyService {

    /** @var CurrencyService  */
    private static $instance;

    /** @var  string */
    public $currency = 'USD';

    /** @var  float */
    public $rate;

    /** @var \Zend_Db_Adapter_Abstract */
    private $db;


    public static function getInstance($dealerId = null) {
        if (!self::$instance) {
            if (!$dealerId) {
                $dealerId = DealerEntity::getDealerId();
            }
            $currency = DealerEntity::getCurrency($dealerId);
            self::$instance = new CurrencyService($currency);
        }
        return self::$instance;
    }

    public function __construct($currency) {
        $this->currency = $currency;
        $this->db = \Zend_Db_Table::getDefaultAdapter();
    }

    private function fetch_cur($from,$to) {
        if (strcasecmp($from,$to)==0) return 1;
        $remote=file_get_contents("http://www.google.com/finance/converter?a=1&from={$from}&to={$to}");
        if (preg_match("#{$from} =[^\d]*([\d.]+) {$to}#",$remote,$match)) return floatval($match[1]);
        return false;
    }

    public function is_usd() {
        return $this->currency == 'USD';
    }
    public static function isUSD() {
        return self::getInstance()->is_usd();
    }

    public function get_symbol() {
        switch($this->currency) {
            case 'USD' : return '$';
            case 'CAD' : return 'CAD $';
            case 'BBD' : return 'BBD $';
            default : return $this->currency;
        }
    }
    public static function getSymbol() {
        return self::getInstance()->get_symbol();
    }

    public function updateRate() {
        if ($this->is_usd()) return 1;

        $rate = $this->fetch_cur('USD', $this->currency);
        if (!$rate) throw new \Exception('Cannot determine exchange rate');
        $this->db->query("replace into currency_exchange set currency='{$this->currency}', rate={$rate}, dt=now()");

        return $rate;
    }

    public function getRate() {
        if ($this->is_usd()) return 1;

        if (empty($this->rate)) {
            $currency_exchange = $this->db->query("select * from currency_exchange where currency='{$this->currency}'")->fetch();
            if (!$currency_exchange || (time() - strtotime($currency_exchange['dt']) > (60 * 60 * 24))) {
                $this->rate = $this->updateRate();
            } else {
                $this->rate = $currency_exchange['rate'];
            }
        }
        if ($this->rate<=0) return 1;

        return $this->rate;
    }

    public function getObjectValue(\My_Model_Abstract $object, $table, $field) {
        $base = $object->$field;
        if ($this->currency == 'USD') return $base;
        $cv = $this->db->query("select * from currency_value where `table`='{$table}' and `id`=".intval($object->id)." and `field`='{$field}' and `currency`='{$this->currency}'")->fetch();
        if ($cv) {
            return $cv['value'];
        }
        $rate = $this->getRate();
        return $rate * $base;
    }

    public function setObjectValue(\My_Model_Abstract $object, $table, $field, $value) {
        $this->db->query("replace into currency_value set `table`='{$table}', `id`={$object->id}, `field`='{$field}', `currency`='{$this->currency}', `value`={$value}");
        $rate = $this->getRate();
        $usd = $value / $rate;
        $this->db->query("update `{$table}` set `{$field}`={$usd} where `id`={$object->id}");
    }

}
