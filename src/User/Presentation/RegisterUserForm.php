<?php declare(strict_types=1);

namespace SocialNews\User\Presentation;

use SocialNews\Framework\Csrf\StoredTokenValidator;
use SocialNews\User\Application\UsernameTakenQuery;
use SocialNews\User\Application\RegisterUser;
use SocialNews\Framework\Csrf\Token;

final class RegisterUserForm
{
    private $storedTokenValidator;
    private $usernameTakenQuery;
    private $token;
    private $username;
    private $password;

    public function __construct(
        StoredTokenValidator $storedTokenValidator,
        UsernameTakenQuery $usernameTakenQuery,
        string $token,
        string $username,
        string $password
    )
    {
        $this->storedTokenValidator = $storedTokenValidator;
        $this->usernameTakenQuery = $usernameTakenQuery;
        $this->token = $token;
        $this->username = $username;
        $this->password = $password;
    }

    public function hasValidationErrors(): bool
    {
        return 0 < count($this->getValidationErrors());
    }

    /**
     * @return string[]
     */
    public function getValidationErrors(): array
    {
        $errors = [];

        if (!$this->storedTokenValidator->validate(
            'registration',
            new Token($this->token)
        )) {
            $errors[] = 'Invalid token';
        }

        if (3 > strlen($this->username) || 20 < strlen($this->username)) {
            $errors[] = 'Username must be between 3 and 20 characters';
        }

        if (!ctype_alnum($this->username)) {
            $errors[] = 'Username can only consist of letters and numbers';
        }

        if (8 > strlen($this->password)) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if ($this->usernameTakenQuery->execute($this->username)) {
            $errors[] = 'This username is already being used';
        }

        return $errors;
    }

    public function toCommand(): RegisterUser
    {
        return new RegisterUser($this->username, $this->password);
    }
}
