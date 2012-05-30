<?php
/**
 * Common Class: This class contains common functions shared thoughout the application
 *
 * @author John Sadler
 */

class Custom_Common {
	
	//builds side menu
    public function build_menu()
    {
    	$config = Zend_Registry::get('config');
		$this->view->app = $config->app;
		$this->MPSProgramName = $config->app->MPSProgramName;
		
    	$serverUrlHelper = new Zend_View_Helper_ServerUrl();
		$baseUrlHelper = new Zend_View_Helper_BaseUrl();
		$baseURL = "http://" . $serverUrlHelper->getHost() . $baseUrlHelper->getBaseUrl();
		
    	$session = new Zend_Session_Namespace('report');
	    $report_id = $session->report_id;
	    
		$reportTable = new Proposalgen_Model_DbTable_Report();
		$where = $reportTable->getAdapter()->quoteInto('id = ?', $report_id, 'INTEGER');
		$report = $reportTable->fetchRow($where);
		$page = $report['report_stage'];
		
		$menu = Array();
		if($page != '') {
        	$menu = Array (
				'company' => Array('url' => $baseURL . '/survey/company'),
				'general' => Array('url' => $baseURL . '/survey/general'),
				'finance' => Array('url' => $baseURL . '/survey/finance'),
				'purchasing' => Array('url' => $baseURL . '/survey/purchasing'),
				'it' => Array('url' => $baseURL . '/survey/it'),
				'users' => Array('url' => $baseURL . '/survey/users'),
				'verify' => Array('url' => $baseURL . '/survey/verify'),
				'upload' => Array('url' => $baseURL . '/data')
			);
			
	       	if($page == 'mapping' || $page == 'leasing' || $page == 'settings' || $page == 'finished') {
				$menu['mapping'] = Array('url' => $baseURL . '/data/devicemapping');
				$session->currentPage = $baseURL . '/data/devicemapping';
		    }
			
	       	if($page == 'leasing' || $page == 'settings' || $page == 'finished') {
				$menu['summary'] = Array('url' => $baseURL . '/data/deviceleasing');
				$session->currentPage = $baseURL . '/data/deviceleasing';
		    }
		      
		    if($page == 'settings' || $page == 'finished') {
				$menu['settings'] = Array('url' => $baseURL . '/data/reportsettings');
				$session->currentPage = $baseURL . '/data/reportsettings';
		    }
		       
		    if($page == 'finished') {
				$menu['report'] = Array('url' => $baseURL . '/report');
				$session->currentPage = $baseURL . '/report';
		    }
		}
		return $menu;
		
    }

    //generate random password
	function create_password() {
		//empty by default
		$password = '';
		
		// set password length
		$pw_length = 8;
		
		// set ASCII range for random character generation
		$lower_ascii_bound = 50; //"2"
		$upper_ascii_bound = 122; //"z"
		
		//Exclude special characters and some confusing alphanumerics
		//o,O,0,I,1,l etc
		$notuse = array (58,59,60,61,62,63,64,73,79,91,92,93,94,95,96,108,111);
		
		$i = 0;
		while ($i < $pw_length) {
			mt_srand ((double)microtime() * 1000000);
			//random limits within ASCII table
			$randnum = mt_rand ($lower_ascii_bound, $upper_ascii_bound);
			if (!in_array ($randnum, $notuse)) {
				$password = $password . chr($randnum);
				$i++;
			}
		}
		return $password;
	}

	//wrapper for sending mail to check if in development and send to dev email instead
	function send_email($body, $fromname, $fromemail, $toname, $toemail, $subject) {
		$config = Zend_Registry::get('config');
		$email = $config->email;
		
        //grab the email configuration settings from application.ini
        $eMailConfig = array(
	 		'auth' => 'login',
	 		'username' => $email->username,
	 		'password' => $email->password,
	 		'ssl' => $email->ssl,
	 		'port' => $email->port);
        	
        //grab the email host from application.ini
		$SmtpServer = $email->host;
		
        //prepare mail transport
		$transport = new Zend_Mail_Transport_Smtp($SmtpServer, $eMailConfig);
		$mail = new Zend_Mail();
		$mail->setBodyHtml($body);
		$mail->setFrom($fromemail, $fromname);
		$mail->setSubject($subject);

		//if development change To info
		if (APPLICATION_ENV == 'development') {
			$mail->addTo($email->devEmail, $email->devName);
		} else {
			$mail->addTo($toemail, $toname);
		}
		
		//send email
		$mail->send($transport);
	}
}

?>