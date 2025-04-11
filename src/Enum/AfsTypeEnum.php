<?php

namespace JingdongCloudTradeBundle\Enum;

/**
 * 京东云交易售后服务类型枚举
 */
enum AfsTypeEnum: string
{
    /**
     * 退货
     */
    case RETURN = 'RETURN';
    
    /**
     * 换货
     */
    case EXCHANGE = 'EXCHANGE';
    
    /**
     * 维修
     */
    case REPAIR = 'REPAIR';
    
    /**
     * 仅退款
     */
    case REFUND_ONLY = 'REFUND_ONLY';
    
    /**
     * 获取类型描述
     */
    public function getDescription(): string
    {
        return match($this) {
            self::RETURN => '退货',
            self::EXCHANGE => '换货',
            self::REPAIR => '维修',
            self::REFUND_ONLY => '仅退款',
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