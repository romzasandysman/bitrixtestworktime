<?php
namespace My\WorkTime\Interfaces;

interface tableDate
{
    public function insertDateStart($linkID, $objectTable = null);

    public function insertDateStop($linkID);

    public function getListWithEmptyDateStart(array $linkID) : array ;

    public function getListWithEmptyDateEnd(array $linkID) : array;
}