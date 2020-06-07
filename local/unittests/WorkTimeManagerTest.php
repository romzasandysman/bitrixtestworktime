<?php

use Bitrix\Main\ORM\Objectify\Collection;
use My\WorkTime\Tables\ProfileTable;
use My\WorkTime\Tables\WorkDayPauseTable;
use My\WorkTime\Tables\WorkDayTable;
use PHPUnit\Framework\TestCase,
    \My\Worktime\WorkDay\WorkTimeManager;

class WorkTimeManagerTest extends TestCase
{
    public function setUp()
    {
        if ($this->includeModuleMyWork() && false) {
            $collectionWorkDayPause = WorkDayPauseTable::getList()->fetchCollection();
            $collectionWorkDay = WorkDayTable::getList()->fetchCollection();
            $collectionLateness = \My\WorkTime\Tables\LatenessTable::getList()->fetchCollection();

            $this->deleteAllrows($collectionWorkDayPause);
            $this->deleteAllrows($collectionWorkDay);
            $this->deleteAllrows($collectionLateness);
        }
    }

    private function deleteAllrows(Collection $collection)
    {
        foreach ($collection as $collect){
            $collect->delete();
            $collect->save();
        }
    }

    /**
     * @dataProvider providerWorkDays
     */
    public function testDayPause($workDays)
    {
        $result =  WorkTimeManager::dayPause($workDays);
        $this->assertTrue($result->isSuccess());
    }

    /**
     * @dataProvider providerProfiles
    */
    public function testDayStart($arProfilesIds)
    {
       $result = WorkTimeManager::dayStart($arProfilesIds);
       $this->assertTrue($result->isSuccess());
    }

    /**
     * @dataProvider providerProfiles
     */
    public function testDayEnd($arProfilesIds)
    {
        $result =  WorkTimeManager::dayEnd($arProfilesIds);
        $this->assertTrue($result->isSuccess());
    }

    /**
     * @dataProvider providerWorkDays
     */
    public function testDayContinue($workDays)
    {
        $result =  WorkTimeManager::dayContinue($workDays);
        $this->assertTrue($result->isSuccess());
    }

    public function testCheckEndDayAllUsers()
    {
        $result =  WorkTimeManager::checkEndDayAllUsers();
        $this->assertIsString($result);
    }

    public function testCheckStartDayAllUsers()
    {
        $result =  WorkTimeManager::checkStartDayAllUsers();
        $this->assertIsString($result);
    }

    /**
     * @dataProvider providerProfiles
     */
    public function testAddLateness($arProfilesIds)
    {
        $result =  WorkTimeManager::addLateness($arProfilesIds);
        $this->assertTrue($result->isSuccess());
    }

    private function includeModuleMyWork()
    {
        return \Bitrix\Main\Loader::includeModule('my.worktime');
    }

    public function providerProfiles()
    {
        return [
                [[1,3,5]]
            ];
    }

    public function providerWorkDays()
    {
        return [
                [[1,2,3,4,5]]
            ];
    }
}
