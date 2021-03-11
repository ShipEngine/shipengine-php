<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

use ShipEngine\Model\Tag\Information;

final class PackageTrackingResult
{
    private Information $information;

    private array $messages;

    public function __construct(
        Information $information,
        array $messages
    ) {
        $this->information = $information;
        $this->messages = $messages;
    }

    public function jsonSerialize(): string
    {
        return json_encode([
           'information' => $this->information,
           'messages' => $this->messages
        ]);
    }
}
