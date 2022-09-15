<?php

namespace Drupal\commerce_ginger\Controller;

use Drupal\commerce_payment\Entity\PaymentInterface;
use Drupal\Core\Form\FormStateInterface;

class OrderController
{
  public static function getOrderPaymentByTransactionId($transaction_id, $entityTypeManager) : PaymentInterface
  {
    /** @var \Drupal\commerce_payment\PaymentStorageInterface $payment_storage */
    $payment_storage = $entityTypeManager->getStorage('commerce_payment');
    $payment = $payment_storage->loadByRemoteId($transaction_id);
    return $payment;
  }

  public static function getIssuerId($values, FormStateInterface $form_state)
  {
    return array_key_exists('issuers', $values) ?  $values['issuers'][$form_state->getUserInput()["payment_process"]["offsite_payment"]['issuers']] : false;
  }

  public static function getCustomerGender($values, FormStateInterface $form_state)
  {
    return array_key_exists('gender', $values) ? $values['gender'][$form_state->getUserInput()["payment_process"]["offsite_payment"]['gender']] : false;
  }

  public static function getBirthdate($values)
  {
    return array_key_exists('birthdate', $values) ? $values['birthdate'] : false;
  }

  public static function getTermsState($values)
  {
    return array_key_exists('verified_terms_of_service', $values) && $values['verified_terms_of_service'] == 1 ? true : false;
  }
}
