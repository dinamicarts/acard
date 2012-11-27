<?php

class AuthenticationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function loginAction()
    {
        if(Zend_auth::getInstance()->hasIdentity()){
            $this->_redirect("index/index");
            return;
        }
        
        $request = $this->getRequest();
        $form = new Application_Form_LoginForm();

        if($request->isPost()){
            if($form->isValid($this->_request->getPost())){

                $authAdapter = $this->getAuthAdapter();

                $email = $form->getValue("email");
                $password = $form->getValue("password");

                $authAdapter->setIdentity($email)
                            ->setCredential($password);

                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                if($result->isValid())
                {
                    $identity = $authAdapter->getResultRowObject();

                    $authStorage = $auth->getStorage();
                    $authStorage->write($identity);

                    $this->_redirect('index/index');
                }
                else
                {
                	//verifico se l'account deve ancora essere attivato
                	$tblUserstmp = new Application_Model_DbTable_Userstmp();
                	if($tblUserstmp->DammiUtenteTmp($email)){
                		echo "L'account non &egrave; ancora stato attivato.<br/>";
                		echo "Controlla la posta elettronica e segui le istruzioni che ti abbiamo inviato.<br/><br/>";
                		echo "Se non hai ricevuto l'email controlla nella cartella dello spam, eventualmente</br>";
                		echo "<a href='".Zend_Controller_Front::getInstance()->getBaseUrl()."/registrazione/resend/email/$email'>clicca qui per ricevere nuovamente l'email</a>";
                	}   
                	else {     	
                		echo "Combinazione email/password errata.";
                    	//$this->_redirect('authentication/login');
                	}
                }

            }
        }
        
        $this->view->form = $form;
                
        
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect("index/index");
    }
    
    private function getAuthAdapter()
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('users')
                    ->setIdentityColumn('email')
                    ->setCredentialColumn('pwd')
                    ->setCredentialTreatment('MD5(?)');
        
        return $authAdapter;
    }


}
