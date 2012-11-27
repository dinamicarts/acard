<?php

class Application_Model_DbTable_Userstmp extends Zend_Db_Table_Abstract
{

    protected $_name = 'userstmp';

    public function DammiUtenteTmp($email){
    	$where = "email LIKE '$email'";
    	return $this->fetchRow($where);
    }
    
    public function inserisci($datiin)
    {
    	//verifico se esiste giˆ
    	$res = $this->fetchRow("email like '" . $datiin["email"] . "'");
    	$dati = array();
    	$k = NULL;
    	$dati["k"] = NULL;
    	if($res==NULL)
    	{
    		$dati["pwd"] = md5($datiin["password"]);
    		$dati["ragionesociale"] = $datiin["cognomenome"];
    		$dati["email"] = $datiin["email"];
    		$dati["ncard"] = $datiin["acard"];
    		$dati["cellulare"] = $datiin["cellulare"];
    		$k = Application_Utils::GenerateKey();
    		$dati["k"] = $k;
    		$dati["datacreazione"] = date("Y-m-d H:i:s");
    		
    		$id = $this->insert($dati);
    		$log = Zend_Registry::get("log");
    		$log->log( "Registrazione in attesa di confema: ".print_r($dati, true), Zend_Log::INFO);
    	}
    	
    	return $dati;
    }

    public function AttivaAccount($k){
    	$where = "k='$k'";
    	$res = $this->fetchAll($where);
    	$res = $this->fetchRow($where);
    	if($res){
    		$tblUsers = new Application_Model_DbTable_Users();
    		$data = array();
    		$data["pwd"] = $res["pwd"];
    		$data["ragionesociale"] = $res["ragionesociale"];
    		$data["email"] = $res["email"];
    		//$data["datanascita"] = $res["datanascita"];
    		$data["cellulare"] = $res["cellulare"];
    		$data["role"] = "user";
    		$data["datacreazione"] = $res["datacreazione"];
    		
    		$id = $tblUsers->insert($data);
    		if($id){
    			$tblAcard = new Application_Model_DbTable_Acard();
    			$tblAcard->AssociaAcard($res["ncard"], $id);
    			$this->delete($where);
    		}
    		$log = Zend_Registry::get("log");
    		$log->log( "(AttivaAccount) Account '" . $res["email"] . "' attivato: ", Zend_Log::INFO);
    	}
    	
    }
}

