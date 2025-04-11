<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 京东云交易订单状态枚举
 *
 * 参考文档：https://developer.jdcloud.com/article/4117
 */
enum OrderStateEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case CREATED = 'CREATED';
    case PAID = 'PAID';
    case SHIPPED = 'SHIPPED';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';
    case CLOSED = 'CLOSED';
    case AFTER_SALE = 'AFTER_SALE';

    public function getLabel(): string
    {
        return match($this) {
            self::CREATED => '已创建',
            self::PAID => '已支付',
            self::SHIPPED => '已发货',
            self::COMPLETED => '已完成',
            self::CANCELLED => '已取消',
            self::CLOSED => '已关闭',
            self::AFTER_SALE => '售后中',
        };
    }

    /**
     * 获取所有状态选项（用于表单选择）
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }
        return $options;
    }
}
