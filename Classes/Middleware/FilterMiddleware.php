<?php

namespace Buepro\Fromes\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FilterMiddleware implements \Psr\Http\Server\MiddlewareInterface
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
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
            (new \Buepro\Fromes\Service\SessionService())->getAccessToken() === $request->getHeader('fromes')[0] &&
            GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('frontend.user', 'isLoggedIn')
        ) {
            $content = $request->getBody()->getContents();
            $filterData = json_decode($content)->data;
//            $filter = new Filter();
//            foreach($filterData as $subfilterData) {
//                $filter->addSubfilter(Subfilter::createFromData($subfilterData));
//            }
//            $feUsers = (new FrontendUserRepository())->setFilterConstraints($filter->getConstraints());
            $response = $this->responseFactory->createResponse()
                ->withHeader('Content-Type', 'application/json; charset=utf-8');
            $response->getBody()->write(json_encode($feUsers ?? []));
            return $response;
        }
        return $handler->handle($request);
    }
}
