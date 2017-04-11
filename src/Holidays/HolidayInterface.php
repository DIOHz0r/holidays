<?php
/**
 * Class HolidayInterface
 *
 * @author Domingo Oropeza <dioh_@hotmail.com>
 * @version 2.0.1
 */

namespace Holidays;


interface HolidayInterface
{

    /**
     * Set ending working hour
     * @param int $endHour
     */
    public function setEndHour($endHour);

    /**
     * Set ending working minute
     * @param int $endMin
     */
    public function setEndMin($endMin);

    /**
     * set the Saturday as a normal working date
     * @param int $saturday
     */
    public function setSaturday($saturday);

    /**
     * set the Sunday as a normal working date
     * @param int $sunday
     */
    public function setSunday($sunday);

    /**
     * Load an array of default holidays
     */
    public function loadHolidays();

    /**
     * @param $nTargetday int day of week (0=Sunday, 1=Monday... etc)
     * @param $nMonth int Month of year (1=Jan, 2=Feb... etc)
     * @param $nYear int
     * @param $nTh int number of week for date, example: 4th Thursday in November
     */
    public function addVariableHoliday($nTargetday, $nMonth, $nYear, $nTh);

    /**
     * Add a fixed holiday
     * @param array $holiday array(day,month)
     */
    public function addHoliday(Array $holiday);

    /**
     * Set a day and month
     * @param $date string yyyy-mm-dd
     * @return mixed
     */
    public function setDate($date);

    /**
     * Check if the day of the date is at weekend
     * @param $date
     * @return bool
     */
    public function isWeekend($date);

    /**
     * Check if the date is a hollyday
     * @param $date
     * @return bool
     */
    public function isHoliday($date);

    /**
     * @param $nTargetday int day of week (0=Sunday, 1=Monday... etc)
     * @param $nMonth int Month of year (1=Jan, 2=Feb... etc)
     * @param $nYear int
     * @param $nTh int number of week for date, example: 4th Thursday of november
     * @return bool|string
     */
    public function getFloatingDate($nTargetday, $nMonth, $nYear, $nTh);

    /**
     * Check if the date is valid
     * @param $date string yyyy-mm-dd
     * @param bool $val_hour
     * @return int
     */
    public function isValidDate($date, $val_hour = false);

    /**
     * Check if the given time is lower than the default assigned
     * @return bool
     */
    public function isValidTime();
}