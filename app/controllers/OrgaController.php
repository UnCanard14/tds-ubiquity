<?php
namespace controllers;
 use models\Organization;
 use Ubiquity\attributes\items\router\Get;
 use Ubiquity\attributes\items\router\Post;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\orm\DAO;
 use Ubiquity\orm\repositories\ViewRepository;
 use Ubiquity\utils\http\URequest;

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

    #[Route(path : 'orga/add', name : "orga.addOrgaForm")]
    public function addOrgaForm(){
        if(URequest::filled('valid')){
            if (URequest::filled('name') && URequest::filled('domain')){
                $orga=new Organization();
                $orga->setName(URequest::post('name'));
                $orga->setDomain(URequest::post('domain'));
                $orga->setAliases(URequest::post('alias'));
                URequest::setValuesToObject($orga);
                if(DAO::insert($orga)){
                    echo 'Nouvelle organisation insérée';
                }
            }else{
                echo 'Remplir les champs';
            }
        }
        print_r($_POST);
        $this->loadView("OrgaController/orgaForm.html");
    }

}
