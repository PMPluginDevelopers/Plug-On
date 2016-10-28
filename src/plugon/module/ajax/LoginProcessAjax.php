<?php
namespace plugon\module\ajax;

use plugon\Plugon;
use plugon\session\SessionUtils;

class LoginProcessAjax extends AjaxModule {

    protected function impl() {
        $response = [
            "status" => false
            ];
            
        if(SessionUtils::isNameRegistered($_REQUEST["username"])) {
        
            $session = SessionUtils::getInstance();
            $session->login($_REQUEST["username"]);
            $session->verifyPassword($_REQUEST["password"]); // this is where passwords are verified
            $response["status"] = $session->isLoggedIn() ? "OK" : false;
        
        } else {
            Plugon::getLog()->e("User not registered"); // debug
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