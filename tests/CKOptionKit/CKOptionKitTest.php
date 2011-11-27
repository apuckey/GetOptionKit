<?php
/*
 * This file is part of the {{ }} package.
 *
 * (c) Yo-An Lin <cornelius.howl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
class CKOptionKitTest extends PHPUnit_Framework_TestCase 
{

    function test()
    {
        $opt = new \CKOptionKit\CKOptionKit;
        $opt->add( 'v|verbose' , 'verbose message' , 'verbose' );
        $opt->add( 'd|debug'   , 'debug message' , 'debug' );
    }


}
