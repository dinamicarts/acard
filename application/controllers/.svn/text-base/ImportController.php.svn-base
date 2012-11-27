<?php

class ImportController extends Zend_Controller_Action
{

    public function init()
    {
        /* questo controller utilizza un output diretto senza template*/
    	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function indexAction()
    {
        // action body
        echo ".";
    }

    /**
     * ricevo via post i dati e gli inserisco nel DB
     *
     *
     */
    public function dataimportAction()
    {
        if(strtolower($_SERVER['HTTP_HOST'])=="localhost")
            return; //questa funzione non deve essere eseguita sul sito lato server
        
        
        $log = Zend_Registry::get("log");
        $log->log('*** (dataimportAction) Inizio importazione dati', Zend_Log::INFO);
        
        try{
            $users = $this->_getParam("users");
            $users = Zend_Json::decode(str_ireplace('\"', '"', $users));
            $acard = $this->_getParam("acard");
            $acard = Zend_Json::decode(str_ireplace('\"', '"', $acard));
            $transaz = $this->_getParam("transaz");
            $transaz = Zend_Json::decode(str_ireplace('\"', '"', $transaz));

            $tableUsers = new Application_Model_DbTable_Users();
            $tableAcard = new Application_Model_DbTable_Acard();
            $tableTransaz = new Application_Model_DbTable_Transaz();
            if(count($users)>0){
                foreach ($users as $row){
                    unset ($row["sync"]);
                    $row["sync"] = date("Y-m-d H:i:s");
                    $tableUsers->insert($row);
                }
                $log->log('*** (dataimportAction) Fine importazione dati: ' . count($users) . " users, ", Zend_Log::INFO);
            }
            if(count($acard)>0){
                foreach ($acard as $row){
                    unset ($row["sync"]);
                    $row["sync"] = date("Y-m-d H:i:s");
                    $tableAcard->insert($row);
                }
                $log->log('*** (dataimportAction) Fine importazione dati: ' . count($acard) . " acard, ", Zend_Log::INFO);
            }
            if(count($transaz)>0){
                foreach ($transaz as $row){
                    unset ($row["sync"]);
                    $row["sync"] = date("Y-m-d H:i:s");
                    $tableTransaz->insert($row);
                }
                $log->log('*** (dataimportAction) Fine importazione dati: ' . count($transaz) . " trasazioni, ", Zend_Log::INFO);
            }
            
            echo "OK!"; //*** QUESTO E' IMPORTANTE! è la conferma inviata al sito amministrativo che i dati sono ok.
        }
        catch(Exception $e) {
            $log->log('Caught exception (dataimportAction): '.  $e->getMessage(),Zend_Log::ERR);
        }
        
    }

    public function eraseallAction()
    {
        if(strtolower($_SERVER['HTTP_HOST'])=="localhost")
            return; //questa funzione non deve essere eseguita  sul sito lato server
        
        $config = Zend_Registry::get("config");
        if($this->_getParam("pwd")==$config->server->communicationpwd){
            //$users = new Application_Model_DbTable_Users();
            //$users->trunc();
            
            $transaz = new Application_Model_DbTable_Transaz();
            $transaz->trunc();
            
            $acard = new Application_Model_DbTable_Acard();
            $acard->trunc();
            
            $log = Zend_Registry::get("log");
            $log->log('*** (eraseallAction) Cancellazione dati eseguita', Zend_Log::INFO);
            echo "OK!"; //*** QUESTO E' IMPORTANTE! è la conferma inviata al sito amministrativo che i dati sono ok.
        }
        else {
            echo "KO!";
        }
    }


}











