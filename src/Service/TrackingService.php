<?php declare(strict_types=1);

namespace ShipEngine\Service;

use Rakit\Validation\Validator;

use ShipEngine\Model\Tracking\Query;
use ShipEngine\Model\Tracking\QueryResult;

/**
 *
 */
final class TrackingService extends AbstractService
{

    private function parseResponse($obj): QueryResult
    {
    }
    
    private function queryLabel(string $label_id): QueryResult
    {
        $body = $this->request('GET', '/labels' . $label_id . '/track');

        return $this->parseResponse($body);
    }

    private function queryTrackingQuery(Query $query): QueryResult
    {
        $url = '/tracking?carrier_code=' . $query->carrier_code . '&tracking_number=' . $query->tracking_number;
        $body = $this->request('GET', $url);

        return $this->parseResponse($body);
    }
    
    public function query(object $query): QueryResult
    {
        if (is_string($query)) {
            return $this->queryLabel($query);
        } elseif (get_class($query) == Query::class) {
            return $this->queryTrackingQuery($query);
        }

        throw new InvalidArgumentException('query must be a Tracking\Query or string representing a label_id');
    }
}
