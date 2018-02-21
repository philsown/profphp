<?php declare(strict_types=1);

namespace SocialNews\Submission\Presentation;

use SocialNews\Framework\Csrf\StoredTokenValidator;
use SocialNews\Framework\Csrf\Token;
use SocialNews\Submission\Application\SubmitLink;

final class SubmissionForm
{
    private $storedTokenValidator;
    private $token;
    private $title;
    private $url;

    /**
     * SubmissionForm constructor.
     * @param StoredTokenValidator $storedTokenValidator
     * @param string $token
     * @param string $title
     * @param string $url
     */
    public function __construct(StoredTokenValidator $storedTokenValidator, string $token, string $title, string $url)
    {
        $this->storedTokenValidator = $storedTokenValidator;
        $this->token = $token;
        $this->title = $title;
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getValidationErrors(): array
    {
        $errors = [];

        if (!$this->storedTokenValidator->validate(
            'submission',
            new Token($this->token)
        )) {
            $errors[] = 'Invalid token';
        }

        if (1 > strlen($this->title) || 200 < strlen($this->title)) {
            $errors[] = 'Title must be between 1 and 200 character';
        }

        if (1 > strlen($this->url) || 200 < strlen($this->url)) {
            $errors[] = 'URL must be between 1 and 200 character';
        }

        return $errors;
    }

    public function hasValidationErrors(): bool
    {
        return 0 < count($this->getValidationErrors());
    }

    public function toCommand(): SubmitLink
    {
        return new SubmitLink($this->url, $this->title);
    }
}
