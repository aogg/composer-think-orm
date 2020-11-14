<?php
/**
 * User: aozhuochao
 * Date: 2020/11/14
 */

namespace aogg\think\orm\tests;


class DbTest extends \aogg\phpunit\think\BaseTestCase
{
    public function test_getPrefix()
    {
        $prefix = \aogg\think\orm\DbFacade::getPrefix();

        $this->assertNotEmpty($prefix);
    }
}