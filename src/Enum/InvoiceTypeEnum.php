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
enum InvoiceTypeEnum: string implements  Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    /**
     * 普通发票
     */
    case NORMAL = '1';
    
    /**
     * 增值税发票
     */
    case VAT = '2';
    
    /**
     * 电子发票
     */
    case ELECTRONIC = '3';
    
    /**
     * 获取类型描述
     */
    public function getLabel(): string
    {
        return match($this) {
            self::NORMAL => '普通发票',
            self::VAT => '增值税发票',
            self::ELECTRONIC => '电子发票',
        };
    }
    
    /**
     * 获取所有类型选项（用于表单选择）
     */
    public static function getChoices(): array
    {
        return [
            '普通发票' => self::NORMAL->value,
            '增值税发票' => self::VAT->value,
            '电子发票' => self::ELECTRONIC->value,
        ];
    }

    public static function getDefault(): string
    {
        return self::ELECTRONIC->value;
    }
}