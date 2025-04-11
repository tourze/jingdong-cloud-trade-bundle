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

    case GOODS = '1';        // 商品明细
    case CATEGORY = '2';     // 商品类别
    case CUSTOM = '3';       // 自定义

    public function getLabel(): string
    {
        return match($this) {
            self::GOODS => '商品明细',
            self::CATEGORY => '商品类别',
            self::CUSTOM => '自定义',
        };
    }

    /**
     * 获取所有类型选项
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
     * 获取默认值
     */
    public static function getDefault(): string
    {
        return self::GOODS->value;
    }
}
