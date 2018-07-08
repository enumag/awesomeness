<?php

declare(strict_types=1);

namespace Prooph\HttpEventStore\ClientOperations;

use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Http\Message\UriFactory;
use Prooph\EventStore\Data\EventId;
use Prooph\EventStore\Data\EventRecord;
use Prooph\EventStore\Data\UserCredentials;
use Prooph\EventStore\Exception\AccessDenied;
use Prooph\EventStore\Internal\DateTimeUtil;
use Prooph\HttpEventStore\Http\RequestMethod;

/** @internal */
class ReadFromSubscriptionOperation extends Operation
{
    /**
     * @return EventRecord[]
     */
    public function __invoke(
        HttpClient $httpClient,
        RequestFactory $requestFactory,
        UriFactory $uriFactory,
        string $baseUri,
        string $stream,
        string $groupName,
        int $amount,
        ?UserCredentials $userCredentials
    ): array {
        $request = $requestFactory->createRequest(
            RequestMethod::Get,
            $uriFactory->createUri(\sprintf(
                '%s/subscriptions/%s/%s/%d?embed=tryharder',
                $baseUri,
                \urlencode($stream),
                \urlencode($groupName),
                $amount
            )),
            [
                'Accept' => 'application/vnd.eventstore.competingatom+json',
            ]
        );

        $response = $this->sendRequest($httpClient, $userCredentials, $request);

        switch ($response->getStatusCode()) {
            case 401:
                throw AccessDenied::toStream($stream);
            case 404:
                throw new \RuntimeException(\sprintf(
                    'Subscription with stream \'%s\' and group name \'%s\' not found',
                    $stream,
                    $groupName
                ));
            case 200:
                $json = \json_decode($response->getBody()->getContents(), true);
                $events = [];

                if (null === $json) {
                    return $events;
                }

                foreach (\array_reverse($json['entries']) as $entry) {
                    $data = $entry['data'] ?? '';

                    if (\is_array($data)) {
                        $data = \json_encode($data);
                    }

                    $field = isset($json['isLinkMetaData']) && $json['isLinkMetaData'] ? 'linkMetaData' : 'metaData';

                    $metadata = $json[$field] ?? '';

                    if (\is_array($metadata)) {
                        $metadata = \json_encode($metadata);
                    }

                    $events[] = new EventRecord(
                        $entry['positionStreamId'],
                        $entry['positionEventNumber'],
                        EventId::fromString($entry['eventId']),
                        $entry['eventType'],
                        $entry['isJson'],
                        $data,
                        $metadata,
                        DateTimeUtil::create($entry['updated'])
                    );
                }

                return $events;
            default:
                throw new \UnexpectedValueException('Unexpected status code ' . $response->getStatusCode() . ' returned');
        }
    }
}
