<?php
namespace App\Enums;
class OfferAddressType
{ 
    public const USE_BUSSINESS_ADDRESS = 'use_business_address';
    public const ONLINE = 'online_only';
    public const PHYSICAL_LOCATION = 'physical_location';

    /**
     * Get all possible values for the enum.
     * These are the actual string values stored in the database.
     */
    public static function all(): array
    {
        return [
            self::USE_BUSSINESS_ADDRESS,
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
            self::USE_BUSSINESS_ADDRESS => 'Use Business Address', 
            self::ONLINE => 'Online Only', 
            self::PHYSICAL_LOCATION => 'Physical Location',
        ];
    }

}
