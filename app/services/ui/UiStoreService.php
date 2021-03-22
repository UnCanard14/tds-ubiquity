<?php

namespace services\ui;

use Ajax\php\ubiquity\UIService;
use Ubiquity\controllers\Controller;
use Ubiquity\utils\http\URequest;

class UiStoreService extends UIService {
    public function __construct(Controller $controller) {
        parent::__construct($controller);
        if(!URequest::isAjax()) {
            $this->jquery->getHref('a[data-target]', '', ['hasLoader' => 'internal', 'historize' => true,'listenerOn'=>'body']);
        }
    }


}
