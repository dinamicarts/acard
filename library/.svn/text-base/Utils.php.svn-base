<?php

class Application_Utils {
    
    public function CheckAcard($ncard, $ragionesoaicale){
	    $arrResp = array();
	    $ragsoc = strtolower($ragionesoaicale);
	    $ragsoc = str_replace("'", " ", $ragsoc); //elimino eventuali apostrofi/accenti
	     
	    $arrResp["acard"] = 'KO';
	    $ragioneOK = false;
	     
	    if(isset($ncard)){
	    	$acard = new Application_Model_DbTable_Acard();
	    	$where = "ncard='$ncard'";
	    	$ris = $acard->fetchRow($where);
	    
	    	if(count($ris)>0){
	    		$arrResp["acard"] = 'OK';
	    		 
	    		//ora verifico la corrispondenza della ragione sociale
	    		//verifico che la ragione sociale comprenda il cognome, il nome e che non avanzino pi di 3 caratteri
	    		if(isset($ragsoc)){
	    
	    			$ragioneDaValidare = "-".strtolower($ris->ragionesociale);
	    			$ragioneDaValidare = str_replace("  ", " ", $ragioneDaValidare); //elimino eventuali spazi eccedenti
	    			$ragioneDaValidare = str_replace("'", " ", $ragioneDaValidare); //elimino eventuali apostrofi/accenti
	    				
	    			$arrRagioneSoc = explode(" ", $ragsoc);
	    			$ragioneOK = true;
	    			foreach($arrRagioneSoc as $k => $v){
	    					
	    				if($v && !strpos($ragioneDaValidare, $v)){
	    					$ragioneOK = false;
	    					break;
	    				}
	    				//elimino la stringa appena cercata
	    				$ragioneDaValidare = str_replace($v, "", $ragioneDaValidare);
	    
	    			}
	    			$carResidui = strlen($ragioneDaValidare)-1;
	    			if($carResidui>3)
	    				$ragioneOK = false;
	    		}
	    		 
	    	}
	    }
	     
	    $arrResp["ragsoc"] = $ragioneOK ? 'OK' : 'KO';
	    return $arrResp;
    }
    
    /**
     * Verifico se esiste giˆ l'utente nelle tabelle users o userstmp
     * @param string $email
     */
    public function EmailExists($email){
    	$tblUsers = new Application_Model_DbTable_Users();
    	$res = $tblUsers->DammiUtente($email);
    	
    	if($res==NULL) {
	    	$tblUsers = new Application_Model_DbTable_Userstmp();
    		$res = $tblUsers->DammiUtenteTmp($email);
    	}
    	
    	return $res!=NULL;
    }
    
    /**
     * 
     * @param array $emailDestinatari
     * @param string $oggetto
     * @param string $messaggio
     */
    public function SendMail($emailDestinatari, $oggetto, $messaggio){
    	$mail = new Zend_Mail();
    	$mail->setBodyText($messaggio);
    	$mail->setFrom("no-reply@acard.it", "Arteni a/card");
    	foreach($emailDestinatari as $k => $v)
    		$mail->addTo($v);
    	$mail->setSubject($oggetto);
    	return $mail->send();
    }
    
    public function GenerateKey() {
    	$k = '';
    	for($i = 0; $i < 6; $i ++) {
    		$k .= chr(mt_rand(97, 122));
    		$k .= chr(mt_rand(65, 90));
    		$k .= chr(mt_rand(48, 57));
    	}
    	return $k;
    }
    
    
}

?>
