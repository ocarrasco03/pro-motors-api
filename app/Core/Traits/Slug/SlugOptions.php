<?php

namespace App\Core\Traits\Slug;

/**
 * Class SlugOptions
 *
 * This class provides options for generating slugs for models.
 * It allows customization of slug generation, including fields to generate from,
 * slug field name, uniqueness, length, and more.
 */
class SlugOptions
{
    /** @var array|callable */
    public $generateSlugFrom;

    /** @var callable */
    public $extraScopeCallback;

    /** @var (callable(string, int): string)|null */
    public $suffixGenerator;

    public string $slugField;

    public bool $generateUniqueSlugs = true;

    public int $maximumLength = 250;

    public bool $skipGenerate = false;

    public bool $generateSlugsOnCreate = true;

    public bool $generateSlugsOnUpdate = true;

    public bool $preventOverwrite = false;

    public string $slugSeparator = '-';

    public string $slugLanguage = 'en';

    public array $translatableLocales = [];

    public int $startSlugSuffixFrom = 1;

    public bool $useSuffixOnFirstOccurrence = false;

    /**
     * Create a new instance of SlugOptions.
     *
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * Create a new instance of SlugOptions with specified locales.
     *
     * @param array $locales
     * @return static
     */
    public static function createWithLocales(array $locales): static
    {
        $slugOptions = static::create();

        $slugOptions->translatableLocales = $locales;

        return $slugOptions;
    }

    /**
     * Generate slugs from the specified field(s).
     *
     * @param string|array|callable $fieldName
     * @return $this
     */
    public function generateSlugsFrom(string | array | callable $fieldName): self
    {
        if (is_string($fieldName)) {
            $fieldName = [$fieldName];
        }

        $this->generateSlugFrom = $fieldName;

        return $this;
    }

    /**
     * Save generated slugs to the specified field.
     *
     * @param string $fieldName
     * @return $this
     */
    public function saveSlugsTo(string $fieldName): self
    {
        $this->slugField = $fieldName;

        return $this;
    }

    /**
     * Allow duplicate slugs to be generated.
     *
     * @return $this
     */
    public function allowDuplicateSlugs(): self
    {
        $this->generateUniqueSlugs = false;

        return $this;
    }

    /**
     * Set the maximum length for generated slugs.
     *
     * @param int $maximumLength
     * @return $this
     */
    public function slugsShouldBeNoLongerThan(int $maximumLength): self
    {
        $this->maximumLength = $maximumLength;

        return $this;
    }

    /**
     * Skip slug generation based on a callable condition.
     *
     * @param callable $callable
     * @return $this
     */
    public function skipGenerateWhen(callable $callable): self
    {
        $this->skipGenerate = $callable() === true;

        return $this;
    }

    public function doNotGenerateSlugsOnCreate(): self
    {
        $this->generateSlugsOnCreate = false;

        return $this;
    }

    public function doNotGenerateSlugsOnUpdate(): self
    {
        $this->generateSlugsOnUpdate = false;

        return $this;
    }

    public function preventOverwrite(): self
    {
        $this->preventOverwrite = true;

        return $this;
    }

    public function usingSeparator(string $separator): self
    {
        $this->slugSeparator = $separator;

        return $this;
    }

    public function usingLanguage(string $language): self
    {
        $this->slugLanguage = $language;

        return $this;
    }

    public function extraScope(callable $callbackMethod): self
    {
        $this->extraScopeCallback = $callbackMethod;

        return $this;
    }

    public function startSlugSuffixFrom(int $startSlugSuffixFrom): self
    {
        $this->startSlugSuffixFrom = max(1, $startSlugSuffixFrom);

        return $this;
    }

    public function useSuffixOnFirstOccurrence(): self
    {
        $this->useSuffixOnFirstOccurrence = true;

        return $this;
    }


    /**
     * @param callable(string $slug, int $iteration): string $generator
     */
    public function usingSuffixGenerator(callable $generator): self
    {
        $this->suffixGenerator = $generator;

        return $this;
    }
}
