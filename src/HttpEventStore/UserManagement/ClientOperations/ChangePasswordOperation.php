<?php

declare(strict_types=1);

namespace Prooph\HttpEventStore\UserManagement\ClientOperations;

use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Http\Message\UriFactory;
use Prooph\EventStore\Data\UserCredentials;
use Prooph\EventStore\Exception\AccessDenied;
use Prooph\EventStore\Exception\UserNotFound;
use Prooph\HttpEventStore\ClientOperations\Operation;
use Prooph\HttpEventStore\Http\RequestMethod;

/** @internal */
class ChangePasswordOperation extends Operation
{
    public function __invoke(
        HttpClient $httpClient,
        RequestFactory $requestFactory,
        UriFactory $uriFactory,
        string $baseUri,
        string $login,
        string $oldPassword,
        string $newPassword,
        ?UserCredentials $userCredentials
    ): void {
        $string = \json_encode([
            'currentPassword' => $oldPassword,
            'newPassword' => $newPassword,
        ]);

        $request = $requestFactory->createRequest(
            RequestMethod::Post,
            $uriFactory->createUri($baseUri . '/users/' . \urlencode($login) . '/command/change-password'),
            [
                'Content-Type' => 'application/json',
                'Content-Length' => \strlen($string),
            ],
            $string
        );

        $response = $this->sendRequest($httpClient, $userCredentials, $request);

        switch ($response->getStatusCode()) {
            case 200:
                return;
            case 401:
                throw AccessDenied::toUserManagementOperation();
            case 404:
                throw UserNotFound::withLogin($login);
            default:
                throw new \UnexpectedValueException('Unexpected status code ' . $response->getStatusCode() . ' returned');
        }
    }
}
