<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 商品退货政策枚举
 *
 * 参考京东API字段 is7ToReturn 和 is15ToReturn
 */
enum ReturnPolicyEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NOT_SUPPORTED = 0;     // 不支持
    case SUPPORTED = 1;         // 支持

    public function getLabel(): string
    {
        return match($this) {
            self::NOT_SUPPORTED => '不支持',
            self::SUPPORTED => '支持',
        };
    }
}
