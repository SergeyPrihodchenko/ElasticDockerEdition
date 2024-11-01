<?php

declare(strict_types=1);

namespace AvySearch\DTO\ExternalEntities\Attributes;

final class Series implements AttributeInterface
{
    // название категории
    private string $title;

    // надпись под названием
    private string $subtitle;

    /**
     * @var $characteristics array<int, array{
     *  title string,
     *  subtitle string,
     * }>
     */
    private array $characteristics;

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @param $characteristics array<int, array{
     *  title string,
     *  subtitle string,
     * }>
     */
    public function setCharacteristics(array $characteristics): self
    {
        $this->characteristics = $characteristics;

        return $this;
    }

    /**
     * @return array<int, array{title string, subtitle string}>
     */
    public function getCharacteristscs(): array
    {
        return $this->characteristics;
    }

    public function getType(): string
    {
        return 'series';
    }

    public function getValue(): string
    {
        return $this->getTitle();
    }

    public function __serialize(): array
    {
        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'characteristics' => $this->characteristics,
        ];
    }
}
