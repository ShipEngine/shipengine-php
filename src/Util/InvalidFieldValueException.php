<?php declare(strict_types=1);

namespace ShipEngine\Util;

use ShipEngine\Util;
use ShipEngine\Message\ShipEngineException;

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
     * @param $field_value
     * @param string|null $request_id
     * @param string|null $source
     * @param string|null $type
     * @param string|null $error_code
     */
    public function __construct(
        string $field_name,
        string $reason,
        $field_value,
        ?string $request_id = null,
        ?string $source = null,
        ?string $type = null,
        ?string $error_code = null
    ) {
        parent::__construct(
            "{$field_name} - {$reason}",
            $request_id,
            $source,
            isset($type) ? $type : ErrorType::VALIDATION,
            isset($error_code) ? $error_code : ErrorCode::FIELD_VALUE_REQUIRED
        );
        $this->field_name = $field_name;
        $this->field_value = $field_value;
    }
}
