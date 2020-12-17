<?php declare(strict_types=1);

namespace ShipEngine\Model\Tracking;

use ShipEngine\Exception;
use ShipEngine\Util;

final class QueryResult extends Exception\Wrapper
{
    use Util\Getters;

    private object $query;
    private Information $information;

    public function __construct(object $query, Information $information, array $exceptions = array())
    {
        $this->query = $query;
        $this->information = $information;

        parent::_construct($exceptions);
    }
}
