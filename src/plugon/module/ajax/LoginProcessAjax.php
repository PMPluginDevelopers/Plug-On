<?php
namespace plugon\module\ajax;

use plugon\Plugon;
use plugon\session\SessionUtils;

class LoginProcessAjax extends AjaxModule {

    protected function impl() {
        $response = ["status" => false];
        if(SessionUtils::isNameRegistered($_REQUEST["username"])) {
            $response["status"] = SessionUtils::getInstance()->login($_REQUEST["username"], $_REQUEST["password"]) ? 'OK' : false;
        } else {
            Plugon::getLog()->i("User not registered"); // debug
        }
        
        echo json_encode($response);
    }

    public function getName() : string {
        return "loginProcess";
    }
    
    protected function needLogin() : bool {
        return false;
    }

    protected function fallback() : bool {
        return true;
    }

}