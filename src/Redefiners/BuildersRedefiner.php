<?php
namespace Drupal\commerce_ginger\Redefiners;

use Drupal\commerce_ginger\Builders\OrderBuilder;

/**
 * Class BuildersRedefiner.
 *
 * This class for redefining Builders
 *
 * @package Drupal\commerce_ginger\Redefiners
 */

class BuildersRedefiner extends OrderBuilder
{
  public function __construct()
  {
    parent::__construct();
  }
  // Here you can redefine all builders functionality
}
