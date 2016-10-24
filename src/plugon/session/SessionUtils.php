<?php
namespace plugon\session;

class SessionUtils {
    
    private static $instance = null;
    
    public static function getInstance() : SessionUtils {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    private function __construct() {
        session_start();
        $this->loadProfileData();
    }
    
    public function isLoggedIn() : bool {
        return !empty($_SESSION["plugon"]["profile"]);
    }

    public function login(int $uid, string $name, \stdClass $opts) {
        $_SESSION["plugon"]["profile"] = [
            "uid" => $uid,
            "name" => $name,
            "opts" => $opts
        ];
        $this->loadProfileData();
    }
    
    public function loadProfileData() {
        # TODO
    }
    
    /**
     * @return array|null
     */
    public function getLoginData() {
        if(!$this->isLoggedIn()) {
            return null;
        }
        return $_SESSION["plugon"]["profile"];
    }
    
    public function persistLoginLoc(string $loc) {
        $_SESSION["plugon"]["loginLoc"] = $loc;
    }
    
    public function removeLoginLoc() : string {
        if(!isset($_SESSION["plugon"]["loginLoc"])) {
            return "";
        }
        $loc = $_SESSION["plugon"]["loginLoc"];
        unset($_SESSION["plugon"]["loginLoc"]);
        return $loc;
    }
    
    public function getName() : string {
        $login = $this->getLoginData();
        return isset($login["name"]) ? $login["name"] : "Guest"; 
    }
    
}