<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the Swish offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "swish",
 *   label = @Translation("Swish (Off-site redirect)"),
 *   display_label = @Translation("Swish"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class Swish extends BaseOffsitePaymentGateway
{

}
