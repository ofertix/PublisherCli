<?php

/*
 * This file is part of the PublisherConsole package.
 *
 * (c) Joan Valduvieco <joan.valduvieco@ofertix.com>
 * (c) Jordi Llonch <jordi.llonch@ofertix.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * usage:
 *   publish:
 *     value as parameter:
 *       php publisher_cli.phar --name=name1 --types=counter,time --values=1,10.5
 *     value from standard input:
 *       echo 'abc' | php publisher_cli.phar --name=name1 --types=counter,time --values=1,STDIN
 *
 *   configure:
 *     get current configuration:
 *       php publisher_cli.phar config
 *     set configuration:
 *       php publisher_cli.phar config set publishers.publisher_1.config.host localhost2
 *
 */

require_once __DIR__ . '/vendor/autoload.php';

//$data = '';
//while (!feof(STDIN)) {
//    $data .= fread(STDIN, 8192);
//}
//
//echo 'data: ' . $data . "\n";

$app = new Stats\App();
$app->run($argv);

__HALT_COMPILER();