<?php

declare(strict_types=1);

namespace Astaroth\Attribute\Event;

use Astaroth\Contracts\AttributeValidatorInterface;
use Astaroth\DataFetcher\DataFetcher;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
/**
 * AttributeOLD defining new message
 */
class MessageNew implements AttributeValidatorInterface
{
    private string $type;

    public function validate(): bool
    {
        return $this->type === "message_new";
    }

    /**
     * @param DataFetcher $haystack
     * @return $this
     */
    public function setHaystack($haystack): static
    {
        $this->type = $haystack->getType();
        return $this;
    }
}