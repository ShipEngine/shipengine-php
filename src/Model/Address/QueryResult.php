<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

/**
 * The result of an \ShipEngine\Service\AddressesService::query() on a \ShipEngine\Model\Address\Query.
 *
 * @property \ShipEngine\Model\Address\Query $query
 * @property ?\ShipEngine\Model\Address\Address $normalized
 */
final class QueryResult
{
    use \ShipEngine\Message\Wrapper;
    use \ShipEngine\Util\Getters;
    
    private Query $query;
    private ?Address $normalized;

    public function __construct(Query $query, ?Address $normalized = null, array $messages = array())
    {
        $this->query = $query;
        $this->normalized = $normalized;
        $this->messages = $messages;
    }
}
