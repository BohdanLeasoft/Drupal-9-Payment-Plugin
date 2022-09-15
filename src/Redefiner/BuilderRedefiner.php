<?php
namespace Drupal\commerce_ginger\Redefiner;

use Drupal\commerce_ginger\Builder\OrderBuilder;

/**
 * Class BuilderRedefiner.
 *
 * This class for redefining Builder
 *
 * @package Drupal\commerce_ginger\Redefiner
 */

class BuilderRedefiner extends OrderBuilder
{
  public function __construct()
  {
    parent::__construct();
  }
  // Here you can redefine all builder functionality
}
