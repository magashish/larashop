<?php

namespace App\Helpers;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;

class PhoneHelper
{
    /**
     * Parse and detect country + formatted E164 number.
     *
     * @param string $rawNumber
     * @param string $defaultRegion (fallback region)
     * @return array
     */
    public static function detectCountry(string $rawNumber, string $defaultRegion = 'IN'): array
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // Parse number with optional default country (IN = India)
            $numberProto = $phoneUtil->parse($rawNumber, $defaultRegion);

            $countryCode = $numberProto->getCountryCode();
            $regionCode  = $phoneUtil->getRegionCodeForNumber($numberProto);
            $isValid     = $phoneUtil->isValidNumber($numberProto);
            $formatted   = $phoneUtil->format($numberProto, \libphonenumber\PhoneNumberFormat::E164);

            return [
                'valid' => $isValid,
                'country' => $regionCode,
                'country_code' => $countryCode,
                'formatted' => $formatted,
            ];
        } catch (NumberParseException $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
