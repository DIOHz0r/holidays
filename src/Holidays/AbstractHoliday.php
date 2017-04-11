<?php
/**
 * Class AbstractHoliday
 *
 * Check if a given date is valid for labor against holidays.
 * Extend the class and then create the object to then check the dates with isValidDate
 *
 * @author Domingo Oropeza <dioh_@hotmail.com>
 * @version 2.0.1
 */

namespace Holidays;


abstract class AbstractHoliday implements HolidayInterface
{
    protected $year; //YYYY
    protected $holidays = array();
    /**
     * if 1 the day is laborer
     * @var int
     */

    protected $saturday = 0;
    protected $sunday = 0;
    /**
     * Hour and minutes limit for work
     * @var int
     */
    protected $endHour = 18;
    protected $endMin = 0;

    /**
     * Value of sunday for "Semana Santa" (latam)/Easter
     * @var null|integer
     */
    private $holySunday = null;

    /**
     * A check to know if Easter has been calc
     * @var bool
     */
    private $holyWeek = false;

    public function __construct($year, $defaults = true)
    {
        if ($defaults === true) {
            $this->loadHolidays();
        }
        $this->addRegionalHolidays($year);
        $this->year = $year;
    }

    /**
     * @inheritdoc
     */
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;
    }

    /**
     * @inheritdoc
     */
    public function setEndMin($endMin)
    {
        $this->endMin = $endMin;
    }

    /**
     * @inheritdoc
     */
    public function setSaturday($saturday)
    {
        $this->saturday = $saturday;
    }

    /**
     * @inheritdoc
     */
    public function setSunday($sunday)
    {
        $this->sunday = $sunday;
    }

    /**
     * @inheritdoc
     */
    public function loadHolidays()
    {
        # Common fixed days
        $this->holidays = array(
            array('day' => 1, 'month' => 1), #New Year
            array('day' => 25, 'month' => 12), #Christmas
        );
    }

    /**
     * @inheritdoc
     */
    public function addVariableHoliday($nTargetday, $nMonth, $nYear, $nTh)
    {
        $floatingDate = $this->getFloatingDate($nTargetday, $nMonth, $nYear, $nTh);
        $holiday = $this->setDate($floatingDate);
        $this->addHoliday($holiday);
    }

    /**
     * @inheritdoc
     */
    public function addHoliday(Array $holiday)
    {
        $this->holidays[] = $holiday;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function setDate($date)
    {
        $holiday['day'] = substr($date, 8, 2);
        $holiday['month'] = substr($date, 5, 2);
        return $holiday;
    }

    /**
     * @inheritdoc
     * @return bool
     */
    public function isWeekend($date)
    {
        $day = date("w", strtotime($date));
        if (($day == 6 && $this->saturday == 0) || ($day == 0 && $this->sunday == 0)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This calculates the "Semana Santa" (latam)/Easter for the given year
     * The formula was made by Gauss a lot of years ago.
     * @param int $year in YYYY format
     * @param boolean $asHoliday if it is true adds Thursday and Friday as Holidays
     * @return array of important days
     */
    public function getHolyWeek($year, $asHoliday)
    {
        $a = $year % 19;
        $b = $year % 4;
        $c = $year % 7;
        $d = (19 * $a + 24) % 30;
        $e = (2 * $b + 4 * $c + 6 * $d + 5) % 7;
        $sunday = $this->holySunday = 22 + $d + $e;
        $thursday = date("Y-m-d", mktime(0, 0, 0, 3, $sunday - 3, $year));
        $friday = date("Y-m-d", mktime(0, 0, 0, 3, $sunday - 2, $year));
        if (true === $asHoliday) {
            $this->addHoliday($this->setDate($thursday));
            $this->addHoliday($this->setDate($friday));
        }
        $this->holyWeek = true;
        return array($thursday, $friday, $sunday);
    }

    /**
     * Latin America's Carnival is 40 days before the Holy Sunday,
     * so we must calculate it before get the carnival
     * @param null|int $year in YYYY format
     * @param bool $markHoliday if it is true adds Monday and Tuesday as Holidays
     * @return array of important days
     */
    public function getCarnival($year = null, $markHoliday = true)
    {
        $year = (null === $year) ? $this->year : $year;
        if (true !== $this->holyWeek || null === $this->holySunday) {
            $this->getHolyWeek($year, $markHoliday);
        }
        $monday = date("Y-m-d", mktime(0, 0, 0, 3, $this->holySunday - 48, $year));
        $tuesday = date("Y-m-d", mktime(0, 0, 0, 3, $this->holySunday - 47, $year));
        if (true === $markHoliday) {
            $this->addHoliday($this->setDate($monday));
            $this->addHoliday($this->setDate($tuesday));
        }
        return array($monday, $tuesday);
    }

    /**
     * @inheritdoc
     * @return bool
     */
    public function isHoliday($date)
    {
        $month = date("n", strtotime($date));
        $day = date("j", strtotime($date));
        foreach ($this->holidays as $holiday) {
            if ($holiday['day'] == $day && $this['month'] == $month) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     * @return bool|string
     */
    public function getFloatingDate($nTargetday, $nMonth, $nYear, $nTh)
    {
        $nEarliestDate = 1 + 7 * ($nTh - 1);
        $nWeekday = date("w", mktime(0, 0, 0, $nMonth, $nEarliestDate, $nYear));
        if ($nTargetday == $nWeekday) {
            $nOffset = 0;
        } else {
            if ($nTargetday < $nWeekday) {
                $nOffset = $nTargetday + (7 - $nWeekday);
            } else {
                $nOffset = ($nTargetday + (7 - $nWeekday)) - 7;
            }
        }
        $tHolidayDate = mktime(0, 0, 0, $nMonth, $nEarliestDate + $nOffset, $nYear);
        return date("Y-m-d", $tHolidayDate);
    }

    /**
     * @inheritdoc
     * @return int
     */
    public function isValidDate($date, $val_hour = false)
    {
        if ($this->isWeekend($date) === false && $this->isHoliday($date) === false) {
            if ($val_hour) {
                if (date("Y-m-d", strtotime("+1 day")) == date("Y-m-d", strtotime($date))) {
                    #Same day
                    if ($this->isValidTime()) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    #Not the next day, so it's not necessary
                    return true;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     * @return bool
     */
    public function isValidTime()
    {
        $actHour = date("G");
        if ($actHour > $this->endHour) {
            return false;
        }
        if ($actHour == $this->endHour) {
            $actMin = date("i");
            if ($actMin > $this->endMin) {
                return false;
            }
        }
        return true;
    }

    /**
     * Add local holidays logic for your region...
     *
     * Examples:
     * $this->addHoliday(array('day' => 1, 'month' => 5)); //Fixed day, International labor day: May 1th
     * $this->addVariableHoliday(4, 11, $year, 4)); //Variable day, Thanksgiving: November's 4th Thursday
     * $this->getCarnival() //Carnival and Easter as Holidays
     *
     * @param $year
     * @return mixed
     */
    abstract public function addRegionalHolidays($year);
}