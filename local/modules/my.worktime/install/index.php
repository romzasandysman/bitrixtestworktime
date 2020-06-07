<?php

class my_worktime extends CModule{
    var $MODULE_ID = 'my.worktime';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $strError = '';

    function __construct()
    {
        \Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);
        $arModuleVersion = array();
        include(__DIR__ . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("RZ_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("RZ_MODULE_DESC");

        $this->PARTNER_NAME = GetMessage("RZ_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("RZ_PARTNER_URI");
    }

    function InstallAgents()
    {
        CAgent::AddAgent('\My\WorkTime\WorkDay\WorkTimeManager::checkStartDayAllUsers();',
            $this->MODULE_ID, "Y", 3600, "", "Y", "", 1);
        CAgent::AddAgent('\My\WorkTime\WorkDay\WorkTimeManager::checkEndDayAllUsers();',
            $this->MODULE_ID, "Y", 3600, "", "Y", "", 2);
    }

    function InstallDB($arParams = array())
    {

        $this->regClasess();
        $arTablesNames = $this->getTablesNames();

        if (!\Bitrix\Main\Application::getConnection()->isTableExists($arTablesNames['PROFILE'])) {
            \My\WorkTime\Tables\ProfileTable::getEntity()->createDbTable();
        }

        if (!\Bitrix\Main\Application::getConnection()->isTableExists($arTablesNames['WORKDAY'])) {
            \My\WorkTime\Tables\WorkDayTable::getEntity()->createDbTable();
        }

        if (!\Bitrix\Main\Application::getConnection()->isTableExists($arTablesNames['LATENESS'])) {
            \My\WorkTime\Tables\LatenessTable::getEntity()->createDbTable();
        }

        if (!\Bitrix\Main\Application::getConnection()->isTableExists($arTablesNames['WORKDAYPAUSE'])) {
            \My\WorkTime\Tables\WorkDayPauseTable::getEntity()->createDbTable();
        }

        $this->fillTableProfile();
        return true;
    }

    function UnInstallDB($arParams = array())
    {
        $this->regClasess();
        $arTablesNames = $this->getTablesNames();

        if (\Bitrix\Main\Application::getConnection()->isTableExists($arTablesNames['WORKDAYPAUSE'])) {
            \My\WorkTime\Tables\WorkDayPauseTable::getEntity()->getConnection()->dropTable($arTablesNames['WORKDAYPAUSE']);
        }

        if (\Bitrix\Main\Application::getConnection()->isTableExists($arTablesNames['LATENESS'])) {
            \My\WorkTime\Tables\LatenessTable::getEntity()->getConnection()->dropTable($arTablesNames['LATENESS']);
        }

        if (\Bitrix\Main\Application::getConnection()->isTableExists($arTablesNames['WORKDAY'])) {
            \My\WorkTime\Tables\WorkDayTable::getEntity()->getConnection()->dropTable($arTablesNames['WORKDAY']);
        }

        if (\Bitrix\Main\Application::getConnection()->isTableExists($arTablesNames['PROFILE'])) {
            \My\WorkTime\Tables\ProfileTable::getEntity()->getConnection()->dropTable($arTablesNames['PROFILE']);
        }

        return true;
    }

    function UnInstallAgents()
    {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
    }

    function DoInstall()
    {
        $this->InstallAgents();
        RegisterModule($this->MODULE_ID);
        $this->InstallDB();
    }

    function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallAgents();
        UnRegisterModule($this->MODULE_ID);
    }

    function regClasess()
    {
        \Bitrix\Main\Loader::switchAutoLoad(false);
        \Bitrix\Main\Loader::includeModule($this->MODULE_ID);
        \Bitrix\Main\Loader::registerAutoLoadClasses($this->MODULE_ID, array(
            '\My\WorkTime\Tables\ProfileTable' => 'lib/tables/profiletable.php',
            '\My\WorkTime\Tables\WorkDayTable' => 'lib/tables/workdaytable.php',
            '\My\WorkTime\Tables\LatenessTable' => 'lib/tables/latenesstable.php',
            '\My\WorkTime\Tables\WorkDayPauseTable' => 'lib/tables/workdaypausetable.php'
        ));
        \Bitrix\Main\Loader::switchAutoLoad(true);
    }

    function fillTableProfile()
    {
        $profiles = new \My\WorkTime\Tables\Profiles;
        $profiles[] = $this->setValuesProfile($this->createProfileObj(), 'first', 'Carl', 'Black', '+0300');
        $profiles[] = $this->setValuesProfile($this->createProfileObj(), 'second', 'Make', 'Trinity', '+0700');
        $profiles[] = $this->setValuesProfile($this->createProfileObj(), 'third', 'Sandy', 'Morfiuce', '-0300');
        $profiles[] = $this->setValuesProfile($this->createProfileObj(), 'those', 'Jone', 'Week', '-0700');
        $profiles[] = $this->setValuesProfile($this->createProfileObj(), 'fifths', 'Anne', 'Anderson', '+0900');
        $profiles->save();
    }

    function setValuesProfile($obj, string $login, string $name, string $lasName, string $offset)
    {
        $obj->setLogin($login);
        $obj->setName($name);
        $obj->setLastName($lasName);
        $obj->setOffset($offset);

        return $obj;
    }

    private function getTablesNames()
    {
        $profileTableName = \My\WorkTime\Tables\ProfileTable::getEntity()->getDBTableName();
        $latenessTableName = \My\WorkTime\Tables\LatenessTable::getEntity()->getDBTableName();
        $workDayTableName = \My\WorkTime\Tables\WorkDayTable::getEntity()->getDBTableName();
        $workDayPauseTableName = \My\WorkTime\Tables\WorkDayPauseTable::getEntity()->getDBTableName();


        return [
            'PROFILE' => $profileTableName,
            'WORKDAYPAUSE' => $workDayPauseTableName,
            'LATENESS' => $latenessTableName,
            'WORKDAY' => $workDayTableName,
        ];
    }

    private function createProfileObj()
    {
        return new \My\WorkTime\Tables\Profile;
    }
}