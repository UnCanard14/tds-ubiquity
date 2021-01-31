<?php
namespace controllers;
use Ubiquity\attributes\items\router\Get;
use Ubiquity\attributes\items\router\Post;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\Router;
use Ubiquity\utils\http\URequest;
use Ubiquity\utils\http\USession;

/**
  * Controller TodosController
 * @property \Ajax\php\ubiquity\JsUtils $jquery
  */


class TodosController extends ControllerBase{
    const CACHE_KEY = 'datas/lists/';
    const EMPTY_LIST_ID='not saved';
    const LIST_SESSION_KEY='list';
    const ACTIVE_LIST_SESSION_KEY='active-list';

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->menu();
    }

    #[Route(path: "todos/menu", name: "todos.menu")]
    public function index(){
        if(USession::exists(self::LIST_SESSION_KEY)){
            $list = USession::get(self::LIST_SESSION_KEY, []);
            return $this->displayList($list);//$list);
        }
        $this->showMessage('Bonjour', "Todolist permet de gerer des listes", 'info', 'info circle',
            [['url' =>Router::path('todos.new'),'caption'=>'Créer une nouvelle liste','style'=>'basic inverted']]);
	}

	#[Post(path: "todos/add", name: "todos.add")]
	public function addElement(){
        $list=USession::get(self::LIST_SESSION_KEY);
        if(URequest::filled('elements')){//Ca passe dans la boucle parce que ca prend le dessus
            $elemnts = explode("\n", URequest::post('elements'));
            foreach ($elemnts as $elm){
                $list[] = $elm;
            }
        }else{
            $list[] = URequest::post('element');
        }
        USession::set(self::LIST_SESSION_KEY, $list);
        $this->displayList($list);
	}


	#[Get(path: "todos/delete/{index}", name: "todos.delete")]
	public function deleteElement($index){
        $list=USession::get(self::LIST_SESSION_KEY);
        if(isset($list[$index])){
            array_splice($list, $index, 1);
            USession::set(self::LIST_SESSION_KEY, $list);
        }
        $this->displayList($list);
	}


	#[Post(path: "todos/edit/{index}", name: "todos.edit")]
	public function editElement($index){
        $list=USession::get(self::LIST_SESSION_KEY);
        if(isset($list[$index])){
            $list[$index] = URequest::post('editElement');
            USession::set(self::LIST_SESSION_KEY, $list);
        }
        $this->displayList($list);
	}


	#[Get(path: "todos/loadList/{uniqid}", name: "todos.loadList")]
	public function loadList($uniqid){
		
	}


	#[Post(path: "todos/loadList", name: "todos.loadListPost")]
	public function loadListFromForm(){
		
	}




	#[Get(path: "todos/new/{force}", name: "todos.new")]
	public function newlist($force = false){
        if($force != false || !USession::exists(self::LIST_SESSION_KEY)){
            USession::set(self::LIST_SESSION_KEY, []);
            $this->displayList(USession::get(self::LIST_SESSION_KEY));
        }else if(USession::exists(self::LIST_SESSION_KEY)) {
            $this->showMessage("Nouvelle Liste", "Une liste existe déjà. Voulez vous la vider ?", "", "",
                [['url' =>Router::path('todos.new/1'),'caption'=>'Créer une nouvelle liste','style'=>'basic inverted'],
                   ['url' =>Router::path('todos.menu'),'caption'=>'Annuler','style'=>'basic inverted']]);
            $this->displayList(USession::get(self::LIST_SESSION_KEY));
        }
	}


	#[Get(path: "todos/saveList", name: "todos.save")]
	public function saveList(){
        $list=USession::get(self::LIST_SESSION_KEY);
        //Sauvegarde $list en cache, avec l'identifiant $id
        //CacheManager::$cache->store(self::CACHE_KEY . $id, $list);
		
	}


	
	private function menu(){
		$this->loadView('TodosController/menu.html');
	}

/*	public function displayList($list){
        $this->jquery->click('#multiple', '$("._form").toggle();');
        //$this->jquery->click('#multiple')
       // $this->jquery->change('#multiple', '$(".formEditElement").toggle();');
		$this->jquery->renderView('TodosController/displayList2.html', ['list'=>$list]);
	}*/

    public function displayList($list){
        $this->jquery->change('#multiple', '$("._form").toggle();');
        $this->jquery->click(".buttonEdit", '$(".item" + this.id).toggle();');

        $this->jquery->renderView('TodosController/displayList.html', ['list'=>$list]);
    }

	public function showMessage(string $header,string $message,string $type = 'info',string $icon = 'info cirlce',array $buttons = []){
		$this->loadView('TodosController/showMessage.html',
            compact('header', 'message','type', 'icon', 'buttons'));
	}


}
