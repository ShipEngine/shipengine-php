<?php declare(strict_types=1);

namespace ShipEngine\Service;

use Psr\Http\Message\ResponseInterface;
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
            $messages[] = new Error($validated['exception_description']);
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
        $messages = array();
        
        foreach ($events as $event) {
            $messages[] = $event->messages;
        }

        return Arr::flatten($messages);
    }

    /**
     *
     */
    public function query($query): QueryResult
    {
        if (is_string($query)) {
            $response = $this->request('GET', '/labels/' . $query . '/track');
        } elseif (get_class($query) == Query::class) {
            $url = '/tracking?carrier_code=' . $query->carrier_code . '&tracking_number=' . $query->tracking_number;
            $response = $this->request('GET', $url);
        } else {
            throw new \InvalidArgumentException('Query must be a `Query` or string representing a `shipment_id`.');
        }

        $messages = array();
        
        $code = $response->getStatusCode();
        if ($code != 200) {
            $messages[] = new Error('HTTP ERROR: ' . $code);
        }

        $body = json_decode((string) $response->getBody(), true);

        if (!$body) {
            return new QueryResult($query, null, $messages);
        }
        
        $information = $this->parseInformation($body);
        if (is_null($information)) {
            $messages[] = new Error('Could not parse tracking information.');
            return new QueryResult($query, null, $messages);
        }

        $messages = array_merge($messages, $this->extractMessages($information->events));

        return new QueryResult($query, $information, $messages);
    }
}
