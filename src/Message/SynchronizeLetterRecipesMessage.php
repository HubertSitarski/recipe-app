<?php

namespace App\Message;

/**
 * Message to trigger synchronization of recipes for a specific letter
 */
final class SynchronizeLetterRecipesMessage
{
    public function __construct(
        public readonly string $letter
    ) {
        if (strlen($letter) !== 1 || !ctype_alpha($letter)) {
            throw new \InvalidArgumentException('Letter must be a single alphabetic character');
        }
    }
}
