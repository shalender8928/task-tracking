<?php

namespace App\Traits;

trait SanitizesNumericInputs
{
	/**
     * Runs before any property is updated
     */
    public function updating($name, $value)
    {
    	// sanitizing properties
        // List of numeric-only properties
        $numericFields = $this->numericFields ?? [];

        if (in_array($name, $numericFields)) {
        	// Strip non-digits and cast to int
            $this->$name = (int) preg_replace('/\D/', '', $value);
        }
    }
}
