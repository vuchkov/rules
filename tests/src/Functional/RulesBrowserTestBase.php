<?php

/**
 * @file
 * Contains \Drupal\Tests\rules\Functional\RulesBrowserTestBase.
 */

namespace Drupal\Tests\rules\Functional;

use Drupal\simpletest\BrowserTestBase;

/**
 * Has some additional helper methods to make test code more readable.
 */
abstract class RulesBrowserTestBase extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected function drupalGet($path, array $options = []) {
    $result = parent::drupalGet($path);
    $this->processHeaderErrors();
    return $result;
  }

  /**
   * Reads headers and fails the test on errors received from the tested site.
   *
   * @see _drupal_log_error()
   */
  protected function processHeaderErrors() {
    $headers = $this->getSession()->getResponseHeaders();
    foreach ($headers as $header_name => $header_values) {
      // Errors are being sent via X-Drupal-Assertion-* headers,
      // generated by _drupal_log_error().
      if (preg_match('/^X-Drupal-Assertion-[0-9]+$/', $header_name)) {
        $error_info = unserialize(urldecode($header_values[0]));
        $this->fail(sprintf('%s: %s in %s on line %d', $error_info[1], (string) $error_info[0], $error_info[2]['file'], $error_info[2]['line']));
      }
    }
    // Temporary core hack to get debug information when random test fails
    // occur. See https://www.drupal.org/node/2659954
    if ($this->getSession()->getStatusCode() == 555) {
      $this->fail($this->getSession()->getPage()->getContent());
    }
  }

  /**
   * Finds link with specified locator.
   *
   * @param string $locator
   *   Link id, title, text or image alt.
   *
   * @return \Behat\Mink\Element\NodeElement|null
   *   The link node element.
   */
  public function findLink($locator) {
    return $this->getSession()->getPage()->findLink($locator);
  }

  /**
   * Finds field (input, textarea, select) with specified locator.
   *
   * @param string $locator
   *   Input id, name or label.
   *
   * @return \Behat\Mink\Element\NodeElement|null
   *   The input field element.
   */
  public function findField($locator) {
    return $this->getSession()->getPage()->findField($locator);
  }

  /**
   * Finds button with specified locator.
   *
   * @param string $locator
   *   Button id, value or alt.
   *
   * @return \Behat\Mink\Element\NodeElement|null
   *   The button node element.
   */
  public function findButton($locator) {
    return $this->getSession()->getPage()->findButton($locator);
  }

  /**
   * Clicks link with specified locator.
   *
   * @param string $locator
   *   Link id, title, text or image alt.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  public function clickLink($locator) {
    $this->getSession()->getPage()->clickLink($locator);
    $this->processHeaderErrors();
  }

  /**
   * Presses button with specified locator.
   *
   * @param string $locator
   *   Button id, value or alt.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  public function pressButton($locator) {
    $this->getSession()->getPage()->pressButton($locator);
    $this->processHeaderErrors();
  }

  /**
   * Fills in field (input, textarea, select) with specified locator.
   *
   * @param string $locator
   *   Input id, name or label.
   * @param string $value
   *   Value.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   *
   * @see \Behat\Mink\Element\NodeElement::setValue
   */
  public function fillField($locator, $value) {
    $this->getSession()->getPage()->fillField($locator, $value);
  }

}
