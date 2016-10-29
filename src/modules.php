<?php
namespace plugon;

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