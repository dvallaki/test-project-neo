<?php

namespace App\Http\Controllers;
use View;
use Illuminate\Support\Facades\Input;
use Redirect;

class HomeController extends Controller
{
	public function getData()
	{
		$start_date = '';
		$end_date = '';
		if(Input::get('start_date') != '')
			$start_date = Input::get('start_date');
		if(Input::get('end_date') != '')
			$end_date = Input::get('end_date');

		if(strtotime($start_date) > strtotime($end_date))
			return Redirect::back()->withInput()->withErrors(['Please enter a valid start and end date.']);
		if(round((strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24)) > 7)
			return Redirect::back()->withInput()->withErrors(['Please enter a date range of 7 or less days.']);

		$labels = [];
	    $numOfAsteroids = [];
	    $maxSpeed = '';
	    $fastestId = '';
	    $minDistance = '';
	    $closestId = '';
	    $averageSize = '';

		if($start_date != '' && $end_date != '')
		{	
			$start_date = date("Y-m-d", strtotime($start_date));
			$end_date = date("Y-m-d", strtotime($end_date));

			$curl = curl_init();
	        $url = "https://api.nasa.gov/neo/rest/v1/feed?start_date=".$start_date."&end_date=".$end_date."&api_key=3c7MQ5ThRLM4W1eSQCQ7Vm4V1tQjx4lyXxKKfMa0";       
	        curl_setopt_array($curl, array(
	            CURLOPT_RETURNTRANSFER => 1,
	            CURLOPT_URL => $url,
	        ));
	        $resp = json_decode(curl_exec($curl));

	        foreach ($resp->near_earth_objects as $key => $value) {
	        	$labels[] = $key;
	        	$numOfAsteroids[] = count($value);
	        	$speed = [];
	        	$size = [];
	        	//$max = 0;
	        	foreach ($value as $k => $v) {
	        		$id = $v->id;
	        		$close_approach_data = $v->close_approach_data;
	        		$speed[$id] = $close_approach_data[0]->relative_velocity->kilometers_per_hour;
	        		$distance[$id] = $close_approach_data[0]->miss_distance->kilometers;

	        		$minRadius[] = ($v->estimated_diameter->kilometers->estimated_diameter_min)/2;
	        		$maxRadius[] = ($v->estimated_diameter->kilometers->estimated_diameter_max)/2;
	        	}
	        	$maxSpeed = round(max($speed),2);
	        	$fastestId = array_keys($speed,max($speed));
	        	$fastestId = $fastestId[0];

	        	$minDistance = round(min($distance),2);
	        	$closestId = array_keys($distance,min($distance));
	        	$closestId = $closestId[0];

	        	$averageMinRadius = array_sum($minRadius)/count($minRadius);
	        	$averageMaxRadius = array_sum($maxRadius)/count($maxRadius);

	        	$averageRadius = ($averageMinRadius+$averageMaxRadius)/2;
	        	$averageSize = round(((4*3.14*$averageRadius*$averageRadius*$averageRadius)/3),2);
	        }

	        curl_close($curl);
		} 

        return View::make('home',compact('labels','numOfAsteroids','maxSpeed','fastestId','minDistance','closestId','averageSize'));
	}
}
