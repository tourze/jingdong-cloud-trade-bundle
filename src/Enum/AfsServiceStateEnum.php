<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 京东云交易售后服务状态枚举
 */
enum AfsServiceStateEnum: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 申请中
     */
    case APPLYING = 'APPLYING';
    
    /**
     * 审核通过
     */
    case APPROVED = 'APPROVED';
    
    /**
     * 审核拒绝
     */
    case REJECTED = 'REJECTED';
    
    /**
     * 处理中
     */
    case PROCESSING = 'PROCESSING';
    
    /**
     * 已完成
     */
    case COMPLETED = 'COMPLETED';
    
    /**
     * 已取消
     */
    case CANCELLED = 'CANCELLED';
    
    /**
     * 获取状态描述
     */
    public function getDescription(): string
    {
        return match($this) {
            self::APPLYING => '申请中',
            self::APPROVED => '审核通过',
            self::REJECTED => '审核拒绝',
            self::PROCESSING => '处理中',
            self::COMPLETED => '已完成',
            self::CANCELLED => '已取消',
        };
    }

    /**
     * 获取标签
     */
    public function getLabel(): string
    {
        return $this->getDescription();
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