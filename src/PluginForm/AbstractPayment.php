<?php

namespace Drupal\commerce_ginger\PluginForm;

require_once __DIR__ . '/../../vendor/autoload.php';

use Drupal;
use Drupal\commerce\Response\NeedsRedirectException;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\user\Entity\User;
use Drupal\commerce_payment\Entity\PaymentInterface;
use Drupal\commerce_ginger\Redefiners\BuildersRedefiner;
use Drupal\commerce_ginger\Helpers\Helper;
use Drupal\commerce_ginger\Bankconfigs\Bankconfig;
use GingerPluginSdk\Properties\Birthdate;

/**
 * Class AbstractPayment.
 *
 * This defines a payment form that Drupal Commerce will redirect to, when the user
 * clicks the Pay and complete purchase button.
 *
 * @package Drupal\commerce_ginger\PluginForm
 */
class AbstractPayment extends PaymentOffsiteForm
{

  use Drupal\commerce_ginger\RedirectTrait;

  /**
   * @var string
   */
  public $name;
  /**
   * @var \GingerPluginSdk\Client
   */
  public $client;
  /**
   * @var Helper
   */
  public $helper;
  /**
   * @var Bankconfig
   */
  public $bankconfig;
  /**
   * @var BuildersRedefiner
   */
  public $buildersRedefiner;

  public function __construct()
  {
    $this->helper = new Helper();
    $this->bankconfig = new Bankconfig();
    $this->buildersRedefiner = new BuildersRedefiner();
    $this->client = $this->buildersRedefiner->getClient();
  }

  /**
   * Creates the checkout form.
   *
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state)
  {
    $this->paymentMethod = $this->entity->getPaymentGateway()->getPluginId();
    $payment = $this->entity;
    $form = parent::buildConfigurationForm($form, $form_state);

    switch ($this->paymentMethod) {
      case 'ideal':
        $issuers = $this->client->getIdealIssuers()->toArray();

        $form['issuers'] = [
          '#type' => 'select',
          '#title' => $this
            ->t('Choose a bank'),
          '#options' => $this->helper->getValueFromIssuersArrayById($issuers, 'name'),
          '#value' => $this->helper->getValueFromIssuersArrayById($issuers, 'id')
        ];
        $form = $this->helper->setDefaultButtons($form);
        break;
      case 'klarna-pay-later':
      case 'afterpay':
        $form['birthdate'] = [
          '#type' => 'date',
          '#title' => $this->t('Please, set your birthday date'),
          '#required' => true
        ];
        $form['gender'] = [
          '#type' => 'select',
          '#title' => $this->t('Choose gender'),
          '#options' => [$this->t('Male'), $this->t('Female')],
          '#value' => ['male', 'female']
        ];
        if ($this->paymentMethod == 'afterpay') {
          $link = $this->bankconfig->getAfterPayTermsLink($this->buildersRedefiner->getLangCode($payment));
          $form['verified_terms_of_service'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('I accept '). '<a href="'.$link.'" target="_blank">'.$this->t('Terms and Conditions').'</a>',
            '#required' => true
          ];
        }
        $form = $this->helper->setDefaultButtons($form);
        break;
      case 'bank-transfer':
        $order = $this->startTransaction($form, $form_state);
        $form = $this->helper->setBanktransferForm($form, $order->toArray());
        break;
      default:
        $this->startTransaction($form, $form_state);
        break;
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state)
  {

    if ($this->entity->getPaymentGateway()->getPluginId() == 'bank-transfer') {
      $order = $this->entity->getOrder();
      $params['subject'] = 'Order payment information!';
      $params['body'] = $form['banktransfer_info'];
      $options = array(
        'langcode' => 'en',
      );
      $message['from'] = \Drupal::config('system.site')->get('mail');
      \Drupal::service(
        'plugin.manager.mail')->mail('commerce_ginger',
        'order_mail',
        $order->getEmail(),
        $this->buildersRedefiner->getLangCode($this->entity),
        $params
      );
    }
    $this->startTransaction($form, $form_state);
  }


  public function getIssuerId($values, FormStateInterface $form_state)
  {
    $issuer = $values['issuers'];
    return $issuer[$form_state->getUserInput()["payment_process"]["offsite_payment"]['issuers']];
  }

  public function getCustomerGender($values, FormStateInterface $form_state)
  {
    $issuer = $values['gender'];
    return $issuer[$form_state->getUserInput()["payment_process"]["offsite_payment"]['gender']];
  }

  public function getBirthdate($values)
  {
    return $values['birthdate'];
  }

  public function getTermsState($values)
  {
    return $values['verified_terms_of_service'] == 1 ? true : false;
  }

  public function startTransaction(array &$form, FormStateInterface $form_state)
  {
    $this->paymentMethod = $this->entity->getPaymentGateway()->getPluginId();
    $payment = $this->entity;
    $issuerId = null;
    $birthdate = null;
    $gender = null;
    $verifiedTerms = null;
    switch ($this->paymentMethod) {
      case 'ideal':
        $values = $form_state->getValue($form['#parents']);
        $issuerId = $this->getIssuerId($values, $form_state);
        break;
      case 'klarna-pay-later':
      case 'afterpay':
        $values = $form_state->getValue($form['#parents']);
        if ($this->paymentMethod == 'afterpay') {
          $verifiedTerms = $this->getTermsState($values);
        }
        $birthdate = new Birthdate($this->getBirthdate($values));
        $gender = $this->getCustomerGender($values, $form_state);
        break;
    }
    $customer = $this->buildersRedefiner->getCustomerData($payment, $birthdate, $gender);

    $order = $this->buildersRedefiner->createOrder($payment, $this->client, $customer, $this->paymentMethod, $issuerId, $verifiedTerms);

    $payment->setRemoteId($order->getId()->get());

    $payment->getOrder()->setOrderNumber($payment->getOrderId());

    $payment->save();

    if ($this->paymentMethod != 'bank-transfer') {
      throw new NeedsRedirectException($order->getPaymentUrl());
    }
    return $order;
  }
}
