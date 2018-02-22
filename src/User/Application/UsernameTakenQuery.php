<?php declare(strict_types=1);

namespace SocialNews\User\Application;

interface UsernameTakenQuery
{
    public function execute(string $username): bool;
}
