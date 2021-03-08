<?php
namespace controllers;

 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\controllers\auth\AuthController;
 use Ubiquity\controllers\auth\WithAuthTrait;

 /**
 * Controller MainController
 **/

class MainController extends ControllerBase{
    use WithAuthTrait;

    #[Route ('_default', name:'home')]
	public function index(){
		$this->loadDefaultView();
	}

    protected function getAuthController(): AuthController
    {
        // TODO: Implement getAuthController() method.
        return $this->_auth??= new \controllers\AuthController($this);
    }

    public function isValid($action) {
        if($action==='myLists' || $action==='deleteList'){
            return $this->getAuthController()->_isValidUser($action);
        }
        return parent::isValid($action);
    }



}
