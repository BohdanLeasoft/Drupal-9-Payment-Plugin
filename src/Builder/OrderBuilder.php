<?php

namespace Drupal\commerce_ginger\Builder;

use Drupal\Core\Url;
use Drupal\commerce_ginger\Bankconfig\Bankconfig;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Entity\PaymentInterface;
use GingerPluginSdk\Collections\OrderLines;
use GingerPluginSdk\Collections\Transactions;
use GingerPluginSdk\Entities\Extra;
use GingerPluginSdk\Entities\Line;
use GingerPluginSdk\Entities\Order;
use GingerPluginSdk\Entities\PaymentMethodDetails;
use GingerPluginSdk\Entities\Transaction;
use GingerPluginSdk\Properties\Amount;
use GingerPluginSdk\Properties\Currency;
use GingerPluginSdk\Properties\Percentage;
use GingerPluginSdk\Properties\VatPercentage;
use GingerPluginSdk\Properties\RawCost;
use GingerPluginSdk\Entities\Customer;
use GingerPluginSdk\Client;
use Drupal\commerce_ginger\Builder\CustomerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Drupal\commerce_ginger\Helper\OrderHelper;
use GingerPluginSdk\Entities\Client as EntitiesClient;

/**
 * Class OrderBuilder.
 *
 * This class contain methods for creating an order
 *
 * @package Drupal\commerce_ginger\Builder
 */

class OrderBuilder extends CustomerBuilder
{
  public function preparePaymentMethodDetails($issuerId, $verifiedTerms)
  {
    return new PaymentMethodDetails(
      [
        'issuer_id' => $issuerId,
        'verified_terms_of_service' => $verifiedTerms,
        'cutomer' => 'cutomer'
      ]);
  }

  public function prepareTransaction($paymentMethod, PaymentMethodDetails $paymentMethodDetails = null)
  {
    return new Transactions(
      new Transaction(
        paymentMethod: $paymentMethod,
        paymentMethodDetails: $paymentMethodDetails
      )
    );
  }

  public function getShipmentsIsEmpty($order)
  {
    $shipments = null;
    try {
      $shipments = $order->get('shipments')->isEmpty();
    } catch (\Exception $exception) {
      return null;
    }
      return $shipments;
  }

  public function getOrderLines(PaymentInterface $payment)
  {
    $payment_amount = $payment->getAmount();
    $raw_amount = $payment_amount->getNumber();

    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $payment->getOrder();
    $orderItemsInfo = $this->getOrderItemsInfo($order);



    $orderLines = new OrderLines();
    foreach ($orderItemsInfo as $orderItem)
    {
      $orderLines->addLine(new Line(
        type: 'physical',
        merchantOrderLineId: $orderItem['id'],
        name: $orderItem['name'],
        quantity: $orderItem['quantity'],
        amount: new Amount(floatval($orderItem['price'])*100),
        vatPercentage: new VatPercentage(new Percentage(0)),
        currency: new Currency(
          'EUR'
        )
      ));
    }


    if ($order->hasField('shipments') && !$this->getShipmentsIsEmpty($order)) {
      $shipments = $order->get('shipments');
      foreach ($order->get('shipments')->referencedEntities() as $shipment) {
        if ($shipment->get('shipping_profile')->isEmpty()) {
          continue;
        }

        $orderLines->addLine(new Line(
          type: 'shipping_fee',
          merchantOrderLineId: 'shipping',
          name: $shipment->getShippingMethod()->getName(),
          quantity: 1,
          amount: new Amount(floatval($shipment->getAmount()->getNumber()) * 100),
          vatPercentage: new VatPercentage(new Percentage(0)),
          currency: new Currency(
            $shipment->getAmount()->getCurrencyCode()
          )
        ));
      }
    }

    return $orderLines;
  }

  /**
   * Get info about ordered products.
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *
   * @return array
   */
  protected function getOrderItemsInfo(OrderInterface $order)
  {
    $order_items_info = [];
    $order_items = $order->getItems();
    foreach ($order_items as $order_item) {
      if (empty($order_item)) continue;
      $order_items_info[] = [
        'id' => $order_item->id(),
        'quantity' => number_format($order_item->getQuantity()),
        'name' => $order_item->getTitle(),
        'price' => $order_item->getUnitPrice()->getNumber()
      ];
    }
    return $order_items_info;
  }

  public function getEntitiesClient()
  {
    return new EntitiesClient(
      $this->getUserAgent(),
      Bankconfig::getPlatformName(),
      null, // For now no ways to gat platform version were found
      Bankconfig::getPluginName(),
      Bankconfig::getPluginVersion()
    );
  }

  /**
   * Collect data for extra_lines
   *
   * @return array
   */
  public function getExtraLines()
  {
    return [
      'user_agent' => $this->getUserAgent(),
      'platform_name' =>  Bankconfig::getPlatformName(),
      'plugin_name' => Bankconfig::getPluginName(),
      'plugin_version' => Bankconfig::getPluginVersion()
    ];
  }

  public function getDescription($orderId)
  {
    return sprintf("%s: %s", t('Order number'), $orderId);
  }

  public function createOrder(
    PaymentInterface $payment,
    Client $client,
    Customer $customer,
    $paymentMethod,
    $issuerId = null,
    $verifiedTerms = null
  ) {
    $payment_amount = $payment->getAmount();
    $raw_amount = $payment_amount->getNumber();
    $transactions = $this->prepareTransaction($paymentMethod, $this->preparePaymentMethodDetails($issuerId, $verifiedTerms));

    $order = new Order(
      currency:  new Currency($payment_amount->getCurrencyCode()),
      amount: new Amount(new RawCost(floatval($raw_amount))),
      transactions: $transactions,
      customer: $customer,
      orderLines: $this->getOrderLines($payment),
      extra: new Extra(
        $this->getExtraLines()
      ),
      client: $this->getEntitiesClient(),
      webhook_url: OrderHelper::getWebhookUrl($payment),
      return_url: OrderHelper::getReturnUrl($payment),
      merchantOrderId: $payment->getOrderId(),
      description: $this->getDescription($payment->getOrderId()),
    );

    return $client->sendOrder($order);
  }
}
