<?php

class UsersController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $utenti = new Application_Model_DbTable_Users();
        $this->view->utenti = $utenti->fetchAll(null, "ragionesociale");
    }


}

