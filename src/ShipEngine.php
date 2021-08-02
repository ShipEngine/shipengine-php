<?php

declare(strict_types=1);

namespace ShipEngine;

use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\ListCarriers\Result as ListCarriersResult;

/**
 * Exposes the functionality of the ShipEngine API.
 *
 * @package ShipEngine
 */
final class ShipEngine
{
    /**
     * ShipEngine SDK Version
     */
    public const VERSION = '1.0.0';

    // /**
    //  *
    //  * @var ShipEngineClient
    //  */
    // protected ShipEngineClient $client;

    /**
     * Global configuration for the ShipEngine API client, such as timeouts,
     * retries, page size, etc. This configuration applies to all method calls,
     * unless specifically overridden when calling a method.
     *
     * @var ShipEngineConfig
     */
    public ShipEngineConfig $config;

    /**
     * Instantiates the ShipEngine class. The `apiKey` you pass in can be either
     * a ShipEngine sandbox or production API Key. (sandbox keys start with "TEST_)
     *
     * @param mixed $config Can be either a string that is your `apiKey` or an `array` {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, eventListener:object}
     */
    public function __construct($config = null)
    {
        $this->config = new ShipEngineConfig(
            is_string($config) ? array('apiKey' => $config) : $config
        );
    }

    /**
     * Fetch the carrier accounts connected to your ShipEngine Account.
     *
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @return array An array of **CarrierAccount** objects that correspond the to carrier accounts connected
     * to a given ShipEngine account.
     */
    public function listCarriers($config = null): array
    {
        $config = $this->config->merge($config);
        $client = new ShipEngineClient();
        $apiResponse = $client->get(
            'v1/carriers',
            $config,
        );

        return $apiResponse;
    }

    /**
     * Address validation ensures accurate addresses and can lead to reduced shipping costs by preventing address
     * correction surcharges. ShipEngine cross references multiple databases to validate addresses and identify
     * potential deliverability issues.
     * See: https://shipengine.github.io/shipengine-openapi/#operation/validate_address
     *
     * @param array $params A list of addresses that are to be validated
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @return array An array of Address objects that correspond the to carrier accounts connected
     * to a given ShipEngine account.
     */
    public function validateAddresses($params, $config = null): array
    {
        $config = $this->config->merge($config);
        $client = new ShipEngineClient();
        $apiResponse = $client->post(
            'v1/addresses/validate',
            $config,
            $params
        );

        return $apiResponse;
    }

    /**
     * When retrieving rates for shipments using the /rates endpoint, the returned information contains a rateId
     * property that can be used to generate a label without having to refill in the shipment information repeatedly.
     * See: https://shipengine.github.io/shipengine-openapi/#operation/create_label_from_rate
     *
     * @param string $rateId A rate identifier for the label
     * @param array $params An array of label params that will dictate the label display and level of verification.
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @return array A label that correspond the to shipment details for a rate id
     */
    public function createLabelFromRate($rateId, $params, $config = null): array
    {
        $config = $this->config->merge($config);
        $client = new ShipEngineClient();
        $apiResponse = $client->post(
            "v1/labels/rates/$rateId",
            $config,
            $params
        );

        return $apiResponse;
    }

    /**
     * Purchase and print a label for shipment.
     * https://shipengine.github.io/shipengine-openapi/#operation/create_label
     *
     * @param array $params An array of shipment details for the label creation.
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @return array A label that correspond the to shipment details
     */
    public function createLabelFromShipmentDetails($params, $config = null): array
    {
        $config = $this->config->merge($config);
        $client = new ShipEngineClient();
        $apiResponse = $client->post(
            'v1/labels',
            $config,
            $params
        );

        return $apiResponse;
    }

    /**
     * Void label with a Label Id.
     * https://shipengine.github.io/shipengine-openapi/#operation/void_label
     *
     * @param string $labelId A label id
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @return array A voided label approval and message
     */
    public function voidLabelWithLabelId($labelId, $config = null): array
    {
        $config = $this->config->merge($config);
        $client = new ShipEngineClient();
        $apiResponse = $client->put(
            "v1/labels/$labelId/void",
            $config
        );

        return $apiResponse;
    }

    /**
     * Given some shipment details and rate options, this endpoint returns a list of rate quotes.
     * See: https://shipengine.github.io/shipengine-openapi/#operation/calculate_rates
     *
     * @param array $params An array of rate options and shipment details.
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @return array An array of Rate objects that correspond to the rate options and shipment details.
     */
    public function getRatesWithShipmentDetails($params, $config = null): array
    {
        $config = $this->config->merge($config);
        $client = new ShipEngineClient();
        $apiResponse = $client->post(
            'v1/rates',
            $config,
            $params
        );

        return $apiResponse;
    }

    /**
     * Retrieve the label's tracking information with Label Id
     * See: https://shipengine.github.io/shipengine-openapi/#operation/get_tracking_log_from_label
     *
     * @param string $labelId A label id
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @return array An array of Tracking information corresponding to the Label Id.
     */
    public function trackUsingLabelId($labelId, $config = null): array
    {
        $config = $this->config->merge($config);
        $client = new ShipEngineClient();
        $apiResponse = $client->get(
            "v1/labels/$labelId/track",
            $config
        );

        return $apiResponse;
    }

    /**
     * Retrieve the label's tracking information with Carrier Code and Tracking Number
     * See: https://shipengine.github.io/shipengine-openapi/#operation/get_tracking_log
     *
     * @param string $carrierCode Carrier code used to retrieve tracking information
     * @param string $trackingNumber The tracking number associated with a shipment
     * @param array|ShipEngineConfig|null $config Optional configuration overrides for this method call {apiKey:string,
     * baseUrl:string, pageSize:int, retries:int, timeout:int, client:HttpClient|null}
     * @return array An array of Tracking information corresponding to the Label Id.
     */
    public function trackUsingCarrierCodeAndTrackingNumber($carrierCode, $trackingNumber, $config = null): array
    {
        $config = $this->config->merge($config);
        $client = new ShipEngineClient();
        $apiResponse = $client->get(
            "v1/tracking?carrier_code=$carrierCode&tracking_number=$trackingNumber",
            $config
        );

        return $apiResponse;
    }
}
