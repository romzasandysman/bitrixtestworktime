<?php


namespace My\WorkTime\Tables;


use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Type\DateTime;

class WorkDayPauseTable extends DataManager
{
    public static function getObjectClass()
    {
        return WorkDayPause::class;
    }

    public static function getCollectionClass()
    {
        return WorkDayPauses::class;
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new IntegerField('WORKDAY_ID'))->configureRequired(),

            (new DatetimeField('DATE_START', [
                'default_value' => (new DateTime())->toString()
            ])),

            (new DatetimeField('DATE_STOP')),

            (new Reference(
                'WORKDAY',
                WorkDayTable::class,
                Join::on('this.WORKDAY_ID', 'ref.ID')
            ))->configureJoinType('inner')
        ];
    }
}