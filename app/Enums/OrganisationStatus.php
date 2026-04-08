<?php
namespace App\Enums;
class OrganisationStatus
{
    public const NEW = 'new';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';

    /**
     * Get all possible raw values for the enum.
     * These are the actual string values stored in the database.
     */
    public static function all(): array
    {
        return [
            self::NEW,
            self::APPROVED,
            self::REJECTED,
        ];
    }

    /**
     * Get user-friendly labels for each enum value.
     */
    public static function labels(): array
    {
        return [
            self::NEW => 'New (Pending Approval)',
            self::APPROVED => 'Approved / Publish',
            self::REJECTED => 'Rejected',
        ];
    }

    // You can add other helper methods here if needed,
    // for example, a method to check if a status is 'approved'.
    // public static function isApproved(string $status): bool
    // {
    //     return $status === self::APPROVED;
    // }
}
