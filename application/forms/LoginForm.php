<?php

class Application_Form_LoginForm extends Zend_Form
{

    public function __construct($options = null) {
        parent::__construct($options);
        
        $this->setName('login');
        
        $email = new Zend_Form_Element_Text("email");
        $email->setLabel("Email:")
                 ->setRequired()
                 ->setValue("");
        $password = new Zend_Form_Element_Password("password");
        $password->setLabel("Password:")
                ->setRequired()
                ->setValue("");
        
        $login = new Zend_Form_Element_Submit("login");
        $login->setLabel("Login");
        
        $this->addElements(array($email, $password, $login));
        $this->setMethod("post");
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl()."/authentication/login");
    }
    
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
    }


}

