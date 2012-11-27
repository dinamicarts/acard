<?php

class Application_Model_LibraryAcl extends Zend_Acl
{
    
    public function __construct() {
        $this->add(new Zend_Acl_Resource("index"));
        
        $this->add(new Zend_Acl_Resource("authentication"));
        $this->add(new Zend_Acl_Resource("login"), "authentication");
        $this->add(new Zend_Acl_Resource("logout"), "authentication");
        
        $this->add(new Zend_Acl_Resource("registrazione"));
        
        $this->add(new Zend_Acl_Resource("user"));
        
        $this->add(new Zend_Acl_Resource("users"));
        
        $this->add(new Zend_Acl_Resource("acard"));
        $this->add(new Zend_Acl_Resource("aggiungicarta"), "acard");
        $this->add(new Zend_Acl_Resource("elencoacard"), "acard");
        $this->add(new Zend_Acl_Resource("elencomovimenti"), "acard");
        $this->add(new Zend_Acl_Resource("insert"), "acard");
        
        $this->add(new Zend_Acl_Resource("menuadmin"));
        $this->add(new Zend_Acl_Resource("menusuperadmin"));
        
        $this->add(new Zend_Acl_Resource("database"));
        $this->add(new Zend_Acl_Resource("reset"));
        
        $this->add(new Zend_Acl_Resource("error"));
        
        //sito pubblico GUEST
        $this->add(new Zend_Acl_Resource("server"));
        $this->add(new Zend_Acl_Resource("import"));
		$this->add(new Zend_Acl_Resource("eraseall"));
        //
        
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('user'), 'guest');
        $this->addRole(new Zend_Acl_Role('admin'), 'user');
        $this->addRole(new Zend_Acl_Role('superadmin'), 'admin');
        
        $this->allow('guest', array('index','login','registrazione','error', 		'server','import'));
        $this->allow('guest', 'login');
        $this->deny('guest', 'logout');
        
        $this->allow('user', array(	'acard',
        							'authentication', 'logout'));
        $this->deny('user', array('login', 'registrazione', 'elencomovimenti'));
        
        $this->allow('admin', array('menuadmin',
        							'users',
        							'acard', 'elencoacard',
        							'authentication', 'logout'));
        
        if(strtolower($_SERVER['HTTP_HOST'])=="localhost"){
            //questo menu deve essere nascosto sul sito lato hosting
            $this->allow('superadmin', 'menusuperadmin');
        }
        
        $this->allow('superadmin', 'reset');
        
        
    }

}