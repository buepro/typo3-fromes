<?php

/*
 * This file is part of the composer package buepro/typo3-fromes.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Fromes\Middleware;

use Buepro\Fromes\Service\SessionService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FilterMiddleware implements MiddlewareInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var StreamFactory */
    private $streamFactory;

    public function __construct(ResponseFactoryInterface $responseFactory, StreamFactory $streamFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Process the request in case a frontend user is logged in and the session access token can be verified.
     *
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (
            isset($request->getHeader('fromes')[0]) &&
            ($request->getHeader('fromes')[0] === 'Filter' || $request->getHeader('fromes')[0] === 'Email')
        ) {
            try {
                if (
                    GeneralUtility::makeInstance(Context::class)
                        ->getPropertyFromAspect('frontend.user', 'isLoggedIn') !== true
                ) {
                    throw new \DomainException('Frontend user not available. Login is required.', 1644474339);
                }
                $extbaseBootstrap = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Core\Bootstrap::class);
                $jsonString = $extbaseBootstrap->run('', [
                    'extensionName' => 'Fromes',
                    'pluginName' => 'Messenger',
                ]);
                return $this->responseFactory->createResponse()
                    ->withHeader('Content-Type', 'application/json; charset=utf-8')
                    ->withBody($this->streamFactory->createStream($jsonString));
            } catch (\Exception $e) {
                return $this->responseFactory->createResponse(500)
                    ->withHeader('Content-Type', 'application/json; charset=utf-8')
                    ->withBody($this->streamFactory->createStream(json_encode([
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ], JSON_THROW_ON_ERROR)));
            }
        }
        return $handler->handle($request);
    }
}
