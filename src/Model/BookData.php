<?php

namespace App\Model;

class BookData
{

    public function __construct(
        private string $title,
        private string $author,
        private ?string $description = null
    )
    {
        
    }



    /**
     * Get the value of title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param ?string $description
     *
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of author
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Set the value of author
     *
     * @param string $author
     *
     * @return self
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }
}