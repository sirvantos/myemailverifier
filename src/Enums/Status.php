<?php

namespace Sirvantos\MyEmailVerifier\Enums;

use Illuminate\Support\Str;

final class Status
{
    private const VALID_TYPE = 'valid';
    private const INVALID_TYPE = 'invalid';
    private const UNKNOWN_TYPE = 'unknown';

    private static array $statuses = [self::VALID_TYPE, self::INVALID_TYPE, self::UNKNOWN_TYPE];

    private string $status;

    public static function make(string $status): self
    {
        if (array_search(Str::lower($status), self::$statuses) === false) {
            throw new \LogicException('Unknown MyVerifier status >> ' . $status);
        }

        return new self(Str::lower($status));
    }

    public function __construct(string $status)
    {
        $this->status = $status;
    }

    public function isValid(): bool
    {
        return $this->status === self::VALID_TYPE;
    }

    public function isInvalid(): bool
    {
        return $this->status === self::INVALID_TYPE;
    }

    public function isUnknown(): bool
    {
        return $this->status === self::UNKNOWN_TYPE;
    }
}
