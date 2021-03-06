<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class SponsorTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmpty()
    {
        $list =
            Sponsor::create();

        $expected = '';

        $rendered = $list->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderWithSponsor()
    {
        $list =
            Sponsor::create()
                ->withPageUrl('http://facebook.com/my-sponsor');

        $expected =
            '<ul class="op-sponsors">'.
                '<li>'.
                    '<a href="http://facebook.com/my-sponsor" rel="facebook"></a>'.
                '</li>'.
            '</ul>';

        $rendered = $list->render();
        $this->assertEquals($expected, $rendered);
    }
}
