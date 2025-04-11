<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 支付状态枚举
 */
enum PaymentStateEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PENDING = '1';      // 待支付
    case PAID = '2';         // 已支付
    case REFUNDING = '3';    // 退款中
    case REFUNDED = '4';     // 已退款
    case FAILED = '5';       // 支付失败

    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => '待支付',
            self::PAID => '已支付',
            self::REFUNDING => '退款中',
            self::REFUNDED => '已退款',
            self::FAILED => '支付失败',
        };
    }
} 