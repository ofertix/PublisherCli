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

namespace Stats\Compiler;

class Compiler
{
    public function compile($pharFile = 'publisher_cli.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }
        $phar = new \Phar($pharFile, 0, $pharFile);
        
        // add all files in the project
        $phar->buildFromDirectory(__DIR__ . '/../../../', '/\.php$|\.yml$/');
        $phar->setStub($phar->createDefaultStub('stub.php', 'stub.php'));
    }

}