<?php
namespace plugon\module\ajax;

include realpath(dirname(__FILE__)) . '/../Module.php';
include realpath(dirname(__FILE__)) . '/../../output/OutputManager.php';
include realpath(dirname(__FILE__)) . '/../../Plugon.php';
include realpath(dirname(__FILE__)) . '/../../session/SessionUtils.php';

use plugon\module\Module;
use plugon\output\OutputManager;
use plugon\Plugon;
use plugon\session\SessionUtils;
use function plugon\redirect;
abstract class AjaxModule extends Module {

    public final function output() {
        $session = SessionUtils::getInstance();
        if($this->needLogin() and !$session->isLoggedIn()) {
            redirect(".");
        }
        
        if(!SessionUtils::getInstance()->validateCsrf($_REQUEST["csrf"] ? $_REQUEST["csrf"] : "this will never match")) {
            if($this->fallback()) {
                http_response_code(403);
                Plugon::getLog()->warning("CSRF failed");
                die;
            }
            return;
        }
        $this->impl();
    }

    /**
     * @return bool
     */
    protected function needLogin(){
        return true;
    }

    /**
     * @return bool true if the request should end with a 403, false if the page should be displayed as a webpage
     */
    protected function fallback(){
        return false;
    }

    /**
     * @param string $message
     */
    protected function errorBadRequest($message) {
        OutputManager::terminateAll();
        http_response_code(400);
        echo json_encode([
            "message" => $message,
            "source_url" => "https://github.com/plugon/plugon"
        ]);
        die;
    }

    protected abstract function impl();

}
