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
        return match ($this) {
            self::WECHAT => '微信支付',
            self::ALIPAY => '支付宝',
            self::UNIONPAY => '银联支付',
            self::OTHER => '其他支付',
        };
    }

    /**
     * 获取所有枚举的选项数组（用于下拉列表等）
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function toSelectItems(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[] = [
                'value' => $case->value,
                'label' => $case->getLabel(),
            ];
        }

        return $result;
    }
}
