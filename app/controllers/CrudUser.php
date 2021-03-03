<?php
namespace controllers;
use controllers\crud\datas\CrudUserDatas;
use Ubiquity\controllers\crud\CRUDDatas;
use controllers\crud\viewers\CrudUserViewer;
use Ubiquity\controllers\crud\viewers\ModelViewer;
use controllers\crud\events\CrudUserEvents;
use Ubiquity\controllers\crud\CRUDEvents;
use controllers\crud\files\CrudUserFiles;
use Ubiquity\controllers\crud\CRUDFiles;
use Ubiquity\attributes\items\router\Route;

#[Route(path: "/users",inherited: true,automated: true)]
class CrudUser extends \Ubiquity\controllers\crud\CRUDController{

	public function __construct(){
		parent::__construct();
		\Ubiquity\orm\DAO::start();
		$this->model='models\\User';
		$this->style='';
	}

	public function _getBaseRoute() {
		return '/users';
	}
	
	protected function getAdminData(): CRUDDatas{
		return new CrudUserDatas($this);
	}

	protected function getModelViewer(): ModelViewer{
		return new CrudUserViewer($this,$this->style);
	}

	protected function getEvents(): CRUDEvents{
		return new CrudUserEvents($this);
	}

	protected function getFiles(): CRUDFiles{
		return new CrudUserFiles();
	}


}
