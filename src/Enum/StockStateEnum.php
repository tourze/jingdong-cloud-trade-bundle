<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 库存状态枚举
 *
 * 参考：https://developer.jdcloud.com/article/4117
 */
enum StockStateEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case IN_STOCK = '1';      // 有货
    case OUT_OF_STOCK = '0';  // 无货

    public function getLabel(): string
    {
        return match($this) {
            self::IN_STOCK => '有货',
            self::OUT_OF_STOCK => '无货',
        };
    }
}
