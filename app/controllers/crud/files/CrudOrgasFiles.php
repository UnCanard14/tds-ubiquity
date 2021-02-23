<?php
namespace controllers\crud\files;

use Ubiquity\controllers\crud\CRUDFiles;
 /**
  * Class CrudOrgasFiles
  */
class CrudOrgasFiles extends CRUDFiles{
	public function getViewIndex(){
		return "CrudOrgas/index.html";
	}

	public function getViewDisplay(){
		return "CrudOrgas/display.html";
	}

	public function getViewForm(){
		return "CrudOrgas/form.html";
	}


}
