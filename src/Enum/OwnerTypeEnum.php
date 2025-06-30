<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 店铺类型枚举
 *
 * 参考：https://developer.jdcloud.com/article/4117
 */
enum OwnerTypeEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    
    case SELF_OPERATED = 'g';  // 自营
    case POP = 'p';            // POP店铺

    public function getLabel(): string
    {
        return match($this) {
            self::SELF_OPERATED => '自营',
            self::POP => 'POP店铺',
        };
    }
} 