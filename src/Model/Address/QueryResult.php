<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Exception;
use ShipEngine\Util;

/**
 * The result of an \ShipEngine\Service\AddressesService::query() on a \ShipEngine\Model\Address\Query.
 *
 * @property \ShipEngine\Model\Address\Query $query
 * @property ?\ShipEngine\Model\Address\Address $normalized
 */
final class QueryResult extends Exception\Wrapper
{
    use Util\Getters;
    
    private Query $query;
    private ?Address $normalized;

    public function __construct(Query $query, ?Address $normalized = null, array $exceptions = array())
    {
        $this->query = $query;
        $this->normalized = $normalized;

        parent::__construct($exceptions);
    }
}
