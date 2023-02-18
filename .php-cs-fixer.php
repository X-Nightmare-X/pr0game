<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('cache')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
])
    ->setFinder($finder);
