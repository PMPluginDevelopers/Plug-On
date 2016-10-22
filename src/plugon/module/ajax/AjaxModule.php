<?php
namespace plugon\module\ajax;

use plugon\module\Module;
use plugon\output\OutputManager;
use plugon\Poggit;
use plugon\session\SessionUtils;
use function plugon\redirect;

abstract class AjaxModule extends Module {

    public final function output() {
        $session = SessionUtils::getInstance();
        if($this->needLogin() and !$session->isLoggedIn()) {
            redirect(".");
        }
        if(!SessionUtils::getInstance()->validateCsrf($_REQUEST["csrf"] ?? "this will never match")) {
            if($this->fallback()) {
                http_response_code(403);
                Poggit::getLog()->w("CSRF failed");
                die;
            }
            return;
        }
        $this->impl();
    }

    protected function needLogin() : bool {
        return true;
    }

    /**
     * @return bool true if the request should end with a 403, false if the page should be displayed as a webpage
     */
    protected function fallback() : bool {
        return false;
    }

    protected function errorBadRequest(string $message) {
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
