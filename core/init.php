<?php
if(isset($_POST["session_id"])) {
    session_id($_POST["session_id"]);
}
if(!isset($_SESSION)) {
session_start();
}
$conf = json_decode(file_get_contents("../config.json") or file_get_contents("../config-default.json"));
$_db = new mysqli($conf["mysql"]["host"], $conf["mysql"]["username"], $conf["mysql"]["password"], $conf["mysql"]["database"], $conf["mysql"]["port"]);
if ($_db->connect_error) {
    die("error connecting to database server");
}

function db_query($sql) {
    return $_db->query($sql);
}

function db_result($query) {
    $result = $this->query($query);
    $return = array();
    while ($row = $result->fech_assoc()) {
        array_push($return, $row);
    }
    return $return;
}

function db_insert($table, $colums) {
    $keys = array_keys($colums);
    db_query("INSERT INTO $table (`".implode($keys, "`,`")."`) VALUES (`".implode($colums, "`,`")."`)");
}

function newcookie($name, $value, $expiry, $path) {
    setcookie($name, $value, time() + $conf["cookie"]["name"], $path)
}

function makehash($string, $salt) {
    return hash('sha256', $string . $salt);
}

function salt($length) {
    return mcrypt_create_iv($length);
}

function newsession($uuid) {
    $sessionid = salt(64);
    $sessionid_hashed = makehash($sessionid);
    newcookie($conf["cookie"]["name"], $sessionid, time() + (86400 * $conf["cookie"]["exipry_days"]), "/");
    db_insert("sessions", array("uuid"=>$uuid));
}

function validatetoken($token) {
    $uuid = $_db->query("SELECT uuid FROM tokens WHERE token = ".$token)->fetch_assoc()["uuid"];
    if ($uuid)  {
        return $uuid;
    } else {
        return null;
    }
}

if (isset($_COOKIE[$conf["cookie"]["name"]])) {
    $uuid = db_query("SELECT uuid FROM sessions WHERE session = ");
} elseif (isset($_GET["token"])) {
    $uuid = validatetoken($_GET["token"]);
} else {
    $uuid = null;
}



//UTF-8 Encoding
header('Content-Type: text/html; charset=utf-8');

//Timezone
if( ! ini_get('date.timezone') ) {
    date_default_timezone_set('GMT');
}
