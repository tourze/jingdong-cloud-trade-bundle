<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 京东云交易售后服务类型枚举
 */
enum AfsTypeEnum: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 退货
     */
    case RETURN = 'RETURN';

    /**
     * 换货
     */
    case EXCHANGE = 'EXCHANGE';

    /**
     * 维修
     */
    case REPAIR = 'REPAIR';

    /**
     * 仅退款
     */
    case REFUND_ONLY = 'REFUND_ONLY';

    /**
     * 获取类型描述
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::RETURN => '退货',
            self::EXCHANGE => '换货',
            self::REPAIR => '维修',
            self::REFUND_ONLY => '仅退款',
        };
    }

    /**
     * 获取标签
     */
    public function getLabel(): string
    {
        return $this->getDescription();
    }

    /**
     * 获取所有类型选项（用于表单选择）
     *
     * @return array<string, string>
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getDescription();
        }

        return $options;
    }
}
