<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('.github')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR2' => true,
    'strict_param' => true
])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
