<?php

class Application_Form_RegutenteForm extends Zend_Form
{

	public function __construct($options = null) {
        parent::__construct($options);
        
        $this->setName('regutente');
        
        $acard = new Zend_Form_Element_Text("acard");
        $acard->setLabel("Numero a/card*")
        ->setAttrib("size", 14)
        ->setAttrib("maxlength", 14)
        ->setRequired()
        ->setValue("")
        ->addValidator('regex', false, array('/^[0-9]/'))
        ->addErrorMessage('Inserire un numero di valido di a/card.');
        
        $cognomenome = new Zend_Form_Element_Text("cognomenome");
        $cognomenome->setLabel("Cognome e Nome*")
        ->setRequired()
        ->setValue("");
        
        $email = new Zend_Form_Element_Text("email");
        $email->setLabel("email*")
        ->setRequired()
        ->setValue("")
        ->addValidator('regex', false, array('/^[\w\-\.]*[\w\.]\@[\w\.]*[\w\-\.]+[\w\-]+[\w]\.+[\w]+[\w $]/'))
        ->addErrorMessage('Inserire un indirizzo email valido.');
        
        $email2 = new Zend_Form_Element_Text("confirmemail");
        $email2->setLabel("Conferma email*")
        ->setRequired()
        ->setValue("")
		->addValidator('Identical', false, array('token' => 'confirmemail'))
	    ->addErrorMessage('Le email non coincidono');        
        
        $password = new Zend_Form_Element_Password("password");
        $password->setLabel("Password*")
                ->setRequired()
                ->setValue("");
        
        $password2 = new Zend_Form_Element_Password("confirmpassword");
        $password2->setLabel("Conferma Password*")
	        ->setRequired()
	        ->setValue("")
	        ->addValidator('Identical', false, array('token' => 'password'))
	        ->addErrorMessage('Le password non coincidono');
        
        $cellulare = new Zend_Form_Element_Text("cellulare");
        $cellulare->setLabel("Numero di cellulare")
        ->setValue("");
        
        $captcha = new Zend_Form_Element_Captcha('foo', array(
		    'label' => "Insert captcha code",
		    'captcha' => 'Image',
			'captchaOptions' => array(
				'captcha' => 'Image',
				'wordLen' => 6,
				'timeout' => 300,
				'imgDir' => dirname( APPLICATION_PATH )."/images/captcha", // This folder has to have 777 permissions.
				'imgUrl' => Zend_Controller_Front::getInstance()->getBaseUrl().'/../images/captcha/',
				'width' => 200,
				'height' => 60,
				'font' => dirname( APPLICATION_PATH )."/font/ariali.ttf",
				//error message
				/*'messages' => array(
						'badCaptcha' => 'Il codice di controllo non corrisponde.'
				)*/
		    ),
		));
        
        $salva = new Zend_Form_Element_Submit("regutente");
        $salva->setLabel("Registra");
        
        $this->addElements(array($acard, $cognomenome, $email, $email2, $password, $password2, $cellulare, $captcha, $salva));
        $this->setMethod("post");
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl()."/registrazione/regutente");
    }
    
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
    }


}

	