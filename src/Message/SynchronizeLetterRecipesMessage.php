<?php

namespace App\Message;

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
