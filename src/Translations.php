<?php

declare(strict_types=1);

namespace Krasnikov\DDDCore;

/**
 * Class Translations
 * @package App\Domain\Core
 */
trait Translations
{
    /**
     * @var array
     */
    private array $translates;

    /**
     * RoleDescription constructor.
     * @param array $translates
     */
    public function __construct(array $translates)
    {
        $this->translates = $translates;
    }

    /**
     * @param string $translates
     * @return static
     */
    public static function fromString(string $translates): self
    {
        return new self(json_decode($translates, true));
    }

    /**
     * @return string|null
     */
    public function value(): ?string
    {
        return $this->translates[app()->getLocale()] ?? null;
    }

    /**
     * @return array
     */
    public function translations(): array
    {
        return $this->translates;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return json_encode($this->translates);
    }
}
