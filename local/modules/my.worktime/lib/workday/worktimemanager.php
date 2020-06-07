<?php


namespace My\Worktime\WorkDay;


use Bitrix\Main\ORM\Data\DataManager;
use My\Worktime\Main;
use My\WorkTime\Tables\ProfileTable;
use My\WorkTime\Tables\WorkDayTable;

/**
 * class for realise work with time of work day
 * has two functions for agents of bitrix
 */
class WorkTimeManager
{

    /**
     * time for detect when update db
    */
    private static $TIME_BEGIN_WORK_DAY = '09:00:00';
    private static $TIME_NEED_END_WORK_DAY = '00:00:00';
    private static $TIME_STOP_ENDED_DAY = '01:02:00';

    /**
     * @param mixed $profileID
     */
    public static function dayStart($profileID)
    {
        return (new FabricTimeWorkday)->createWorkDay()->insertDateStart($profileID, self::createObjProfileTable());
    }

    /**
     * @param mixed $profileID
     */

    public static function dayEnd($profileID)
    {
        return (new FabricTimeWorkday)->createWorkDay()->insertDateStop($profileID);
    }

    /**
     * @param mixed $workDayId
     */
    public static function dayPause($workDayId)
    {
       return (new FabricTimeWorkday)->createWorkPauseDay()->insertDateStart($workDayId, (new WorkDayTable()));
    }

    /**
     * @param mixed $workDayId
     */
    public static function dayContinue($workDayId)
    {
        return (new FabricTimeWorkday)->createWorkPauseDay()->insertDateStop($workDayId);
    }

    /**
     * @param mixed $profileID
     */
    public static function addLateness($profileID)
    {
        return (new FabricTimeWorkday)->createWorkDayLatenessDay()->insertDate($profileID, self::createObjProfileTable());
    }

    /**
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *  use by Agent every hour
     * end work day if user miss
     */
    public static function checkEndDayAllUsers()
    {
        $strReturned = '\My\Worktime\WorkDay\WorkTimeManager::checkEndDayAllUsers();';

        if (!Main::selfLoad()) return $strReturned;

        $arProfilesDayEnd = [];
        foreach (self::getAllProfiles() as $arProfile){
            if (self::isDayEnd($arProfile['OFFSET'])){
                $arProfilesDayEnd[] = $arProfile['ID'];
            }
        }

        if (!$arProfilesDayEnd) return $strReturned;

        $arWorkDaysWithEmptyDayEnd = (new FabricTimeWorkday)->createWorkDay()->getListWithEmptyDateEnd($arProfilesDayEnd);
        self::dayEnd(
            self::getProfilesNotInArray($arProfilesDayEnd, $arWorkDaysWithEmptyDayEnd)
        );

        return $strReturned;
    }

    /**
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * use by Agent every hour
     * add lateness if user late on work
     */
    public static function checkStartDayAllUsers()
    {
        $strReturned = '\My\Worktime\WorkDay\WorkTimeManager::checkStartDayAllUsers();';

        if (!Main::selfLoad()) return $strReturned;

        $arProfilesDayBegin = [];
        foreach (self::getAllProfiles() as $arProfile){
            if (self::isDayWorkBegin($arProfile['OFFSET'])){
                $arProfilesDayBegin[] = $arProfile['ID'];
            }
        }

        if (!$arProfilesDayBegin) return $strReturned;

        $arWorkDaysWithEmptyDayStart = (new FabricTimeWorkday)->createWorkDay()->getListWithEmptyDateStart($arProfilesDayBegin);

        self::addLateness(
            self::getProfilesNotInArray($arProfilesDayBegin, $arWorkDaysWithEmptyDayStart)
        );

        return $strReturned;
    }

    /**
     * @param array $arProfiles
     * @param array $arProfilesInDataBase
     * @return array
     * system function, filter users in db from users which time with offset do some in db
     */
    private static function getProfilesNotInArray(array $arProfiles, array $arProfilesInDataBase) : array
    {
        $arReturn = [];

        foreach ($arProfilesInDataBase as $arProfileDB){
            if (in_array($arProfileDB['PROFILE_ID'], $arProfiles)){
                $arReturn[] = $arProfileDB['PROFILE_ID'];
            }
        }

        return array_diff($arProfiles, $arReturn);
    }

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function getAllProfiles()
    {
        return self::createObjProfileTable()->getList([
            'cache' => ([
                "ttl"=>3600
            ])
        ])->fetchAll();
    }

    /**
     * @return ProfileTable
     */
    private static function createObjProfileTable() : DataManager
    {
        return (new ProfileTable());
    }

    /**
     * @param string $offset
     * @return bool
     * @throws \Exception
     */
    private static function isDayWorkBegin(string $offset) : bool
    {
        $dateOffset = new \DateTime('now', timezone_open($offset));
        $dateBeginWork = new \DateTime(self::$TIME_BEGIN_WORK_DAY);

        return self::checkTimeOffsetPastDate($dateOffset, $dateBeginWork);
    }

    /**
     * @param string $offset
     * @return bool
     * @throws \Exception
     */
    private static function isDayEnd(string $offset): bool
    {
        $dateOffset = new \DateTime('now', timezone_open($offset));
        $dateEndWork = new \DateTime(self::$TIME_NEED_END_WORK_DAY);
        $dateStopEndWork = new \DateTime(self::$TIME_STOP_ENDED_DAY);

        return self::checkTimeOffsetPastDate($dateOffset, $dateEndWork) && !self::checkTimeOffsetPastDate($dateOffset, $dateStopEndWork);
    }

    /**
     * @param \DateTime $dateOffset
     * @param \DateTime $date
     * @return bool
     * compare two dates, bring them to same format, make unix time, return bool from compare usual date with date with offset
     * date offset past date or not
     */
    private static function checkTimeOffsetPastDate(\DateTime $dateOffset, \DateTime $date): bool
    {
        $dateTimeOffset = strtotime($dateOffset->format('Y-m-d H:i:s'));
        $dateTime = strtotime($date->format('Y-m-d H:i:s'));

        $subTime = $dateTime - $dateTimeOffset;
        $hours = ($subTime/(60*60))%24;
        $minutes = ($subTime/60)%60;

        return ($hours < 0 || $minutes < 0);
    }
}