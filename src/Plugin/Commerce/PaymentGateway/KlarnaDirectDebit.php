<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;

/**
 * Provides the Klarna Direct Debit offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "klarna-direct-debit",
 *   label = @Translation("Klarna Direct Debit (Off-site redirect)"),
 *   display_label = @Translation("Klarna Direct Debit"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class KlarnaDirectDebit extends BaseOffsitePaymentGateway
{

}
