<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 发票类型枚举
 */
enum InvoiceTypeEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case NORMAL = '1';
    case VAT = '2';
    case ELECTRONIC = '3';

    public function getLabel(): string
    {
        return match ($this) {
            self::NORMAL => '普通发票',
            self::VAT => '增值税发票',
            self::ELECTRONIC => '电子发票',
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
