<?php

namespace App\Dto;

final class CreatePostDto
{
    /** @var string $title */
    private $title;

    /** @var string $text */
    private $text;

    /** @var int $authorId */
    private $authorId;

    public function __construct(string $title, string $text, int $authorId)
    {
        $this->title    = $title;
        $this->text     = $text;
        $this->authorId = $authorId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }
}