<?php

declare(strict_types=1);

namespace ApiClients\Client\Supervisord;

use ApiClients\Foundation\Hydrator\Options as HydratorOptions;
use ApiClients\Foundation\Options as FoundationOptions;
use ApiClients\Foundation\Transport\Options as TransportOptions;
use ApiClients\Middleware\HttpExceptions\HttpExceptionsMiddleware;
use ApiClients\Middleware\UserAgent\Options as UserAgentMiddlewareOptions;
use ApiClients\Middleware\UserAgent\UserAgentMiddleware;
use ApiClients\Middleware\UserAgent\UserAgentStrategies;
use ApiClients\Middleware\Xml\XmlDecodeMiddleware;
use ApiClients\Middleware\Xml\XmlEncodeMiddleware;
use function ApiClients\Foundation\options_merge;

final class ApiSettings
{
    const NAMESPACE = 'ApiClients\\Client\\Supervisord\\Resource';
    const TRANSPORT_OPTIONS = [
        FoundationOptions::HYDRATOR_OPTIONS => [
            HydratorOptions::NAMESPACE => self::NAMESPACE,
            HydratorOptions::NAMESPACE_DIR => __DIR__ . DIRECTORY_SEPARATOR . 'Resource' . DIRECTORY_SEPARATOR,
        ],
        FoundationOptions::TRANSPORT_OPTIONS => [
            TransportOptions::SCHEMA => 'http',
            TransportOptions::PATH => '/RPC2',
            TransportOptions::MIDDLEWARE => [
                HttpExceptionsMiddleware::class,
                UserAgentMiddleware::class,
                XmlEncodeMiddleware::class,
                XmlDecodeMiddleware::class,
            ],
            TransportOptions::DEFAULT_REQUEST_OPTIONS => [
                UserAgentMiddleware::class => [
                    UserAgentMiddlewareOptions::STRATEGY => UserAgentStrategies::PACKAGE_VERSION,
                    UserAgentMiddlewareOptions::PACKAGE => 'api-clients/supervisord',
                ],
            ],
        ],
    ];

    public static function getOptions(string $host, array $suppliedOptions, string $suffix): array
    {
        $options = options_merge(self::TRANSPORT_OPTIONS, $suppliedOptions);
        $options[FoundationOptions::HYDRATOR_OPTIONS][HydratorOptions::NAMESPACE_SUFFIX] = $suffix;

        list($ip, $port) = explode(':', $host);

        $options[FoundationOptions::TRANSPORT_OPTIONS][TransportOptions::HOST] = $ip;
        $options[FoundationOptions::TRANSPORT_OPTIONS][TransportOptions::PORT] = $port;

        return $options;
    }
}
