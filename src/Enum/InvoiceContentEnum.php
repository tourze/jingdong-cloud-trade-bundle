<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 发票内容类型枚举
 */
enum InvoiceContentEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case GOODS = '1';
    case CATEGORY = '2';
    case CUSTOM = '3';

    public function getLabel(): string
    {
        return match ($this) {
            self::GOODS => '商品明细',
            self::CATEGORY => '商品类别',
            self::CUSTOM => '自定义',
        };
    }

    /**
     * 获取适用于EasyAdmin ChoiceField的选项数组
     *
     * @return array<string, string>
     */
    public static function toSelectChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->getLabel()] = $case->value;
        }

        return $choices;
    }

    /**
     * 获取所有类型选项
     *
     * @return array<string, string>
     */
    public static function getChoices(): array
    {
        return [
            '商品明细' => self::GOODS->value,
            '商品类别' => self::CATEGORY->value,
            '自定义' => self::CUSTOM->value,
        ];
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
}
