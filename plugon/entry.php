<?php
namespace {
    if(!defined('PLUGON_INSTALL_PATH')) define('PLUGON_INSTALL_PATH', realpath(__DIR__) . DIRECTORY_SEPARATOR);
}

namespace plugon {

    include_once realpath(dirname(__FILE__)) . '/src/plugon/utils/Logger.php';
    include_once realpath(dirname(__FILE__)) . '/src/plugon/module/archive/ArchiveModule.php';
    include_once realpath(dirname(__FILE__)) . '/src/plugon/module/archive/ListModule.php';
    include_once realpath(dirname(__FILE__)) . '/src/plugon/output/OutputManager.php';
    include_once realpath(dirname(__FILE__)) . '/src/plugon/module/error/InternalErrorModule.php';
    include_once realpath(dirname(__FILE__)) . '/src/plugon/module/error/NotFoundPage.php';
    include_once realpath(dirname(__FILE__)) . '/src/plugon/module/Module.php';
    include_once realpath(dirname(__FILE__)) . '/src/modules.php';

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
        $requestPath = !empty($_GET["__path"]) ? $_GET["__path"] : DIRECTORY_SEPARATOR;
        $input = file_get_contents("php://input");
        $log->info($_SERVER["REMOTE_ADDR"] . " " . $requestPath);
        $log->verbose($requestPath . " " . json_encode($input, JSON_UNESCAPED_SLASHES));
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
        $log->verbose("Safely completed: " . ((int) (($endEvalTime - $startEvalTime) * 1000)) . "ms");
        Plugon::showStatus();
        $outputManager->output();
    } catch(\Throwable $e) {
        error_handler(E_ERROR, get_class($e) . ": " . $e->getMessage() . "\n" .
            $e->getTraceAsString(), $e->getFile(), $e->getLine());
    }

    Plugon::getDb();

    /**
     * @param string $class
     */
    function registerModule($class) {
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
    /**
     * @return string
     */
    function getInput(){
        global $input;
        return $input;
    }
    /**
     * @return string
     */
    function getRequestPath(){
        global $requestPath;
        return $requestPath;
    }
    /**
     * Redirect user to a path under the Plugon root, without a leading slash
     *
     * @param string $target   default homepage
     * @param bool   $absolute default true
     */
    function redirect($target = "", $absolute = false) {
        header("Location: " . ((!$absolute and $target !== "") ? Plugon::getRootPath() : "") . $target);
        Plugon::showStatus();
        exit();
    }

    /**
     * @param $err_no
     * @param $error
     * @param $err_file
     * @param $err_line
     */
    function error_handler($err_no, $error, $err_file, $err_line) {
        global $log;
        http_response_code(500);
        $ref_id = mt_rand();
        if(Plugon::$plainTextOutput) {
            OutputManager::$current->outputTree();
            echo "Error#$ref_id Level $err_no error at $err_file:$err_line: $error\n";
        }
        if(!isset($log)) $log = new Logger();
        $log->error("Error#$ref_id Level $err_no error at $err_file:$err_line: $error");
        if(!Plugon::$plainTextOutput) {
            OutputManager::terminateAll();
            (new InternalErrorModule((string) $ref_id))->output();
        }
        die;
    }

}