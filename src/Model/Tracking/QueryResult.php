<?php declare(strict_types=1);

namespace ShipEngine\Model\Tracking;

/**
 *
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