<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 京东云交易支付类型枚举
 */
enum PayTypeEnum: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 在线支付
     */
    case ONLINE = 'ONLINE';
    
    /**
     * 货到付款
     */
    case COD = 'COD';
    
    /**
     * 获取类型描述
     */
    public function getDescription(): string
    {
        return match($this) {
            self::ONLINE => '在线支付',
            self::COD => '货到付款',
        };
    }

    public function getLabel(): string
    {
        return $this->getDescription();
    }
    
    /**
     * 获取所有类型选项（用于表单选择）
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