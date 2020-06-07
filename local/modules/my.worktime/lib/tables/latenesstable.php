<?php


namespace My\WorkTime\Tables;


use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Type\Date;

class LatenessTable extends DataManager
{
    public static function getObjectClass()
    {
        return Lateness::class;
    }

    public static function getCollectionClass()
    {
        return Latenesses::class;
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new IntegerField('PROFILE_ID'))->configureRequired(),

            (new DateField('DATE', [
                'default_value' => (new Date())->toString()
            ])),

            (new Reference(
                'PROFILE',
                ProfileTable::class,
                Join::on('this.PROFILE_ID', 'ref.ID')
            ))->configureJoinType('inner')
        ];
    }
}