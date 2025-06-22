<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 京东云交易评分等级枚举
 */
enum ScoreEnum: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 1分 - 非常不满意
     */
    case ONE = '1';
    
    /**
     * 2分 - 不满意
     */
    case TWO = '2';
    
    /**
     * 3分 - 一般
     */
    case THREE = '3';
    
    /**
     * 4分 - 满意
     */
    case FOUR = '4';
    
    /**
     * 5分 - 非常满意
     */
    case FIVE = '5';
    
    /**
     * 获取评分描述
     */
    public function getDescription(): string
    {
        return match($this) {
            self::ONE => '非常不满意',
            self::TWO => '不满意',
            self::THREE => '一般',
            self::FOUR => '满意',
            self::FIVE => '非常满意',
        };
    }

    public function getLabel(): string
    {
        return $this->getDescription();
    }
    
    /**
     * 获取所有评分选项（用于表单选择）
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