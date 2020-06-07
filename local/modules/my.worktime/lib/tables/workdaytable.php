<?php


namespace My\WorkTime\Tables;


use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Test\Typography\StoreBookTable;
use Bitrix\Main\Type\DateTime;

class WorkDayTable extends DataManager
{
    public static function getObjectClass()
    {
        return WorkDay::class;
    }

    public static function getCollectionClass()
    {
        return WorkDays::class;
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new IntegerField('PROFILE_ID'))->configureRequired(),

            (new DatetimeField('DATE_START', [
                'default_value' => (new DateTime())->toString()
            ])),

            (new DatetimeField('DATE_STOP')),

            (new OneToMany('WORKDAY_PAUSES', WorkDayPauseTable::class, 'WORKDAY'))->configureJoinType('inner'),

            (new Reference(
                'PROFILE',
                ProfileTable::class,
                Join::on('this.PROFILE_ID', 'ref.ID')
            ))->configureJoinType('inner')
        ];
    }
}