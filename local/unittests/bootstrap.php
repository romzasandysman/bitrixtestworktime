<?php
// bitrix/modules/main/include.php with no authorizing and Agents execution
define("NOT_CHECK_PERMISSIONS", true);
define("NO_AGENT_CHECK", true);
$GLOBALS["DBType"] = 'mysql';
$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../..';
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

include '../composer/vendor/autoload.php';

function initBitrixCore()
{
    // manual saving of DB resource
    global $DB;
    $app = \Bitrix\Main\Application::getInstance();
    $con = $app->getConnection();
    $DB->db_Conn = $con->getResource();

    // "authorizing" as admin
    $_SESSION["SESS_AUTH"]["USER_ID"] = 1;
}