<?php
namespace Astramedia\Links;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class LinksTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'links';
    }
    public static function getMap(){
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\IntegerField('RHYTHMOLOGIST_ID'),
            new Entity\ReferenceField(
                'RHYTHMOLOGIST',
                '\Bitrix\Iblock\ElementTable',
                array('=this.RHYTHMOLOGIST_ID' => 'ref.ID')
            ),
            new Entity\IntegerField('SERVICE_ID'),
            new Entity\ReferenceField(
                'SERVICE',
                '\Bitrix\Iblock\ElementTable',
                array('=this.SERVICE_ID' => 'ref.ID')
            ),
            new Entity\IntegerField('SUBSPECIES_SERVICES_ID'),
            new Entity\ReferenceField(
                'SUBSPECIES_SERVICES',
                '\Bitrix\Iblock\ElementTable',
                array('=this.SUBSPECIES_SERVICES_ID' => 'ref.ID')
            ),
            new Entity\TextField('DURATION_OR_TYPE_ID'),
            new Entity\TextField("LINK"),
            new Entity\DatetimeField("DATE_CREATE",array(
                'default_value' => new Type\DateTime
            )),
            new Entity\DatetimeField("DATE_UPDATE",array(
                'default_value' => new Type\DateTime
            )),
        );
    }
}