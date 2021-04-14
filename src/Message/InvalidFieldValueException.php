<?php declare(strict_types=1);

namespace ShipEngine\Util;

use ShipEngine\Message\ShipEngineException;
use ShipEngine\Util;
use ShipEngine\Util\Constants\ErrorCode;
use ShipEngine\Util\Constants\ErrorType;

/**
 * This error occurs when a field has been set to an invalid value.
 */
final class InvalidFieldValueException extends ShipEngineException
{
    use Util\Getters;

    /**
     * The name of the invalid field.
     */
    private string $field_name;

    /**
     * The value of the invalid field.
     */
    private string $field_value;

    /**
     * Instantiates a client-side error.
     *
     * @param string $field_name
     * @param string $reason
     * @param mixed $field_value
     */
    public function __construct(
        string $field_name,
        string $reason,
        $field_value
    ) {
        parent::__construct(
            "{$field_name} - {$reason}",
            null,
            null,
            ErrorType::VALIDATION,
            ErrorCode::FIELD_VALUE_REQUIRED,
            null
        );
        $this->field_name = $field_name;
        $this->field_value = $field_value;
    }
}
