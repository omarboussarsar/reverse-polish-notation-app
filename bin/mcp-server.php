#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Mcp\ProtocolHandler;
use App\Mcp\StdioServer;
use App\Service\ReversePolishNotation;

require_once dirname(__DIR__).'/vendor/autoload.php';

$handler = new ProtocolHandler(new ReversePolishNotation());
$server = new StdioServer();
$server->run(STDIN, STDOUT, $handler);
