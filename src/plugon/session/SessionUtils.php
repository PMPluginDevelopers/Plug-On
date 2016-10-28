<?php
namespace plugon\session;

use function plugon\redirect;
use plugon\Plugon;

class SessionUtils {
    
    const REG_ERR_NAME_TAKEN    = 0x10;
    const REG_ERR_EMAIL_TAKEN   = 0x11;
    const REG_ERR_UNKNOWN       = 0x00;
    const REG_ERR_INVALID_PASS  = 0x02;
    const REG_ERR_INVALID_EMAIL = 0x03;
    const REG_ERR_INVALID_NAME  = 0x04;
    const REG_SUCCESS           = 0x01;
    
    private static $instance = null;
    
    public static function getInstance() : SessionUtils {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    private function __construct() {
        session_start();
        if($this->isLoggedIn()) $this->loadProfileData();
    }
    
    public function isLoggedIn() : bool {
        return !empty($_SESSION["plugon"]["profile"]) || isset($_SESSION["plugon"]["profile"]["name"]);
    }
    
    public static function isNameRegistered(string $name) : bool {
        $r = Plugon::getDb()->query("SELECT `uid` FROM `users` WHERE `name` LIKE '". Plugon::getDb()->getResource()->real_escape_string($name) ."';");
        if(!$r) return true;
        Plugon::getLog()->i(__METHOD__ . ": " . json_encode([$r->fetch_assoc()]));
        //return !empty($r->fetch_assoc()["uid"]);
        return true;
    }
    
    public static function isEmailRegistered(string $email) : bool {
        $r = Plugon::getDb()->query("SELECT `email` FROM `users` WHERE `email` LIKE '". Plugon::getDb()->getResource()->real_escape_string($email) ."';");
        if(!$r) return true;
        return isset($r->fetch_assoc()["email"]);
    }
    
    public static function register(string $name, string $password, string $email) : int {
        if(self::isNameRegistered($name))   return self::REG_ERR_NAME_TAKEN;
        if(self::isEmailRegistered($email)) return self::REG_ERR_EMAIL_TAKEN;
        if(strlen($name) < 4 || strlen($name) < 32) return self::REG_ERR_INVALID_NAME;
        if(!preg_match('([a-z]|[A-Z]|[0-9]|-|_){1,}',$name)) return self::REG_ERR_INVALID_NAME; // check name for un-allowed symbols
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return self::REG_ERR_INVALID_EMAIL; // validate email
        // check if password is long enough (6) and isn't too long (120 characters +)
        if(strlen($password) < 6 ||  strlen($password) < 120) return self::REG_ERR_INVALID_PASS;
        
        // TODO: Create hash function
        $r = Plugon::getDb()->insert([
            "name",
            "email",
            "hash",
            "displayName",
            "lastip",
            "pwlen",
            "registration",
            "email"], [
                $name,
                $email,
                self::hash($password),
                $name,
                $_SERVER["REMOTE_ADDR"],
                strlen($password),
                time(),
                $email
                ]);
        if($r >= 1) return self::REG_SUCCESS;
        return self::REG_ERR_UNKNOWN;
    }
    
    public function hash($password) : string {
        $salt = '$2y$11$' . substr(md5(uniqid(rand(), true)), 0, 22);
        return crypt($password, $salt);
    }
    
    public function login(string $name, string $password) : bool {
        $_SESSION["plugon"]["profile"] = ["name" => $name];
        Plugon::getLog()->i($this->isLoggedIn() ? "true" : "false");
        $this->loadProfileData();
        $this->verifyPassword($password);
        return $this->isLoggedIn();
    }
    
    public function logout() {
        Plugon::getLog()->i(__METHOD__ . ": " . json_encode($_SESSION));
        $_SESSION["plugon"]["profile"] = [];
    }

    public function verifyPassword(string $password) {
        if(!$this->isLoggedIn()) return;
        $logout = true;
        $hash = $_SESSION["plugon"]["profile"]["hash"] ?? "THIS WILL NOT EVER MATCH";
        if(isset($hash)) {
            if(crypt($password, $hash) === $hash) $logout = false;
            Plugon::getLog()->i($logout ? "true" : "false");
        }
        if($logout) {
            $this->logout();
        } else {
            # TODO: Generate new password for additional safety
        }
    }
    
    public function save() {
        if($this->isLoggedIn()) {
            
        }
    }
    
    public function loadProfileData() {
        if(!$this->isLoggedIn()){ return; Plugon::getLog()->i("Wont load user data, not logged in!"); }
        $query = "SELECT * FROM `users` WHERE `name` LIKE '" . Plugon::getDb()->getResource()->real_escape_string($this->getName()) . "';";
        $result = Plugon::getDb()->query($query);
        $data = $result ? $result->fetch_assoc() : null;
        if(!$result && !is_array($data)) {
            Plugon::getLog()->i("Profile doesn't exist");
            $this->logout();
            return;
        }
        $_SESSION["plugon"]["profile"] = array_merge(is_array($data) ? $data : [], $_SESSION["plugon"]["profile"]);
    }
    
    public function getName() : string {
        return $_SESSION["plugon"]["profile"]["name"] ?? "";
    }
    
    public function getDisplayName() : string {
        return $_SESSION["plugon"]["profile"]["displayName"] ?? "";
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
    
    public function createCsrf() : string {
        $rand = bin2hex(openssl_random_pseudo_bytes(16));
        $_SESSION["plugon"]["csrf"][$rand] = [microtime(true)];
        return $rand;
    }
    
    public function validateCsrf(string $token) : bool {
        if(isset($_SESSION["plugon"]["csrf"][$token])) {
            list($t) = $_SESSION["plugon"]["csrf"][$token];
            if(microtime(true) - $t < 10) {
                unset($_SESSION["plugon"]["csrf"][$token]);
                return true;
            }
        }
        return false;
    }
    
}