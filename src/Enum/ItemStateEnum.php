<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 京东云交易订单商品状态枚举
 */
enum ItemStateEnum: string implements Itemable, Labelable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 正常
     */
    case NORMAL = 'NORMAL';

    /**
     * 退货中
     */
    case RETURNING = 'RETURNING';

    /**
     * 已退货
     */
    case RETURNED = 'RETURNED';

    /**
     * 换货中
     */
    case EXCHANGING = 'EXCHANGING';

    /**
     * 已换货
     */
    case EXCHANGED = 'EXCHANGED';

    /**
     * 获取状态描述
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::NORMAL => '正常',
            self::RETURNING => '退货中',
            self::RETURNED => '已退货',
            self::EXCHANGING => '换货中',
            self::EXCHANGED => '已换货',
        };
    }

    public function getLabel(): string
    {
        return $this->getDescription();
    }

    /**
     * 获取所有状态选项（用于表单选择）
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

    public function getBadge(): string
    {
        return match ($this) {
            self::NORMAL => self::SUCCESS,
            self::RETURNING => self::WARNING,
            self::RETURNED => self::SECONDARY,
            self::EXCHANGING => self::INFO,
            self::EXCHANGED => self::PRIMARY,
        };
    }
}
