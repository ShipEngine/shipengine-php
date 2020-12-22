<?php declare(strict_types=1);

namespace ShipEngine\Model\Tracking;

/**
 * The result of a \ShipEngine\Service\TrackingService::query() on a \ShipEngine\Model\Tracking\Query.
 *
 * @property mixed $query
 * @property \ShipEngine\Model\Tracking\Information $information
 */
final class QueryResult
{
    use \ShipEngine\Message\Wrapper;
    use \ShipEngine\Util\Getters;

    private $query;
    private ?Information $information;

    public function __construct($query, ?Information $information, array $messages = array())
    {
        $this->query = $query;
        $this->information = $information;
        $this->messages = $messages;
    }
}
