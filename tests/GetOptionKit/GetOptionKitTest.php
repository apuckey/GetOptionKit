<?php
/*
 * This file is part of the GetOptionKit package.
 *
 * (c) Yo-An Lin <cornelius.howl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
class GetOptionKitTest extends PHPUnit_Framework_TestCase 
{

    function testSpec()
    {
        $opt = new \GetOptionKit\GetOptionKit;
        ok( $opt );

        $opt->add( 'f|foo:' , 'option require value' );
        $opt->add( 'b|bar+' , 'option with multiple value' );
        $opt->add( 'z|zoo?' , 'option with optional value' );
        $opt->add( 'v|verbose' , 'verbose message' );
        $opt->add( 'd|debug'   , 'debug message' );

        $spec = $opt->get('foo');
        ok( $spec->isAttributeRequire() );

        $spec = $opt->get('bar');
        ok( $spec->isAttributeMultiple() );

        $spec = $opt->get('zoo');
        ok( $spec->isAttributeOptional() );

        $spec = $opt->get( 'debug' );
        ok( $spec );
        is_class( 'GetOptionKit\\OptionSpec', $spec );
        is( 'debug', $spec->long );
        is( 'd', $spec->short );
        ok( $spec->isAttributeFlag() );
    }

    function testRequire()
    {
        $opt = new \GetOptionKit\GetOptionKit;
        ok( $opt );
        $opt->add( 'f|foo:' , 'option require value' );
        $opt->add( 'b|bar+' , 'option with multiple value' );
        $opt->add( 'z|zoo?' , 'option with optional value' );
        $opt->add( 'v|verbose' , 'verbose message' );
        $opt->add( 'd|debug'   , 'debug message' );

        // option required a value should throw an exception
        try {
            $result = $opt->parse( array( 'program' , '-f' , '-v' , '-d' ) );
        }
        catch (Exception $e) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    function testMultiple()
    {
        $opt = new \GetOptionKit\GetOptionKit;
        ok( $opt );
        $opt->add( 'b|bar+' , 'option with multiple value' );
        $result = $opt->parse(explode(' ','program -b 1 -b 2 --bar 3'));

        ok( $result->bar );
        count_ok(3,$result->bar->value);
    }

    function testIntegerTypeNonNumeric()
    {
        $opt = new \GetOptionKit\GetOptionKit;
        ok( $opt );
        $opt->add( 'b|bar:=i' , 'option with integer type' );

        $spec = $opt->get('bar');
        ok( $spec->isTypeInteger() );

        // test non numeric
        try {
            $result = $opt->parse(explode(' ','program -b test'));
            ok( $result->bar );
        } catch ( GetOptionKit\NonNumericException $e ) {
            ok( $e );
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    function testIntegerTypeNumeric()
    {
        $opt = new \GetOptionKit\GetOptionKit;
        ok( $opt );
        $opt->add( 'b|bar:=i' , 'option with integer type' );

        $spec = $opt->get('bar');
        ok( $spec->isTypeInteger() );

        $result = $opt->parse(explode(' ','program -b 123123'));
        ok( $result->bar );
        ok( $result->bar->value === 123123 );
    }



    function testStringType()
    {
        $opt = new \GetOptionKit\GetOptionKit;
        ok( $opt );
        $opt->add( 'b|bar:=s' , 'option with type' );

        $spec = $opt->get('bar');
        ok( $spec->isTypeString() );

        $result = $opt->parse(explode(' ','program -b text arg1 arg2 arg3'));
        ok( $result->bar );

        $args = $result->getArguments();
        ok( $args );
        count_ok( 3,$args);

        ok( $result->program );
    }


    function testSpec2()
    {
        $opt = new \GetOptionKit\GetOptionKit;
        ok( $opt );
        $opt->add( 'long'   , 'long option name only.' );
        $opt->add( 'a'   , 'short option name only.' );
        $opt->add( 'b'   , 'short option name only.' );
        ok( $opt->specs->all() );
        ok( $opt->specs );
        ok( $opt->getSpecs() );
        ok( $result = $opt->parse(explode(' ','program -a -b --long')) );
        ok( $result->a );
        ok( $result->b );
    }

    function testSpecCollection()
    {
        $opt = new \GetOptionKit\GetOptionKit;
        ok( $opt );

        $opt->add( 'f|foo:' , 'option requires a value.' );
        $opt->add( 'b|bar+' , 'option with multiple value.' );
        $opt->add( 'z|zoo?' , 'option with optional value.' );
        $opt->add( 'v|verbose' , 'verbose message.' );
        $opt->add( 'd|debug'   , 'debug message.' );
        $opt->add( 'long'   , 'long option name only.' );
        $opt->add( 's'   , 'short option name only.' );

        ok( $opt->specs->all() );
        ok( $opt->specs );
        ok( $opt->getSpecs() );

        count_ok( 7 , $array = $opt->specs->toArray() );
        ok( isset($array[0]['long'] ));
        ok( isset($array[0]['short'] ));
        ok( isset($array[0]['description'] ));

        ob_start();
        $opt->printOptions();
        $content = ob_get_contents();
        ob_clean();
        like( '/Available options/m', $content );

        # echo "\n".$content;
    }

    function test()
    {
        $opt = new \GetOptionKit\GetOptionKit;
        ok( $opt );

        $opt->add( 'f|foo:' , 'option require value' );
        $opt->add( 'b|bar+' , 'option with multiple value' );
        $opt->add( 'z|zoo?' , 'option with optional value' );
        $opt->add( 'v|verbose' , 'verbose message' );
        $opt->add( 'd|debug'   , 'debug message' );

        $result = $opt->parse( array( 'program' , '-f' , 'foo value' , '-v' , '-d' ) );
        ok( $result );
        ok( $result->foo );
        ok( $result->verbose );
        ok( $result->debug );
        is( 'foo value', $result->foo->value );
        ok( $result->verbose->value );
        ok( $result->debug->value );

        $result = $opt->parse( array( 'program' , '-f=foo value' , '-v' , '-d' ) );
        ok( $result );
        ok( $result->foo );
        ok( $result->verbose );
        ok( $result->debug );

        is_class( 'GetOptionKit\\OptionSpec' , $result->foo );
        is_class( 'GetOptionKit\\OptionSpec' , $result->verbose );
        is_class( 'GetOptionKit\\OptionSpec' , $result->debug );

        is( 'foo value', $result->foo->value );
        ok( $result->verbose->value );
        ok( $result->debug->value );

        $result = $opt->parse( array( 'program' , '-vd' ) );
        ok( $result->verbose );
        ok( $result->debug );
    }


}
