<?php declare(strict_types=1);

namespace ShipEngine\Service\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\ShipEngine;

final class TagsTraitTest extends TestCase
{
    private ShipEngine $shipengine;
    
    public static function setUpBeforeClass(): void
    {
        exec('hoverctl import simengine/v1/tags.json');
    }
        
    public static function tearDownAfterClass(): void
    {
        exec('hoverctl delete --force simengine/v1/tags.json');
    }
    
    protected function setUp(): void
    {
        $this->shipengine = new ShipEngine(['api_key' => 'foobar', 'base_uri' => 'http://localhost:8500/v1']);
    }
    
    public function testCreateTag(): void
    {
        $expected = 'foobar';
        $got = $this->shipengine->createTag($expected);
        $this->assertEquals($expected, $got);
    }
}
