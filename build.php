#!/usr/bin/env php
<?php
require 'vendor/autoload.php';
require 'vendor/squizlabs/php_codesniffer/autoload.php';

use PHP_CodeSniffer\Config as PHPCSConfig;
use PHP_CodeSniffer\Files\FileList as PHPCSFileList;
use PHP_CodeSniffer\Reporter as PHPCSReporter;
use PHP_CodeSniffer\Ruleset as PHPCSRuleset;
use PHP_CodeSniffer\Util\Tokens as PHPCSTokens;
use PHPUnit\Util\Configuration as PHPUnitConfiguration;
use PHPUnit\TextUI\TestRunner as PHPUnitTestRunner;

$phpcsConfig = new PHPCSConfig();
$phpcsConfig->standards = ['PSR1'];
$phpcsConfig->files = ['src', 'tests', 'build.php'];
$tokens = new PHPCSTokens();
$phpcsRuleset = new PHPCSRuleset($phpcsConfig);
$phpcsReporter = new PHPCSReporter($phpcsConfig);

$phpcsFileList = new PHPCSFileList($phpcsConfig, $phpcsRuleset);
foreach ($phpcsFileList as $file) {
    $file->process();
    $phpcsReporter->cacheFileReport($file, $phpcsConfig);
    $file->cleanUp();
}

$phpcsReporter->printReports();

$phpcsViolations = $phpcsReporter->totalErrors + $phpcsReporter->totalWarnings;
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
