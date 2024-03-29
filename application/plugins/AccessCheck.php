<?php

class Plugin_AccessCheck extends Zend_Controller_Plugin_Abstract{
    
    private $_acl = null;
    private $_auth = null;
    
    public function __construct(Zend_Acl $acl, Zend_Auth $auth) {
        $this->_acl = $acl;
        $this->_auth = $auth;
    }
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        
        //return; //to do questo non va!!!!
        $resource = $request->getControllerName();
        $action = $request->getActionName();
        $role = Zend_Registry::get("role");
        $allowed = $this->_acl->isAllowed($role, $resource, $action);
        
        if(!$allowed){
            $request->setControllerName('authentication')
                    ->setActionName('login');
       }
        
    }
    
}
