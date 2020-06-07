<?php
namespace My\WorkTime\Interfaces;

interface FabricTime
{
    public function createWorkDay() : tableDate;

    public function createWorkPauseDay() : tableDate;

    public function createWorkDayLatenessDay() : tableSingleDate;
}