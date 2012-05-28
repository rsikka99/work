<?php 
class Tangent_Log extends Zend_Log {
static $_logger;



    /**
     * @return Tangent_Log
     */
    public static function getInstance ()
    {
        
        if (! isset(self::$_logger))
        {
        	$logger = new Zend_Log();
        	$infoWriter = new Zend_Log_Writer_Stream(DATA_PATH."/logs/info.log");
            $alertWriter = new Zend_Log_Writer_Stream(DATA_PATH."/logs/alert.log");
            $logger->addWriter($infoWriter);
            $logger->addWriter($alertWriter);
            $filter = new Zend_Log_Filter_Priority(Zend_Log::CRIT);
			$alertWriter->addFilter($filter);
			self::$_logger = $logger;        	
        }
        return self::$_logger;
    }
    /**
     * @param message text
     * @param message priority
     */
	public static function message ($message, $priority)
    {
       self::getInstance()->$priority($message);
    }
    
}
