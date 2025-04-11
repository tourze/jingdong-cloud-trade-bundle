<?php

namespace JingdongCloudTradeBundle\Enum;

/**
 * 京东云交易订单商品状态枚举
 */
enum ItemStateEnum: string
{
    /**
     * 正常
     */
    case NORMAL = 'NORMAL';
    
    /**
     * 退货中
     */
    case RETURNING = 'RETURNING';
    
    /**
     * 已退货
     */
    case RETURNED = 'RETURNED';
    
    /**
     * 换货中
     */
    case EXCHANGING = 'EXCHANGING';
    
    /**
     * 已换货
     */
    case EXCHANGED = 'EXCHANGED';
    
    /**
     * 获取状态描述
     */
    public function getDescription(): string
    {
        return match($this) {
            self::NORMAL => '正常',
            self::RETURNING => '退货中',
            self::RETURNED => '已退货',
            self::EXCHANGING => '换货中',
            self::EXCHANGED => '已换货',
        };
    }
    
    /**
     * 获取所有状态选项（用于表单选择）
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