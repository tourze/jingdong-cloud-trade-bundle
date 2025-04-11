<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum AreaLevel: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PROVINCE = 1;
    case CITY = 2;
    case AREA = 3;
    case TOWN = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::PROVINCE => '省级',
            self::CITY => '市级',
            self::AREA => '县级',
            self::TOWN => '镇级',
        };
    }
}
