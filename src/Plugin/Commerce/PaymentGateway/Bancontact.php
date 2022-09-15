<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the Bancontact offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "bancontact",
 *   label = @Translation("Bancontact (Off-site redirect)"),
 *   display_label = @Translation("Bancontact"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class Bancontact extends BaseOffsitePaymentGateway
{

}
