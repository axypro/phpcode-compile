<?php
/**
 * Compilation to PHP code
 *
 * @package axy\phpcode\compile
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 * @license https://raw.github.com/axypro/phpcode-compile/master/LICENSE MIT
 * @link https://github.com/axypro/phpcode-compile repository
 * @link https://packagist.org/packages/axy/phpcode-compile composer package
 * @uses PHP5.4+
 */

namespace axy\phpcode\compile;

if (!is_file(__DIR__.'/vendor/autoload.php')) {
    throw new \LogicException('Please: composer install');
}

require_once(__DIR__.'/vendor/autoload.php');
