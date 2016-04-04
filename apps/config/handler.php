<?php

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Page not found : 404   ');
    };
};

$container['errorHandler'] = function ($c) use ($beritagar) {
    return function (\Psr\Http\Message\RequestInterface $request, $response, Exception $exception) use ($c,$beritagar) {
        \Beritagar\Log::init('app')->addError($exception->getMessage(),array(
            'url' => (string) $request->getUri()->getPath(),
            'device' => \Beritagar\Device::detect()
        ));
        echo $exception->getMessage();
        return $c['response']->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('Something went wrong!');
    };
};
