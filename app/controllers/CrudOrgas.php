<?php
namespace controllers;
use controllers\crud\datas\CrudOrgasDatas;
use Ubiquity\controllers\crud\CRUDDatas;
use controllers\crud\viewers\CrudOrgasViewer;
use Ubiquity\controllers\crud\viewers\ModelViewer;
use controllers\crud\events\CrudOrgasEvents;
use Ubiquity\controllers\crud\CRUDEvents;
use controllers\crud\files\CrudOrgasFiles;
use Ubiquity\controllers\crud\CRUDFiles;
use Ubiquity\attributes\items\router\Route;

#[Route(path: "/Orgas",inherited: true,automated: true)]
class CrudOrgas extends \Ubiquity\controllers\crud\CRUDController{

	public function __construct(){
		parent::__construct();
		\Ubiquity\orm\DAO::start();
		$this->model='models\\Organization';
		$this->style='';
	}

	public function _getBaseRoute() {
		return '/Orgas';
	}
	
	protected function getAdminData(): CRUDDatas{
		return new CrudOrgasDatas($this);
	}

	protected function getModelViewer(): ModelViewer{
		return new CrudOrgasViewer($this,$this->style);
	}

	protected function getEvents(): CRUDEvents{
		return new CrudOrgasEvents($this);
	}

	protected function getFiles(): CRUDFiles{
		return new CrudOrgasFiles();
	}


}
