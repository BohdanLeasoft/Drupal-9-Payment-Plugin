<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the Klarna Pay Now offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "klarna-pay-now",
 *   label = @Translation("Klarna Pay Now (Off-site redirect)"),
 *   display_label = @Translation("Klarna Pay Now"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class KlarnaPayNow extends BaseOffsitePaymentGateway
{

}
