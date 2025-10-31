<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 商品状态枚举
 *
 * 参考：https://developer.jdcloud.com/article/4117
 */
enum SkuStateEnum: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case ON_SALE = '1';
    case OFF_SALE = '0';

    public function getLabel(): string
    {
        return match ($this) {
            self::ON_SALE => '上架',
            self::OFF_SALE => '下架',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::ON_SALE => self::SUCCESS,
            self::OFF_SALE => self::SECONDARY,
        };
    }
}
