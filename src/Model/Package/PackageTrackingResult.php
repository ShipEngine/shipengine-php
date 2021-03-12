<?php declare(strict_types=1);

namespace ShipEngine\Model\Package;

final class PackageTrackingResult
{
    private array $information;

    private array $messages;

    public function __construct(
        array $information,
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
