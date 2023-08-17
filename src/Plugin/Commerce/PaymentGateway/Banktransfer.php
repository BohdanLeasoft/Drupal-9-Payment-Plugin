<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;

/**
 * Provides the Banktransfer offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "bank-transfer",
 *   label = @Translation("Banktransfer (Off-site redirect)"),
 *   display_label = @Translation("Banktransfer"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class Banktransfer extends BaseOffsitePaymentGateway
{

}
