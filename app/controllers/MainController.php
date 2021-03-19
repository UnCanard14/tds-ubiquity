<?php
namespace controllers;

 use models\Basket;
 use models\Order;
 use models\Product;
 use models\Section;
 use services\dao\OrgaRepository;
 use Ubiquity\attributes\items\di\Autowired;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\controllers\auth\AuthController;
 use Ubiquity\controllers\auth\WithAuthTrait;
 use Ubiquity\orm\DAO;
 use Ubiquity\utils\http\USession;

 /**
 * Controller MainController
 **/

class MainController extends ControllerBase{
    use WithAuthTrait;

    #[Autowired]
    private OrgaRepository $repo;

    /**
     * @return OrgaRepository
     */
    public function getRepo(): OrgaRepository
    {
        return $this->repo;
    }

    /**
     * @param OrgaRepository $repo
     */
    public function setRepo(OrgaRepository $repo): void
    {
        $this->repo = $repo;
    }

    #[Route ('_default', name:'home')]
	public function index(){
        $numOrders = count(DAO::getAll(Order::class, 'idUser= ?', false, [USession::get("idUser")]));
        $articlesPromo = DAO::getAll(Product::class, 'promotion< ?', false, [0]);
        $numBaskets = count(DAO::getAll(Basket::class, 'idUser= ?', false, [USession::get("idUser")]));
		$this->loadDefaultView(['numOrders'=>$numOrders ,'articlesPromo'=>$articlesPromo, 'numBaskets'=>$numBaskets]);
	}

    protected function getAuthController(): AuthController
    {
        // TODO: Implement getAuthController() method.
        return $this->_auth??= new \controllers\AuthController($this);
    }

    #[Route ('store', name:'store')]
    public function storePage(){
       $store = DAO::getAll(Product::class, false, false);
       $sections = DAO::getAll(Section::class, false, false);
       $this->loadDefaultView(['store'=>$store, 'sections'=>$sections]);
    }

    #[Route ('order', name:'order')]
    public function orderPage(){
        $orders = DAO::getAll(Order::class, 'idUser= ?', false, [USession::get("idUser")]);
        $this->loadDefaultView(['orders'=>$orders]);
    }

    #[Route ('newBasket', name:'newBasket')]
    public function newBasket(){
        $this->loadDefaultView();
//        echo '<pre>';
//        print_r($orders);
//        echo '</pre>';
    }

    #[Route ('Basket', name:'basket')]
    public function basket(){
        $baskets = DAO::getAll(Basket::class, 'idUser= ?', false, [USession::get("idUser")]);
        $this->loadDefaultView(['baskets'=>$baskets]);
    }

	#[Route(path: "section/{id}",name: "section")]
	public function section($id){
        $articles = DAO::getAll(Product::class, 'idSection= ?', false, [$id]);
		$this->loadDefaultView(['idSection'=>$id, 'articles'=>$articles]);
	}

}
