<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the Payconiq offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "payconiq",
 *   label = @Translation("Payconiq (Off-site redirect)"),
 *   display_label = @Translation("Payconiq"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class Payconiq extends BaseOffsitePaymentGateway
{

}
