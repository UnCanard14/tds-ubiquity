<?php
namespace controllers;
use Ubiquity\cache\CacheManager;
use Ubiquity\controllers\Router;
use Ubiquity\controllers\Startup;
use Ubiquity\utils\http\UResponse;
use Ubiquity\utils\http\USession;
use Ubiquity\utils\http\URequest;
use controllers\auth\files\LoginControllerFiles;
use Ubiquity\controllers\auth\AuthFiles;
use Ubiquity\attributes\items\router\Route;

#[Route(path: "/login",inherited: true,automated: true)]
class LoginController extends \Ubiquity\controllers\auth\AuthController{

    protected $headerView = "@activeTheme/main/vHeader.html";

    public function initialize() {
        if (! URequest::isAjax()) {
            $this->loadView($this->headerView);
        }
    }


	protected function onConnect($connected) {
		$urlParts=$this->getOriginalURL();
		USession::set($this->_getUserSessionKey(), $connected);
		if(isset($urlParts)){
			$this->_forward(implode("/",$urlParts));
		}else{
			//TODO
			//Forwarding to the default controller/action
//            UResponse::header('location',Router::path('todos.menu'));
//            echo Router::path('todos.menu');
            UResponse::header('location', '/'.Router::path('todos.menu'));
		}
	}

	protected function _connect() {
		if(URequest::isPost()){
			$email=URequest::post($this->_getLoginInputName());
			//TODO
			//Loading from the database the user corresponding to the parameters
			//Checking user creditentials
			//Returning the user
            if (CacheManager::$cache->exists("data/user/" .md5($email))){
                $userInfo = CacheManager::$cache->fetch("data/user/" .md5($email));
                if($userInfo['email'] == $email){
                    if(URequest::password_verify($this->_getPasswordInputName(),$userInfo['password'])){
                        return $email;
                    }
                }
            }
		}
		return;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Ubiquity\controllers\auth\AuthController::isValidUser()
	 */
	public function _isValidUser($action=null) {
		return USession::exists($this->_getUserSessionKey());
	}

	public function _getBaseRoute() {
		return '/login';
	}
	
	protected function getFiles(): AuthFiles{
		return new LoginControllerFiles();
	}

    public function _displayInfoAsString() {
        return true;
    }



}
