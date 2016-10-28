<?php
namespace plugon\module\ajax;

include realpath(dirname(__FILE__)) . '/../../Plugon.php';
include realpath(dirname(__FILE__)) . '/../../session/SessionUtils.php';

use plugon\Plugon;
use plugon\session\SessionUtils;

class LoginProcessAjax extends AjaxModule {

    protected function impl() {
        $response = ["status" => false];
        if(SessionUtils::isNameRegistered($_REQUEST["username"])) {
            $response["status"] = SessionUtils::getInstance()->login($_REQUEST["username"], $_REQUEST["password"]) ? 'OK' : false;
        } else {
            Plugon::getLog()->info("User not registered"); // debug
        }
        
        echo json_encode($response);
    }

    /**
     * @return string
     */
    public function getName(){
        return "LoginProcess";
    }

    /**
     * @return bool
     */
    protected function needLogin(){
        return false;
    }

    /**
     * @return bool
     */
    protected function fallback() {
        return true;
    }

}