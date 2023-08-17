<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the Sofort offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "sofort",
 *   label = @Translation("Sofort (Off-site redirect)"),
 *   display_label = @Translation("Sofort"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class Sofort extends BaseOffsitePaymentGateway
{

}
