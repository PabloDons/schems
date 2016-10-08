<?php
require_once "core/init.php";

if ($uuid) {
    newsession($uuid);
    header("");
    die();
} else {
    header($conf["server"]["protocol"]."://".$conf["server"]["host"]."/register.php");
    die();
}



require "core/sqlclose.php";
