<?php

declare(strict_types=1);


namespace Astaroth\Attribute;

use Astaroth\Contracts\AttributeValidatorInterface;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
/**
 * Attribute that determines the click on the button (payload)
 */
class Payload implements AttributeValidatorInterface
{

    public const KEY_EXISTS = 0;
    public const STRICT = 1;
    public const CONTAINS = 2;

    private mixed $haystack;

    public function __construct(private array|string $payload_or_key, private int $validation = Payload::STRICT)
    {
    }

    /**
     * @throws NotImplementedHaystackException
     */
    public function validate(): bool
    {
        isset($this->haystack) ?: throw new NotImplementedHaystackException("No haystack specified for " . __CLASS__ . " Attribute");

        $casted_haystack = is_string($this->haystack) ? $this->haystack : "";
        $casted_payload = @json_decode($casted_haystack, true);
        if ($casted_payload) {
            return match ($this->validation) {
                static::STRICT => $this->payload_or_key === $casted_payload,
                static::KEY_EXISTS => array_key_exists($this->payload_or_key, $casted_payload),
                static::CONTAINS => count(array_intersect($this->payload_or_key, $casted_payload)) > 0,
            };
        }

        return false;
    }

    public function setHaystack($haystack): static
    {
        $this->haystack = $haystack;
        return $this;
    }
}