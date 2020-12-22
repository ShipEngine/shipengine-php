<?php declare(strict_types=1);

namespace ShipEngine\Service;

use ShipEngine\Model\Tracking\Information;
use ShipEngine\Model\Tracking\Query;

/**
 * Provides convenience methods onto \ShipEngine\Service\TrackingService.
 */
trait TrackingTrait
{
    /**
     * @see \ShipEngine\Service\TrackingService::track().
     */
    public function trackShipment(): Information
    {
        if (func_num_args() === 1) {
            return $this->tracking->track(func_get_arg(0));
        } else {
            $query = new Query(...func_get_args());
            return $this->tracking->track($query);
        }
    }
}
