<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 配送方式枚举
 *
 * 参考：https://developer.jdcloud.com/article/4117
 */
enum DeliveryTypeEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case JD_DELIVERY = '1';      // 京东配送
    case NON_JD_DELIVERY = '0';  // 非京东配送

    public function getLabel(): string
    {
        return match($this) {
            self::JD_DELIVERY => '京东配送',
            self::NON_JD_DELIVERY => '非京东配送',
        };
    }
}
