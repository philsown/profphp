<?php declare(strict_types=1);

namespace SocialNews\Submission\Domain;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

final class Submission
{
	private $id;
	private $url;
	private $title;
	private $createdAt;

	private function __construct(
		UuidInterface $id,
		string $url,
		string $title,
		DateTimeImmutable $createdAt
	)
	{
		$this->id = $id;
		$this->url = $url;
		$this->title = $title;
		$this->createdAt = $createdAt;
	}

	public function getId(): UuidInterface
	{
		return $this->id;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getCreatedAt(): DateTimeImmutable
	{
		return $this->createdAt;
	}

	public static function submit(string $url, string $title): Submission
	{
		return new Submission(
			Uuid::uuid4(),
			$url,
			$title,
			new DateTimeImmutable()
		);
	}
}
