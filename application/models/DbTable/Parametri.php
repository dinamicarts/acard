<?php

class Application_Model_DbTable_Parametri extends Zend_Db_Table_Abstract
{

    protected $_name = 'parametri';

	public function GetDBVersion(){
		try{
			$where = "codparametro='dbversion'";
			$res = $this->fetchRow($where);
			return $res["valore"];
		}
		catch (Exception $e){
			return null;
		}
	}
	
	public function SetDBVersion($intNumber){
		$where = "codparametro='dbversion'";
		$res = $this->update(array("valore"=>$intNumber), $where);
	}
}

