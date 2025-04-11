<?php

namespace JingdongCloudTradeBundle\Enum;

/**
 * 京东云交易支付类型枚举
 */
enum PayTypeEnum: string
{
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