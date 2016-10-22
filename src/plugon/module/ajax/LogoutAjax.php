<?php
namespace plugon\module\ajax;

class LogoutAjax extends AjaxModule {

    protected function impl() {
        $_SESSION["plugon"] = [];
        echo "{}";
    }

    public function getName() : string {
        return "logout";
    }

    protected function fallback() : bool {
        ?>
        <html>
        <head>
            <?php $this->headIncludes() ?>
        </head>
        <body>
        <?php $this->bodyHeader() ?>
        <div id="body">
            <h1>Logout</h1>
            <p>Do you really want to logout?</p>
            <span class="action" onclick="logout()">Logout</span>
        </div>
        </body>
        </html>
        <?php
        return false;
    }

}