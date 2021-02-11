<?php
namespace controllers;
 use models\Organization;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\orm\DAO;
 use Ubiquity\orm\repositories\ViewRepository;

 /**
  * Controller OrgaController
  */
class OrgaController extends ControllerBase{

    private ViewRepository $repo;

    public function initialize(){
        parent::initialize();
        $this->repo = new ViewRepository($this, Organization::class);
    }

    #[Route('orga')]
	public function index(){
        $this->repo->all("",false);
		$this->loadView("OrgaController/index.html");
	}

	#[Route(path: "getOne/{idOrga}",name: "orga.getOne")]
	public function getOne($idOrga){
        $this->repo->byId($idOrga,['users.groupes', 'groupes.users']);
        //$orga =DAO::getById(Organization::class,$idOrga,['users.groupes','groupes.users']);
		$this->loadDefaultView();
	}

}
