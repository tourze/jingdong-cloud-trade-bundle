<?php

namespace JingdongCloudTradeBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 支付渠道枚举
 */
enum PaymentChannelEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case WECHAT = 'WECHAT';
    case ALIPAY = 'ALIPAY';
    case UNIONPAY = 'UNIONPAY';
    case OTHER = 'OTHER';

    public function getLabel(): string
    {
        return match($this) {
            self::WECHAT => '微信支付',
            self::ALIPAY => '支付宝',
            self::UNIONPAY => '银联',
            self::OTHER => '其他',
        };
    }
    
    /**
     * 获取所有渠道选项（用于表单选择）
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }
        return $options;
    }
} 