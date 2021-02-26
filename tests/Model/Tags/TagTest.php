<?php


namespace Model\Tags;

use PHPUnit\Framework\TestCase;
use ShipEngine\Model\Tags\Tag;

/**
 * Test the Tag type
 *
 * @covers \ShipEngine\Model\Tags\Tag;
 */
class TagTest extends TestCase
{
    private Tag $tag;
    private const TAG_VALUE = 'calque_rpc_tag';

    protected function setUp(): void
    {
        $this->tag = new Tag(self::TAG_VALUE);
    }

    public function testTagInstanceOf(): void
    {
        $this->assertInstanceOf(Tag::class, $this->tag);
    }

    public function testTagReturnValue(): void
    {
       $this->assertEquals(self::TAG_VALUE, $this->tag->name);
    }
}