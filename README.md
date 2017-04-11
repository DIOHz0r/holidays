# Holidays
This library helps you to add and check dates against holidays and worktime.

## Install

Is recommended to install this via composer:

```bash
$ composer require diohz0r/holidays
```

## Use
The use is very simple, extend the class add your regional holidays and create the object. Example:


```php
    <?php
	use Holidays\AbstractHoliday;

	class Holiday extends AbstractHoliday {
	
	    /**
	     * @inheritdoc
	     */
	    public function addRegionalHolidays($year)
	    {
            //Fixed Days
            $dates = array(
                array('day'=>4,'month'=>6),
                array('day'=>12,'month'=>10),
            );
            foreach ($dates as $date) {
                $this->addHoliday($date);
            }
            //Variable day
            $this->addVariableHoliday(4, 11, $year, 4); #Thanksgiving: November's 4th Thursday
            
            //In Venezuela Carnival and Easter are holidays so we call this method to add both
            $this->getCarnival();
	    }
	}

	$objHoliday = new Holiday(date("Y"));
	if($objHoliday->isValidDate("2014-06-04")){
		//Add your logic for valid dates
	} else {
		//Add your logic for invalid dates
	}
```