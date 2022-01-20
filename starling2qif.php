#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Commands\Csv2Qif;
use Symfony\Component\Console\Application;

$app = new Application();

$app->add(new Csv2Qif());

$app->run();