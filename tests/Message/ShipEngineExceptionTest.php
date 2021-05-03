<?php declare(strict_types=1);

namespace Message;

use PHPUnit\Framework\TestCase;
use ShipEngine\Message\AccountStatusException;
use ShipEngine\Message\BusinessRuleException;
use ShipEngine\Message\RateLimitExceededException;
use ShipEngine\Message\SecurityException;
use ShipEngine\Message\SystemException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Util\Constants\ErrorCode;
use ShipEngine\Util\Constants\ErrorType;

/**
 * @covers \ShipEngine\Message\AccountStatusException
 * @covers \ShipEngine\Message\BusinessRuleException
 * @covers \ShipEngine\Message\SecurityException
 * @covers \ShipEngine\Message\SystemException
 * @covers \ShipEngine\Message\RateLimitExceededException
 * @covers \ShipEngine\Message\ValidationException
 * @covers \ShipEngine\Message\ShipEngineException
 */
final class ShipEngineExceptionTest extends TestCase
{
    public function testAccountStatusException()
    {
        $account_status_exception = new AccountStatusException(
            'There is a hold on your account.',
            'req_989hsfun4w4398',
            'shipengine',
            ErrorType::ACCOUNT_STATUS,
            ErrorCode::UNSPECIFIED
        );

        $this->assertInstanceOf(AccountStatusException::class, $account_status_exception);
    }

    public function testBusinessRuleException(): void
    {
        $business_rule_exception = new BusinessRuleException(
            'There is an issue with the address you provided.',
            'req_989hsfun4w4398',
            'shipengine',
            ErrorType::BUSINESS_RULES,
            ErrorCode::UNSPECIFIED
        );

        $this->assertInstanceOf(BusinessRuleException::class, $business_rule_exception);
    }

    public function testSecurityException(): void
    {
        $security_exception = new SecurityException(
            'Halt! You shall not pass!',
            'req_989hsfun4w4398',
            'shipengine',
            ErrorType::SECURITY,
            ErrorCode::UNSPECIFIED
        );

        $this->assertInstanceOf(SecurityException::class, $security_exception);
    }

    public function testSystemException(): void
    {
        $system_exception = new SystemException(
            'Something went wrong - we could not reach the server.',
            'req_989hsfun4w4398',
            'shipengine',
            ErrorType::SYSTEM,
            ErrorCode::UNSPECIFIED
        );

        $this->assertInstanceOf(SystemException::class, $system_exception);
    }

    public function testValidationException(): void
    {
        $validation_exception = new ValidationException(
            'The provided value has too many characters.',
            'req_989hsfun4w4398',
            'shipengine',
            ErrorType::VALIDATION,
            ErrorCode::INVALID_STRING_LENGTH
        );

        $this->assertInstanceOf(ValidationException::class, $validation_exception);
    }

    public function testRateLimitExceededException(): void
    {
        $rate_limit_exception = new RateLimitExceededException(
            new \DateInterval('PT5S'),
            'shipengine',
            'req_989hsfun4w4398'
        );

        $this->assertInstanceOf(RateLimitExceededException::class, $rate_limit_exception);
    }
}
