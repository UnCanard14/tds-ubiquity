<?php
namespace controllers\auth\files;

use Ubiquity\controllers\auth\AuthFiles;
 /**
  * Class Auth ControllerFiles
  */
class Auth ControllerFiles extends AuthFiles{
	public function getViewIndex(){
		return "Auth Controller/index.html";
	}

	public function getViewInfo(){
		return "Auth Controller/info.html";
	}

	public function getViewNoAccess(){
		return "Auth Controller/noAccess.html";
	}

	public function getViewDisconnected(){
		return "Auth Controller/disconnected.html";
	}

	public function getViewMessage(){
		return "Auth Controller/message.html";
	}

	public function getViewBaseTemplate(){
		return "Auth Controller/baseTemplate.html";
	}


}
