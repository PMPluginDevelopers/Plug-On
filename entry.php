<?php
namespace {
    if(!defined('PLUGON_INSTALL_PATH')) define('PLUGON_INSTALL_PATH', realpath(__DIR__) . DIRECTORY_SEPARATOR);
}

namespace plugon {
    
    use plugon\utils\Logger;
    use plugon\output\OutputManager;
    use plugon\module\error\InternalErrorModule;
    use plugon\module\error\NotFoundPage;
    use plugon\module\Module;
    use RuntimeException;
    
    if(!defined('plugon\DEFAULT_MODULE')) define('plugon\DEFAULT_MODULE', "archive");
    if(!defined('plugon\INSTALL_PATH')) define('plugon\INSTALL_PATH', PLUGON_INSTALL_PATH);
    if(!defined('plugon\SOURCE_PATH')) define('plugon\SOURCE_PATH', INSTALL_PATH . "src" . DIRECTORY_SEPARATOR);
    if(!defined('plugon\LIBS_PATH')) define('plugon\LIBS_PATH', INSTALL_PATH . "libs" . DIRECTORY_SEPARATOR);
    if(!defined('plugon\SECRET_PATH')) define('plugon\SECRET_PATH', INSTALL_PATH . "secret" . DIRECTORY_SEPARATOR);
    if(!defined('plugon\RES_DIR')) define('plugon\RES_DIR', INSTALL_PATH . "res" . DIRECTORY_SEPARATOR);
    if(!defined('plugon\RESOURCE_DIR')) define('plugon\RESOURCE_DIR', INSTALL_PATH . "resources" . DIRECTORY_SEPARATOR);
    if(!defined('plugon\JS_DIR')) define('plugon\JS_DIR', "res" . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR);
    if(!defined('plugon\CSS_DIR')) define('plugon\CSS_DIR', "res" . DIRECTORY_SEPARATOR . "css" . DIRECTORY_SEPARATOR);
    if(!defined('plugon\FONT_DIR')) define('plugon\FONT_DIR', "res" . DIRECTORY_SEPARATOR . "fonts" . DIRECTORY_SEPARATOR);
    if(!defined('plugon\SASS_DIR')) define('plugon\SASS_DIR', "res" . DIRECTORY_SEPARATOR . "sass" . DIRECTORY_SEPARATOR);
    if(!defined('plugon\ASSETS_DIR')) define('plugon\ASSETS_DIR', INSTALL_PATH . "assets" . DIRECTORY_SEPARATOR);
    if(!defined('plugon\LOG_DIR')) define('plugon\LOG_DIR', INSTALL_PATH . "logs" . DIRECTORY_SEPARATOR);
    
    /** @var Module[] */
    $MODULES = [];

    try {
        spl_autoload_register(function (string $class) {
            $bases = [SOURCE_PATH . str_replace("\\", DIRECTORY_SEPARATOR, $class)];
            $extensions = [".php" . PHP_MAJOR_VERSION . PHP_MINOR_VERSION, ".php" . PHP_MAJOR_VERSION, ".php"];
            foreach($extensions as $ext) {
                foreach($bases as $base) {
                    $file = $base . $ext;
                    if(is_file($file)) {
                        require_once $file;
                        return;
                    }
                }
            }
        });
        set_error_handler(__NAMESPACE__ . "\\error_handler");
        Plugon::checkDeps();
        $outputManager = new OutputManager;
        $log = new Logger;
        include_once SOURCE_PATH . "modules.php"; // Load default modules
        $requestPath = $_GET["__path"] ?? DIRECTORY_SEPARATOR;
        $input = file_get_contents("php://input");
        $log->i($_SERVER["REMOTE_ADDR"] . " " . $requestPath);
        $log->v($requestPath . " " . json_encode($input, JSON_UNESCAPED_SLASHES));
        $startEvalTime = microtime(true);
        $paths = array_filter(explode(DIRECTORY_SEPARATOR, $requestPath, 2));
        if(count($paths) === 0) $paths[] = DEFAULT_MODULE;
        if(count($paths) === 1) $paths[] = "";
        list($module, $query) = $paths;
        if(isset($MODULES[$module])) {
            $class = $MODULES[$module];
            $page = new $class($query);
        } else {
            $page = new NotFoundPage($requestPath);
        }
        Module::$currentPage = $page;
        $page->output();
        $endEvalTime = microtime(true);
        $log->v("Safely completed: " . ((int) (($endEvalTime - $startEvalTime) * 1000)) . "ms");
        Plugon::showStatus();
        $outputManager->output();
    } catch(\Throwable $e) {
        error_handler(E_ERROR, get_class($e) . ": " . $e->getMessage() . "\n" .
            $e->getTraceAsString(), $e->getFile(), $e->getLine());
    }
    function registerModule(string $class) {
        global $MODULES;
        if(!(class_exists($class) and is_subclass_of($class, Module::class))) {
            throw new RuntimeException("Want Class<? extends Module>, got Class<$class>");
        }
        /** @var Module $instance */
        $instance = new $class("");
        foreach($instance->getAllNames() as $name) {
            $MODULES[$name] = $class;
        }
    }
    function getInput() : string {
        global $input;
        return $input;
    }
    function getRequestPath() : string {
        global $requestPath;
        return $requestPath;
    }
    /**
     * Redirect user to a path under the Plugon root, without a leading slash
     *
     * @param string $target   default homepage
     * @param bool   $absolute default true
     */
    function redirect(string $target = "", bool $absolute = false) {
        header("Location: " . ((!$absolute and $target !== "") ? Plugon::getRootPath() : "") . $target);
        Plugon::showStatus();
        die;
    }
    function error_handler(int $errno, string $error, string $errfile, int $errline) {
        global $log;
        http_response_code(500);
        $refid = mt_rand();
        if(Plugon::$plainTextOutput) {
            OutputManager::$current->outputTree();
            echo "Error#$refid Level $errno error at $errfile:$errline: $error\n";
        }
        if(!isset($log)) $log = new Logger();
        $log->e("Error#$refid Level $errno error at $errfile:$errline: $error");
        if(!plugon::$plainTextOutput) {
            OutputManager::terminateAll();
            (new InternalErrorModule((string) $refid))->output();
        }
        die;
    }
}