<?php

class DbupdaterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    /**
     * Legge il file dbupdate.ini ed esegue le query in modo da aggiornare il DB all'ultima versione
     */
    public static function UpdateDBToCurrentVersion(){
    	$tblParams = new Application_Model_DbTable_Parametri();
    	$currVersion = $tblParams->GetDBVersion();
    	
    	$dbAdapter = Zend_Db_Table::getDefaultAdapter();
    	
    	$log = Zend_Registry::get("log");
    	
    	$stop = false;
    	while(!$stop){
	    	try{
	    		if($currVersion===NULL)
		    		$currVersion = 0;
		    	else 
		    		$currVersion++;
	    		$dbAdapter->beginTransaction();
	    		$updates = new Zend_Config_Ini(APPLICATION_PATH . "/configs/dbupdate.ini", "version$currVersion");
	    		$queries = $updates->queries;
	    		$queryArray = explode(";", trim($queries));

	    		$log->log("*** (UpdateDBToCurrentVersion) Inizio aggiornamento database a versione $currVersion", Zend_Log::INFO);
	    		foreach($queryArray as $k => $v){
	    			$v=trim($v);
	    			if($v){
	    				$dbAdapter->query($v);
	    				$log->log("- query: $v", Zend_Log::INFO);
	    			}
	    		}
	    		$dbAdapter->commit();
	    		if($currVersion!=0)
		    		$tblParams->SetDBVersion($currVersion);
	    		$log->log("*** (UpdateDBToCurrentVersion) Database aggiornato alla versione $currVersion", Zend_Log::INFO);
	    	}
	    	catch (Exception $e){
	    		//$log->log("*** (UpdateDBToCurrentVersion) Errore $e", Zend_Log::INFO);
	    		$dbAdapter->rollBack();
	    		$stop=true;
	    		break;
	    	}
	    	
    	}
    }

}

