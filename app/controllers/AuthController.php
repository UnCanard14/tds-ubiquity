<?php
namespace controllers;
use classes\LocalBasket;
use models\Basket;
use models\Basketdetail;
use models\User;
use Ubiquity\orm\DAO;
use Ubiquity\utils\http\UResponse;
use Ubiquity\utils\http\USession;
use Ubiquity\utils\http\URequest;
use controllers\auth\files\AuthControllerFiles;
use Ubiquity\controllers\auth\AuthFiles;


class AuthController extends \Ubiquity\controllers\auth\AuthController{

    protected function initializeAuth() {
        if (!URequest::isAjax()) {
            $this->loadView('@activeTheme/main/vHeader.html');
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
            USession::set('recentlyViewedProducts',[]);
            UResponse::header('location', '/');
		}
	}

	protected function _connect() {
		if(URequest::isPost()){
			$email=URequest::post($this->_getLoginInputName());

			//TODO
            if($email != null){
                $password=URequest::post($this->_getPasswordInputName());
                $user = DAO::getOne(User::class, 'email= ?', false, [$email]);
                if(isset($user) && $user->getPassword() == $password) {
                    USession::set('idUser', $user->getId());
                    $LocalBasket = new LocalBasket("_current_", $user);
                    USession::set('defaultBasket', $LocalBasket);
                    return $user;
                }
            }
			//Loading from the database the user corresponding to the parameters
			//Checking user creditentials
			//Returning the user
            return ;
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
		return 'AuthController';
	}

	protected function getFiles(): AuthFiles{
		return new AuthControllerFiles();
	}

    public function _displayInfoAsString() {
        return true;
    }

}
