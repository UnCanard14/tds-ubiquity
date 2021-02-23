<?php
namespace controllers;
 use models\Group;
 use models\User;
 use services\dao\OrgaRepository;
 use services\ui\UIGroups;
 use Ubiquity\attributes\items\di\Autowired;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\controllers\auth\AuthController;
 use Ubiquity\controllers\auth\WithAuthTrait;
 use Ubiquity\orm\DAO;
 use Ubiquity\utils\http\USession;

 /**
  * Controller MainController
  */
class MainController extends ControllerBase{
    use WithAuthTrait;

    #[Autowired]
    private OrgaRepository $repo;
    private UIGroups $uiService;

    public function initialize()
    {
        parent::initialize();
        //$this->uiService
    }

    /**
     * @param OrgaRepository $repo
     */
    public function setRepo(OrgaRepository $repo): void
    {
        $this->repo = $repo;
    }

    #[Route('_default', name: 'home')]
	public function index(){
        $this->uiService=new UIGroups($this);
        $this->jquery->getHref('a[data-target]', parameters: ['historize'=>false, 'hasLoader'=>'internal', 'listenOn']);
		$this->jquery->renderView("MainController/index.html");
	}

    protected function getAuthController(): AuthController
    {
        return new MyAuth($this);
    }

    #[Route(path: "test/ajax", name: "main.testAjax")]
    public function testAjax(){
        $user = DAO::getById(User::class,[2], false);
        $this->loadView('MainController/testAjax.html',['user'=>$user]);
    }

    #[Route('user/details{id}', name:'user.details')]
    public function userDetails($id){
        $user =DAO::getById(User::class,[$id], true);
        echo $user->getOrganization();
    }

    #[Route('groups/list', name:'groups.list')]
public function listGroups(){
        $idOrga = USession::get('odOrga');
        $groups=DAO::getAll(Group::class,'idOrganization= ?', false, [$idOrga]);
        $this->uiService->listGroups($groups);
        $this->jquery->renderView();
    }
}
