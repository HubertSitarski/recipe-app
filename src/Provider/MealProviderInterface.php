<?php

namespace App\Provider;

interface MealProviderInterface
{
    public function fetchByLetter(string $letter): array;

    public function fetchById(string $id): array;

    public function getSource(): string;
}
