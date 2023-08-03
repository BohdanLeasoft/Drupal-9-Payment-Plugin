<?php

namespace Drupal\commerce_ginger\Builder;
use GingerPluginSdk\Collections\AdditionalAddresses;
use GingerPluginSdk\Collections\PhoneNumbers;
use GingerPluginSdk\Entities\Address;
use GingerPluginSdk\Entities\Customer;
use GingerPluginSdk\Properties\Birthdate;
use GingerPluginSdk\Properties\Country;
use GingerPluginSdk\Properties\EmailAddress;
use GingerPluginSdk\Properties\Locale;
use Drupal\commerce_ginger\Builder\ClientBuilder;

/**
 * Class CustomerBuilder.
 *
 * This class contain methods for collecting data about customer
 *
 * @package Drupal\commerce_ginger\Builder
 */

class CustomerBuilder extends ClientBuilder
{
  public function getAddress($billing_info, $addressType)
  {
    return new Address(
      addressType: $addressType,
      postalCode: $billing_info->getPostalCode(),
      country: new Country($billing_info->getCountryCode()),
      street: $billing_info->getAddressLine1(),
      city: $billing_info->getLocality()
    );
  }

  public function getLangCode($payment)
  {
    return $payment->getOrder()->getBillingProfile()->get('address')->getValue()[0]['langcode'];
  }

  public function getAdditionalAddresses($payment)
  {
    return new AdditionalAddresses(
      $this->getAddress($payment,'customer'),
      $this->getAddress($payment, 'billing')
    );
  }

  public function getCustomerData($payment, $birthdate = null, $gender = null)
  {
    /** @var \Drupal\profile\Entity\ProfileInterface $profile */
    $profile = $payment->getOrder()->getBillingProfile();
    $order = $payment->getOrder();

    $customer = $order->getCustomer();
    $billing_address = $profile->get('address')->getValue()[0];
    /** @var \Drupal\address\Plugin\Field\FieldType\AddressItem $billing_info */
    $billing_info = $profile->get('address')->first();

    $phone = null;
    if($profile->hasField('field_phone')) {
      $phone = $profile->get('field_phone')->value;
    } elseif ($profile->hasField('telephone')) {
      $phone = $profile->get('telephone')->value;
    }
    $additionalAddresses = $this->getAdditionalAddresses($billing_info);
    return new Customer(
      additionalAddresses: $additionalAddresses,
      firstName: $billing_info->getGivenName(),
      lastName: $billing_info->getFamilyName(),
      emailAddress: new EmailAddress($order->getEmail()),
      phoneNumbers: new PhoneNumbers($phone ?? ''),
      birthdate:$birthdate,
      gender: $gender,
      locale: new Locale($this->getLangCode($payment))
    );
  }
}
