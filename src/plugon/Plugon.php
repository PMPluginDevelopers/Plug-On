<?php
namespace plugon;

use mysqli;
use plugon\utils\Logger;
use plugon\module\error\InternalErrorPage;
use plugon\output\OutputManager;
use plugon\utils\MySQLHelper;
use RuntimeException;
use stdClass;

final class Plugon {
    
    const PLUGON_VERSION = "1.0";

    const PROJECT_TYPE_PLUGIN   = 1;
    const PROJECT_TYPE_LIBRARY  = 2;

    const BUILD_CLASS_DEV       = 1;
    const BUILD_CLASS_BETA      = 2;
    const BUILD_CLASS_RELEASE   = 3;
    const BUILD_CLASS_PR        = 4;
    
    const CG_NONE                       = 0x00;
    const CG_ADMIN_TOOLS                = 0x01;
    const CG_ANTI_GRIEFING_TOOLS        = 0x02;
    const CG_CHAT_RELATED               = 0x03;
    const CG_DEVELOPER_TOOLS            = 0x04;
    const CG_ECONOMY                    = 0x05;
    const CG_FUN                        = 0x06;
    const CG_GENERAL                    = 0x07;
    const CG_INFORMATIONAL              = 0x08;
    const CG_MECHANICS                  = 0x09;
    const CG_MISCELLANEOUS              = 0x10;
    const CG_TELEPORTATION              = 0x11;
    const CG_WORLD_EDITING_N_MANAGMENT  = 0x12;
    const CG_WORLD_GENERATOR            = 0x13;

    public static $PROJECT_TYPE_HUMAN = [
        self::PROJECT_TYPE_PLUGIN => "Plugin",
        self::PROJECT_TYPE_LIBRARY => "Library"
    ];
    
    public static $BUILD_CLASS_HUMAN = [
        self::BUILD_CLASS_DEV => "Dev",
        self::BUILD_CLASS_BETA => "Beta",
        self::BUILD_CLASS_RELEASE => "Release",
        self::BUILD_CLASS_PR => "PR"
    ];
    
    public static $BUILD_CLASS_IDEN = [
        self::BUILD_CLASS_DEV => "dev",
        self::BUILD_CLASS_BETA => "beta",
        self::BUILD_CLASS_RELEASE => "rc",
        self::BUILD_CLASS_PR => "pr"
    ];
    
    
    public static $PLUGIN_CATEGORY = [
        self::CG_NONE => "N/A",
        self::CG_ADMIN_TOOLS => "Admin Tools",
        self::CG_ANTI_GRIEFING_TOOLS => "Anti-Griefing Tools",
        self::CG_CHAT_RELATED => "Chat Related",
        self::CG_DEVELOPER_TOOLS => "Developer Tools",
        self::CG_ECONOMY => "Economy",
        self::CG_FUN => "Fun",
        self::CG_GENERAL => "General",
        self::CG_INFORMATIONAL => "Informational",
        self::CG_MECHANICS => "Mechanics",
        self::CG_MISCELLANEOUS => "Miscellaneous",
        self::CG_TELEPORTATION => "Teleportational",
        self::CG_WORLD_EDITING_N_MANAGMENT => "World Editing & Managment",
        self::CG_WORLD_GENERATOR => "World Generator"
    ];

    public static $curlCounter = 0;
    public static $curlTime = 0;
    public static $mysqlCounter = 0;
    public static $mysqlTime = 0;

    public static $plainTextOutput = false;

    public static $lastCurlHeaders;
    public static $ghRateRemain;

    /**
     * Returns the internally absolute path to Plugon site.
     *
     * Example return value: <code>/plugon/</code>
     *
     * @return string
     */
    public static function getRootPath() : string {
        // by splitting into two trim calls, only one slash will be returned for empty paths.url value
        return rtrim("/" . ltrim(Plugon::getSecret("path.url"), "/"), "/") . "/";
    }

    public static function getSecret(string $name) {
        global $secretsCache;
        if(!isset($secretsCache)) {
            $secretsCache = json_decode($path = file_get_contents(SECRET_PATH . "secrets.json"), true);
        }
        $secrets = $secretsCache;
        if(isset($secrets[$name])) {
            return $secrets[$name];
        }
        $parts = explode(".", $name);
        foreach($parts as $part) {
            if(!is_array($secrets) or !isset($secrets[$part])) {
                throw new RuntimeException("Unknown secret $part");
            }
            $secrets = $secrets[$part];
        }
        if(count($parts) > 1) {
            $secretsCache[$name] = $secrets;
        }
        return $secrets;
    }

    public static function getDb() : MySQLHelper {
        global $db;
        if(isset($db)) {
            self::getLog()->i("Returning cached db");
            return $db;
        }
        $data = Plugon::getSecret("mysql");
        try {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            $s = microtime(true);
            $db = @new MySQLHelper($data["host"], $data["user"], $data["password"], $data["schema"], $data["port"] ?? 3306);
            self::getLog()->i("Connected to mysql {$data['host']}:{$data['port']} in " . (microtime(true) - $s) . "s");
        } catch(\Exception $e) {
            Plugon::getLog()->e("mysqli error: " . $e->getMessage());
        }
        $mysqli = $db->getResource(); 
        if($mysqli->connect_error) {
            $rand = mt_rand();
            Plugon::getLog()->e("Error#$rand mysqli error: $mysqli->connect_error");
            OutputManager::$current->terminate();
            (new InternalErrorPage($rand))->output();
            die;
        }
        return $db;
    }

    public static function getLog() : Logger {
        global $log;
        return $log;
    }

    public static function getTmpFile($ext = ".tmp") : string {
        $tmpDir = rtrim(self::getSecret("meta.tmpPath") ?: sys_get_temp_dir(), "/") . "/";
        $file = tempnam($tmpDir, $ext);
//        do {
//            $file = $tmpDir . bin2hex(random_bytes(4)) . $ext;
//        } while(is_file($file));
//        register_shutdown_function("unlink", $file);
        return $file;
    }

    public static function queryAndFetch(string $query, string $types = "", ...$args) {
        self::$mysqlCounter++;
        $start = microtime(true);
        $db = self::getDb();
        if($types !== "") {
            $stmt = $db->prepare($query);
            if($stmt === false) {
                throw new RuntimeException("Failed to prepare statement: " . $db->error);
            }
            Plugon::getLog()->v("Executing MySQL query $query with args $types: " . json_encode($args));
            $stmt->bind_param($types, ...$args);
            if(!$stmt->execute()) {
                throw new RuntimeException("Failed to execute query: " . $db->error);
            }
            $result = $stmt->get_result();
        } else {
            Plugon::getLog()->v("Executing MySQL query $query");
            $result = $db->query($query);
            if($result === false) {
                throw new RuntimeException("Failed to execute query: " . $db->error);
            }
        }
        if($result instanceof \mysqli_result) {
            $rows = [];
            while(is_array($row = $result->fetch_assoc())) {
                $rows[] = $row;
            }
            $ret = $rows;
        } else {
            $ret = $db;
        }
        $end = microtime(true);
        self::$mysqlTime += $end - $start;
        return $ret;
    }

    public static function curl(string $url, string $postContents, string $method, string ...$extraHeaders) {
        self::$curlCounter++;
        $headers = ["User-Agent: Plugon/" . Plugon::PLUGON_VERSION];
        foreach($extraHeaders as $header) {
            if(strpos($header, "Accept: ") === 0) {
                $headers[1] = $header;
            } else {
                $headers[] = $header;
            }
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        if(strlen($postContents) > 0) curl_setopt($ch, CURLOPT_POSTFIELDS, $postContents);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $startTime = microtime(true);
        $ret = curl_exec($ch);
        $headerLength = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        self::$lastCurlHeaders = substr($ret, 0, $headerLength);
        $ret = substr($ret, $headerLength);
        $endTime = microtime(true);
        self::$curlTime += $endTime - $startTime;
        curl_close($ch);
        Plugon::getLog()->v("cURL $method: $url, returned content of " . strlen($ret) . " bytes");
        return $ret;
    }

    public static function curlPost(string $url, $postFields, string ...$extraHeaders) {
        self::$curlCounter++;
        $headers = ["User-Agent: Plugon/" . Plugon::PLUGON_VERSION];
        foreach($extraHeaders as $header) {
            if(strpos($header, "Accept: ") === 0) {
                $headers[1] = $header;
            } else {
                $headers[] = $header;
            }
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $startTime = microtime(true);
        $ret = curl_exec($ch);
        $headerLength = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        self::$lastCurlHeaders = substr($ret, 0, $headerLength);
        $ret = substr($ret, $headerLength);
        $endTime = microtime(true);
        self::$curlTime += $endTime - $startTime;
        curl_close($ch);
        Plugon::getLog()->v("cURL POST: $url, returned content of " . strlen($ret) . " bytes");
        return $ret;
    }

    public static function curlGet(string $url, string ...$extraHeaders) {
        self::$curlCounter++;
        $headers = ["User-Agent: Plugon/" . Plugon::PLUGON_VERSION];
        foreach($extraHeaders as $header) {
            $headers[] = $header;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $startTime = microtime(true);
        $ret = curl_exec($ch);
        $headerLength = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        self::$lastCurlHeaders = substr($ret, 0, $headerLength);
        $ret = substr($ret, $headerLength);
        $endTime = microtime(true);
        self::$curlTime += $endTime - $startTime;
        curl_close($ch);
        Plugon::getLog()->v("cURL GET: $url, returned content of " . strlen($ret) . " bytes");
        return $ret;
    }

    /**
     * @param string|stdClass $owner
     * @param string|int      $avatar
     * @param int             $avatarWidth
     */
    public static function displayUser($owner, $avatar = "", int $avatarWidth = 16) {
        if($owner instanceof stdClass) {
            self::displayUser($owner->login, $owner->avatar_url, $avatar ?: 16);
            return;
        }
        if($avatar !== "") {
            echo "<img src='$avatar' width='$avatarWidth'> ";
        }
        echo $owner, " ";
    }

    public static function displayAnchor($name) {
        ?>
        <a class="dynamic-anchor" name="<?= $name ?>" href="#<?= $name ?>">&sect;</a>
        <?php
    }

    public static function showStatus() {
        global $startEvalTime;
        header("X-Status-Execution-Time: " . (microtime(true) - $startEvalTime));
        header("X-Status-cURL-Queries: " . Plugon::$curlCounter);
        header("X-Status-cURL-Time: " . Plugon::$curlTime);
        header("X-Status-MySQL-Queries: " . Plugon::$mysqlCounter);
        header("X-Status-MySQL-Time: " . Plugon::$mysqlTime);
    }

    public static function startsWith(string $string, string $prefix) : bool {
        return strlen($string) >= strlen($prefix) and substr($string, 0, strlen($prefix)) === $prefix;
    }

    public static function endsWith(string $string, string $suffix) : bool {
        return strlen($string) >= strlen($suffix) and substr($string, -strlen($suffix)) === $suffix;
    }

    public static function copyToObject($source, $object) {
        foreach($source as $k => $v) {
            $object->{$k} = $v;
        }
    }

    public static function checkDeps() {
//        assert(function_exists("apcu_store"));
        assert(function_exists("curl_init"));
        assert(class_exists(mysqli::class));
        assert(!ini_get("phar.readonly"));
        assert(function_exists("yaml_emit"));
    }

    private function __construct() {
    }
}
