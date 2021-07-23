<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

final class AddressMessage implements \JsonSerializable
{
    public string $code;

    public string $message;

    public string $type;

    public ?string $detail_code;

    public function __construct(array $address_message)
    {
        $this->code = $address_message['code'];
        $this->message = $address_message['message'];
        $this->type = $address_message['type'];
        $this->detail_code = $address_message['detail_code'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }
}
