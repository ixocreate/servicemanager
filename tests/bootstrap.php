<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Test\Template;

/**
 * TODO: find out why bypass-finals causes the directories to not be created
 */
// use DG\BypassFinals;

\chdir(\dirname(__DIR__));
include 'vendor/autoload.php';
// BypassFinals::enable();
