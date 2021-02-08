<?php
namespace controllers\auth\files;

use Ubiquity\controllers\auth\AuthFiles;
 /**
  * Class LoginControllerFiles
  */
class LoginControllerFiles extends AuthFiles{
	public function getViewIndex(){
		return "LoginController/index.html";
	}

	public function getViewNoAccess(){
		return "LoginController/noAccess.html";
	}

	public function getViewDisconnected(){
		return "LoginController/disconnected.html";
	}

	public function getViewInfo(){
		return "LoginController/info.html";
	}

	public function getViewBaseTemplate(){
		return "LoginController/baseTemplate.html";
	}

	public function getViewMessage(){
		return "LoginController/message.html";
	}


}
