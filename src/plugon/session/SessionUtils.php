<?php
/*
 * poggit
 *
 * Copyright (C) 2016 poggit
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
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
        if(!isset($_SESSION["plugon"]["anti_forge"])) {
            $_SESSION["plugon"]["anti_forge"] = bin2hex(openssl_random_pseudo_bytes(64));
        }
    }
    public function isLoggedIn() : bool {
        return !empty($_SESSION["plugon"]["session"]);
    }

    public function setAntiForge(string $state) {
        $_SESSION["plugon"]["anti_forge"] = $state;
    }
    
    public function getAntiForge() {
        return $_SESSION["plugon"]["anti_forge"];
    }

    public function login(int $uid, string $name, \stdClass $opts) {
        $_SESSION["plugon"]["session"] = [
            "uid" => $uid,
            "name" => $name,
            "opts" => $opts
        ];
        # TODO: Load other data from database
    }
    /**
     * @return array|null
     */
    public function getLogin() {
        if(!$this->isLoggedIn()) {
            return null;
        }
        return $_SESSION["plugon"]["github"];
    }
    public function getAccessToken($default = "") {
        return $this->isLoggedIn() ? $_SESSION["plugon"]["github"]["access_token"] : $default;
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
}