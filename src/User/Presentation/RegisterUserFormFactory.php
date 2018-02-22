<?php declare(strict_types=1);

namespace SocialNews\User\Presentation;

use SocialNews\Framework\Csrf\StoredTokenValidator;
use SocialNews\User\Application\UsernameTakenQuery;
use Symfony\Component\HttpFoundation\Request;

final class RegisterUserFormFactory
{
    private $storedTokenValidator;
    private $usernameTakenQuery;

    public function __construct(
        StoredTokenValidator $storedTokenValidator,
        UsernameTakenQuery $usernameTakenQuery
    )
    {
        $this->storedTokenValidator = $storedTokenValidator;
        $this->usernameTakenQuery = $usernameTakenQuery;
    }

    public function createFromRequest(Request $request): RegisterUserForm
    {
        return new RegisterUserForm(
            $this->storedTokenValidator,
            $this->usernameTakenQuery,
            (string) $request->get('token'),
            (string) $request->get('username'),
            (string) $request->get('password')
        );
    }
}
