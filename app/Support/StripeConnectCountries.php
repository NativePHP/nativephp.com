<?php

namespace App\Support;

class StripeConnectCountries
{
    /**
     * Stripe Connect supported countries for Express accounts.
     *
     * @var array<string, array{name: string, flag: string, default_currency: string, currencies: list<string>}>
     */
    public const SUPPORTED_COUNTRIES = [
        'AE' => ['name' => 'United Arab Emirates', 'flag' => "\u{1F1E6}\u{1F1EA}", 'default_currency' => 'AED', 'currencies' => ['AED']],
        'AG' => ['name' => 'Antigua & Barbuda', 'flag' => "\u{1F1E6}\u{1F1EC}", 'default_currency' => 'XCD', 'currencies' => ['XCD']],
        'AL' => ['name' => 'Albania', 'flag' => "\u{1F1E6}\u{1F1F1}", 'default_currency' => 'ALL', 'currencies' => ['ALL']],
        'AM' => ['name' => 'Armenia', 'flag' => "\u{1F1E6}\u{1F1F2}", 'default_currency' => 'AMD', 'currencies' => ['AMD']],
        'AR' => ['name' => 'Argentina', 'flag' => "\u{1F1E6}\u{1F1F7}", 'default_currency' => 'ARS', 'currencies' => ['ARS']],
        'AT' => ['name' => 'Austria', 'flag' => "\u{1F1E6}\u{1F1F9}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'AU' => ['name' => 'Australia', 'flag' => "\u{1F1E6}\u{1F1FA}", 'default_currency' => 'AUD', 'currencies' => ['AUD', 'USD']],
        'BA' => ['name' => 'Bosnia & Herzegovina', 'flag' => "\u{1F1E7}\u{1F1E6}", 'default_currency' => 'BAM', 'currencies' => ['BAM']],
        'BE' => ['name' => 'Belgium', 'flag' => "\u{1F1E7}\u{1F1EA}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'BG' => ['name' => 'Bulgaria', 'flag' => "\u{1F1E7}\u{1F1EC}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'BH' => ['name' => 'Bahrain', 'flag' => "\u{1F1E7}\u{1F1ED}", 'default_currency' => 'BHD', 'currencies' => ['BHD']],
        'BJ' => ['name' => 'Benin', 'flag' => "\u{1F1E7}\u{1F1EF}", 'default_currency' => 'XOF', 'currencies' => ['XOF']],
        'BN' => ['name' => 'Brunei', 'flag' => "\u{1F1E7}\u{1F1F3}", 'default_currency' => 'BND', 'currencies' => ['BND']],
        'BO' => ['name' => 'Bolivia', 'flag' => "\u{1F1E7}\u{1F1F4}", 'default_currency' => 'BOB', 'currencies' => ['BOB']],
        'BS' => ['name' => 'Bahamas', 'flag' => "\u{1F1E7}\u{1F1F8}", 'default_currency' => 'BSD', 'currencies' => ['BSD']],
        'BW' => ['name' => 'Botswana', 'flag' => "\u{1F1E7}\u{1F1FC}", 'default_currency' => 'BWP', 'currencies' => ['BWP']],
        'CA' => ['name' => 'Canada', 'flag' => "\u{1F1E8}\u{1F1E6}", 'default_currency' => 'CAD', 'currencies' => ['CAD', 'USD']],
        'CH' => ['name' => 'Switzerland', 'flag' => "\u{1F1E8}\u{1F1ED}", 'default_currency' => 'CHF', 'currencies' => ['CHF', 'EUR', 'GBP', 'USD', 'DKK', 'NOK', 'SEK']],
        'CI' => ['name' => "C\u{00F4}te d'Ivoire", 'flag' => "\u{1F1E8}\u{1F1EE}", 'default_currency' => 'XOF', 'currencies' => ['XOF']],
        'CL' => ['name' => 'Chile', 'flag' => "\u{1F1E8}\u{1F1F1}", 'default_currency' => 'CLP', 'currencies' => ['CLP']],
        'CO' => ['name' => 'Colombia', 'flag' => "\u{1F1E8}\u{1F1F4}", 'default_currency' => 'COP', 'currencies' => ['COP']],
        'CR' => ['name' => 'Costa Rica', 'flag' => "\u{1F1E8}\u{1F1F7}", 'default_currency' => 'CRC', 'currencies' => ['CRC']],
        'CY' => ['name' => 'Cyprus', 'flag' => "\u{1F1E8}\u{1F1FE}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'CZ' => ['name' => 'Czech Republic', 'flag' => "\u{1F1E8}\u{1F1FF}", 'default_currency' => 'CZK', 'currencies' => ['CZK', 'EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'DE' => ['name' => 'Germany', 'flag' => "\u{1F1E9}\u{1F1EA}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'DK' => ['name' => 'Denmark', 'flag' => "\u{1F1E9}\u{1F1F0}", 'default_currency' => 'DKK', 'currencies' => ['DKK', 'EUR', 'GBP', 'USD', 'CHF', 'NOK', 'SEK']],
        'DO' => ['name' => 'Dominican Republic', 'flag' => "\u{1F1E9}\u{1F1F4}", 'default_currency' => 'DOP', 'currencies' => ['DOP']],
        'EC' => ['name' => 'Ecuador', 'flag' => "\u{1F1EA}\u{1F1E8}", 'default_currency' => 'USD', 'currencies' => ['USD']],
        'EE' => ['name' => 'Estonia', 'flag' => "\u{1F1EA}\u{1F1EA}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'EG' => ['name' => 'Egypt', 'flag' => "\u{1F1EA}\u{1F1EC}", 'default_currency' => 'EGP', 'currencies' => ['EGP']],
        'ES' => ['name' => 'Spain', 'flag' => "\u{1F1EA}\u{1F1F8}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'ET' => ['name' => 'Ethiopia', 'flag' => "\u{1F1EA}\u{1F1F9}", 'default_currency' => 'ETB', 'currencies' => ['ETB']],
        'FI' => ['name' => 'Finland', 'flag' => "\u{1F1EB}\u{1F1EE}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'FR' => ['name' => 'France', 'flag' => "\u{1F1EB}\u{1F1F7}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'GB' => ['name' => 'United Kingdom', 'flag' => "\u{1F1EC}\u{1F1E7}", 'default_currency' => 'GBP', 'currencies' => ['GBP', 'EUR', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'GH' => ['name' => 'Ghana', 'flag' => "\u{1F1EC}\u{1F1ED}", 'default_currency' => 'GHS', 'currencies' => ['GHS']],
        'GM' => ['name' => 'Gambia', 'flag' => "\u{1F1EC}\u{1F1F2}", 'default_currency' => 'GMD', 'currencies' => ['GMD']],
        'GR' => ['name' => 'Greece', 'flag' => "\u{1F1EC}\u{1F1F7}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'GT' => ['name' => 'Guatemala', 'flag' => "\u{1F1EC}\u{1F1F9}", 'default_currency' => 'GTQ', 'currencies' => ['GTQ']],
        'GY' => ['name' => 'Guyana', 'flag' => "\u{1F1EC}\u{1F1FE}", 'default_currency' => 'GYD', 'currencies' => ['GYD']],
        'HK' => ['name' => 'Hong Kong', 'flag' => "\u{1F1ED}\u{1F1F0}", 'default_currency' => 'HKD', 'currencies' => ['HKD', 'USD']],
        'HR' => ['name' => 'Croatia', 'flag' => "\u{1F1ED}\u{1F1F7}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'HU' => ['name' => 'Hungary', 'flag' => "\u{1F1ED}\u{1F1FA}", 'default_currency' => 'HUF', 'currencies' => ['HUF', 'EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'ID' => ['name' => 'Indonesia', 'flag' => "\u{1F1EE}\u{1F1E9}", 'default_currency' => 'IDR', 'currencies' => ['IDR']],
        'IE' => ['name' => 'Ireland', 'flag' => "\u{1F1EE}\u{1F1EA}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'IL' => ['name' => 'Israel', 'flag' => "\u{1F1EE}\u{1F1F1}", 'default_currency' => 'ILS', 'currencies' => ['ILS']],
        'IN' => ['name' => 'India', 'flag' => "\u{1F1EE}\u{1F1F3}", 'default_currency' => 'INR', 'currencies' => ['INR']],
        'IS' => ['name' => 'Iceland', 'flag' => "\u{1F1EE}\u{1F1F8}", 'default_currency' => 'ISK', 'currencies' => ['ISK', 'EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'IT' => ['name' => 'Italy', 'flag' => "\u{1F1EE}\u{1F1F9}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'JM' => ['name' => 'Jamaica', 'flag' => "\u{1F1EF}\u{1F1F2}", 'default_currency' => 'JMD', 'currencies' => ['JMD']],
        'JO' => ['name' => 'Jordan', 'flag' => "\u{1F1EF}\u{1F1F4}", 'default_currency' => 'JOD', 'currencies' => ['JOD']],
        'JP' => ['name' => 'Japan', 'flag' => "\u{1F1EF}\u{1F1F5}", 'default_currency' => 'JPY', 'currencies' => ['JPY']],
        'KE' => ['name' => 'Kenya', 'flag' => "\u{1F1F0}\u{1F1EA}", 'default_currency' => 'KES', 'currencies' => ['KES']],
        'KH' => ['name' => 'Cambodia', 'flag' => "\u{1F1F0}\u{1F1ED}", 'default_currency' => 'USD', 'currencies' => ['USD']],
        'KR' => ['name' => 'South Korea', 'flag' => "\u{1F1F0}\u{1F1F7}", 'default_currency' => 'KRW', 'currencies' => ['KRW']],
        'KW' => ['name' => 'Kuwait', 'flag' => "\u{1F1F0}\u{1F1FC}", 'default_currency' => 'KWD', 'currencies' => ['KWD']],
        'LC' => ['name' => 'St. Lucia', 'flag' => "\u{1F1F1}\u{1F1E8}", 'default_currency' => 'XCD', 'currencies' => ['XCD']],
        'LI' => ['name' => 'Liechtenstein', 'flag' => "\u{1F1F1}\u{1F1EE}", 'default_currency' => 'CHF', 'currencies' => ['CHF', 'EUR', 'GBP', 'USD', 'DKK', 'NOK', 'SEK']],
        'LK' => ['name' => 'Sri Lanka', 'flag' => "\u{1F1F1}\u{1F1F0}", 'default_currency' => 'LKR', 'currencies' => ['LKR']],
        'LT' => ['name' => 'Lithuania', 'flag' => "\u{1F1F1}\u{1F1F9}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'LU' => ['name' => 'Luxembourg', 'flag' => "\u{1F1F1}\u{1F1FA}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'LV' => ['name' => 'Latvia', 'flag' => "\u{1F1F1}\u{1F1FB}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'MA' => ['name' => 'Morocco', 'flag' => "\u{1F1F2}\u{1F1E6}", 'default_currency' => 'MAD', 'currencies' => ['MAD']],
        'MC' => ['name' => 'Monaco', 'flag' => "\u{1F1F2}\u{1F1E8}", 'default_currency' => 'EUR', 'currencies' => ['EUR']],
        'MD' => ['name' => 'Moldova', 'flag' => "\u{1F1F2}\u{1F1E9}", 'default_currency' => 'MDL', 'currencies' => ['MDL']],
        'MG' => ['name' => 'Madagascar', 'flag' => "\u{1F1F2}\u{1F1EC}", 'default_currency' => 'MGA', 'currencies' => ['MGA']],
        'MK' => ['name' => 'North Macedonia', 'flag' => "\u{1F1F2}\u{1F1F0}", 'default_currency' => 'MKD', 'currencies' => ['MKD']],
        'MN' => ['name' => 'Mongolia', 'flag' => "\u{1F1F2}\u{1F1F3}", 'default_currency' => 'MNT', 'currencies' => ['MNT']],
        'MO' => ['name' => 'Macao', 'flag' => "\u{1F1F2}\u{1F1F4}", 'default_currency' => 'MOP', 'currencies' => ['MOP']],
        'MT' => ['name' => 'Malta', 'flag' => "\u{1F1F2}\u{1F1F9}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'MU' => ['name' => 'Mauritius', 'flag' => "\u{1F1F2}\u{1F1FA}", 'default_currency' => 'MUR', 'currencies' => ['MUR']],
        'MX' => ['name' => 'Mexico', 'flag' => "\u{1F1F2}\u{1F1FD}", 'default_currency' => 'MXN', 'currencies' => ['MXN']],
        'MY' => ['name' => 'Malaysia', 'flag' => "\u{1F1F2}\u{1F1FE}", 'default_currency' => 'MYR', 'currencies' => ['MYR']],
        'NA' => ['name' => 'Namibia', 'flag' => "\u{1F1F3}\u{1F1E6}", 'default_currency' => 'NAD', 'currencies' => ['NAD']],
        'NG' => ['name' => 'Nigeria', 'flag' => "\u{1F1F3}\u{1F1EC}", 'default_currency' => 'NGN', 'currencies' => ['NGN']],
        'NL' => ['name' => 'Netherlands', 'flag' => "\u{1F1F3}\u{1F1F1}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'NO' => ['name' => 'Norway', 'flag' => "\u{1F1F3}\u{1F1F4}", 'default_currency' => 'NOK', 'currencies' => ['NOK', 'EUR', 'GBP', 'USD', 'CHF', 'DKK', 'SEK']],
        'NZ' => ['name' => 'New Zealand', 'flag' => "\u{1F1F3}\u{1F1FF}", 'default_currency' => 'NZD', 'currencies' => ['NZD']],
        'OM' => ['name' => 'Oman', 'flag' => "\u{1F1F4}\u{1F1F2}", 'default_currency' => 'OMR', 'currencies' => ['OMR']],
        'PA' => ['name' => 'Panama', 'flag' => "\u{1F1F5}\u{1F1E6}", 'default_currency' => 'USD', 'currencies' => ['USD']],
        'PE' => ['name' => 'Peru', 'flag' => "\u{1F1F5}\u{1F1EA}", 'default_currency' => 'PEN', 'currencies' => ['PEN']],
        'PH' => ['name' => 'Philippines', 'flag' => "\u{1F1F5}\u{1F1ED}", 'default_currency' => 'PHP', 'currencies' => ['PHP']],
        'PK' => ['name' => 'Pakistan', 'flag' => "\u{1F1F5}\u{1F1F0}", 'default_currency' => 'PKR', 'currencies' => ['PKR']],
        'PL' => ['name' => 'Poland', 'flag' => "\u{1F1F5}\u{1F1F1}", 'default_currency' => 'PLN', 'currencies' => ['PLN', 'EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'PT' => ['name' => 'Portugal', 'flag' => "\u{1F1F5}\u{1F1F9}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'PY' => ['name' => 'Paraguay', 'flag' => "\u{1F1F5}\u{1F1FE}", 'default_currency' => 'PYG', 'currencies' => ['PYG']],
        'QA' => ['name' => 'Qatar', 'flag' => "\u{1F1F6}\u{1F1E6}", 'default_currency' => 'QAR', 'currencies' => ['QAR']],
        'RO' => ['name' => 'Romania', 'flag' => "\u{1F1F7}\u{1F1F4}", 'default_currency' => 'RON', 'currencies' => ['RON', 'EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'RS' => ['name' => 'Serbia', 'flag' => "\u{1F1F7}\u{1F1F8}", 'default_currency' => 'RSD', 'currencies' => ['RSD']],
        'RW' => ['name' => 'Rwanda', 'flag' => "\u{1F1F7}\u{1F1FC}", 'default_currency' => 'RWF', 'currencies' => ['RWF']],
        'SA' => ['name' => 'Saudi Arabia', 'flag' => "\u{1F1F8}\u{1F1E6}", 'default_currency' => 'SAR', 'currencies' => ['SAR']],
        'SE' => ['name' => 'Sweden', 'flag' => "\u{1F1F8}\u{1F1EA}", 'default_currency' => 'SEK', 'currencies' => ['SEK', 'EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK']],
        'SG' => ['name' => 'Singapore', 'flag' => "\u{1F1F8}\u{1F1EC}", 'default_currency' => 'SGD', 'currencies' => ['SGD', 'USD']],
        'SI' => ['name' => 'Slovenia', 'flag' => "\u{1F1F8}\u{1F1EE}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'SK' => ['name' => 'Slovakia', 'flag' => "\u{1F1F8}\u{1F1F0}", 'default_currency' => 'EUR', 'currencies' => ['EUR', 'GBP', 'USD', 'CHF', 'DKK', 'NOK', 'SEK']],
        'SN' => ['name' => 'Senegal', 'flag' => "\u{1F1F8}\u{1F1F3}", 'default_currency' => 'XOF', 'currencies' => ['XOF']],
        'SV' => ['name' => 'El Salvador', 'flag' => "\u{1F1F8}\u{1F1FB}", 'default_currency' => 'USD', 'currencies' => ['USD']],
        'TH' => ['name' => 'Thailand', 'flag' => "\u{1F1F9}\u{1F1ED}", 'default_currency' => 'THB', 'currencies' => ['THB']],
        'TN' => ['name' => 'Tunisia', 'flag' => "\u{1F1F9}\u{1F1F3}", 'default_currency' => 'TND', 'currencies' => ['TND']],
        'TR' => ['name' => "T\u{00FC}rkiye", 'flag' => "\u{1F1F9}\u{1F1F7}", 'default_currency' => 'TRY', 'currencies' => ['TRY']],
        'TT' => ['name' => 'Trinidad & Tobago', 'flag' => "\u{1F1F9}\u{1F1F9}", 'default_currency' => 'TTD', 'currencies' => ['TTD']],
        'TW' => ['name' => 'Taiwan', 'flag' => "\u{1F1F9}\u{1F1FC}", 'default_currency' => 'TWD', 'currencies' => ['TWD']],
        'TZ' => ['name' => 'Tanzania', 'flag' => "\u{1F1F9}\u{1F1FF}", 'default_currency' => 'TZS', 'currencies' => ['TZS']],
        'US' => ['name' => 'United States', 'flag' => "\u{1F1FA}\u{1F1F8}", 'default_currency' => 'USD', 'currencies' => ['USD']],
        'UY' => ['name' => 'Uruguay', 'flag' => "\u{1F1FA}\u{1F1FE}", 'default_currency' => 'UYU', 'currencies' => ['UYU']],
        'UZ' => ['name' => 'Uzbekistan', 'flag' => "\u{1F1FA}\u{1F1FF}", 'default_currency' => 'UZS', 'currencies' => ['UZS']],
        'VN' => ['name' => 'Vietnam', 'flag' => "\u{1F1FB}\u{1F1F3}", 'default_currency' => 'VND', 'currencies' => ['VND']],
        'ZA' => ['name' => 'South Africa', 'flag' => "\u{1F1FF}\u{1F1E6}", 'default_currency' => 'ZAR', 'currencies' => ['ZAR']],
    ];

    /**
     * @var array<string, string>
     */
    public const CURRENCY_NAMES = [
        'AED' => 'UAE Dirham',
        'ALL' => 'Albanian Lek',
        'AMD' => 'Armenian Dram',
        'ARS' => 'Argentine Peso',
        'AUD' => 'Australian Dollar',
        'BAM' => 'Convertible Mark',
        'BHD' => 'Bahraini Dinar',
        'BND' => 'Brunei Dollar',
        'BOB' => 'Boliviano',
        'BSD' => 'Bahamian Dollar',
        'BWP' => 'Botswana Pula',
        'CAD' => 'Canadian Dollar',
        'CHF' => 'Swiss Franc',
        'CLP' => 'Chilean Peso',
        'COP' => 'Colombian Peso',
        'CRC' => 'Costa Rican Colon',
        'CZK' => 'Czech Koruna',
        'DKK' => 'Danish Krone',
        'DOP' => 'Dominican Peso',
        'EGP' => 'Egyptian Pound',
        'ETB' => 'Ethiopian Birr',
        'EUR' => 'Euro',
        'GBP' => 'British Pound',
        'GHS' => 'Ghanaian Cedi',
        'GMD' => 'Gambian Dalasi',
        'GTQ' => 'Guatemalan Quetzal',
        'GYD' => 'Guyanese Dollar',
        'HKD' => 'Hong Kong Dollar',
        'HUF' => 'Hungarian Forint',
        'IDR' => 'Indonesian Rupiah',
        'ILS' => 'Israeli Shekel',
        'INR' => 'Indian Rupee',
        'ISK' => 'Icelandic Krona',
        'JMD' => 'Jamaican Dollar',
        'JOD' => 'Jordanian Dinar',
        'JPY' => 'Japanese Yen',
        'KES' => 'Kenyan Shilling',
        'KRW' => 'South Korean Won',
        'KWD' => 'Kuwaiti Dinar',
        'LKR' => 'Sri Lankan Rupee',
        'MAD' => 'Moroccan Dirham',
        'MDL' => 'Moldovan Leu',
        'MGA' => 'Malagasy Ariary',
        'MKD' => 'Macedonian Denar',
        'MNT' => 'Mongolian Tugrik',
        'MOP' => 'Macanese Pataca',
        'MUR' => 'Mauritian Rupee',
        'MXN' => 'Mexican Peso',
        'MYR' => 'Malaysian Ringgit',
        'NAD' => 'Namibian Dollar',
        'NGN' => 'Nigerian Naira',
        'NOK' => 'Norwegian Krone',
        'NZD' => 'New Zealand Dollar',
        'OMR' => 'Omani Rial',
        'PEN' => 'Peruvian Sol',
        'PHP' => 'Philippine Peso',
        'PKR' => 'Pakistani Rupee',
        'PLN' => 'Polish Zloty',
        'PYG' => 'Paraguayan Guarani',
        'QAR' => 'Qatari Riyal',
        'RON' => 'Romanian Leu',
        'RSD' => 'Serbian Dinar',
        'RWF' => 'Rwandan Franc',
        'SAR' => 'Saudi Riyal',
        'SEK' => 'Swedish Krona',
        'SGD' => 'Singapore Dollar',
        'THB' => 'Thai Baht',
        'TND' => 'Tunisian Dinar',
        'TRY' => 'Turkish Lira',
        'TTD' => 'Trinidad & Tobago Dollar',
        'TWD' => 'New Taiwan Dollar',
        'TZS' => 'Tanzanian Shilling',
        'USD' => 'US Dollar',
        'UYU' => 'Uruguayan Peso',
        'UZS' => 'Uzbekistani Som',
        'VND' => 'Vietnamese Dong',
        'XCD' => 'East Caribbean Dollar',
        'XOF' => 'West African CFA Franc',
        'ZAR' => 'South African Rand',
    ];

    public static function currencyName(string $code): string
    {
        return self::CURRENCY_NAMES[strtoupper($code)] ?? strtoupper($code);
    }

    /**
     * @return array<string, array{name: string, flag: string, default_currency: string, currencies: list<string>}>
     */
    public static function all(): array
    {
        return self::SUPPORTED_COUNTRIES;
    }

    public static function isSupported(string $code): bool
    {
        return isset(self::SUPPORTED_COUNTRIES[strtoupper($code)]);
    }

    public static function defaultCurrency(string $countryCode): ?string
    {
        return self::SUPPORTED_COUNTRIES[strtoupper($countryCode)]['default_currency'] ?? null;
    }

    /**
     * @return list<string>
     */
    public static function availableCurrencies(string $countryCode): array
    {
        return self::SUPPORTED_COUNTRIES[strtoupper($countryCode)]['currencies'] ?? [];
    }

    public static function isValidCurrencyForCountry(string $countryCode, string $currencyCode): bool
    {
        return in_array(strtoupper($currencyCode), self::availableCurrencies($countryCode), true);
    }

    /**
     * @return list<string>
     */
    public static function supportedCountryCodes(): array
    {
        return array_keys(self::SUPPORTED_COUNTRIES);
    }
}
