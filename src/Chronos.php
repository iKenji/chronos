<?php

namespace Cake\Chronos;

use DateTimeImmutable;
use DateTimeZone;

/**
 * A simple API extension for DateTimeInterface
 *
 * @property-read integer $year
 * @property-read integer $yearIso
 * @property-read integer $month
 * @property-read integer $day
 * @property-read integer $hour
 * @property-read integer $minute
 * @property-read integer $second
 * @property-read integer $timestamp seconds since the Unix Epoch
 * @property-read DateTimeZone $timezone the current timezone
 * @property-read DateTimeZone $tz alias of timezone
 * @property-read integer $micro
 * @property-read integer $dayOfWeek 0 (for Sunday) through 6 (for Saturday)
 * @property-read integer $dayOfYear 0 through 365
 * @property-read integer $weekOfMonth 1 through 5
 * @property-read integer $weekOfYear ISO-8601 week number of year, weeks starting on Monday
 * @property-read integer $daysInMonth number of days in the given month
 * @property-read integer $age does a diffInYears() with default parameters
 * @property-read integer $quarter the quarter of this instance, 1 - 4
 * @property-read integer $offset the timezone offset in seconds from UTC
 * @property-read integer $offsetHours the timezone offset in hours from UTC
 * @property-read boolean $dst daylight savings time indicator, true if DST, false otherwise
 * @property-read boolean $local checks if the timezone is local, true if local, false otherwise
 * @property-read boolean $utc checks if the timezone is UTC, true if UTC, false otherwise
 * @property-read string  $timezoneName
 * @property-read string  $tzName
 */
class Chronos extends DateTimeImmutable implements CarbonInterface
{
    use CarbonTrait;

    /**
     * Create a new Chronos instance.
     *
     * Please see the testing aids section (specifically static::setTestNow())
     * for more on the possibility of this constructor returning a test instance.
     *
     * @param string              $time
     * @param DateTimeZone|string $tz
     */
    public function __construct($time = null, $tz = null)
    {
        // If the class has a test now set and we are trying to create a now()
        // instance then override as required
        if (static::hasTestNow() && (empty($time) || $time === 'now' || static::hasRelativeKeywords($time))) {
            $testInstance = clone static::getTestNow();
            if (static::hasRelativeKeywords($time)) {
                $testInstance = $testInstance->modify($time);
            }

            //shift the time according to the given time zone
            if ($tz !== NULL && $tz != static::getTestNow()->tz) {
                $testInstance = $testInstance->setTimezone($tz);
            } else {
                $tz = $testInstance->tz;
            }

            $time = $testInstance->toDateTimeString();
        }

        parent::__construct($time, static::safeCreateDateTimeZone($tz));
    }

    /**
     * Create a new mutable instance from current immutable instance.
     *
     * @return Carbon
     */
    public function toMutable()
    {
        return Carbon::instance($this);
    }
}