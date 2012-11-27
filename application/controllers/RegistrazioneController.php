<?php

class RegistrazioneController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        
    }

    public function regutenteAction()
    {
    	$log = Zend_Registry::get("log");
    	
    	$request = $this->getRequest();
    	$form = new Application_Form_RegutenteForm();
    	
    	if($request->isPost()){
    		if($form->isValid($this->_request->getPost())){
    	
    			$acard = $form->getValue("acard");
    			$ragsoc = $form->getValue("cognomenome");
    			$cellulare = $form->getValue("cellulare");
    			$password = $form->getValue("password");
    			$email = $form->getValue("email");
    			
    			$ancoraErrori = false;
    			
    			//controllo i dati di registrazione della acard
    			$check = new Application_Utils();
    			$ris = $check->CheckAcard($acard, $ragsoc);
    			if(!($ris["acard"]=="OK" && $ris["ragsoc"]=="OK")){
    				echo "<script>ShowCheckAcardMessage('".json_encode($ris)."');</script>";
    				$ancoraErrori = true;
    			}
    			
    			//controllo che l'utente non esista giˆ
    			if($check->EmailExists($email)){
    				echo "<script>ShowCheckUserMessage('KO');</script>";
    				$ancoraErrori = true;
    			}
    			
    			if(!$ancoraErrori){
    				$log->log("regutenteAction: Richiesta registrazione: " . print_r($form->getValues(), true) ,Zend_Log::INFO);
    				
    				$tblNewUsers = new Application_Model_DbTable_Userstmp();
    				$datiInsert = $tblNewUsers->inserisci($form->getValues());
    				
    				$this->InviaMailRegistrazioneUtente($datiInsert["email"]);
    				$this->_redirect('registrazione/mailinviata');
    				return;	
    			}
    			else {
    				echo 
    				$log->log("regutenteAction: Dati a/card non validi: " . json_encode($ris) ,Zend_Log::INFO);
    			}
    			
    		}
    		else {
    			$log->log("regutenteAction: Form non valida: " . print_r($form->getValues(), true) ,Zend_Log::INFO);
    			$this->view->form = $form;
    		}
    	}
    	
    	$this->view->form = $form;$this->view->form = $form;
    	
    }

    private function InviaMailRegistrazioneUtente($email)
    {
    	$tblUsersTmp = new Application_Model_DbTable_Userstmp();
    	$usrtmp = $tblUsersTmp->fetchRow("email like '$email'");
    	
    	$emailDestinatari = array($usrtmp["email"]);
    	$oggetto = "Arteni a/card - Attivazione account";
    	 
    	$config = Zend_Registry::get("config");
    	$base = $config->publicsite;
    	 
    	$messaggio = "";
    	$messaggio .= $usrtmp["ragionesociale"].", questo indirizzo email e' stato utilizzato per la registrazione al sito Arteni a/card.\n\n";
    	$messaggio .= "Grazie per esserti registrato/a.\n\n";
    	$messaggio .= "Ti chiediamo di validare la tua registrazione per assicurarci che l'email inserita sia corretta.\n";
    	$messaggio .= "Per validare la tua registrazine ed attivare il tuo account e' sufficiente cliccare sul seguente link: \n\n";
    	$messaggio .= $base.Zend_Controller_Front::getInstance()->getBaseUrl()."/registrazione/attiva/k/".$usrtmp["k"]."\n\n";
    	$messaggio .= "(se dovessi riscontrare qualche problema, copia ed incolla il link direttamente nella barra indirizzi del tuo browser)\n\n";
    	$messaggio .= "Cordiali saluti,\n";
    	$messaggio .= "Arteni Confezioni Spa\n";
    
    	$res = Application_Utils::SendMail($emailDestinatari, $oggetto, $messaggio);
    	
    	$log = Zend_Registry::get("log");
    	$log->log( "InviaMailRegistrazioneUtente: Mail inviata a ".$usrtmp["email"], Zend_Log::INFO);
    	
    	return $res;
    }

    public function mailinviataAction()
    {
        // action body
    }

    public function attivaAction()
    {
       $key = $this->_getParam("k");
       $tblUsers = new Application_Model_DbTable_Userstmp();
       $tblUsers->AttivaAccount($key);
       $this->_redirect('registrazione/accountattivo');
    }

    public function accountattivoAction()
    {
        // action body
    }

    public function resendAction()
    {
        $email = $this->_getParam("email");
        if($email){
        	$this->InviaMailRegistrazioneUtente($email);
        	$this->_redirect('registrazione/mailreinviata');
    		return;	
        }
        echo "Si &egrave; verificato un errore.";
    }

    public function mailreinviataAction()
    {
        // action body
    }


}













