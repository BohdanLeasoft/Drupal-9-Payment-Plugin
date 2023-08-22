<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the Viacash offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "viacash",
 *   label = @Translation("Viacash (Off-site redirect)"),
 *   display_label = @Translation("Viacash"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class Viacash extends BaseOffsitePaymentGateway
{

}
