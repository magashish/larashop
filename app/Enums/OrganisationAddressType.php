<?php
namespace App\Enums;
class OrganisationAddressType
{
    public const ONLINE = 'online_only';
    public const PHYSICAL_LOCATION = 'physical_location';

    /**
     * Get all possible values for the enum.
     * These are the actual string values stored in the database.
     */
    public static function all(): array
    {
        return [
            self::ONLINE,
            self::PHYSICAL_LOCATION,
        ];
    }

    /**
     * Get user-friendly labels for each enum value.
     */
    public static function labels(): array
    {
        return [
            self::ONLINE => 'Online Only', // Example label
            self::PHYSICAL_LOCATION => 'Physical Location',
        ];
    }

    // You can add other helper methods here if needed,
    // for example, a method to get a random value, etc.
}
