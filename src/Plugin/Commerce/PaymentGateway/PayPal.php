<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the PayPal offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "paypal",
 *   label = @Translation("PayPal (Off-site redirect)"),
 *   display_label = @Translation("PayPal"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class PayPal extends BaseOffsitePaymentGateway
{

}
