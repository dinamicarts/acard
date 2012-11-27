<?php

class AcardController extends Zend_Controller_Action
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

    
    /**
     * Ritorna il saldo punti per il codcliente richiesto
     */
    public function saldoAction(){
        $transaz = new Application_Model_DbTable_Transaz();
        $this->view->saldo = $transaz->saldoPunti("codcliente='" . $this->_getParam("codcliente") . "'");   
    }

    /**
     * Importa i files e trasferisce tutto in DB
     */
    public function importaAction()
    {
        if(strtolower($_SERVER['HTTP_HOST'])!="localhost")
            return; //questa funzione non deve essere eseguita sul sito lato pubblico
        
        $msgs = Application_ImportExport::ImportaTutto();
        foreach($msgs as $msg)
            echo $msg;
        
        //esporto al sito pubblico
        Application_ImportExport::ExportAll();
    }

    /**
     * Svuota le tabelle del DB
     *
     *
     *
     *
     *
     */
    public function emptyallAction()
    {
        $transaz = new Application_Model_DbTable_Transaz();
        $acard = new Application_Model_DbTable_Acard();
        $transaz->trunc();
        $acard->trunc();
        
        if(strtolower($_SERVER['HTTP_HOST'])=="localhost")
            Application_ImportExport::EraseAllOnRemote(); //questa funzione non deve essere eseguita sul sito lato pubblico
        
        echo "Operazione eseguita";
    }

    public function resyncAction()
    {
        if(strtolower($_SERVER['HTTP_HOST'])!="localhost")
            return; //questa funzione non deve essere eseguita sul sito lato pubblico
        
        Application_ImportExport::RisincronizzaDatiRemoti();
    }

    
    private function _movimenti($ncard=null, $codcliente=null){
    	$where = null;
    	if($codcliente!=NULL)
    		$where = "codcliente='$codcliente'";
    	else if($ncard!=NULL)
    		$where = "ncard='$ncard'";
    	
    	$transaz = new Application_Model_DbTable_Transaz();
    	$ris = $transaz->fetchAll($where, "dataoratransazione");
    	
    	$result = array();
    	$result["transaz"] = $ris;
    	unset($transaz);
    	
    	if($where!=""){
    		$saldo = 0;
    		foreach($ris as $row){
    			$saldo += floatval($row["puntiassegnati"]);
    		}
    	}
    	else
    		$saldo = null;
    	$result["saldo"] = $saldo;
    	 
    	return $result;
    }
    
    /**
     * Ritorna l'elenco dei movimenti per il codcliente richiesto
     *
     */
    public function movimentiAction()
    {
    	$res = $this->_movimenti($this->_getParam("ncard"), $this->_getParam("codcliente"));
    	if($res){
    		$this->view->transaz = $res["transaz"];
    		$this->view->saldo = $res["saldo"];
    	}
    	else {
    		$this->view->transaz = array();
    		$this->view->saldo = array();
    	}
    }    
    
    public function movimentiacardAction()
    {
    	$iduser = Zend_Registry::get("userinfo")->id;
    	$tblAcard = new Application_Model_DbTable_Acard();
    	$res = $tblAcard->fetchRow("iduser=$iduser");
    	if($res){
    		$res = $this->_movimenti($res["ncard"]);
    		$this->view->transaz = $res["transaz"];
    		$this->view->saldo = $res["saldo"];
    	}
    	else {
    		$this->view->transaz = array();
    		$this->view->saldo = array();
    	}
    }

    /**
     * Ritorna l'elenco delle acard in archivio
     */
    public function elencoAction()
    {
        $where = null;
        if($this->_getParam("codcliente")!=NULL)
        	$where = "codcliente='".$this->_getParam("codcliente")."'";
        else if($this->_getParam("ragsoc")!=NULL)
        	$where = "ragionesociale like '%".$this->_getParam("ragsoc")."%'";
        
        $acard = new Application_Model_DbTable_Acard();
        $ris = $acard->fetchAll($where, array("ragionesociale", "ncard"));
        $this->view->acards = $ris;
        unset($acard);
    }

    /**
     * Ritorna l'elenco di acard associate all'utente loggato
     */
    public function elencoacardutenteAction()
    {
        // action body
    	$iduser = Zend_Registry::get("userinfo")->id;
    	if(isset($iduser)){
	    	$where = "iduser=$iduser";
	    	$acard = new Application_Model_DbTable_Acard();
	    	$ris = $acard->fetchAll($where, array("ragionesociale", "ncard"));
	    	$this->view->acards = $ris;
	    	unset($acard);
    	}
    }


}