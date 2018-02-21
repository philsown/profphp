<?php declare(strict_types=1);

namespace SocialNews\FrontPage\Infrastructure;

use SocialNews\FrontPage\Application\Submission;
use SocialNews\FrontPage\Application\SubmissionsQuery;

final class MockSubmissionsQuery implements SubmissionsQuery
{
	private $submissions;

	public function __construct()
	{
		$this->submissions = [
			new Submission('https://duckduckgo.com', 'DuckDuckGo'),
			new Submission('https://google.com', 'Google'),
			new Submission('https://bing.com', 'Bing'),
		];
	}

	public function execute(): array
	{
		return $this->submissions;
	}
}
