<?php

class Application_Model_DbTable_Configpunti extends Zend_Db_Table_Abstract
{

    protected $_name = 'configpunti';

    /**
     *
     * @param float $importo
     * @param datatime $data
     * @return float
     * Dato un importo in euro ed una data, legge la configurazione nel DB e ritorna il numero di punti assegnati
     * per il giorno/ora specifici
     */
    public function DammiPuntiGiornata($importo, $data){
        $res = $this->fetchRow("datainizio <= '$data'", "datainizio DESC");
        $punti = $res["punti_euro"] * round($importo);
        return $punti;
    }
}

