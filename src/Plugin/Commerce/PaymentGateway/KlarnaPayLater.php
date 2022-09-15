<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the KlarnaPayLater offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "klarna-pay-later",
 *   label = @Translation("Klarna Pay Later (Off-site redirect)"),
 *   display_label = @Translation("Klarna Pay Later"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class KlarnaPayLater extends BaseOffsitePaymentGateway
{
  /**
   * Checks whether the given payment can be captured.
   *
   * @param \Drupal\commerce_payment\Entity\PaymentInterface $payment
   *   The payment to capture.
   *
   * @return bool
   *   TRUE if the payment can be captured, FALSE otherwise.
   */
  public function canCapturePayment(PaymentInterface $payment)
  {
    return true;
  }

  public function prepareForm(array $form)
  {
    $form = $this->helper->setBirthday($form);
    $form = $this->helper->setGender($form);
    $form = $this->helper->setDefaultButtons($form);
    return $form;
  }
}
