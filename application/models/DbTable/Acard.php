<?php

class Application_Model_DbTable_Acard extends Zend_Db_Table_Abstract
{

    protected $_name = 'acard';


    /**
     *
     * @param array ["ncard", "codcliente", "ragionesociale","datanascita", "sesso"]
     * @return array ["id","inserito"]
     * Inserisce la ACARD cliente se non esiste gia' (ricerca per ncard)
     * Ritorna l'id della carta trovata o inserita.
     */
    public function inserisci($dati)
    {
    	//verifico se esiste gia'
    	$res = $this->fetchRow("ncard LIKE '" . $dati["ncard"] . "'");
    
    	$result = array(
    			"id"        => -1,
    			"inserito"  => false,
    	);
    
    	if($res==NULL)
    	{
    		$id = $this->insert($dati);
    		$log = Zend_Registry::get("log");
    		/*$log->log(
    		 "Nuova carta inserita: " .
    				" ID=" . $id . " - " .
    				" ID Cliente=" . $dati["idanagrafica"] . " - " .
    				" N Carta=" . $dati["ncard"]
    				, Zend_Log::INFO);
    		*/
    		$result["id"] = $id;
    		$result["inserito"] = true;
    	}
    	else {
    		$result["id"] = $res["id"];
    	}
    
    	return $result;
    }
    
    /**
     * Svuota la tabella
     */
    public function trunc(){
    	$res = $this->delete("1=1");
    }
    
    /**
     * 
     * @param string $numAcard
     * @param int $idutente
     */
    public function AssociaAcard($numAcard, $idutente){
    	$where = "ncard='$numAcard'";
    	$log = Zend_Registry::get("log");
    	$log->log( "(AssociaAcard) Associazione acard '$numAcard' all'utente con id: $idutente", Zend_Log::INFO);
    	return $this->update(array("iduser" => $idutente), $where);
    }
    
}

