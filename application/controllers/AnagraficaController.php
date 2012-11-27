<?php

class AnagraficaController extends Zend_Controller_Action
{
	private $_idanagrafica = null;

    public function init()
    {
        if(Zend_Registry::get("userinfo"))
	    	$this->_idanagrafica = Zend_Registry::get("userinfo")->id;
    }

    public function indexAction()
    {
        // action body
    }

    public function elencoAction()
    {
        $anagrafica = new Application_Model_DbTable_Anagrafica();
        $this->view->anagrafica = $anagrafica->fetchAll(null, "ragionesociale");
        unset($anagrafica);
    }

    public function editAction()
    {
        // action body
    }

    public function aggiungicartaAction()
    {
        // action body
    	echo "iduser=".$this->_idanagrafica;
    }

    public function editanagraficaAction()
    {
        // action body
    	echo "iduser=".$this->_idanagrafica;
    }


}











