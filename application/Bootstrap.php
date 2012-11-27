<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    private $_acl = null;
    private $_auth = null;
    
    protected function _initLanguage()
    {
    	$localeValue = 'it';

        $locale = new Zend_Locale($localeValue);
        Zend_Registry::set('Zend_Locale', $locale);
        $translationPath = dirname( APPLICATION_PATH ) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . $localeValue;

        $translate = new Zend_Translate('array', $translationPath, $localeValue);

        Zend_Registry::set('Zend_Translate', $translate);
        Zend_Validate_Abstract::setDefaultTranslator($translate);
        Zend_Form::setDefaultTranslator($translate);
    }
    
    protected function _initAppAutoload()
    {
    	$moduleLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH
        ));
        
        if(Zend_Auth::getInstance()->hasIdentity()){
            Zend_Registry::set('role', Zend_Auth::getInstance()->getStorage()->read()->role);
            Zend_Registry::set('userinfo', Zend_Auth::getInstance()->getStorage()->read());
        }
        else{
            Zend_Registry::set('role', 'guest'); 
        }
        
        $moduleLoader->addResourceType('library', 'library/', 'Library');
        
        $this->_acl = new Application_Model_LibraryAcl();
        $this->_auth = Zend_Auth::getInstance();
        
        $fc = Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new Plugin_AccessCheck($this->_acl, $this->_auth));
        
        return $moduleLoader;
    }
    
    
    protected function _initNavigator()
    {
		$this->bootstrap("layout");
		$layout = $this->getResource("layout");
		$view = $layout->getView();
		
        $navContainerConfig = new Zend_Config_Xml(APPLICATION_PATH . "/configs/navigation.xml", "nav");
		$navContainer = new Zend_Navigation($navContainerConfig);
		
        $view->navigation($navContainer)->setAcl($this->_acl)->setRole(Zend_Registry::get("role"));
    }
    
}

