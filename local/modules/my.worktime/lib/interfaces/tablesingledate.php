<?php
namespace My\WorkTime\Interfaces;

use Bitrix\Main\ORM\Data\DataManager;

interface tableSingleDate
{
    public function insertDate($linkId, $relativeTable = null);
}