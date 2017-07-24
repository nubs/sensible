#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use PHPUnit\Util\Configuration as PHPUnitConfiguration;
use PHPUnit\TextUI\TestRunner as PHPUnitTestRunner;

$phpcsCLI = new PHP_CodeSniffer_CLI();
$phpcsViolations = $phpcsCLI->process(['standard' => ['PSR1'], 'files' => ['src', 'tests', 'build.php']]);
if ($phpcsViolations > 0) {
    exit(1);
}

$phpunitConfiguration = PHPUnitConfiguration::getInstance(__DIR__ . '/phpunit.xml');
$phpunitArguments = ['coverageHtml' => __DIR__ . '/coverage', 'configuration' => $phpunitConfiguration];
$testRunner = new PHPUnitTestRunner();
$result = $testRunner->doRun($phpunitConfiguration->getTestSuiteConfiguration(), $phpunitArguments, false);
if (!$result->wasSuccessful()) {
    exit(1);
}

$coverageReport = $result->getCodeCoverage()->getReport();
if ($coverageReport->getNumExecutedLines() !== $coverageReport->getNumExecutableLines()) {
    file_put_contents('php://stderr', "Code coverage was NOT 100%\n");
    exit(1);
}

file_put_contents('php://stderr', "Code coverage was 100%\n");
