<?php

declare(strict_types=1);

namespace Tests\App\Handler;

use App\Handler\PermissionHandler;
use App\HTTP\Response\JSONResponse;
use App\Provider\TokenDataProvider;
use PHPUnit\Framework\TestCase;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class PermissionHandlerTest extends TestCase
{
    private TokenDataProvider $tokenDataProvider;
    private PermissionHandler $handler;
    private ServerRequestInterface $request;
    private RouteParameters $parameters;

    protected function setUp(): void
    {
        $this->tokenDataProvider = new TokenDataProvider();
        $this->handler = new PermissionHandler($this->tokenDataProvider);
        
        // Mock ServerRequestInterface
        $this->request = $this->createMock(ServerRequestInterface::class);
        $uri = $this->createMock(UriInterface::class);
        $this->request->method('getUri')->willReturn($uri);
        
        // Create RouteParameters
        $this->parameters = new RouteParameters([]);
    }

    public function testHasReadPermissionWithValidToken(): void
    {
        $this->parameters = new RouteParameters(['token' => 'token1234']);
        $this->request->method('getQueryParams')->willReturn(['permission' => 'read']);

        /** @var JSONResponse $response */
        $response = ($this->handler)($this->request, $this->parameters);
        
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData();
        $this->assertTrue($data['has_permission']);
        $this->assertEquals('token1234', $data['token']);
        $this->assertEquals('read', $data['permission']);
    }

    public function testHasWritePermissionWithValidToken(): void
    {
        $this->parameters = new RouteParameters(['token' => 'token1234']);
        $this->request->method('getQueryParams')->willReturn(['permission' => 'write']);

        /** @var JSONResponse $response */
        $response = ($this->handler)($this->request, $this->parameters);
        
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData();
        $this->assertTrue($data['has_permission']);
    }

    public function testReadOnlyTokenCannotWrite(): void
    {
        $this->parameters = new RouteParameters(['token' => 'tokenReadonly']);
        $this->request->method('getQueryParams')->willReturn(['permission' => 'write']);

        /** @var JSONResponse $response */
        $response = ($this->handler)($this->request, $this->parameters);
        
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData();
        $this->assertFalse($data['has_permission']);
    }

    public function testInvalidTokenReturns404(): void
    {
        $this->parameters = new RouteParameters(['token' => 'nonexistent']);
        $this->request->method('getQueryParams')->willReturn(['permission' => 'read']);

        /** @var JSONResponse $response */
        $response = ($this->handler)($this->request, $this->parameters);
        
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $data = $response->getData();
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Token not found', $data['error']);
    }

    public function testMissingTokenReturns400(): void
    {
        $this->parameters = new RouteParameters([]);
        
        /** @var JSONResponse $response */
        $response = ($this->handler)($this->request, $this->parameters);
        
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $data = $response->getData();
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Token is required', $data['error']);
    }

    public function testDefaultPermissionIsRead(): void
    {
        $this->parameters = new RouteParameters(['token' => 'tokenReadonly']);
        $this->request->method('getQueryParams')->willReturn([]);

        /** @var JSONResponse $response */
        $response = ($this->handler)($this->request, $this->parameters);
        
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $data = $response->getData();
        $this->assertTrue($data['has_permission']);
        $this->assertEquals('read', $data['permission']);
    }
}
