<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 发票抬头类型枚举
 */
enum InvoiceTitleTypeEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case PERSONAL = '1'; // 个人
    case COMPANY = '2';  // 企业

    public function getLabel(): string
    {
        return match($this) {
            self::PERSONAL => '个人',
            self::COMPANY => '企业',
        };
    }

    /**
     * 获取所有类型选项（用于表单选择）
     */
    public static function getChoices(): array
    {
        return [
            '个人' => self::PERSONAL->value,
            '企业' => self::COMPANY->value,
        ];
    }

    /**
     * 获取默认值
     */
    public static function getDefault(): string
    {
        return self::PERSONAL->value;
    }
}
