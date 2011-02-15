<?php

function twitter_time_format($date)
{
	$blocks = array (
		array('year',  (3600 * 24 * 365)),
		array('month', (3600 * 24 * 30)),
		array('week',  (3600 * 24 * 7)),
		array('day',   (3600 * 24)),
		array('hour',  (3600)),
		array('min',   (60)),
		array('sec',   (1))
	);

	#Get the time from the function arg and the time now
	$argtime = strtotime($date);
	$nowtime = time();

	#Get the time diff in seconds
	$diff    = $nowtime - $argtime;

	#Store the results of the calculations
	$res = array();

	#Calculate the largest unit of time
	for ($i = 0; $i < count($blocks); $i++)
	{
		$title = $blocks[$i][0];
		$calc  = $blocks[$i][1];
		$units = floor($diff / $calc);
		
		if ($units > 0)
		{
			$res[$title] = $units;
		}
	}

	if (isset($res['year']) && $res['year'] > 0)
	{
		if (isset($res['month']) && $res['month'] > 0 && $res['month'] < 12)
		{
			$format      = "%s %s %s %s ago";
			$year_label  = $res['year'] > 1 ? 'years' : 'year';
			$month_label = $res['month'] > 1 ? 'months' : 'month';
			return sprintf($format, $res['year'], $year_label, $res['month'], $month_label);
		}
		else
		{
			$format     = "%s %s ago";
			$year_label = $res['year'] > 1 ? 'years' : 'year';
			return sprintf($format, $res['year'], $year_label);
		}
	}

	if (isset($res['month']) && $res['month'] > 0)
	{
		if (isset($res['day']) && $res['day'] > 0 && $res['day'] < 31)
		{
			$format      = "%s %s %s %s ago";
			$month_label = $res['month'] > 1 ? 'months' : 'month';
			$day_label   = $res['day'] > 1 ? 'days' : 'day';
			return sprintf($format, $res['month'], $month_label, $res['day'], $day_label);
		}
		else
		{
			$format      = "%s %s ago";
			$month_label = $res['month'] > 1 ? 'months' : 'month';
			return sprintf($format, $res['month'], $month_label);
		}
	}

	if (isset($res['day']) && $res['day'] > 0)
	{
		if ($res['day'] == 1)
		{
			return sprintf("Yesterday %s", date('h:i a', $argtime));
		}
		
		if ($res['day'] <= 7)
		{
			return date("j M", $argtime);
		}
		
		if ($res['day'] <= 31)
		{
			return date("j M", $argtime);
		}
	}

	if (isset($res['hour']) && $res['hour'] > 0)
	{
		if ($res['hour'] > 1)
		{
			return sprintf("%s hours ago", $res['hour']);
		}
		else
		{
			return "1 hour ago";
		}
	}

	if (isset($res['min']) && $res['min'])
	{
		if ($res['min'] == 1)
		{
			return "1 minute ago";
		}
		else
		{
			return sprintf("%s minutes ago", $res['min']);
		}
	}

	if (isset ($res['sec']) && $res['sec'] > 0)
	{
		if ($res['sec'] == 1)
		{
			return "1 second ago";
		}
		else
		{
			return sprintf("%s seconds ago", $res['sec']);
		}
	}
}
