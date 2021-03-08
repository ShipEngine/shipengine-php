<?php declare(strict_types=1);

namespace Service\Tag;

use PHPUnit\Framework\TestCase;
use ShipEngine\Model\Tag\Tag;
use ShipEngine\ShipEngine;

/**
 * Tests the methods provided in the `TagService`.
 *
 * @covers \ShipEngine\Model\Tag\Tag
 * @covers \ShipEngine\Service\Tag\TagService
 * @covers \ShipEngine\Service\Tag\TagTrait
 * @covers \ShipEngine\Service\AbstractService
 * @covers \ShipEngine\Service\ServiceFactory
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 */
final class TagServiceTest extends TestCase
{
    /**
     * @var ShipEngine
     */
    private ShipEngine $shipengine;

    /**
     * @var string
     */
    private string $test_tag;

    /**
     * Import `simengine/rpc/rpc.json` into *Hoverfly* before class instantiation.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        exec('hoverctl import simengine/rpc/rpc.json');
    }

    /**
     * Delete `simengine/rpc/rpc.json` from *Hoverfly*.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        exec('hoverctl delete --force simengine/rpc/rpc.json');
    }

    /**
     * Pass an `api-key` into the new instance of the *ShipEngine* class.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->test_tag = 'calque';
        $this->shipengine = new ShipEngine('baz');
    }

    /**
     * Test the `create()` convenience method on the *TagService* successfully creates a new tag using
     * the `tag/create` remote procedure.
     *
     * @return void
     */
    public function testCreateTagRequest(): void
    {
        $new_tag = $this->shipengine->tags->create(array('name' => $this->test_tag));

        $this->assertEquals($new_tag->name, $this->test_tag);
    }

    /**
     * Test the return type, should be an instance of the `Tag` Type.
     */
    public function testReturnValue(): void
    {
        $new_tag = $this->shipengine->tags->create(array('name' => $this->test_tag));

        $this->assertInstanceOf(Tag::class, $new_tag);
    }
}
