<?php require_once('session.php'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
		<style>

			#content {
				padding-top: 20px;
			}
			#header a {
				color: black;
				text-decoration: none;
			}
			.card {
				cursor: pointer;
				font-size: 1em;
				margin: 5px 0px;
				padding: 2px 5px;
			}
			.star {
				border-bottom: 1px solid black;
				border-top: 1px solid black;
				font-weight: bold;
			}
			.pos-qb { background-color: #ccf; }
			.pos-rb, .pos-fb { background-color: #fcc; }
			.pos-wr { background-color: #cff; }
			.pos-te { background-color: #ffc; }
			.pos-dst, .pos-def { background-color: #cfa; }
			.pos-k { background-color: #bcb; }
			.taken {
				background-color: #fff;
				border: none;
				color: #ccc;
				font-weight: normal;
			}
			@media(max-width: 991px) {
				.card {
					margin: 10px 0px;
					padding: 10px 5px;
				}
			}
			#button-clear {
				border: 2px solid red;
				color: red;
				cursor: pointer;
				margin: 50px 10px;
				padding: 10px;
			}

			.filters { text-align: right; padding: 16px; }
			.filters .hint { font-style: italic; color: #ccc; }
			.filters input[type=checkbox] {
				position:absolute;
				z-index: -1;
				opacity: 0;
			}
		</style>
	</head>
<body>

	<div id="content" class="container">
		<div id="header"><h1><a href="<?= str_replace('ff.php', '', $_SERVER['REQUEST_URI']) ?>">Flea Flicker</a></h1></div>
		<div class="row filters"><span class="hint">(deselect to hide position)</span>
		</div><!-- .row .filters -->
		<div class="row">

			<div id="row1" class="col-sm-3"></div>
			<div id="row2" class="col-sm-3"></div>
			<div id="row3" class="col-sm-3"></div>
			<div id="row4" class="col-sm-3"></div>

		</div>
	</div>

	<script>
		taken = ["<?= isset($_SESSION['stash']) ? implode('","', array_keys($_SESSION['stash'])) : '' ?>"];
		star = ["<?= isset($_SESSION['star']) ? implode('","', $_SESSION['star']) : '' ?>"];
		depth = $.parseJSON('<?= isset($_SESSION['depth']) ? str_replace("'", "\'", json_encode($_SESSION['depth'])) : '{}' ?>');
		star_found = [];
		/* Sample 2015 NFL Draft Lists */
		lists = [
			// espn mike clay 2015
			"Le'Veon Bell, PIT: RB; Adrian Peterson, MIN: RB; Jamaal Charles, KC: RB; Eddie Lacy, GB: RB; Marshawn Lynch, SEA: RB; Antonio Brown, PIT: WR; Julio Jones, ATL: WR; Demaryius Thomas, DEN: WR; Odell Beckham Jr, NYG: WR; Dez Bryant, DAL: WR; Randall Cobb, GB: WR; Calvin Johnson, DET: WR; Rob Gronkowski, NE: TE; A.J. Green, CIN:WR; Matt Forte, CHI: RB; Alshon Jeffery, CHI: WR; C.J. Anderson, DEN:RB; Jeremy Hill, CIN: RB; LeSean McCoy, BUF: RB; Mike Evans, TB: WR; T.Y. Hilton, IND:WR; Brandin Cooks, NO: WR; Justin Forsett, BAL: RB; Lamar Miller, MIA: RB; DeMarco Murray, PHI: RB; Emmanuel Sanders, DEN: WR; Frank Gore, IND: RB; Mark Ingram, NO: RB; DeAndre Hopkins, HOU: WR; Julian Edelman, NE: WR; Andre Ellington, ARI: RB; Keenan Allen, SD: WR; Andrew Luck, IND: QB; Carlos Hyde, SF: RB; Alfred Morris, WAS: RB; Andre Johnson, IND: WR; C.J. Spiller, NO:RB; Aaron Rodgers, GB: QB; Latavius Murray, OAK: RB; Melvin Gordon, SD: RB; Jordan Matthews, PHI: WR; Amari Cooper, OAK: WR; Jeremy Maclin, KC: WR; T.J. Yeldon, JAC:RB; Jarvis Landry, MIA: WR; Golden Tate, DET: WR; Davante Adams, GB: WR; Allen Robinson, JAC: WR; Ameer Abdullah, DET: RB; Jonathan Stewart, CAR: RB; Joseph Randle, DAL: RB; Travis Kelce, KC: TE; Jimmy Graham, SEA: TE; Greg Olsen, CAR: TE; Brandon Marshall, NYJ: WR; Vincent Jackson, TB: WR; Eric Decker, NYJ: WR; Giovani Bernard, CIN: RB; Todd Gurley, STL: RB; Arian Foster, HOU: RB; Sammy Watkins, BUF: WR; DeSean Jackson, WAS: WR; Drew Brees, NO: QB; Russell Wilson, SEA: QB; Martellus Bennett, CHI: TE; Anquan Boldin, SF: WR; Peyton Manning, DEN: QB; John Brown, ARI: WR; Martavis Bryant, PIT: WR; Steve Smith Sr, BAL: WR; Michael Floyd, ARI: WR; Pierre Garcon, WAS: WR; Marques Colston, NO: WR; Jason Witten, DAL: TE; Tony Romo, DAL: QB; Ben Roethlisberger, PIT: QB; Joique Bell, DET: RB; Danny Woodhead, SD: RB; Chris Ivory, NYJ: RB; LeGarrette Blount, NE: RB; Kendall Wright, TEN: WR; Charles Johnson, MIN: WR; Torrey Smith, SF: WR; Zach Ertz, PHI: TE; Matt Ryan, ATL: QB; Larry Fitzgerald, ARI: WR; Victor Cruz, NYG: WR; Breshad Perriman, BAL: WR; Roddy White, ATL: WR; Devonta Freeman, ATL: RB; Tevin Coleman, ATL: RB; Shane Vereen, NYG: RB; Duke Johnson, CLE: RB; Isaiah Crowell, CLE: RB; Ryan Tannehill, MIA: QB; Cam Newton, CAR: QB; Julius Thomas, JAC: TE; Jordan Cameron, MIA: TE; Delanie Walker, TEN: TE; Bishop Sankey, TEN: RB",
			// cbs jamey eisenberg 2015
			"Adrian Peterson, MIN:RB;Le'Veon Bell, PIT:RB;Jamaal Charles, KC:RB;Eddie Lacy, GB:RB;Rob Gronkowski, NE:TE;Antonio Brown, PIT:WR;Dez Bryant, DAL:WR;Julio Jones, ATL:WR;Calvin Johnson, DET:WR;Demaryius Thomas, DEN:WR;C.J. Anderson, DEN:RB;Matt Forte, CHI:RB;Odell Beckham, NYG:WR;A.J. Green, CIN:WR;Marshawn Lynch, SEA:RB;Jeremy Hill, CIN:RB;Randall Cobb, GB:WR;T.Y. Hilton, IND:WR;Alshon Jeffery, CHI:WR;DeMarco Murray, PHI:RB;LeSean McCoy, BUF:RB;Jimmy Graham, SEA:TE;Justin Forsett, BAL:RB;Frank Gore, IND:RB;Lamar Miller, MIA:RB;Jordan Matthews, PHI:WR;Brandin Cooks, NO:WR;Mike Evans, TB:WR;Andrew Luck, IND:QB;DeAndre Hopkins, HOU:WR;Aaron Rodgers, GB:QB;Joseph Randle, DAL:RB;Andre Ellington, ARI:RB;Emmanuel Sanders, DEN:WR;Mark Ingram, NO:RB;Andre Johnson, IND:WR;Ameer Abdullah, DET:RB;Davante Adams, GB:WR;Julian Edelman, NE:WR;Amari Cooper, OAK:WR;Melvin Gordon, SD:RB;Jonathan Stewart, CAR:RB;Latavius Murray, OAK:RB;Carlos Hyde, SF:RB;Travis Kelce, KC:TE;Alfred Morris, WAS:RB;C.J. Spiller, NO:RB;Jarvis Landry, MIA:WR;Martavis Bryant, PIT:WR;T.J. Yeldon, JAC:RB;Greg Olsen, CAR:TE;Ben Roethlisberger, PIT:QB;Drew Brees, NO:QB;Doug Martin, TB:RB;Chris Ivory, NYJ:RB;Peyton Manning, DEN:QB;DeSean Jackson, WAS:WR;Giovani Bernard, CIN:RB;Nelson Agholor, PHI:WR;Keenan Allen, SD:WR;Allen Robinson, JAC:WR;Charles Johnson, MIN:WR;Brandon Marshall, NYJ:WR;Golden Tate, DET:WR;Joique Bell, DET:RB;LeGarrette Blount, NE:RB;Sammy Watkins, BUF:WR;Matt Ryan, ATL:QB;Arian Foster, HOU:RB;Todd Gurley, STL:RB;Isaiah Crowell, CLE:RB;Devonta Freeman, ATL:RB;Tevin Coleman, ATL:RB;Russell Wilson, SEA:QB;Martellus Bennett, CHI:TE;Vincent Jackson, TB:WR;John Brown, ARI:WR;Ryan Mathews, PHI:RB;Rashad Jennings, NYG:RB;Danny Woodhead, SD:RB;Duke Johnson, CLE:RB;Jeremy Maclin, KC:WR;Tony Romo, DAL:QB;David Johnson, ARI:RB;Roddy White, ATL:WR;Victor Cruz, NYG:WR;Michael Floyd, ARI:WR;Matthew Stafford, DET:QB;Anquan Boldin, SF:WR;Steve Smith, BAL:WR;David Cobb, TEN:RB;Alfred Blue, HOU:RB;Tre Mason, STL:RB;Shane Vereen, NYG:RB;Jordan Cameron, MIA:TE;Larry Fitzgerald, ARI:WR;Kendall Wright, TEN:WR;Eddie Royal, CHI:WR;Eli Manning, NYG:QB;Breshad Perriman, BAL:WR;Brandon LaFell, NE:WR;Ryan Tannehill, MIA:QB;Mike Wallace, MIN:WR;Devin Funchess, CAR:WR;DeVante Parker, MIA:WR;Tom Brady, NE:QB;Pierre Garcon, WAS:WR;Cam Newton, CAR:QB;Eric Decker, NYJ:WR;Delanie Walker, TEN:TE;Jason Witten, DAL:TE;Julius Thomas, JAC:TE;Bishop Sankey, TEN:RB;Tyler Eifert, CIN:TE;Charles Sims, TB:RB;Knile Davis, KC:RB;Reggie Bush, SF:RB;Kyle Rudolph, MIN:TE;Andre Williams, NYG:RB;Dwayne Allen, IND:TE;Sam Bradford, PHI:QB;Darren McFadden, DAL:RB;Torrey Smith, SF:WR;Marques Colston, NO:WR;Terrance Williams, DAL:WR;Carson Palmer, ARI:QB;Benjamin Watson, NO:TE;Brian Quick, STL:WR;Roy Helu, OAK:RB;Austin Seferian-Jenkins, TB:TE;Matt Jones, WAS:RB;Ronnie Hillman, DEN:RB;Dan Herron, IND:RB;Teddy Bridgewater, MIN:QB;Steve Johnson, SD:WR;Markus Wheaton, PIT:WR;Cody Latimer, DEN:WR;Allen Hurns, JAC:WR;Lance Dunbar, DAL:RB;Lorenzo Taliaferro, BAL:RB;Doug Baldwin, SEA:WR;Bilal Powell, NYJ:RB;Cameron Artis-Payne, CAR:RB;Dorial Green-Beckham, TEN:WR;Jerick McKinnon, MIN:RB;Jeff Janis, GB:WR;James White, NE:RB;Darren Sproles, PHI:RB;DeAngelo Williams, PIT:RB;Zach Ertz, PHI:TE;Jonathan Grimes, HOU:RB;Theo Riddick, DET:RB;Denard Robinson, JAC:RB;Khiry Robinson, NO:RB;Antonio Gates, SD:TE;Montee Ball, DEN:RB;Brandon Coleman, NO:WR;Coby Fleener, IND:TE;Marvin Jones, CIN:WR;Kenny Stills, MIA:WR;Rueben Randle, NYG:WR;Philip Rivers, SD:QB;Dwayne Bowe, CLE:WR;Malcom Floyd, SD:WR;Phillip Dorsett, IND:WR;Javorius Allen, BAL:RB;Percy Harvin, BUF:WR;Jaelen Strong, HOU:WR;Andy Dalton, CIN:QB;Robert Turbin, SEA:RB;James Starks, GB:RB;Owen Daniels, DEN:TE;Fred Jackson, BUF:RB;Heath Miller, PIT:TE;Seahawks, SEA:DST;Charles Clay, BUF:TE;Colin Kaepernick, SF:QB;Vernon Davis, SF:TE;Jay Cutler, CHI:QB;Larry Donnell, NYG:TE;Cole Beasley, DAL:WR;Eric Ebron, DET:TE;Bills, BUF:DST;Josh Hill, NO:TE;Damien Williams, MIA:RB;Jordan Reed, WAS:TE;Marcus Mariota, TEN:QB;Jameis Winston, TB:QB;Dolphins, MIA:DST;Ladarius Green, SD:TE;Jace Amaro, NYJ:TE;Harry Douglas, TEN:WR;Jay Ajayi, MIA:RB;Christine Michael, SEA:RB;Joe Flacco, BAL:QB;Cecil Shorts, HOU:WR;Michael Crabtree, OAK:WR;Jets, NYJ:DST;Jeremy Langford, CHI:RB;Texans, HOU:DST",
			// fantasy pros 2015
			"Le'Veon Bell, PIT:RB;Adrian Peterson, MIN:RB;Jamaal Charles, KC:RB;Antonio Brown, PIT:WR;Eddie Lacy, GB:RB;Julio Jones, ATL:WR;Rob Gronkowski, NE:TE;Dez Bryant, DAL:WR;Marshawn Lynch, SEA:RB;Demaryius Thomas, DEN:WR;C.J. Anderson, DEN:RB;Odell Beckham Jr., NYG:WR;Matt Forte, CHI:RB;Calvin Johnson, DET:WR;A.J. Green, CIN:WR;Randall Cobb, GB:WR;Jeremy Hill, CIN:RB;DeMarco Murray, PHI:RB;Justin Forsett, BAL:RB;LeSean McCoy, BUF:RB;Alshon Jeffery, CHI:WR;Mike Evans, TB:WR;T.Y. Hilton, IND:WR;Andrew Luck, IND:QB;Lamar Miller, MIA:RB;Aaron Rodgers, GB:QB;DeAndre Hopkins, HOU:WR;Brandin Cooks, NO:WR;Frank Gore, IND:RB;Mark Ingram, NO:RB;Jordan Matthews, PHI:WR;Emmanuel Sanders, DEN:WR;Alfred Morris, WAS:RB;Melvin Gordon, SD:RB;Andre Johnson, IND:WR;Jimmy Graham, SEA:TE;Andre Ellington, ARI:RB;Jonathan Stewart, CAR:RB;Latavius Murray, OAK:RB;Keenan Allen, SD:WR;Julian Edelman, NE:WR;C.J. Spiller, NO:RB;Amari Cooper, OAK:WR;Carlos Hyde, SF:RB;Russell Wilson, SEA:QB;Brandon Marshall, NYJ:WR;T.J. Yeldon, JAC:RB;Peyton Manning, DEN:QB;Joseph Randle, DAL:RB;Greg Olsen, CAR:TE;Travis Kelce, KC:TE;Golden Tate, DET:WR;Ameer Abdullah, DET:RB;Drew Brees, NO:QB;Jeremy Maclin, KC:WR;Allen Robinson, JAC:WR;Sammy Watkins, BUF:WR;Jarvis Landry, MIA:WR;Ben Roethlisberger, PIT:QB;Davante Adams, GB:WR;DeSean Jackson, WAS:WR;Todd Gurley, STL:RB;Giovani Bernard, CIN:RB;LeGarrette Blount, NE:RB;Vincent Jackson, TB:WR;Matt Ryan, ATL:QB;Martellus Bennett, CHI:TE;Doug Martin, TB:RB;Tony Romo, DAL:QB;Larry Fitzgerald, ARI:WR;Christopher Ivory, NYJ:RB;Roddy White, ATL:WR;Steve Smith, BAL:WR;Eric Decker, NYJ:WR;Anquan Boldin, SF:WR;Rashad Jennings, NYG:RB;Eli Manning, NYG:QB;Ryan Tannehill, MIA:QB;Cam Newton, CAR:QB;Joique Bell, DET:RB;Shane Vereen, NYG:RB;Nelson Agholor, PHI:WR;Brandon LaFell, NE:WR;Charles Johnson, MIN:WR;Mike Wallace, MIN:WR;Tevin Coleman, ATL:RB;John Brown, ARI:WR;Martavis Bryant, PIT:WR;Jordan Cameron, MIA:TE;Jason Witten, DAL:TE;Isaiah Crowell, CLE:RB;Matthew Stafford, DET:QB;Tom Brady, NE:QB;Delanie Walker, TEN:TE;Marques Colston, NO:WR;Arian Foster, HOU:RB;Devonta Freeman, ATL:RB;Philip Rivers, SD:QB;Torrey Smith, SF:WR;Kendall Wright, TEN:WR;Julius Thomas, JAC:TE;Pierre Garcon, WAS:WR;Zach Ertz, PHI:TE;Devin Funchess, CAR:WR;Bishop Sankey, TEN:RB;Duke Johnson, CLE:RB;Victor Cruz, NYG:WR;Tyler Eifert, CIN:TE;Ryan Mathews, PHI:RB;Tre Mason, STL:RB;Michael Floyd, ARI:WR;Eddie Royal, CHI:WR;Danny Woodhead, SD:RB;Reggie Bush, SF:RB;Sam Bradford, PHI:QB;Brian Quick, STL:WR;Kyle Rudolph, MIN:TE;David Cobb, TEN:RB;Teddy Bridgewater, MIN:QB;Breshad Perriman, BAL:WR;Colin Kaepernick, SF:QB;Steve Johnson, SD:WR;Austin Seferian-Jenkins, TB:TE;Larry Donnell, NYG:TE;Dwayne Allen, IND:TE;Owen Daniels, DEN:TE;Charles Sims, TB:RB;Heath Miller, PIT:TE;Roy Helu, OAK:RB;Antonio Gates, SD:TE;Kenny Stills, MIA:WR;Joe Flacco, BAL:QB;Rueben Randle, NYG:WR;David Johnson, ARI:RB;Alfred Blue, HOU:RB;Darren McFadden, DAL:RB;Markus Wheaton, PIT:WR;Terrance Williams, DAL:WR;Carson Palmer, ARI:QB;Seahawks, SEA:DST;Devante Parker, MIA:WR;Jordan Reed, WAS:TE;Darren Sproles, PHI:RB;Michael Crabtree, OAK:WR;Marvin Jones, CIN:WR;Josh Hill, NO:TE;Charles Clay, BUF:TE;Jay Cutler, CHI:QB;Vernon Davis, SF:TE;Knile Davis, KC:RB;Dwayne Bowe, CLE:WR;Andy Dalton, CIN:QB;Andre Williams, NYG:RB;Bills, BUF:DST;Doug Baldwin, SEA:WR;Rams, STL:DST;Texans, HOU:DST;Ladarius Green, SD:TE;Coby Fleener, IND:TE;Fred Jackson, BUF:RB;Percy Harvin, BUF:WR;Eric Ebron, DET:TE;Cody Latimer, DEN:WR;Jerick McKinnon, MIN:RB;Matt Jones, WAS:RB;Malcom Floyd, SD:WR;Terrance West, CLE:RB;Denard Robinson, JAC:RB;Jay Ajayi, MIA:RB;Jameis Winston, TB:QB;Brandon Coleman, NO:WR;James White, NE:RB;Kenny Britt, STL:WR;Lance Dunbar, DAL:RB;Chris Polk, HOU:RB;Cardinals, ARI:DST;Phillip Dorsett, IND:WR;Dorial Green-Beckham, TEN:WR;Jonas Gray, NE:RB;Richard Rodgers, GB:TE;Cecil Shorts, HOU:WR;Jared Cook, STL:TE;Khiry Robinson, NO:RB;Marcus Mariota, TEN:QB;Robert Griffin III, WAS:QB;Alex Smith, KC:QB;Javorius Allen, BAL:RB;Jeff Janis, GB:WR;Montee Ball, DEN:RB;Ronnie Hillman, DEN:RB;Daniel Herron, IND:RB;Kevin White, CHI:WR;Allen Hurns, JAC:WR;Jace Amaro, NYJ:TE;DeAngelo Williams, PIT:RB;Donte Moncrief, IND:WR;Derek Carr, OAK:QB;Cameron Artis-Payne, CAR:RB;Ty Montgomery, GB:WR;Broncos, DEN:DST;Andrew Hawkins, CLE:WR;Mychal Rivera, OAK:TE;Blake Bortles, JAC:QB;Lorenzo Taliaferro, BAL:RB;Patriots, NE:DST;Jets, NYJ:DST;Panthers, CAR:DST;Maxx Williams, BAL:TE;Stedman Bailey, STL:WR;Dolphins, MIA:DST;Benjamin Watson, NO:TE;Marquess Wilson, CHI:WR;Theo Riddick, DET:RB;James Starks, GB:RB;Cole Beasley, DAL:WR;Stevan Ridley, NYJ:RB;Marqise Lee, JAC:WR;Nick Foles, STL:QB;Leonard Hankerson, ATL:WR;Ravens, BAL:DST;Bengals, CIN:DST;Brian Hartline, CLE:WR;Jerricho Cotchery, CAR:WR;Jaelen Strong, HOU:WR;Philly Brown, CAR:WR;Greg Jennings, MIA:WR;Tyler Lockett, SEA:WR;Trent Richardson, OAK:RB;Bilal Powell, NYJ:RB;Virgil Green, DEN:TE;Kamar Aiken, BAL:WR;Mohamed Sanu, CIN:WR;Jacob Tamme, ATL:TE;Rob Housler, CLE:TE;Damien Williams, MIA:RB;Nick Toon, NO:WR;Josh Huff, PHI:WR;Robert Woods, BUF:WR;Chiefs, KC:DST;49ers, SF:DST;Reggie Wayne, NE:WR;Danny Amendola, NE:WR;Branden Oliver, SD:RB;Antone Smith, ATL:RB;Taylor Gabriel, CLE:WR;Eagles, PHI:DST;Albert Wilson, KC:WR;Ryan Fitzpatrick, NYJ:QB;Jonathan Grimes, HOU:RB;Darren Fells, ARI:TE;Karlos Williams, BUF:RB;Lions, DET:DST;Jermaine Kearse, SEA:WR;Hakeem Nicks, TEN:WR;",
			// cbs dave richard 2015
			"Le'Veon Bell, PIT:RB;Eddie Lacy, GB:RB;Adrian Peterson, MIN:RB;Jamaal Charles, KC:RB;Antonio Brown, PIT:WR;Matt Forte, CHI:RB;C.J. Anderson, DEN:RB;Demaryius Thomas, DEN:WR;Dez Bryant, DAL:WR;Rob Gronkowski, NE:TE;Calvin Johnson, DET:WR;Odell Beckham, NYG:WR;Julio Jones, ATL:WR;A.J. Green, CIN:WR;Marshawn Lynch, SEA:RB;Jeremy Hill, CIN:RB;Randall Cobb, GB:WR;T.Y. Hilton, IND:WR;DeMarco Murray, PHI:RB;Alshon Jeffery, CHI:WR;Brandin Cooks, NO:WR;Mike Evans, TB:WR;LeSean McCoy, BUF:RB;Justin Forsett, BAL:RB;Frank Gore, IND:RB;Jimmy Graham, SEA:TE;Lamar Miller, MIA:RB;Jordan Matthews, PHI:WR;Andrew Luck, IND:QB;Emmanuel Sanders, DEN:WR;Joseph Randle, DAL:RB;Latavius Murray, OAK:RB;Melvin Gordon, SD:RB;Amari Cooper, OAK:WR;DeAndre Hopkins, HOU:WR;Mark Ingram, NO:RB;Ameer Abdullah, DET:RB;Alfred Morris, WAS:RB;Carlos Hyde, SF:RB;Doug Martin, TB:RB;Andre Ellington, ARI:RB;T.J. Yeldon, JAC:RB;Aaron Rodgers, GB:QB;Andre Johnson, IND:WR;Peyton Manning, DEN:QB;Julian Edelman, NE:WR;Jarvis Landry, MIA:WR;Travis Kelce, KC:TE;Greg Olsen, CAR:TE;Keenan Allen, SD:WR;C.J. Spiller, NO:RB;Jonathan Stewart, CAR:RB;Sammy Watkins, BUF:WR;Giovani Bernard, CIN:RB;Davante Adams, GB:WR;Nelson Agholor, PHI:WR;Allen Robinson, JAC:WR;Brandon Marshall, NYJ:WR;Charles Johnson, MIN:WR;DeSean Jackson, WAS:WR;Martavis Bryant, PIT:WR;Chris Ivory, NYJ:RB;Golden Tate, DET:WR;Jeremy Maclin, KC:WR;Matt Ryan, ATL:QB;Drew Brees, NO:QB;Todd Gurley, STL:RB;Joique Bell, DET:RB;Vincent Jackson, TB:WR;Arian Foster, HOU:RB;Rashad Jennings, NYG:RB;Michael Floyd, ARI:WR;Ryan Mathews, PHI:RB;Devonta Freeman, ATL:RB;Martellus Bennett, CHI:TE;John Brown, ARI:WR;Tevin Coleman, ATL:RB;Tony Romo, DAL:QB;Ben Roethlisberger, PIT:QB;Russell Wilson, SEA:QB;Roddy White, ATL:WR;Victor Cruz, NYG:WR;Mike Wallace, MIN:WR;Anquan Boldin, SF:WR;Steve Smith, BAL:WR;Duke Johnson, CLE:RB;Bishop Sankey, TEN:RB;LeGarrette Blount, NE:RB;Julius Thomas, JAC:TE;Kendall Wright, TEN:WR;Isaiah Crowell, CLE:RB;Tre Mason, STL:RB;Tom Brady, NE:QB;Larry Fitzgerald, ARI:WR;Torrey Smith, SF:WR;Alfred Blue, HOU:RB;Eddie Royal, CHI:WR;Danny Woodhead, SD:RB;Shane Vereen, NYG:RB;David Johnson, ARI:RB;Eli Manning, NYG:QB;Cam Newton, CAR:QB;Matthew Stafford, DET:QB;Knile Davis, KC:RB;Zach Ertz, PHI:TE;DeVante Parker, MIA:WR;Cody Latimer, DEN:WR;Devin Funchess, CAR:WR;Brandon LaFell, NE:WR;Antonio Gates, SD:TE;Delanie Walker, TEN:TE;Jordan Cameron, MIA:TE;Ryan Tannehill, MIA:QB;Terrance Williams, DAL:WR;Jason Witten, DAL:TE;Darren Sproles, PHI:RB;Texans, HOU:DST;Allen Hurns, JAC:WR;Darren McFadden, DAL:RB;Seahawks, SEA:DST;Breshad Perriman, BAL:WR;Steve Johnson, SD:WR;Reggie Bush, SF:RB;Ronnie Hillman, DEN:RB;Matt Jones, WAS:RB;Charles Sims, TB:RB;David Cobb, TEN:RB;Dwayne Allen, IND:TE;Eric Decker, NYJ:WR;Carson Palmer, ARI:QB;Marques Colston, NO:WR;Tyler Eifert, CIN:TE;Dolphins, MIA:DST;Teddy Bridgewater, MIN:QB;Cole Beasley, DAL:WR;Robert Turbin, SEA:RB;Sam Bradford, PHI:QB;Andre Williams, NYG:RB;Cameron Artis-Payne, CAR:RB;Khiry Robinson, NO:RB;Damien Williams, MIA:RB;Roy Helu, OAK:RB;Dan Herron, IND:RB;DeAngelo Williams, PIT:RB;Michael Crabtree, OAK:WR;Kyle Rudolph, MIN:TE;Panthers, CAR:DST;Jets, NYJ:DST;Benjamin Watson, NO:TE;Pierre Garcon, WAS:WR;Austin Seferian-Jenkins, TB:TE;Dorial Green-Beckham, TEN:WR;Markus Wheaton, PIT:WR;Lance Dunbar, DAL:RB;Bengals, CIN:DST;Bills, BUF:DST;Marvin Jones, CIN:WR;Rams, STL:DST;Jonas Gray, NE:RB;Jeff Janis, GB:WR;Ty Montgomery, GB:WR;Philip Rivers, SD:QB;Vernon Davis, SF:TE;Lorenzo Taliaferro, BAL:RB;James Starks, GB:RB;Brandon Coleman, NO:WR;Denard Robinson, JAC:RB;Brian Hartline, CLE:WR;James White, NE:RB;Bilal Powell, NYJ:RB;Phillip Dorsett, IND:WR;Theo Riddick, DET:RB;Eric Ebron, DET:TE;Jerick McKinnon, MIN:RB;Broncos, DEN:DST;Kenny Britt, STL:WR;Harry Douglas, TEN:WR;Owen Daniels, DEN:TE;Packers, GB:DST;Coby Fleener, IND:TE;Malcom Floyd, SD:WR;Rod Streater, OAK:WR;Jeremy Langford, CHI:RB;Stedman Bailey, STL:WR;Jay Cutler, CHI:QB;Colin Kaepernick, SF:QB;Javorius Allen, BAL:RB;Dwayne Bowe, CLE:WR;Fred Jackson, BUF:RB;Brian Quick, STL:WR;Rueben Randle, NYG:WR;Mohamed Sanu, CIN:WR;Jaelen Strong, HOU:WR;Charles Clay, BUF:TE;Richard Rodgers, GB:TE;Crockett Gillmore, BAL:TE;Kenny Stills, MIA:WR;Marqise Lee, JAC:WR;Colts, IND:DST;Robert Woods, BUF:WR;"
		];

		/** positional filters */
		var $filterButton = function(label) {
			var $label, $input, $button;
			$label = $("<label>").addClass("btn btn-primary active").append(label.toUpperCase());
			$input = $("<input>")
				.attr({ type: "checkbox", id: "filter-" + label })
				.val(label)
				.click(function() {
					$(this).parent().toggleClass("active");

					if ($(this).parent().hasClass("active")) {
						$("div.pos-" + $(this).val()).show();
					} else {
						$("div.pos-" + $(this).val()).hide();
					}
			});

			return $label.append($input);
		}, $filters = $("<div>").addClass("btn-group").data("toggle", "button"), filtersApplied = [];

		$(lists).each(function(i, val) {
			var splits = val.split(';');
			$(splits).each(function(j, card) {
				card = card.replace(',','').replace(/&nbsp;/g, ' ').trim();
				var pos = card.substr(card.indexOf(':')+1).replace(/[0-9]/g, '').trim().toLowerCase();
				pos = pos.replace(/^def$/, 'dst').replace(/^fb$/, 'rb');

				if (filtersApplied.indexOf(pos) === -1) {
					$filters.append($filterButton(pos));
					filtersApplied.push(pos);
				}

				var name = card.substr(0, card.indexOf(':'));
				
				var namePieces = name.split(' ');
				var team = namePieces[namePieces.length-1].trim();
				var rawName = name.replace(team, '').trim().toLowerCase();
				if (typeof(depth[team]) != 'undefined' && typeof(depth[team][rawName]) != 'undefined') {
					card = card + ' <span style="color:#aaa;">[' + depth[team][rawName] + ']</span>';
				}
				
				$('#row'+(i+1)).append('<div class="card pos-'+pos+($.inArray(name, taken)>-1?' taken':'')+($.inArray(name, star)>-1?' star':'')+'" data-name="'+name+'">'+card+'</div>');
				if ($.inArray(name, star) > -1) {
					star_found.push(name);
				}
			});
		});

		$(".filters").append($filters); //trying to only have the one DOM reflow for filters

		Array.prototype.diff = function(a) {
			return this.filter(function(i) {return a.indexOf(i) < 0;});
		};
		var star_missing = star.diff(star_found);
		if (star_missing.length) {
			console.log(star_missing);
		}

		$('.card').click(function() {
			if ($(this).hasClass('taken')) {
				$(this).removeClass('taken');
				$('.card[data-name="'+$(this).attr('data-name')+'"]').removeClass('taken');
				$.ajax({
					url: '',
					type: 'post',
					data: { remove: $(this).attr('data-name') }
				});
			} else {
				$(this).addClass('taken');
				$('.card[data-name="'+$(this).attr('data-name')+'"]').addClass('taken');
				$.ajax({
					url: '',
					type: 'post',
					data: { add: $(this).attr('data-name') }
				});
			}
		});

		if (taken.length > 0) {
			$('#content').append('<div id="button-clear">Clear Saved Picks (No longer save between page refreshes)</div>');
			$('#button-clear').click(function(){
				$.ajax({
					url: '',
					type: 'post',
					data: { clear: 1 }
				});
			});
		}


	</script>

</body>
