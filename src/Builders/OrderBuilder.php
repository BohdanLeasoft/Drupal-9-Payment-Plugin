<?php

namespace Drupal\commerce_ginger\Builders;

use Drupal\Core\Url;
use Drupal\commerce_ginger\Bankconfigs\Bankconfig;
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
use GingerPluginSdk\Entities\Customer;
use GingerPluginSdk\Client;
use Drupal\commerce_ginger\Builders\CustomerBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrderBuilder.
 *
 * This class contain methods for creating an order
 *
 * @package Drupal\commerce_ginger\Builders
 */

class OrderBuilder extends CustomerBuilder
{
  /**
   * @var Bankconfig
   */
  public $bankconfig;
  /**
   * @var CustomerBuilder
   */
  public $customerBuilder;
  /**
   * @var ClientBuilder
   */
  public $clientBuilder;

  public function __construct()
  {
    parent::__construct();
    $this->bankconfig = new Bankconfig();
    $this->customerBuilder = New CustomerBuilder();
    $this->clientBuilder = New ClientBuilder();
  }

  public function getOrderPaymentByTransactionId($transaction_id, $entityTypeManager)
  {
    /** @var \Drupal\commerce_payment\PaymentStorageInterface $payment_storage */
    $payment_storage = $entityTypeManager->getStorage('commerce_payment');
    $payment = $payment_storage->loadByRemoteId($transaction_id);
    return $payment;
  }

  public function isOrderRefunded(Order $order)
  {
    $orderArray = $order->toArray();
    if (isset($orderArray['flags'])) {
      foreach ($orderArray['flags'] as $flag) {
        if ($flag == 'has-refunds') {
          return true;
        }
      }
    }
    return false;
  }

  public function isOrderCapturable(Order $order)
  {
    $orderArray = $order->toArray();
    if (isset(current($orderArray['transactions'])['is_capturable'])) {
      return current($orderArray['transactions'])['is_capturable'];
    }
    return false;
  }


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

    if ($order->hasField('shipments') && !$order->get('shipments')->isEmpty()) {
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
      return $orderLines;
    }
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
      if (!empty($order_item)) {
        $order_items_info[] = [
          'id' => $order_item->id(),
          'quantity' => number_format($order_item->getQuantity()),
          'name' => $order_item->getTitle(),
          'price' => $order_item->getUnitPrice()->getNumber()
        ];
      }
    }
    return $order_items_info;
  }

  public function getWebhookUrl($payment)
  {
    /** @var \Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface $payment_gateway_plugin */
    $payment_gateway_plugin = $payment->getPaymentGateway()->getPlugin();
    return $payment_gateway_plugin->getNotifyUrl()->toString();
  }


  public function getReturnUrl($payment)
  {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $payment->getOrder();
    return Url::fromRoute('commerce_payment.checkout.return', [
      'commerce_order' => $order->id(),
      'step' => 'payment',
    ], ['absolute' => TRUE])->toString();
  }

  public function getCancelUrl($payment) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $payment->getOrder();

    return Url::fromRoute('commerce_payment.checkout.cancel', [
      'commerce_order' => $order->id(),
      'step' => 'payment',
    ], ['absolute' => TRUE])->toString();
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
      amount: new Amount(floatval($raw_amount)*100),
      transactions: $transactions,
      customer: $customer,
      orderLines: $this->getOrderLines($payment),
      extra: new Extra(
        $this->clientBuilder->getExtraLines()
      ),
      client: $this->clientBuilder->getEntitiesClient(),
      webhook_url: $this->getWebhookUrl($payment),
      return_url: $this->getReturnUrl($payment),
      flags: null,
      id: $payment->getOrderId(),
      status: null,
      merchantOrderId: $payment->getOrderId(),
      description: sprintf("%s: %s", t('Order number'), $payment->getOrderId()),
    );

    return $client->sendOrder($order);
  }
}
