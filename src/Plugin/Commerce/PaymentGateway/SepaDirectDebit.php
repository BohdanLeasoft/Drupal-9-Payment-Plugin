<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the Sepa Direct Debit offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "sepa-direct-debit",
 *   label = @Translation("Sepa Direct Debit (Off-site redirect)"),
 *   display_label = @Translation("Sepa Direct Debit"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class SepaDirectDebit extends BaseOffsitePaymentGateway
{

}
