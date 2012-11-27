<?php

class Application_Model_DbTable_Acards extends Zend_Db_Table_Abstract
{

    protected $_name = 'acards';

    /**
     *
     * @param array ["idanagrafica", "ncard", "codcliente"]
     * @return array ["id","inserito"]
     * Inserisce la ACARD cliente se non esiste già (ricerca per ncard)
     * Ritorna l'id della carta trovata o inserita. 
     */
    public function inserisci($dati)
    {
        //verifico se esiste già
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
}

