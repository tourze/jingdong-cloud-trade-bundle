<?php

namespace JingdongCloudTradeBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use JingdongCloudTradeBundle\Entity\Order;
use JingdongCloudTradeBundle\Service\Client;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Order::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Order::class)]
#[WithMonologChannel(channel: 'jingdong_cloud_trade')]
final readonly class OrderSubscriber
{
    public function __construct(
        private Client $client,
        private LoggerInterface $logger,
    ) {
    }

    public function prePersist(Order $order): void
    {
        if ($order->isSynced()) {
            return;
        }

        try {
            $result = $this->client->execute($order->getAccount(), 'jingdong.ctp.order.createOrder', [
                'orderId' => $order->getOrderId(),
                'orderState' => $order->getOrderState(),
                'paymentState' => $order->getPaymentState(),
                'logisticsState' => $order->getLogisticsState(),
                'receiverName' => $order->getReceiverName(),
                'receiverMobile' => $order->getReceiverMobile(),
                'receiverProvince' => $order->getReceiverProvince(),
                'receiverCity' => $order->getReceiverCity(),
                'receiverCounty' => $order->getReceiverCounty(),
                'receiverAddress' => $order->getReceiverAddress(),
                'orderTotalPrice' => $order->getOrderTotalPrice(),
                'orderPaymentPrice' => $order->getOrderPaymentPrice(),
                'freightPrice' => $order->getFreightPrice(),
                'orderTime' => $order->getOrderTime()->format('Y-m-d H:i:s'),
                'paymentTime' => $order->getPaymentTime()?->format('Y-m-d H:i:s'),
                'deliveryTime' => $order->getDeliveryTime()?->format('Y-m-d H:i:s'),
                'finishTime' => $order->getCompletionTime()?->format('Y-m-d H:i:s'),
            ]);

            $order->setSynced(true);
        } catch (\Throwable $e) {
            $this->logger->error('同步订单到京东失败', [
                'exception' => $e,
                'orderId' => $order->getOrderId(),
            ]);

            throw $e;
        }
    }

    public function preUpdate(Order $order): void
    {
        if ($order->isSynced()) {
            return;
        }

        try {
            $result = $this->client->execute($order->getAccount(), 'jingdong.ctp.order.updateOrder', [
                'orderId' => $order->getOrderId(),
                'orderState' => $order->getOrderState(),
                'paymentState' => $order->getPaymentState(),
                'logisticsState' => $order->getLogisticsState(),
                'receiverName' => $order->getReceiverName(),
                'receiverMobile' => $order->getReceiverMobile(),
                'receiverProvince' => $order->getReceiverProvince(),
                'receiverCity' => $order->getReceiverCity(),
                'receiverCounty' => $order->getReceiverCounty(),
                'receiverAddress' => $order->getReceiverAddress(),
                'orderTotalPrice' => $order->getOrderTotalPrice(),
                'orderPaymentPrice' => $order->getOrderPaymentPrice(),
                'freightPrice' => $order->getFreightPrice(),
                'orderTime' => $order->getOrderTime()->format('Y-m-d H:i:s'),
                'paymentTime' => $order->getPaymentTime()?->format('Y-m-d H:i:s'),
                'deliveryTime' => $order->getDeliveryTime()?->format('Y-m-d H:i:s'),
                'finishTime' => $order->getCompletionTime()?->format('Y-m-d H:i:s'),
            ]);

            $order->setSynced(true);
        } catch (\Throwable $e) {
            $this->logger->error('更新订单到京东失败', [
                'exception' => $e,
                'orderId' => $order->getOrderId(),
            ]);

            throw $e;
        }
    }
}
