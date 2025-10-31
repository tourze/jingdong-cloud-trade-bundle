<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
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
enum StockStateEnum: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case IN_STOCK = '1';
    case OUT_OF_STOCK = '0';

    public function getLabel(): string
    {
        return match ($this) {
            self::IN_STOCK => '有货',
            self::OUT_OF_STOCK => '无货',
        };
    }

    /**
     * 获取所有枚举的选项数组（用于下拉列表等）
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function toSelectItems(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[] = [
                'value' => $case->value,
                'label' => $case->getLabel(),
            ];
        }

        return $result;
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::IN_STOCK => self::SUCCESS,
            self::OUT_OF_STOCK => self::DANGER,
        };
    }
}
