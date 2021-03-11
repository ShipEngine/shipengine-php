<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Message\MessageWrapper;
use ShipEngine\Util;

/**
 * AddressValidateResult Type to be returned by *AddressService*.
 *
 * @package ShipEngine\Model\Address
 */
final class AddressValidateResult
{
    use MessageWrapper;
    use Util\Getters;

    /**
     * @var bool
     */
    private ?bool $valid;

    /**
     * @var array
     */
    private array $messages;

    /**
     * @var Address|null
     */
    private ?Address $address;

    /**
     * AddressValidateResult Type constructor.
     *
     * @param bool $valid
     * @param Address|null $address
     * @param array $messages
     */
    public function __construct(
        bool $valid,
        array $messages,
        ?Address $address
    ) {
        $this->valid = $valid;
        $this->address = $address;
        $this->messages = $messages;
    }

    // TODO: add Docstring with json example.
    public function jsonSerialize(): string
    {
        return json_encode([
            'valid' => $this->valid,
            'address' => $this->address,
            'messages' => $this->messages
        ]);
    }
}
