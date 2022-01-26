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
            (new SessionService())->getAccessToken() === $request->getHeader('fromes')[0]
        ) {
            $extbaseBootstrap = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Core\Bootstrap::class);
            $json = $extbaseBootstrap->run('', [
                'extensionName' => 'Fromes',
                'pluginName' => 'Messenger',
            ]);
            return $this->responseFactory->createResponse()
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withBody($this->streamFactory->createStream($json));
        }
        return $handler->handle($request);
    }
}
