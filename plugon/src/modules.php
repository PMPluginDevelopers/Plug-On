<?php
namespace plugon;

include_once realpath(dirname(__FILE__)) . '/plugon/module/archive/ArchiveModule.php';
include_once realpath(dirname(__FILE__)) . '/plugon/module/res/JsModule.php';
include_once realpath(dirname(__FILE__)) . '/plugon/module/res/ResModule.php';
include_once realpath(dirname(__FILE__)) . '/plugon/module/ajax/CsrfModule.php';
include_once realpath(dirname(__FILE__)) . '/plugon/module/ajax/LogoutAjax.php';
include_once realpath(dirname(__FILE__)) . '/plugon/module/ajax/LoginProcessAjax.php';
include_once realpath(dirname(__FILE__)) . '/plugon/module/ajax/PersistLocAjax.php';
include_once realpath(dirname(__FILE__)) . '/plugon/module/auth/SignInModule.php';
include_once realpath(dirname(__FILE__)) . '/plugon/module/auth/SignUpModule.php';

use plugon\module\archive\ArchiveModule;
use plugon\module\res\JsModule;
use plugon\module\res\ResModule;
use plugon\module\ajax\CsrfModule;
use plugon\module\ajax\LogoutAjax;
use plugon\module\ajax\LoginProcessAjax;
use plugon\module\ajax\PersistLocAjax;
use plugon\module\auth\SignInModule;
use plugon\module\auth\SignUpModule;

registerModule(ArchiveModule::class);
registerModule(JsModule::class);
registerModule(ResModule::class);
registerModule(CsrfModule::class);
registerModule(LogoutAjax::class);
registerModule(PersistLocAjax::class);
registerModule(LoginProcessAjax::class);
registerModule(SignInModule::class);
registerModule(SignUpModule::class);