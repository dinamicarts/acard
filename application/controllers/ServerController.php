<?php

class ServerController extends Zend_Controller_Action
{

    public function init()
    {
        /* questo controller utilizza un output diretto senza template*/
    	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function indexAction()
    {
        // action body
    }

    /**
     * A seguito del controllo sui campi: acard, cognome e nome
     * ritorna un json nel seguente formato: {"acard":"OK","ragsoc":"OK"}
     */
    public function checkAction()
    {
    	$ncard = strtolower($this->_getParam("acard"));
    	$ragsoc = strtolower($this->_getParam("ragsoc"));
    	$utils = new Application_Utils();
    	$risp = $utils->CheckAcard($ncard, $ragsoc);
    	echo json_encode($risp);
    }

    /**
     * Ritorna OK se l'utente non esiste
     */
    public function checkuserAction()
    {
        $email = strtolower($this->_getParam("email"));
        $utils = new Application_Utils();
        $risp = $utils->EmailExists($email);
        echo $risp ? 'KO' : 'OK';
    }


}





