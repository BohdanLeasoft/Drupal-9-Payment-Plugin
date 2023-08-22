<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the Giropay offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "giropay",
 *   label = @Translation("Giropay (Off-site redirect)"),
 *   display_label = @Translation("Giropay"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class Giropay extends BaseOffsitePaymentGateway
{

}
