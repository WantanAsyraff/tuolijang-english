<?php

declare(strict_types=1);

namespace App\Http\Requests\Traits;

use crmeb\utils\Regex;

/**
 * Normalizes display separators before request validation without converting
 * local numbers to a different stored representation.
 */
trait NormalizesPhoneInput
{
    /**
     * @param string[] $fields
     */
    protected function normalizePhoneInputs(array $fields): void
    {
        $normalized = [];
        foreach ($fields as $field) {
            $value = request()->input($field);
            if (is_scalar($value) || $value instanceof \Stringable) {
                $normalized[$field] = Regex::normalizePhone((string) $value);
            }
        }

        if ($normalized) {
            request()->merge($normalized);
        }
    }
}