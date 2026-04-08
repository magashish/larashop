<?php

namespace App\Enums;

class YesNo
{
    public const YES = 'yes'; // Changed to string 'yes'
    public const NO = 'no';   // Changed to string 'no'

    /**
     * Get all possible raw values for the enum.
     * These are the actual string values stored in the database.
     */
    public static function all(): array
    {
        return [
            self::NO,  // Order changed for 'no' first, then 'yes'
            self::YES,
        ];
    }

    /**
     * Get user-friendly labels for each enum value.
     */
    public static function labels(): array
    {
        return [
            self::NO => 'No',  // Label for 'no'
            self::YES => 'Yes', // Label for 'yes'
        ];
    }

    /**
     * Get the label for a given value.
     * Updated to accept string $value.
     */
    public static function label(string $value): string
    {
        return self::labels()[$value] ?? 'Unknown';
    }
}
