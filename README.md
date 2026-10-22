## SilverStripe Great Date Field

[![Latest Stable Version](https://poser.pugx.org/hudhaifas/silverstripe-greatdate-field/v/stable)](https://packagist.org/packages/hudhaifas/silverstripe-greatdate-field) [![Total Downloads](https://poser.pugx.org/hudhaifas/silverstripe-greatdate-field/downloads)](https://packagist.org/packages/hudhaifas/silverstripe-greatdate-field) [![Latest Unstable Version](https://poser.pugx.org/hudhaifas/silverstripe-greatdate-field/v/unstable)](https://packagist.org/packages/hudhaifas/silverstripe-greatdate-field) [![License](https://poser.pugx.org/hudhaifas/silverstripe-greatdate-field/license)](https://packagist.org/packages/hudhaifas/silverstripe-greatdate-field)

A date type for dates that a normal date column cannot hold:
- B.C. dates and very old dates (any year except 0).
- Partial dates: year only, or year and month.
- Dates entered in the Hijri calendar.

### Requirements
- SilverStripe Framework 5 (branch `5`). Branches `4` and `master` are for SilverStripe 4.
- PHP `calendar` extension (used for Hijri and day calculations).

### Installation
```
composer require hudhaifas/silverstripe-greatdate-field:5.x-dev
```
Then run `dev/build?flush=1`.

### How a date is stored
A `GreatDate` field named `BirthDate` creates three integer columns:

| Column | Value |
|---|---|
| `BirthDateYear` | The year. Negative means B.C. (`-550` = 550 B.C.). `0` means no date. |
| `BirthDateMonth` | `1`–`12`, or `13` when the month is unknown. |
| `BirthDateDay` | `1`–`31`, or `32` when the day is unknown. |

A date exists when the year is not `0`.

### Usage

**Model**
```php
private static $db = [
    'BirthDate' => 'GreatDate',
];
```
Scaffolded forms use `GreatDateField` automatically. To add it yourself:
```php
use HudhaifaS\Forms\GreatDateField;

GreatDateField::create('BirthDate', 'Birth date');
```

**Form field**
- Day, month and year boxes, and a calendar menu: Gregorian (G) or Hijri (H).
- Leave day, or day and month, empty for a partial date.
- Hijri input is converted to Gregorian on save. Only the Gregorian date is stored.
- The boxes accept digits 0–9. Arabic-Indic (٠–٩) and Persian (۰–۹) digits are converted to 0–9 while typing, and again on the server.

**Templates**

For `BirthDate` = 12 July 1801:

| Call | Output |
|---|---|
| `$BirthDate.Nice` | `1801/07/12` |
| `$BirthDate.Long` | `July 12, 1801` |
| `$BirthDate.Hijri` | `Rabe-ul-awwal 2, 1216 A.H.` |
| `$BirthDate.Both` | Hijri, then Gregorian |
| `$BirthDate.ISO` | `1801-07-12` |
| `$BirthDate.MonthDay` | `July 12` |
| `$BirthDate.Ago` | `225 years ago` |
| `$BirthDate.isBC` | `false` |

Partial dates show only what is known: `Long` gives `July 1801` or `1801`, and `ISO` gives `1801-07` or `1801`. B.C. dates add ` B.C.` (`550 B.C.`), and `ISO` gives `-00550`.

**PHP**
```php
use HudhaifaS\FieldType\DBGreatDate;

$date = DBGreatDate::create_great_date(1801, 7, 12);
$date = DBGreatDate::create_great_date('1801/7/12');
$date = DBGreatDate::create_great_date(1801);   // year only
$today = DBGreatDate::today();

$date->getYear(); $date->getMonth(); $date->getDay();
$date->exists();            // has a valid year
$date->isEstimatedMonth();  // month unknown
$date->isEstimatedDay();    // day unknown
$date->DaysAgo();           // days from this date to today
$date->toArray();           // ['day' => 12, 'month' => 7, 'year' => 1801], or null
```
`toArray()` returns the stored values, so an unknown month is `13` and an unknown day is `32`.

### Translations
Month names, Hijri month names, labels and date formats are in `lang/en.yml` and `lang/ar.yml`, under `GreatDate`.

### Limits
- The Hijri calendar is calculated by formula, so it can differ by a day or two from an official or moon-sighting calendar.
- The day is checked against 1–31 only, not against the month's real length.
- The year box accepts digits only, so B.C. years cannot be typed in the form. Set them in code.

### License

    MIT License

    Copyright (c) 2018 Hudhaifa Shatnawi

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
    documentation files (the "Software"), to deal in the Software without restriction, including without 
    limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of 
    the Software, and to permit persons to whom the Software is furnished to do so, subject to the following
    conditions:

    The above copyright notice and this permission notice shall be included in all copies or substantial portions 
    of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED 
    TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
    THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF 
    CONTRACT,  TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
    DEALINGS IN THE SOFTWARE.

Issues: <https://github.com/hudhaifas/silverstripe-greatdate-field/issues>
