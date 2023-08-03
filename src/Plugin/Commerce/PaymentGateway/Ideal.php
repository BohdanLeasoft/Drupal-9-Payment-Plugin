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
  public function prepareForm(array $form)
  {
    $issuers = $this->builderRedefiner->getClient()->getIdealIssuers()->toArray();

    $form['issuers'] = [
      '#type' => 'select',
      '#title' => $this
        ->t('Choose a bank'),
      '#options' => $this->helper->getValueFromIssuersArrayById($issuers, 'name'),
      '#value' => $this->helper->getValueFromIssuersArrayById($issuers, 'id')
    ];
    $form = $this->helper->setDefaultButtons($form);

    return $form;
  }
}
