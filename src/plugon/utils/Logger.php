<?php

/*
 * Poggit
 *
 * Copyright (C) 2016 Poggit
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

namespace plugon\utils;

include realpath(dirname(__FILE__)) . '../module/Module.php';
use plugon\module\Module;

class Logger {
    const LEVEL_VERBOSE = "verbose";
    const LEVEL_DEBUG = "debug";
    const LEVEL_INFO = "info";
    const LEVEL_WARN = "warn";
    const LEVEL_ERROR = "error";
    const LEVEL_ASSERT = "assert";

    /**
     * @var array
     */
    private $streams = [];

    public function __construct() {
        if(!is_dir(Module::LOG_DIR)) {
            mkdir(Module::LOG_DIR, 0777, true);
        }
    }

    /**
     * @param string $message
     */
    public function verbose($message) {
        $this->log(self::LEVEL_VERBOSE, $message);
    }

    /**
     * @param string $message
     */
    public function debug($message) {
        $this->log(self::LEVEL_DEBUG, $message);
    }

    /**
     * @param string $message
     */
    public function info($message) {
        $this->log(self::LEVEL_INFO, $message);
    }

    /**
     * @param string $message
     */
    public function warning($message) {
        $this->log(self::LEVEL_WARN, $message);
    }

    /**
     * @param string $message
     */
    public function error($message) {
        $this->log(self::LEVEL_ERROR, $message);
    }

    /**
     * @param string $message
     */
    public function wtf($message) {
        $this->log(self::LEVEL_ASSERT, $message);
    }

    /**
     * @param string $level
     * @param string $message
     */
    private function log( $level, $message) {
        if(!isset($this->streams[$level])) {
            $this->createStream($level);
        }
        fwrite($this->streams[$level], date('M j H:i:s ') . $message . "\n");
    }

    /**
     * @param string $level
     */
    private function createStream($level) {
        $this->streams[$level] = fopen(Module::LOG_DIR . "$level.log", "at");
    }

    public function __destruct() {
        foreach($this->streams as $stream) {
            fclose($stream);
        }
    }
}
