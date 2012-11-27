<?php

class Application_Model_DbTable_Users extends Zend_Db_Table_Abstract
{

    protected $_name = 'users';

    public function DammiUtente($email){
    	$where = "email LIKE '$email'";
    	return $this->fetchRow($where);
    }
    
    public function inserisci($dati){
    	
    }
    
}

