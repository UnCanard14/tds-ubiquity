<?php
namespace services\ui;

 use models\Group;
 use models\User;
 use Ubiquity\controllers\Controller;
 use Ubiquity\controllers\Router;
 use Ubiquity\utils\http\URequest;

 /**
  * Class UIGroups
  */
class UIGroups extends \Ajax\php\ubiquity\UIService{
    public function __construct(Controller $controller)
    {
        parent::__construct($controller);
        if(!URequest::isAjax()) {
            $this->jquery->getHref('a[data-target]', '', ['hasLoader' => 'internal', 'historize' => false,'listenerOn'=>'body']);
        }
    }

    public function listGroups(array $groups){
        $dt =$this->semantic->dataTable('dt-groups', Group::class, $groups);
        $dt->setFields(['name']);

    }

    public function orgaForm(\models\Organization $orga)
    {
        $frm=$this->semantic->dataForm('frmOrga', $orga);
        $frm->setFields(['id', 'name', 'domain', 'submit']);
        $frm->fieldAsHidden('id');
        $frm->fieldAsLabeledInput('name', ['rules'=>'empty']);
        $frm->fieldAsLabeledInput('domain', ['rules'=>['empty','email']]);
        $frm->setValidationParams(["on"=>"blur", "inline"=>true]);
        $frm->fieldAsSubmit('submit','positive', Router::path('addOrga'),"#reponse");
    }

    public function userForm(\models\User $user)
    {
        $frm=$this->semantic->dataForm('frmUser', $user);
        $frm->setFields(['email']);
//        $frm->fieldAsHidden('id');
//        $frm->fieldAsLabeledInput('name', ['rules'=>'empty']);
//        $frm->fieldAsLabeledInput('domain', ['rules'=>['empty','email']]);
//        $frm->setValidationParams(["on"=>"blur", "inline"=>true]);
//        $frm->fieldAsSubmit('submit','positive', Router::path('addOrga'),"#reponse");
    }


}
