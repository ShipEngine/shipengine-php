<?php declare(strict_types=1);

namespace ShipEngine\Service;

use Rakit\Validation\Validator;

use ShipEngine\Message\Error;
use ShipEngine\Message\Info;
use ShipEngine\Model\Tracking\Event;
use ShipEngine\Model\Tracking\Information;
use ShipEngine\Model\Tracking\Location;
use ShipEngine\Model\Tracking\Query;
use ShipEngine\Model\Tracking\QueryResult;
use ShipEngine\Util\ISOString;
use ShipEngine\Util\Arr;

/**
 *
 */
final class TrackingService extends AbstractService
{
    /**
     *
     */
    private function parseLocation($event): ?Location
    {
        $location = Arr::subArray($event, 'city_locality', 'state_province', 'postal_code', 'country_code');
        $values = array_values($location);
        
        if (empty(array_filter($values))) {
            return null;
        }

        return new Location(...$values);
    }
    
    /**
     *
     */
    private function parseEvent(array $event): ?Event
    {
        $validator = new Validator();
        
        $guard = array(
            'occurred_at' => 'required',
            'status_code' => 'required',
            'status_description' => 'required',
            'carrier_status_code' => 'required',
            'carrier_detail_code' => 'required',
            // MESSAGES
            'carrier_status_description' => 'default:|present',
            'exception_description' => 'default:|present',
            // LOCATION
            'city_locality' => 'default:|present',
            'state_province' => 'default:|present',
            'postal_code' => 'default:|present',
            'country_code' => 'default:|present',
            //
            'signer' => 'present'
        );

        $validation = $validator->validate($event, $guard);

        if ($validation->fails()) {
            return null;
        }

        $validated = $validation->getValidData();

        $messages = array();
        if ($validated['carrier_status_description'] != '') {
            $messages[] = new Info($validated['carrier_status_description']);
        }
        if ($validated['exception_description'] != '') {
            $messages[] = new Exception($validated['exception_description']);
        }

        $location = $this->parseLocation($validated);

        return new Event(
            new ISOString($validated['occurred_at']),
            $validated['status_code'],
            $validated['status_description'],
            $validated['carrier_status_code'],
            $validated['carrier_detail_code'],
            $messages,
            $location,
            $validated['signer']
        );
    }

    /**
     *
     */
    private function parseInformation(array $body): ?Information
    {
        $validator = new Validator();

        $guard = array(
            'tracking_number' => 'required',
            'estimated_delivery_date' => 'required',
            'events' => 'required|array'
        );

        $validation = $validator->validate($body, $guard);

        if ($validation->fails()) {
            return null;
        }

        $validated = $validation->getValidData();
        
        $events = array_map(function ($event) {
            return $this->parseEvent($event);
        }, $validated['events']);

        return new Information(
            $validated['tracking_number'],
            new ISOString($validated['estimated_delivery_date']),
            array_filter($events)
        );
    }

    /**
     *
     */
    private function extractMessages(array $events): array
    {
        return array();
    }

    /**
     *
     */
    private function queryLabel(string $label_id): QueryResult
    {
        $body = $this->request('GET', '/labels' . $label_id . '/track');

        $code = $response->getStatusCode();
        if ($code != 200 && $code != 404) {
            throw new HttpException('HTTP EXCEPTION', $request, $response);
        }
        
        $messages = array();
        if ($code == 404) {
            $messages[] = new Error('Label ' . $label_id . ' not found.');
            return new QueryResult($label_id, null, $messages);
        }
        
        $body = json_decode((string) $response->getBody(), true);
        
        $information = $this->parseInformation($body);
        if (is_null($information)) {
            $messages[] = new Error('Could not parse tracking information.');
            return new QueryResult($label_id, null, $messages);
        }
        
        $messages = array_merge($messages, $this->extractMessages($information->events));

        return new QueryResult($label_id, $information, $messages);
    }

    /**
     *
     */
    private function queryTrackingQuery(Query $query): QueryResult
    {
        $url = '/tracking?carrier_code=' . $query->carrier_code . '&tracking_number=' . $query->tracking_number;
        $response = $this->request('GET', $url);

        $code = $response->getStatusCode();
        if ($code != 200) {
            throw new HttpException('HTTP EXCEPTION', $request, $response);
        }

        $body = json_decode((string) $response->getBody(), true);

        $messages = array();
        
        $information = $this->parseInformation($body);
        if (is_null($information)) {
            $messages[] = new Error('Could not parse tracking information.');
            return new QueryResult($query, null, $messages);
        }
        
        $messages = array_merge($messages, $this->extractMessages($information->events));

        return new QueryResult($query, $information, $messages);
    }

    /**
     *
     */
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
