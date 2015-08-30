<?php

	error_reporting(0);

	if (session_status() != PHP_SESSION_ACTIVE) {
		session_start();
	}

	if (!isset($_SESSION['raw_depth'])) {
		$url = 'http://fftoolbox.scout.com/football/depth-charts.cfm';
		$opts = array( 
			CURLOPT_POST => 1, 
			CURLOPT_HEADER => 0, 
			CURLOPT_URL => $url, 
			CURLOPT_FRESH_CONNECT => 1, 
			CURLOPT_RETURNTRANSFER => 1, 
			CURLOPT_TIMEOUT => 4, 
			CURLOPT_POSTFIELDS => http_build_query(array('injury' => 1)) 
		); 
		$ch = curl_init(); 
		curl_setopt_array($ch, $opts); 
		$result = curl_exec($ch);
		curl_close($ch);
		$_SESSION['raw_depth'] = $result;
	} else {
		$result = $_SESSION['raw_depth'];
	}
	
	$players = array();
	
	$parsed = new DOMDocument();
	$parsed->loadHTML($result);
	$divs = $parsed->getElementsByTagName('div');
	foreach ($divs as $d) {
		$isTeam = false;
		foreach ($d->attributes as $a) {
			if ($a->name == 'class' && $a->value == 'team') {
				$isTeam = true;
			}
		}
		if ($isTeam) {
			
			$matches = array();
			preg_match('/nfl_team=([A-Z]*)[^A-Z]/', $parsed->saveHTML($d), $matches, PREG_OFFSET_CAPTURE);
			if (sizeof($matches) > 1) {
				$teamKey = $matches[1][0];
			} else {
				continue;
			}
			//echo '<pre>'.print_r($matches,true).'</pre>';			exit;
			
			$players[$teamKey] = array();
			$lis = $d->getElementsByTagName('li');
			foreach ($lis as $L) {
				$player = explode(' ', trim($L->textContent), 2);
				$playerDepth = $player[0];
				$playerName = trim(preg_replace('/\[.*\]/', '', strtolower(trim($player[1]))));
				$players[$teamKey][$playerName] = $playerDepth;
			}
		}
	}
	
	$_SESSION['depth'] = $players;
	
	echo json_encode($players);
	
?>