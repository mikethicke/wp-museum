<?php
class DateClass
{
	var $months = array();
	var $days = array ();
	var $years = array();
	var $shortmonths = array();
	
	var $year;
	var $month;
	var $day;
	
	
	function DateClass()
	{
		for ($i = 1; $i <= 31; $i++) { $this->days[$i] = $i; }
		for ($yyear = date('Y') -5, $max_year = date('Y') + 5; $yyear < $max_year; $yyear++)
		{
			$this->years[$yyear] = $yyear;
		}
		$this->months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 
				6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 
				11 => 'November', 12 => 'December');
		$this->shortmonths = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 
				6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 
				11 => 'Nov', 12 => 'Dec');
	}
	
	function toString()
	{
		if ($this->month < 10)
		{
			$monthstr = '0' . $this->month;
		}
		else
		{
			$monthstr = $this->month;
		}
		if ($this->day < 10)
		{
			$daystr = '0' . $this->day;
		}
		else
		{
			$daystr = $this->day;
		}
	
		$str = $this->year . '-' . $monthstr . '-' . $daystr;
	
		return $str;
	}
	
	function toLongString()
	{
		$str = $this->shortmonths[$this->month];
		
		$str = $str . " $this->day, $this->year";
		
		return $str;
	}
	
	/*2004-12-31*/
	function fromString($str)
	{
		$yearstr='';
		$yearstr = $yearstr . $str{0};
		$yearstr = $yearstr . $str{1};
		$yearstr = $yearstr . $str{2};
		$yearstr = $yearstr . $str{3};
		
		$monthstr='';
		if ($str{5} != '0')
		{
			$monthstr = $monthstr . $str{5};
			$monthstr = $monthstr . $str{6};
		}
		else
		{
			$monthstr = $monthstr . $str{6};
		}
		
		$daystr='';
		if ($str{8} != '0')
		{
			$daystr = $daystr . $str{8};
			$daystr = $daystr . $str{9};
		}
		else
		{
			$daystr = $daystr . $str{9};
		}
		
		$this->year = $yearstr;
		$this->month = $monthstr;
		$this->day = $daystr;
	}
	
		
}
?>