<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the Amex offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "amex",
 *   label = @Translation("Amex (Off-site redirect)"),
 *   display_label = @Translation("Amex"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class Amex extends BaseOffsitePaymentGateway
{

}
