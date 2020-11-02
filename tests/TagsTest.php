<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class TagsTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        exec('hoverctl import simengine/v1/tags.json');
    }

    public static function tearDownAfterClass(): void
    {
        exec('hoverctl delete --force simengine/v1/tags.json');
    }
    
    public function testCreateTag(): void
    {
        $tag = 'foobar';
        
        $client = new GuzzleHttp\Client(['base_uri' => 'http://localhost:8500/v1/']);
        $response = $client->request('POST', 'tags/' . $tag);
        $body = (string) $response->getBody();
        $obj = json_decode($body, true);

        $this->assertEquals($obj['name'], $tag);
    }
}
