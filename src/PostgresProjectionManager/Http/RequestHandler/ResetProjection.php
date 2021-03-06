<?php

declare(strict_types=1);

namespace Prooph\PostgresProjectionManager\Http\RequestHandler;

use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\Router;
use Amp\Promise;
use Prooph\PostgresProjectionManager\Exception\ProjectionNotFound;
use Prooph\PostgresProjectionManager\Messages\ResetMessage;
use Prooph\PostgresProjectionManager\ProjectionManager;
use Throwable;
use function Amp\call;

/** @internal */
class ResetProjection implements RequestHandler
{
    /** @var ProjectionManager */
    private $projectionManager;

    public function __construct(ProjectionManager $projectionManager)
    {
        $this->projectionManager = $projectionManager;
    }

    public function handleRequest(Request $request): Promise
    {
        $args = $request->getAttribute(Router::class);
        $name = $args['name'];

        \parse_str($request->getUri()->getQuery(), $query);
        $enableRunAs = $query['enableRunAs'] ?? null;

        return call(function () use ($name, $enableRunAs) {
            try {
                $message = new ResetMessage($name, $enableRunAs);
                yield $this->projectionManager->handle($message);
            } catch (ProjectionNotFound $e) {
                return new Response(404, ['Content-Type' => 'text/plain'], 'Not Found');
            } catch (Throwable $e) {
                return new Response(500, ['Content-Type' => 'text/plain'], 'Error');
            }

            return new Response(200, ['Content-Type' => 'text/html'], 'OK');
        });
    }
}
