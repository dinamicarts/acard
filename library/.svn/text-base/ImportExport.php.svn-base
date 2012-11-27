<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileImport
 *
 * @author luca
 */
class Application_ImportExport {
    
    
    protected function __construct() {
        
    }

    /**
     * Ciclo su tutti i file contenuti nella directory "_upload"
     * ed importo i file delle transazioni
     */
    public static function ImportaTutto(){
        $config = Zend_Registry::get("config");
        if(strtolower($_SERVER['HTTP_HOST'])!="localhost")
            return; //questa funzione non deve essere eseguita sul sito lato pubblico
        
        try{
            $log = Zend_Registry::get("log");
            $log->log('(ImportaTutto) Inizio importazione transazioni acard', Zend_Log::INFO);

            $config = Zend_Registry::get("config");
            $directory = APPLICATION_PATH . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . $config->importdir;
            $msgs = array(); 
            if (is_dir($directory)) {
                if ($directory_handle = opendir($directory)) {
                    while (($file = readdir($directory_handle)) !== false) {
                        $path_info = pathinfo($file);
                        if(array_key_exists('extension', $path_info) && 
                                strtolower($path_info['extension'])=="txt" &&
                                strtolower($file)!="end.txt"){

                            //verifico che il file termini correttamente, quindi procedo all'importazione
                            if(Application_ImportExport::CheckEofFile($directory . DIRECTORY_SEPARATOR . $file))
                               $msgs[] = Application_ImportExport::ImportaFile($directory . DIRECTORY_SEPARATOR . $file);
                        }

                    }
                    closedir($directory_handle);
                }
            }
            $log->log('(ImportaTutto) Fine importazione transazioni acard', Zend_Log::INFO);

            return $msgs;
        }
        catch(Exception $e) {
            $log->log('Caught exception (RisincronizzaDatiRemoti): '.  $e->getMessage(),Zend_Log::ERR);
        }
    }
    
    /**
     *
     * @param string $fullpath_filename
     * @param string $new_extension
     * @return string 
     * Sostituisce l'estensione del file (ultimi 3 caratteri)
     */
    private static function replace_extension($fullpath_filename, $new_extension) {
        $withoutExt = substr($fullpath_filename, 0 , -3);
        return $withoutExt . $new_extension;
    }
    
    /**
     *
     * @param string $fullpath_filename
     * @return Bool 
     * Verifica che il file contenga la stringa *EOF
     */
    private static function CheckEofFile($fullpath_filename){
        //verifico che il file contenga la stringa *END
        $testo = file_get_contents($fullpath_filename, true);
        $config = Zend_Registry::get("config");
        $pos = stripos(strtolower($testo), strtolower($config->acard->eof));
        return $pos>0;
    }
    
    /**
     *
     * @param string $fullpath_filename 
     * Legge il file di testo ed inserisce i record nel DB verificando prima che il record non esista già.
     */
    private static function ImportaInDB($fullpath_filename){
        $config = Zend_Registry::get("config");
        if(strtolower($_SERVER['HTTP_HOST'])!="localhost")
            return; //questa funzione non deve essere eseguita sul sito lato pubblico
        
        //leggo le singole righe del file
        try{
            $log = Zend_Registry::get("log");
            $config = Zend_Registry::get("config");
            $lines = file($fullpath_filename, FILE_SKIP_EMPTY_LINES);


            $configPunti = new Application_Model_DbTable_Configpunti();
            $transaz = new Application_Model_DbTable_Transaz();
            $acard = new Application_Model_DbTable_Acard();
            
            $acardImportate = 0;
            $acardDuplicate = 0;
            $transazImportate = 0;
            $transazDuplicate = 0;
            foreach ($lines as $line_num => $line) {
                try{
                    $line = trim($line);

                    if(strlen($line)>20 && substr($line,0,1)!="*"){
                        //echo "$line<br>";
                        $arrayDataRow = explode("#", $line);
                        $log->log('(ImportaInDB) read line: '. $line ,Zend_Log::INFO);
                        
                        $dtNascita = $arrayDataRow[$config->acard->DataNascita];
                        if(strlen($dtNascita)<6){
                            //throw new Exception("Data di nascita non valida: " . $dtNascita. " - record non importato: " . $line);
                            $log->log('(ImportaInDB) Data di nascita non valida (record importato ugualmente): '. $line ,Zend_Log::WARN);
                            $dtNascita = "19000101";
                        }
                        $dtNascita = date_create_from_format('Ymd', $dtNascita)->format('Y-m-d');
                        
                        $dtAcquisto = $arrayDataRow[$config->acard->DataOraAcquisto];
                        if(strlen($dtAcquisto)<6)
                            throw new Exception("Data di acquisto non valida: " . $dtAcquisto. " - record non importato: " . $line);
                        $dtAcquisto = date_create_from_format('YmdHi', $dtAcquisto)->format('Y-m-d H:i:s');
                        
                        $dtTransaz = $arrayDataRow[$config->acard->DataOraTransazione];
                        if(strlen($dtTransaz)<6){
                            //throw new Exception("Data transazione non valida: " . $dtTransaz. " - record non importato: " . $line);
                            $log->log('(ImportaInDB) Data trasazione non valida (record importato ugualmente): '. $line ,Zend_Log::WARN);
                            $dtTransaz = "19000101";
                        }
                        $dtTransaz = date_create_from_format('YmdHi', $dtTransaz)->format('Y-m-d H:i:s');

                        $importo = floatval(str_replace(",",".",$arrayDataRow[$config->acard->Importo]));
                        $puntiAssegnati = $configPunti->DammiPuntiGiornata($importo, $dtAcquisto);

                        //eventuale inserimento della a/card associata all'anagrafica
                        $resInsCard = $acard->inserisci(array(
                            	"ncard"         	=> $arrayDataRow[$config->acard->NumAcard],
	                            "codcliente"        => $arrayDataRow[$config->acard->CodCliente],
	                            "ragionesociale"    => $arrayDataRow[$config->acard->RagioneSociale],
	                            "datanascita"       => $dtNascita,
	                            "sesso"             => $arrayDataRow[$config->acard->Sesso]
                        ));
                        if($resInsCard["inserito"])
                            $acardImportate++;
                        else
                            $acardImportate++;

                        $resInsTransaz = $transaz->inserisci(array(
                            "codcliente"        => $arrayDataRow[$config->acard->CodCliente],
                            "ncard"             => $arrayDataRow[$config->acard->NumAcard],
                            "ragionesociale"    => $arrayDataRow[$config->acard->RagioneSociale],
                            "datanascita"       => $dtNascita,
                            "sesso"             => $arrayDataRow[$config->acard->Sesso],
                            "puntovendita"      => $arrayDataRow[$config->acard->PuntoVendita],
                            "codcassa"          => $arrayDataRow[$config->acard->NumeroCassa],
                            "nscontrino"        => $arrayDataRow[$config->acard->NumeroScontrino],
                            "dataoraacquisto"   => $dtAcquisto,
                            "importo"           => $importo,
                            "dataoratransazione"=> $dtTransaz,
                            "puntiassegnati"    => $puntiAssegnati
                        ));
                        if($resInsTransaz["inserito"])
                            $transazImportate++;
                        else
                            $transazDuplicate++;

                    }

                    if(strtolower($line) == strtolower($config->acard->eof))
                        break;
                }
                catch (Exception $e){
                    $log->log('Caught exception (ImportaInDB): '.  $e->getMessage(),Zend_Log::ERR);
                }
            }

            $path_parts = pathinfo($fullpath_filename);
            $msg = "(ImportaInDB) Esito importazione file ".$path_parts["filename"].": ".
                   "a/card inserite/gi&agrave; presenti: $acardImportate/$acardDuplicate - ".
                   "Transazioni inserite/gi&agrave; presenti: $transazImportate/$transazDuplicate";
            $log->log($msg, Zend_Log::INFO);
            $msg = "<b>Esito importazione file ".$path_parts["filename"]."</b><br>".
                   "a/card inserite/gi&agrave; presenti: $acardImportate/$acardDuplicate<br>".
                   "Transazioni inserite/gi&agrave; presenti: $transazImportate/$transazDuplicate<br>";
            return $msg;
        }
        catch(Exception $e) {
            $log->log('Caught exception (ImportaInDB): '.  $e->getMessage(),Zend_Log::ERR);
        }
    }

    /**
     *
     * @param string $fullpath_filename 
     * Lavora il file da importare:
     *  - Rinomina il file in .tmp
     *  - Richiama la funzione di inserimento in DB
     *  - Sposta il file nella cartella di output
     */
    public static function ImportaFile($fullpath_filename){
        if(strtolower($_SERVER['HTTP_HOST'])!="localhost")
            return; //questa funzione non deve essere eseguita sul sito lato pubblico
        
        $log = Zend_Registry::get("log");
        $log->log('(ImportaFile) Importazione del file ' . $fullpath_filename, Zend_Log::INFO);
  
        //rinomino il file in tmp
        $tmp_name = Application_ImportExport::replace_extension($fullpath_filename, "tmp");
        rename($fullpath_filename, $tmp_name);
        
        //leggo il file
        $msg = Application_ImportExport::ImportaInDB($tmp_name);
        
        //sposto il file già lavorato
        $config = Zend_Registry::get("config");
        $outPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . $config->outputdir;
        $path_parts = pathinfo($fullpath_filename);
        $newFullpathName = $outPath . DIRECTORY_SEPARATOR . $path_parts["basename"];
        rename($tmp_name, $newFullpathName);
        $log->log('File ' . $path_parts["basename"] . ' spostato nella cartella ' . $outPath, Zend_Log::INFO);
        
        return $msg;
    }
    
    
    
    private static function ExportGroup($arrDati, $howMany, $tipoDato){
        if(strtolower($_SERVER['HTTP_HOST'])!="localhost")
            return;
        
        try{
            $log = Zend_Registry::get("log");
            $config = Zend_Registry::get("config");
            $url = $config->publicsite . "/public/import/dataImport";
            $client = new Zend_Http_Client($url);
            $client->setConfig(array(
               "timeout"    => 180 
            ));
            $client->setMethod(Zend_Http_Client::POST);
            $totRecords = count($arrDati);
            $contatore = 0;
            while(count($arrDati)>0){
                $tmpArray = array();
                for($x=0; $x<$howMany; $x++){
                    $contatore++;
                    $tmpArray[] = array_pop($arrDati);
                    if(count($arrDati)==0)
                        break;
                }
                $rowsJson = json_encode($tmpArray);
                $client->setParameterPost(array(
                    $tipoDato   => $rowsJson
                ));
                $log->log("(ExportGroup) Post parziale (max=$howMany) dei dati su '$url' => $tipoDato= $contatore/$totRecords", Zend_Log::INFO);
                if(count($tmpArray)>0){
                    $client->request();
                    if ($client->getLastResponse()->isSuccessful()) {
                        $response = $client->getLastResponse()->getBody();
                        if($response=="OK!"){
                            $log->log("(ExportGroup) Esportazione parziale terminata correttamente", Zend_Log::INFO);
                        }
                        else {
                            $log->log("(ExportGroup) Si &egrave; verificato un errore durante l'esportazione parziale dei dati. Response: $response", Zend_Log::ERR);
                        }
                    }
                    else {
                        $log->log("(ExportGroup) Chiamata al sito pubblico (".$config->publicsite.") non riuscita", Zend_Log::WARN);
                    }
                }
            }
        }
        catch (Exception $e){
            $log->log('Caught exception (ExportGroup): '.  $e->getMessage(),Zend_Log::ERR);
            return false;
        }
        
        return true;
        
    }
    
    
    /**
     * Esporta i dati nelle rispettive tabella del db online
     */
    public static function ExportAll()
    {
    	$config = Zend_Registry::get("config");
        if(strtolower($_SERVER['HTTP_HOST'])!="localhost")
            return;
        
        try{
            $log = Zend_Registry::get("log");
            $log->log('(exportallAction) Inizio esportazione dati su sito pubblico  ('.$config->publicsite.')', Zend_Log::INFO);

            $where = "sync IS NULL";
            
            $log->log('(exportallAction) Esporto Utenti ', Zend_Log::INFO);
            $tableUsers = new Application_Model_DbTable_Users();
            $rowsUsers = $tableUsers->fetchAll($where);
            $arrUsers = $rowsUsers->toArray();
            $nUsers = count($arrUsers);  
            if(Application_ImportExport::ExportGroup($arrUsers, 50, "users"))
                Application_ImportExport::setSyncField($tableUsers, $rowsUsers);
            unset($arrUsers);
            unset($tableUsers);
            unset($rowsUsers);
            
            
            $log->log('(exportallAction) Esporto a/card ', Zend_Log::INFO);
            $tableAcard = new Application_Model_DbTable_Acard();
            $rowsAcard = $tableAcard->fetchAll($where);
            $arrAcard = $rowsAcard->toArray();
            $nAcard = count($arrAcard);  
            if(Application_ImportExport::ExportGroup($arrAcard, 50, "acard"))
                Application_ImportExport::setSyncField($tableAcard, $rowsAcard);
            unset($rowsAcard);
            unset($tableAcard);
            unset($rowsAcard);
            
            $log->log('(exportallAction) Esporto Transazioni ', Zend_Log::INFO);
            $tableTransaz = new Application_Model_DbTable_Transaz();
            $rowsTransaz = $tableTransaz->fetchAll($where);
            $arrTransaz = $rowsTransaz->toArray();
            $nTransaz = count($arrTransaz);  
            if(Application_ImportExport::ExportGroup($arrTransaz, 50, "transaz"))
                Application_ImportExport::setSyncField($tableTransaz, $rowsTransaz);
            unset($arrTransaz);
            unset($tableTransaz);
            unset($rowsTransaz);
            
        }
        catch(Exception $e) {
            $log->log('Caught exception (exportallAction): '.  $e->getMessage(),Zend_Log::ERR);
        }
        $log->log('(exportallAction) Fine esportazione dati su sito pubblico  ('.$config->publicsite.')', Zend_Log::INFO);

    }
    
    private static function setSyncField($table, $rows){
        foreach($rows as $row){
            $dati = array(
                "sync"  => date('Y-m-d H:i:s')
            );
            $where = $table->getAdapter()->quoteInto('id = ?', $row["id"]);
            $table->update($dati, $where);
        }
    }
    
    private static function delSyncField($table, $rows){
        foreach($rows as $row){
            $dati = array(
                "sync"  => null
            );
            $where = $table->getAdapter()->quoteInto('id = ?', $row["id"]);
            $table->update($dati, $where);
        }
    }
    
    
    public static function EraseAllOnRemote(){
    	$config = Zend_Registry::get("config");
        if(strtolower($_SERVER['HTTP_HOST'])!="localhost")
            return;
        
        try{
            $log = Zend_Registry::get("log");
            $log->log('(EraseAllOnRemote) Invio comando di svuotamento DB a sito pubblico  ('.$config->publicsite.')', Zend_Log::INFO);
            $url = $config->publicsite . "/public/import/eraseall";
            
            $client = new Zend_Http_Client($url);
            $client->setConfig(array(
               "timeout"    => 180 
            ));
            $client->setMethod(Zend_Http_Client::POST);
            $client->setParameterPost(array(
                "pwd"   => $config->server->communicationpwd
            ));
            $client->request();
            if ($client->getLastResponse()->isSuccessful()) {
                $response = $client->getLastResponse()->getBody();
                if($response=="OK!"){
                    $log->log("(EraseAllOnRemote) Proc. di cancellazione dati dal sito pubblico (".$config->publicsite.") terminata correttamente", Zend_Log::INFO);
                }
                else {
                    $log->log("(EraseAllOnRemote) Si &egrave; verificato un errore durante la cancellazione dati dal sito pubblico (".$config->publicsite."). Response=$response", Zend_Log::ERR);
                }
            }
            else {
                $log->log("(EraseAllOnRemote) Chiamata al sito pubblico  (".$config->publicsite.") non riuscita", Zend_Log::WARN);
            }
            
        }
        catch(Exception $e) {
            $log->log('Caught exception (EraseAllOnRemote): '.  $e->getMessage(),Zend_Log::ERR);
        }
    }
    
    /**
     * Cancella il DB remoto, resetta il campo "sync" nel db locale e reinvia tutti i dati
     */
    public static function RisincronizzaDatiRemoti(){
        if(strtolower($_SERVER['HTTP_HOST'])!="localhost")
            return;
            
        try {
            $log = Zend_Registry::get("log");

            Application_ImportExport::EraseAllOnRemote();
            $log->log("(RisincronizzaDatiRemoti) Cancellati dati da DB remoto", Zend_Log::INFO);

            $tableUsers = new Application_Model_DbTable_Users();
            $rowsUsers = $tableUsers->fetchAll();
            Application_ImportExport::delSyncField($tableUsers, $rowsUsers);
            unset($rowsUsers);
            
            $tableAcard = new Application_Model_DbTable_Acard();
            $rowsAcard = $tableAcard->fetchAll();
            Application_ImportExport::delSyncField($tableAcard, $rowsAcard);
            unset($rowsAcard);
            
            $tableTransaz = new Application_Model_DbTable_Transaz();
            $rowsTransaz = $tableTransaz->fetchAll();
            Application_ImportExport::delSyncField($tableTransaz, $rowsTransaz);
            unset($rowsTransaz);
            
            $log->log("(RisincronizzaDatiRemoti) Rimosso il valore 'sync' da db locale", Zend_Log::INFO);

            $log->log("(RisincronizzaDatiRemoti) Reinvio dei dati a remoto ", Zend_Log::INFO);
            Application_ImportExport::ExportAll();
        }
        catch(Exception $e) {
            $log->log('Caught exception (RisincronizzaDatiRemoti): '.  $e->getMessage(),Zend_Log::ERR);
        }
    }
    
}
