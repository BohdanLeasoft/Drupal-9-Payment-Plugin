<?php

namespace Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_ginger\Bankconfig\Bankconfig;
use Drupal\commerce_ginger\Plugin\Commerce\PaymentGateway\BaseOffsitePaymentGateway;
use Drupal\commerce_payment\Entity\PaymentInterface;

/**
 * Provides the AfterPay offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "afterpay",
 *   label = @Translation("AfterPay (Off-site redirect)"),
 *   display_label = @Translation("AfterPay "),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_ginger\PluginForm\AbstractPayment",
 *   }
 * )
 */
class AfterPay extends BaseOffsitePaymentGateway
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
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $form = $this->helper->setBirthday($form);
    $form = $this->helper->setGender($form);
    $link = Bankconfig::getAfterPayTermsLink($language);
    $form = $this->helper->setTermsLink($form, $link);
    $form = $this->helper->setDefaultButtons($form);
    return $form;
  }
}
