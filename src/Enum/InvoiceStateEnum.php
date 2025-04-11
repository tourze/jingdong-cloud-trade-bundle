<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 发票状态枚举
 */
enum InvoiceStateEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NOT_APPLIED = '0';      // 未申请
    case PENDING = '1';          // 申请中
    case ISSUED = '2';           // 已开票
    case FAILED = '3';           // 开票失败
    case CANCELLED = '4';        // 已取消

    public function getLabel(): string
    {
        return match($this) {
            self::NOT_APPLIED => '未申请',
            self::PENDING => '申请中',
            self::ISSUED => '已开票',
            self::FAILED => '开票失败',
            self::CANCELLED => '已取消',
        };
    }

    /**
     * 获取所有状态选项
     */
    public static function getChoices(): array
    {
        return [
            '未申请' => self::NOT_APPLIED->value,
            '申请中' => self::PENDING->value,
            '已开票' => self::ISSUED->value,
            '开票失败' => self::FAILED->value,
            '已取消' => self::CANCELLED->value,
        ];
    }
    
    /**
     * 获取默认值
     */
    public static function getDefault(): string
    {
        return self::NOT_APPLIED->value;
    }
}
