<?php
namespace My\Worktime\WorkDay;

use My\WorkTime\Interfaces\FabricTime;
use My\WorkTime\Interfaces\tableDate;
use My\WorkTime\Interfaces\tableSingleDate;

/**
 * get objects of process work day
*/
class FabricTimeWorkday implements FabricTime
{

    /**
     * @return tableDate
     */
    public function createWorkDay(): tableDate
    {
        return new WorkdayController();
    }

    /**
     * @return tableDate
     */
    public function createWorkPauseDay(): tableDate
    {
        return new WorkdayPauseController();
    }

    /**
     * @return tableSingleDate
     */
    public function createWorkDayLatenessDay(): tableSingleDate
    {
        return new WorkdayLatenessController();
    }
}