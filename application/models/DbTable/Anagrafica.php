<?php

class Application_Model_DbTable_Anagrafica extends Zend_Db_Table_Abstract
{

    protected $_name = 'anagrafica';

    /**
     *
     * @param array ["codcliente","ncard","ragionesociale","datanascita","sesso"]
     * @return array ["id","inserito"]
     * Inserisce l'anagrafica cliente se non esiste già (ricerca per codcliente)
     * Ritorna l'id dell'anagrafica trovata o inserita. 
     */
    public function inserisci($dati)
    {
        //verifico se esiste già
        $res = $this->fetchRow("codcliente LIKE '" . $dati["codcliente"] . "'");
        
        $result = array(
            "id"        => -1,
            "inserito"  => false,
        );
        
        if($res==NULL)
        {
            $id = $this->insert($dati);
            $log = Zend_Registry::get("log");
            /*$log->log(
                    "Nuova anagrafica inserita: " . 
                    " ID=" . $id . " - " .
                    " Ragione sociale=" . $dati["ragionesociale"] . " - " .
                    " Cod. cliente=" . $dati["codcliente"]
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
}

