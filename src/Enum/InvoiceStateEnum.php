<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 发票状态枚举
 */
enum InvoiceStateEnum: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case NOT_APPLIED = '0';
    case PENDING = '1';
    case ISSUED = '2';
    case FAILED = '3';
    case CANCELLED = '4';

    public function getLabel(): string
    {
        return match ($this) {
            self::NOT_APPLIED => '未申请',
            self::PENDING => '申请中',
            self::ISSUED => '已开票',
            self::FAILED => '开票失败',
            self::CANCELLED => '已取消',
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

    public function getBadge(): string
    {
        return match ($this) {
            self::NOT_APPLIED => self::LIGHT,
            self::PENDING => self::WARNING,
            self::ISSUED => self::SUCCESS,
            self::FAILED => self::DANGER,
            self::CANCELLED => self::SECONDARY,
        };
    }
}
