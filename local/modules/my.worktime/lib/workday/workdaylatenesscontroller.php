<?php
namespace My\Worktime\WorkDay;

use Bitrix\Main\Type\Date;
use Bitrix\Main\ORM\Data\DataManager;
use My\WorkTime\Tables\Lateness;
use My\WorkTime\Interfaces\tableSingleDate;
use My\WorkTime\Tables\Latenesses;
use My\WorkTime\Tables\LatenessTable;

class WorkdayLatenessController implements tableSingleDate
{

    /**
     * @param $profileID
     * @param null $profileTable
     * @return bool|null
     * May get arr or string ProfileId
     */
    public function insertDate($profileID, $profileTable = null)
    {
        if (!$profileID || !$profileTable) return null;
        $profileID = !is_array($profileID) ? [$profileID] : $profileID;

        if ($profileID = $this->filterProfilesWhichNotYetLate($profileID)) {
            $returnedObjectIncomeTable = $profileTable::getList(['filter' => ['ID' => $profileID]])->fetchCollection();
            $objWorkDaysCollection = $this->getObjCollectionTable();

            foreach ($returnedObjectIncomeTable as $returnedObject) {
                $objWorkDaysCollection[] = $this->getObjTable()->setProfile($returnedObject);
            }

            return $objWorkDaysCollection->save();
        }else{
            return true;
        }
    }

    /**
     * @param $profileID
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function getProfilesInLatenessToday($profileID) : array
    {
        $arLateness = LatenessTable::getList([
           'filter' => ['PROFILE_ID' => $profileID, 'DATE' => (new Date())->toString()],
            'cache' => ([
                "ttl" => 86400
            ])
        ])->fetchAll();

        if (!$arLateness) return [];

        $arReturn = [];

        foreach ($arLateness as $arLate){
            $arReturn[] = $arLate['PROFILE_ID'];
        }

        return $arReturn;
    }

    /**
     * @param array $newProfiles
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function filterProfilesWhichNotYetLate(array $newProfiles)
    {
        $arProfilesInDb = $this->getProfilesInLatenessToday($newProfiles);

        return array_diff($newProfiles, $arProfilesInDb);
    }

    /**
     * @return \Bitrix\Main\ORM\Objectify\EntityObject
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    private function getObjTable()
    {
        return LatenessTable::createObject();
    }

    /**
     * @return \Bitrix\Main\ORM\Objectify\Collection
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    private function getObjCollectionTable()
    {
        return LatenessTable::createCollection();
    }
}