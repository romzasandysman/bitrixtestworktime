<?php
namespace My\Worktime\WorkDay;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\Type\DateTime;
use \My\WorkTime\Interfaces\tableDate;
use My\WorkTime\Tables\WorkDayPauseTable;
use My\WorkTime\Tables\WorkDays;
use My\WorkTime\Tables\WorkDayTable;

class WorkdayController implements tableDate
{
    /**
     * @param string $profileId
     * @param DataManager|null $objProfileTable
     * @return |null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * May get arr or string ProfileId
     */
    public function insertDateStart($profileId, $objProfileTable = null)
    {
        if (!$profileId) return null;

        $returnedObjectIncomeTable = $objProfileTable::getList(['filter' => ['ID' => $profileId]])->fetchCollection();
        $objWorkDaysCollection = $this->getObjCollectionTable();

        foreach ($returnedObjectIncomeTable as $returnedObject) {
            $objWorkDaysCollection[] = $this->getObjTable()->setProfile($returnedObject);
        }

        return $objWorkDaysCollection->save();
    }

    /**
     * @param string $profileId
     * @return \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult|bool|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * May get arr or string ProfileId
     */
    public function insertDateStop($profileId)
    {
        if (!$profileId) return null;

        $workDaysCollections = $this->getRowsWithStartedDay($profileId, true);

        foreach ($workDaysCollections as $workDayCollection){
            $workDayCollection->setDateStop((new DateTime())->toString());
        }

        return $workDaysCollections->save();
    }

    /**
     * @param array $arProfilesIds
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * by Profile
     */
    public function getListWithEmptyDateStart(array $arProfilesIds): array
    {
        $arReturn = [];

        if ($arReturn = self::getRowsWithStartedDay($arProfilesIds)){
            return $arReturn;
        }else{
            return [];
        }
    }

    /**
     * @param array $arProfilesIds
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * by Profile
     */
    public function getListWithEmptyDateEnd(array $arProfilesIds): array
    {
        $arReturn = [];

        if ($arReturn = self::getRowsWithNotEndedDay($arProfilesIds)){
            return $arReturn;
        }else{
            return [];
        }
    }

    /**
     * @param mixed $profileId
     * @return array|bool|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * by arr or string Profile
     */
    private function getRowsWithStartedDay($profileId, $bGetCollection = false)
    {
        if (!$profileId) return null;

        $dbData = WorkDayTable::getList([
            'filter' => ['PROFILE_ID' => $profileId, '!DATE_START' => null]
        ]);

        if ($bGetCollection){
            return $dbData->fetchCollection();
        }else{
            $dbData->fetchAll();
        }
    }


    /**
     * @param $profileId
     * @return array|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * by arr or string Profile
     */
    private function getRowsWithNotEndedDay($profileId)
    {
        if (!$profileId) return null;

        return WorkDayTable::getList([
            'filter' => ['PROFILE_ID' => $profileId, 'DATE_STOP' => null]
        ])->fetchAll();
    }

    /**
     * @return DataManager
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    private function getObjTable()
    {
        return WorkDayTable::createObject();
    }

    /**
     * @return WorkDays
     */
    private function getObjCollectionTable()
    {
        return new WorkDays;
    }
}