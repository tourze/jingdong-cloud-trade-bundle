<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum FlashSaleEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NOT_FLASH_SALE = 0;
    case FLASH_SALE = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::NOT_FLASH_SALE => '非秒杀商品',
            self::FLASH_SALE => '秒杀商品',
        };
    }
}
