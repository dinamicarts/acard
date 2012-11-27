<?php

class IndexController extends Zend_Controller_Action
{

    protected $session;
    
    public function init()
    {
        $this->config = Zend_Registry::get("config");
    }
    
    public function preDispatch()
    {
        $this->session = new Zend_Session_Namespace('Default');
    }

    public function indexAction()
    {
        echo "APPLICATION_ENV: ". APPLICATION_ENV;
    }


}



