<?php


namespace My\WorkTime\Tables;


use Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\StringField;

class ProfileTable extends DataManager
{
    public static function getObjectClass()
    {
        return Profile::class;
    }

    public static function getCollectionClass()
    {
        return Profiles::class;
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new OneToMany('LATENESS', Lateness::class, 'PROFILE'))->configureJoinType('inner'),
            (new OneToMany('WORKDAYS', WorkDay::class, 'PROFILE'))->configureJoinType('inner'),

            (new StringField('LOGIN', ['size' => 255]))->configureRequired(),
            (new StringField('NAME', ['size' => 255])),
            (new StringField('LAST_NAME', ['size' => 255])),
            (new StringField('OFFSET', ['size' => 10])),
        ];
    }

    public static function OnAfterAdd(Event $event)
    {
        self::getEntity()->cleanCache();
    }

    public static function OnAfterUpdate(Event $event)
    {
        self::getEntity()->cleanCache();
    }
}