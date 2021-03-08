<?php
namespace controllers;

 use Ubiquity\attributes\items\router\Route;

 /**
 * Controller MainController
 **/

class MainController extends ControllerBase{

    #[Route ('_default', name:'home')]
	public function index(){
		$this->loadDefaultView();
	}

}
