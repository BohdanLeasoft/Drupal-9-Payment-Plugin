<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;

/**
 * Provides the Ideal offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "ideal",
 *   label = @Translation("Ideal (Off-site redirect)"),
 *   display_label = @Translation("Ideal"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class Ideal extends BaseOffsitePaymentGateway
{

}
