<?php
use Tangent\Controller\Action;

/**
 * Class Api_IndexController
 */
class Api_IndexController extends Action
{
    public function supportAction() {
        $config = \Zend_Registry::get('config');

        $mail = new Zend_Mail ();
        $mail->setFrom($config->app->supportEmail, 'MPSToolbox Support');
        $mail->addTo($config->app->supportEmail, 'MPSToolbox Support');
        if (@$_POST['Email']) $mail->setReplyTo($_POST['Email']);
        $mail->setSubject('MPSToolbox Support');

        $tr = '';
        foreach ($_POST as $k=>$v) {
            $tr .= '<tr><td>'.$k.': </td><td>'.$v.'</td></tr>';
        }

        $body = "<h2>MPSToolbox Support</h2>";
        $body .= "<table>";
        $body .= $tr;
        $body .= "</table>";
        $mail->setBodyHtml($body);
        $mail->send();

        $this->sendJson(['ok'=>true]);
    }
}