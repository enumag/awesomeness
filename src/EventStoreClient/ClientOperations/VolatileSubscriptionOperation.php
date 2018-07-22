<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\ClientOperations;

use Prooph\EventStoreClient\EventStoreSubscription;
use Prooph\EventStoreClient\Internal\EventMessageConverter;
use Prooph\EventStoreClient\Internal\VolatileEventStoreSubscription;
use Prooph\EventStoreClient\Messages\ClientMessages\StreamEventAppeared;
use Prooph\EventStoreClient\Messages\ClientMessages\SubscribeToStream;
use Prooph\EventStoreClient\Messages\ClientMessages\SubscriptionConfirmation;
use Prooph\EventStoreClient\SystemData\InspectionDecision;
use Prooph\EventStoreClient\SystemData\InspectionResult;
use Prooph\EventStoreClient\SystemData\TcpCommand;
use Prooph\EventStoreClient\SystemData\TcpFlags;
use Prooph\EventStoreClient\SystemData\TcpPackage;

/** @internal  */
class VolatileSubscriptionOperation extends AbstractSubscriptionOperation
{
    protected function createSubscriptionPackage(): TcpPackage
    {
        $message = new SubscribeToStream();
        $message->setEventStreamId($this->streamId);
        $message->setResolveLinkTos($this->resolveLinkTos);

        return new TcpPackage(
            TcpCommand::subscribeToStream(),
            $this->userCredentials ? TcpFlags::authenticated() : TcpFlags::none(),
            $this->correlationId,
            $message->serializeToString(),
            $this->userCredentials
        );
    }

    protected function preInspectPackage(TcpPackage $package): ?InspectionResult
    {
        if ($package->command()->equals(TcpCommand::subscriptionConfirmation())) {
            $message = new SubscriptionConfirmation();
            $message->mergeFromString($package->data());

            $this->confirmSubscription($message->getLastCommitPosition(), $message->getLastEventNumber());

            return new InspectionResult(InspectionDecision::subscribed(), 'SubscriptionConfirmation');
        }

        if ($package->command()->equals(TcpCommand::streamEventAppeared())) {
            $message = new StreamEventAppeared();
            $message->mergeFromString($package->data());
            $event = EventMessageConverter::convertResolvedEventMessageToResolvedEvent($message->getEvent());
            $this->eventAppeared($event);

            return new InspectionResult(InspectionDecision::doNothing(), 'StreamEventAppeared');
        }

        return null;
    }

    protected function createSubscriptionObject(int $lastCommitPosition, ?int $lastEventNumber): EventStoreSubscription
    {
        return new VolatileEventStoreSubscription(
                $this,
                $this->streamId,
                $lastCommitPosition,
                $lastEventNumber
        );
    }

    public function name(): string
    {
        return 'VolatileSubscription';
    }

    public function __toString(): string
    {
        return \sprintf(
            'StreamId: %s, ResolveLinkTos : %s',
            $this->streamId,
            $this->resolveLinkTos ? 'yes' : 'no'
        );
    }
}
