<?php
namespace plugon;

use plugon\module\archive\ArchiveModule;
use plugon\module\submit\SubmitModule;
use plugon\module\res\JsModule;
use plugon\module\res\ResModule;
use plugon\module\ajax\CsrfModule;
use plugon\module\ajax\LogoutAjax;
use plugon\module\ajax\PersistLocAjax;

registerModule(ArchiveModule::class);
//registerModule(SubmitModule::class);

registerModule(JsModule::class);
registerModule(ResModule::class);

registerModule(CsrfModule::class);
registerModule(LogoutAjax::class);
registerModule(PersistLocAjax::class);