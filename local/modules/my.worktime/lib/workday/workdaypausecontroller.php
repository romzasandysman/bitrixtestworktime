<?php
namespace My\Worktime\WorkDay;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Objectify\Collection;
use Bitrix\Main\Type\DateTime;
use \My\WorkTime\Interfaces\tableDate;
use My\WorkTime\Tables\WorkDayPauses;
use My\WorkTime\Tables\WorkDayPauseTable;

class WorkdayPauseController implements tableDate
{
    private static $objWorkDayPause;

    /**
     * WorkdayPause constructor.
     */
    public function __construct()
    {
        self::$objWorkDayPause = $this->getObjTable();
    }

    /**
     * @param string $workDayId
     * @param DataManager|null $objectTable
     * @return |null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * by arr or string work day id
     */
    public function insertDateStart($workDayId, $objectTable = null)
    {
        $returnedObjectIncomeTable = $objectTable::getList(['filter' => ['ID' => $workDayId]])->fetchCollection();
        $objWorkDaysPauseCollection = $this->getObjTableCollection();

        foreach ($returnedObjectIncomeTable as $returnedObject) {
            $objWorkDaysPauseCollection[] = $this->getObjTable()->setWorkday($returnedObject);
        }

        return $objWorkDaysPauseCollection->save();
    }

    /**
     * @param string $workDayId
     * @return \Bitrix\Main\ORM\Data\AddResult|\Bitrix\Main\ORM\Data\Result|\Bitrix\Main\ORM\Data\UpdateResult|bool|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * by arr or string work day id
     */
    public function insertDateStop($workDayId)
    {
        if (!$workDayId) return null;

        $workDaysPauseCollections = $this->getWorkPauseDayToStop($workDayId, true);

        foreach ($workDaysPauseCollections as $wordDayPauseCollection){
            $wordDayPauseCollection->setDateStop(new DateTime());
        }

        return $workDaysPauseCollections->save();
    }

    /** TODO: if will needed
     * @param array $linkID
     * @return array
     */
    public function getListWithEmptyDateStart(array $linkID) : array
    {
        return [];
    }

    /** TODO: if will needed
     * @param array $linkID
     * @return array
     */

    public function getListWithEmptyDateEnd(array $linkID) : array
    {
        return [];
    }

    /**
     * @param string $workDayId
     * @param bool $bGetCollection
     * @return array|bool|false|Collection
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException by arr or string work day id
     */
    private function getWorkPauseDayToStop($workDayId, $bGetCollection = false)
    {
        $dbData = WorkDayPauseTable::getList([
            'filter' => ['WORKDAY_ID' => $workDayId, 'DATE_START' != null]
        ]);
        return $bGetCollection ? $dbData->fetchCollection() : $dbData->fetch();
    }

    /**
     * @return |WorkDayPause
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    private function getObjTable()
    {
        return WorkDayPauseTable::createObject();
    }

    /**
     * @return WorkDayPauses
     */
    private function getObjTableCollection() : Collection
    {
        return new WorkDayPauses;
    }
}