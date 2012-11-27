<?php

class Application_Model_DbTable_Transaz extends Zend_Db_Table_Abstract
{

    protected $_name =  "transaz";

    /**
     *
     * @param array ["codcliente","ncard" ,"ragionesociale","datanascita","sesso","puntovendita","codcassa","nscontrino", "dataoraacquisto","importo","dataoratransazione","puntiassegnati"]
     * @return array ["id","inserito"]
     * Inserisce l'eventuale transazione verificando che non esista già.
     * Ritorna l'id della transazione oppure null se esiste già
     */
    public function inserisci($dati)
    {
        //verifico se esiste già
        $res = $this->fetchRow("ncard='" . $dati["ncard"] . "' AND dataoraacquisto='".$dati["dataoraacquisto"] . "' AND importo='".$dati["importo"]."'");
        
        $result = array(
            "id"        => -1,
            "inserito"  => false,
        );
        
        if($res==NULL)
        {
            $id = $this->insert($dati);
            $log = Zend_Registry::get("log");
            /*$log->log(
                    "Nuova transazione inserita: " . 
                    " ID=" . $id . " - " .
                    " NCard=" . $dati["ncard"] . " - " .
                    " Ragione Rociale=" . $dati["ragionesociale"] . " - " .
                    " Punto vendita=" . $dati["puntovendita"] . " - " .
                    " Dataora acquisto=" . $dati["dataoraacquisto"] . " - " .
                    " Importo=" . $dati["importo"]
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
     * @param string $where "codcliente=?"
     * @return array ["anagrafica", "saldo"]
     * Data la condizione Where, ritorna il saldo dei punti
     */
    public function saldoPunti($where){
        $punti = 0;
        $res = $this->fetchAll($where);
        foreach ($res as $row){
            $punti += floatval($row["puntiassegnati"]);
        }
        
        return $punti;
    }
    
}

