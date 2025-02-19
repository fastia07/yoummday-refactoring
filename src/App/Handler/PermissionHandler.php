<?php

declare(strict_types=1);

namespace App\Handler;

use App\DTO\Token;
use App\HTTP\Response\JSONResponse;
use App\Provider\TokenDataProvider;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Attribute\Route;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Handler\HandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\HttpMethod;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

#[Route(httpMethod: HttpMethod::GET, uri: '/has_permission/{token}')]
class PermissionHandler implements HandlerInterface
{
    private const DEFAULT_PERMISSION = 'read';
    private const ERROR_TOKEN_REQUIRED = 'Token is required';
    private const ERROR_TOKEN_NOT_FOUND = 'Token not found';

    private TokenDataProvider $tokenDataProvider;

    public function __construct(TokenDataProvider $tokenDataProvider)
    {
        $this->tokenDataProvider = $tokenDataProvider;
    }

    public function __invoke(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface
    {
        $tokenId = $parameters->get('token');
        if (empty($tokenId)) {
            return $this->createErrorResponse(self::ERROR_TOKEN_REQUIRED, 400);
        }

        $token = $this->findToken($tokenId);
        if ($token === null) {
            return $this->createErrorResponse(self::ERROR_TOKEN_NOT_FOUND, 404);
        }

        $requiredPermission = $this->getRequiredPermission($serverRequest);
        $hasPermission = $token->hasPermission($requiredPermission);

        return $this->createSuccessResponse($token, $requiredPermission, $hasPermission);
    }

    private function getRequiredPermission(ServerRequestInterface $request): string
    {
        return $request->getQueryParams()['permission'] ?? self::DEFAULT_PERMISSION;
    }

    private function findToken(string $tokenId): ?Token
    {
        $tokens = $this->tokenDataProvider->getTokens();
        foreach ($tokens as $tokenData) {
            if ($tokenData['token'] === $tokenId) {
                return new Token($tokenData['token'], $tokenData['permissions']);
            }
        }
        return null;
    }

    private function createErrorResponse(string $message, int $statusCode): ResponseInterface
    {
        return new JSONResponse(['error' => $message], $statusCode);
    }

    private function createSuccessResponse(Token $token, string $permission, bool $hasPermission): ResponseInterface
    {
        return new JSONResponse([
            'has_permission' => $hasPermission,
            'token' => $token->getId(),
            'permission' => $permission
        ]);
    }
}
