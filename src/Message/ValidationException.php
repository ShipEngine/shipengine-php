<?php declare(strict_types=1);

namespace ShipEngine\Message;

/**
 * Class ValidationException - ValidationException - an exception thrown by the ShipEngine SDK when a given value
 * does not meet our requirements to communicate with ShipEngine API properly.
 *
 * Is throwable.
 * @package ShipEngine\Message
 * @param string $message
 * @param string|null $request_id
 * @param string|null $source
 * @param string|null $type
 * @param string|null $error_code
 * @param string|null $url
 *
 * @package ShipEngine\Message
 */
final class ValidationException extends ShipEngineException
{
}
