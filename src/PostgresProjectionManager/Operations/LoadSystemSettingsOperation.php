<?php

declare(strict_types=1);

namespace Prooph\PostgresProjectionManager\Operations;

use Amp\Postgres\Pool;
use Amp\Postgres\ResultSet;
use Amp\Postgres\Statement;
use Error;
use Generator;
use Prooph\EventStore\Common\SystemStreams;
use Prooph\EventStore\Data\SystemSettings;

/** @internal */
class LoadSystemSettingsOperation
{
    public function __invoke(Pool $pool): Generator
    {
        /** @var Statement $statement */
        $statement = yield $pool->prepare(<<<SQL
SELECT
    COALESCE(e1.event_id, e2.event_id) as event_id,
    e1.event_number as event_number,
    COALESCE(e1.event_type, e2.event_type) as event_type,
    COALESCE(e1.data, e2.data) as data,
    COALESCE(e1.meta_data, e2.meta_data) as meta_data,
    COALESCE(e1.is_json, e2.is_json) as is_json,
    COALESCE(e1.updated, e2.updated) as updated
FROM
    events e1
LEFT JOIN events e2
    ON (e1.link_to_stream_name = e2.stream_name AND e1.link_to_event_number = e2.event_number)
WHERE e1.stream_name = ?
ORDER BY e1.event_number DESC
LIMIT 1
SQL
        );
        /** @var ResultSet $result */
        $result = yield $statement->execute([SystemStreams::SettingsStream]);

        if (! yield $result->advance(ResultSet::FETCH_OBJECT)) {
            $this->settings = SystemSettings::default();

            return null;
        }

        $event = $result->getCurrent();

        $data = \json_decode($event->data, true);

        if (0 !== \json_last_error()) {
            throw new Error('Could not json decode system settings');
        }

        return SystemSettings::fromArray($data);
    }
}
