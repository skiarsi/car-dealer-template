<?php

namespace App\Support;

trait LocalizesAttributes
{
    public function localized(string $attribute): string
    {
        $locale = app()->getLocale();

        if ($locale !== 'en') {
            $localized = $attribute.'_'.$locale;
            $value = $this->getAttribute($localized);

            if (filled($value)) {
                return (string) $value;
            }
        }

        return (string) ($this->getAttribute($attribute) ?? '');
    }
}
