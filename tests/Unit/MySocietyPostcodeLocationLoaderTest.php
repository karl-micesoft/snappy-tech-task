<?php

namespace Tests\Unit;

use App\Helpers\PostcodeLocationLoaders\MySocietyPostcodeLocationLoader;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class MySocietyPostcodeLocationLoaderTest extends TestCase
{
    private MySocietyPostcodeLocationLoader $sut;

    /** @var MockObject|Response */
    private Response|MockObject $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new MySocietyPostcodeLocationLoader();

        $this->response = $this->createMock(Response::class);
        Http::shouldReceive('get')->once()->andReturn($this->response);
    }

    public function testFailedDownload()
    {
        $this->response->method('successful')->willReturn(false);

        $this->expectException(Exception::class);
        $this->sut->read();
    }

    public function testSuccessfulDownload()
    {
        $this->response->method('successful')->willReturn(true);

        $this->response->method('body')->willReturn(
            file_get_contents(__DIR__ . '/fixtures/2022-11.zip')
        );

        $data = $this->sut->read();
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertIsArray($data[0]);
        $this->assertArrayHasKey('postcode', $data[0]);
        $this->assertArrayHasKey('latitude', $data[0]);
        $this->assertArrayHasKey('longitude', $data[0]);

        $data = $this->sut->read(5);
        $this->assertIsArray($data);
        $this->assertCount(3, $data);

        foreach ($data as $item) {
            $this->assertIsArray($item);
            $this->assertArrayHasKey('postcode', $item);
            $this->assertArrayHasKey('latitude', $item);
            $this->assertArrayHasKey('longitude', $item);
        }
    }
}
