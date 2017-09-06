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
			/* overrides */
			.col-sm-2 { 
				font-size: 12px;
				width: 20%; 
			}
			.filters .btn.active {
				background-color: green;
			}
		</style>
	</head>
<body>

	<div id="content" class="container">
		<div id="header"><h1><a href="<?= str_replace('ff.php', '', $_SERVER['REQUEST_URI']) ?>">Flea Flicker</a></h1></div>
		<div class="row filters"><span class="hint">(deselect to hide position)</span>
		</div><!-- .row .filters -->
		<div class="row">

			<div id="row1" class="col-sm-2"></div>
			<div id="row2" class="col-sm-2"></div>
			<div id="row3" class="col-sm-2"></div>
			<div id="row4" class="col-sm-2"></div>
			<div id="row5" class="col-sm-2"></div>

		</div>
	</div>

	<script>
		taken = ["<?= isset($_SESSION['stash']) ? implode('","', array_keys($_SESSION['stash'])) : '' ?>"];
		star = ["<?= isset($_SESSION['star']) ? implode('","', $_SESSION['star']) : '' ?>"];
		depth = $.parseJSON('<?= isset($_SESSION['depth']) ? str_replace("'", "\'", json_encode($_SESSION['depth'])) : '{}' ?>');
		star_found = [];
		/* Sample 2017 NFL Draft Lists */
		lists = [
			// fantasy pros ppr.5 2017
			"David Johnson,ARI:RB;Le'Veon Bell,PIT:RB;Antonio Brown,PIT:WR;Julio Jones,ATL:WR;Odell Beckham Jr.,NYG:WR;LeSean McCoy,BUF:RB;Mike Evans,TB:WR;Melvin Gordon,LAC:RB;A.J. Green,CIN:WR;Devonta Freeman,ATL:RB;Jordy Nelson,GB:WR;Jay Ajayi,MIA:RB;DeMarco Murray,TEN:RB;Jordan Howard,CHI:RB;Michael Thomas,NO:WR;Dez Bryant,DAL:WR;Doug Baldwin,SEA:WR;Todd Gurley,LAR:RB;Amari Cooper,OAK:WR;Brandin Cooks,NE:WR;Ezekiel Elliott,DAL:RB;Rob Gronkowski,NE:TE;T.Y. Hilton,IND:WR;Demaryius Thomas,DEN:WR;Kareem Hunt,KC:RB;DeAndre Hopkins,HOU:WR;Isaiah Crowell,CLE:RB;Leonard Fournette,JAC:RB;Dalvin Cook,MIN:RB;Alshon Jeffery,PHI:WR;Terrelle Pryor,WAS:WR;Keenan Allen,LAC:WR;Carlos Hyde,SF:RB;Travis Kelce,KC:TE;Aaron Rodgers,GB:QB;Lamar Miller,HOU:RB;Marshawn Lynch,OAK:RB;Davante Adams,GB:WR;Christian McCaffrey,CAR:RB;Allen Robinson,JAC:WR;Michael Crabtree,OAK:WR;Tom Brady,NE:QB;Ty Montgomery,GB:RB;Golden Tate,DET:WR;Larry Fitzgerald,ARI:WR;Joe Mixon,CIN:RB;Greg Olsen,CAR:TE;Tyreek Hill,KC:WR;Drew Brees,NO:QB;Martavis Bryant,PIT:WR;Mark Ingram,NO:RB;Emmanuel Sanders,DEN:WR;Jordan Reed,WAS:TE;Stefon Diggs,MIN:WR;Sammy Watkins,LAR:WR;Bilal Powell,NYJ:RB;C.J. Anderson,DEN:RB;Jimmy Graham,SEA:TE;Kelvin Benjamin,CAR:WR;Ameer Abdullah,DET:RB;Jarvis Landry,MIA:WR;Jamison Crowder,WAS:WR;Russell Wilson,SEA:QB;Matt Ryan,ATL:QB;Tevin Coleman,ATL:RB;Pierre Garcon,SF:WR;Devante Parker,MIA:WR;Danny Woodhead,BAL:RB;Mike Gillislee,NE:RB;Brandon Marshall,NYG:WR;Doug Martin,TB:RB;Frank Gore,IND:RB;Tyler Eifert,CIN:TE;Kyle Rudolph,MIN:TE;Jeremy Maclin,BAL:WR;Marcus Mariota,TEN:QB;Kirk Cousins,WAS:QB;DeSean Jackson,TB:WR;Terrance West,BAL:RB;Zach Ertz,PHI:TE;Jameis Winston,TB:QB;Adrian Peterson,NO:RB;Paul Perkins,NYG:RB;Delanie Walker,TEN:TE;Tyrell Williams,LAC:WR;Cam Newton,CAR:QB;Eric Decker,TEN:WR;Robert Kelley,WAS:RB;Donte Moncrief,IND:WR;Theo Riddick,DET:RB;Ben Roethlisberger,PIT:QB;Derek Carr,OAK:QB;Derrick Henry,TEN:RB;Martellus Bennett,GB:TE;Duke Johnson,CLE:RB;Kenny Britt,CLE:WR;Willie Snead,NO:WR;Andrew Luck,IND:QB;Dak Prescott,DAL:QB;Randall Cobb,GB:WR;Philip Rivers,LAC:QB;Chris Hogan,NE:WR;Adam Thielen,MIN:WR;Jordan Matthews,BUF:WR;Hunter Henry,LAC:TE;Matthew Stafford,DET:QB;LeGarrette Blount,PHI:RB;Corey Coleman,CLE:WR;Jonathan Stewart,CAR:RB;Mike Wallace,BAL:WR;John Brown,ARI:WR;Eric Ebron,DET:TE;James White,NE:RB;Jack Doyle,IND:TE;Ted Ginn,NO:WR;Eddie Lacy,SEA:RB;Darren Sproles,PHI:RB;Andy Dalton,CIN:QB;Darren McFadden,DAL:RB;Matt Forte,NYJ:RB;Eli Manning,NYG:QB;Thomas Rawls,SEA:RB;Rishard Matthews,TEN:WR;Marvin Jones,DET:WR;Corey Davis,TEN:WR;C.J. Prosise,SEA:RB;Rex Burkhead,NE:RB;Carson Palmer,ARI:QB;Carson Wentz,PHI:QB;Coby Fleener,NO:TE;Jason Witten,DAL:TE;Kevin White,CHI:WR;Zay Jones,BUF:WR;Austin Hooper,ATL:TE;Jacquizz Rodgers,TB:RB;Jeremy Hill,CIN:RB;Tyrod Taylor,BUF:QB;Robby Anderson,NYJ:WR;Samaje Perine,WAS:RB;Sterling Shepard,NYG:WR;Cole Beasley,DAL:WR;C.J. Fiedorowicz,HOU:TE;Seattle Seahawks,SEA:DST;Denver Broncos,DEN:DST;Cameron Brate,TB:TE;Jamaal Williams,GB:RB;Houston Texans,HOU:DST;Julius Thomas,MIA:TE;Tyler Lockett,SEA:WR;Kansas City,Chiefs:DST;Giovani Bernard,CIN:RB;Jamaal Charles,DEN:RB;Kenny Stills,MIA:WR;Shane Vereen,NYG:RB;Cooper Kupp,LAR:WR;Antonio Gates,LAC:TE;Josh Doctson,WAS:WR;Alvin Kamara,NO:RB;Jay Cutler,MIA:QB;Sam Bradford,MIN:QB;Kenny Golladay,DET:WR;Chris Thompson,WAS:RB;Minnesota Vikings,MIN:DST;DeAndre Washington,OAK:RB;Marqise Lee,JAC:WR;Joe Flacco,BAL:QB;Arizona Cardinals,ARI:DST;Latavius Murray,MIN:RB;Kendall Wright,CHI:WR;New England Patriots,NE:DST;Evan Engram,NYG:TE;Devin Funchess,CAR:WR;Justin Tucker,BAL:K;Alex Smith,KC:QB;Breshad Perriman,BAL:WR;Stephen Gostkowski,NE:K;D'Onta Foreman,HOU:RB;Charles Clay,BUF:TE;Taylor Gabriel,ATL:WR;Jared Cook,OAK:TE;J.J. Nelson,ARI:WR;Jonathan Williams,FA:RB;Marlon Mack,IND:RB;O.J. Howard,TB:TE;Mohamed Sanu,ATL:WR;Torrey Smith,PHI:WR;Charles Sims,TB:RB;Dan Bailey,DAL:K;New York Giants,NYG:DST;Wendell Smallwood,PHI:RB;Blake Bortles,JAC:QB;Matt Bryant,ATL:K;John Ross,CIN:WR;Robert Woods,LAR:WR;Robert Turbin,IND:RB;Brian Hoyer,SF:QB;Carolina Panthers,CAR:DST;Danny Amendola,NE:WR;Paul Richardson,SEA:WR;Dion Lewis,NE:RB;",
			// espn analyst avg ppr 2017
			"David Johnson,ARI:RB;Le'Veon Bell,PIT:RB;Antonio Brown,PIT:WR;Odell Beckham Jr.,NYG:WR;Julio Jones,ATL:WR;Jordy Nelson,GB:WR;LeSean McCoy,BUF:RB;Mike Evans,TB:WR;A.J. Green,CIN:WR;Devonta Freeman,ATL:RB;Michael Thomas,NO:WR;Melvin Gordon,LAC:RB;Jordan Howard,CHI:RB;Brandin Cooks,NE:WR;DeMarco Murray,TEN:RB;Doug Baldwin,SEA:WR;Jay Ajayi,MIA:RB;Todd Gurley,LAR:RB;T.Y. Hilton,IND:WR;Amari Cooper,OAK:WR;Rob Gronkowski,NE:TE;Dez Bryant,DAL:WR;Ezekiel Elliott,DAL:RB;Leonard Fournette,JAC:RB;Demaryius Thomas,DEN:WR;Lamar Miller,HOU:RB;Terrelle Pryor Sr.,WAS:WR;DeAndre Hopkins,HOU:WR;Alshon Jeffery,PHI:WR;Isaiah Crowell,CLE:RB;Michael Crabtree,OAK:WR;Keenan Allen,LAC:WR;Kareem Hunt,KC:RB;Golden Tate,DET:WR;Christian McCaffrey,CAR:RB;Carlos Hyde,SF:RB;Marshawn Lynch,OAK:RB;Larry Fitzgerald,ARI:WR;Ty Montgomery,GB:RB;Dalvin Cook,MIN:RB;Jordan Reed,WAS:TE;Davante Adams,GB:WR;Emmanuel Sanders,DEN:WR;Tom Brady,NE:QB;Aaron Rodgers,GB:QB;Jarvis Landry,MIA:WR;Kelvin Benjamin,CAR:WR;Travis Kelce,KC:TE;Jamison Crowder,WAS:WR;Bilal Powell,NYJ:RB;Danny Woodhead,BAL:RB;Allen Robinson,JAC:WR;Tyreek Hill,KC:WR;Joe Mixon,CIN:RB;Greg Olsen,CAR:TE;Sammy Watkins,LAR:WR;Drew Brees,NO:QB;Pierre Garcon,SF:WR;Martavis Bryant,PIT:WR;Stefon Diggs,MIN:WR;Mark Ingram,NO:RB;Frank Gore,IND:RB;Brandon Marshall,NYG:WR;DeVante Parker,MIA:WR;Theo Riddick,DET:RB;Mike Gillislee,NE:RB;Matt Ryan,ATL:QB;Rob Kelley,WAS:RB;Adrian Peterson,NO:RB;C.J. Anderson,DEN:RB;Ameer Abdullah,DET:RB;Paul Perkins,NYG:RB;Jeremy Maclin,BAL:WR;Eric Decker,TEN:WR;DeSean Jackson,TB:WR;Terrance West,BAL:RB;Duke Johnson Jr.,CLE:RB;Jimmy Graham,SEA:TE;Russell Wilson,SEA:QB;Mike Wallace,BAL:WR;Donte Moncrief,IND:WR;Kenny Britt,CLE:WR;Adam Thielen,MIN:WR;Tevin Coleman,ATL:RB;Delanie Walker,TEN:TE;Kyle Rudolph,MIN:TE;Tyrell Williams,LAC:WR;Matt Forte,NYJ:RB;Eddie Lacy,SEA:RB;Chris Hogan,NE:WR;Kirk Cousins,WAS:QB;Cam Newton,CAR:QB;James White,NE:RB;Willie Snead,NO:WR;Darren McFadden,DAL:RB;Zach Ertz,PHI:TE;Doug Martin,TB:RB;Jonathan Stewart,CAR:RB;Rishard Matthews,TEN:WR;Randall Cobb,GB:WR;Darren Sproles,PHI:RB;LeGarrette Blount,PHI:RB;Zay Jones,BUF:WR;Thomas Rawls,SEA:RB;Dak Prescott,DAL:QB;Giovani Bernard,CIN:RB;Andrew Luck,IND:QB;Jordan Matthews,BUF:WR;Corey Coleman,CLE:WR;Tyler Eifert,CIN:TE;C.J. Prosise,SEA:RB;Jameis Winston,TB:QB;Jacquizz Rodgers,TB:RB;Kevin White,CHI:WR;Marcus Mariota,TEN:QB;Corey Davis,TEN:WR;Derek Carr,OAK:QB;Ted Ginn Jr.,NO:WR;John Brown,ARI:WR;Matthew Stafford,DET:QB;Chris Thompson,WAS:RB;Martellus Bennett,GB:TE;Marvin Jones Jr.,DET:WR;Rex Burkhead,NE:RB;Derrick Henry,TEN:RB;Danny Amendola,NE:WR;Hunter Henry,LAC:TE;Josh Doctson,WAS:WR;Ben Roethlisberger,PIT:QB;Broncos D/ST,DEN:D/ST;Seahawks D/ST,SEA:D/ST;Philip Rivers,LAC:QB;Jack Doyle,IND:TE;Chiefs D/ST,KC:D/ST;Texans D/ST,HOU:D/ST;Vikings D/ST,MIN:D/ST;Latavius Murray,MIN:RB;Shane Vereen,NYG:RB;Justin Tucker,BAL:K;Matt Bryant,ATL:K;Stephen Gostkowski,NE:K;Sterling Shepard,NYG:WR;Adam Vinatieri,IND:K;Dan Bailey,DAL:K;Charles Sims,TB:RB;Cardinals D/ST,ARI:D/ST;Marqise Lee,JAC:WR;Alvin Kamara,NO:RB;Matt Prater,DET:K;Patriots D/ST,NE:D/ST;Charcandrick West,KC:RB;Dustin Hopkins,WAS:K;Eric Ebron,DET:TE;Kendall Wright,CHI:WR;Torrey Smith,PHI:WR;Jeremy Hill,CIN:RB;D'Onta Foreman,HOU:RB;Andy Dalton,CIN:QB;Tyrod Taylor,BUF:QB;Panthers D/ST,CAR:D/ST;Jason Witten,DAL:TE;Kenny Stills,MIA:WR;Mason Crosby,GB:K;Carson Wentz,PHI:QB;Wendell Smallwood,PHI:RB;Jaguars D/ST,JAC:D/ST;Bengals D/ST,CIN:D/ST;Samaje Perine,WAS:RB;Cairo Santos,KC:K;Tyler Lockett,SEA:WR;Austin Hooper,ATL:TE;Jamaal Williams,GB:RB;Robert Woods,LAR:WR;Dion Lewis,NE:RB;Cooper Kupp,LAR:WR;DeAndre Washington,OAK:RB;Cole Beasley,DAL:WR;Jamaal Charles,DEN:RB;Mohamed Sanu,ATL:WR;Giants D/ST,NYG:D/ST;Jalen Richard,OAK:RB;Eagles D/ST,PHI:D/ST;Wil Lutz,NO:K;James Conner,PIT:RB;Taylor Gabriel,ATL:WR;Devontae Booker,DEN:RB;Devin Funchess,CAR:WR;Eli Manning,NYG:QB;Carson Palmer,ARI:QB;Cameron Brate,TB:TE;Chris Boswell,PIT:K;Caleb Sturgis,PHI:K;Breshad Perriman,BAL:WR;Julius Thomas,MIA:TE;John Ross,CIN:WR;J.J. Nelson,ARI:WR;Curtis Samuel,CAR:WR;Will Fuller V,HOU:WR;Marlon Mack,IND:RB;De'Angelo Henderson,DEN:RB;",
			// eat drink sleep football ppr 2017
			"Le'Veon Bell,PIT:RB;David Johnson,ARI:RB;Antonio Brown,PIT:WR;Julio Jones,ATL:WR;Odell Beckham,NYG:WR;LeSean McCoy,BUF:RB;Jordy Nelson,GB:WR;Mike Evans,TB:WR;A.J. Green,CIN:WR;Devonta Freeman,ATL:RB;Melvin Gordon,LAC:RB;DeMarco Murray,TEN:RB;Michael Thomas,NO:WR;Jay Ajayi,MIA:RB;Ezekiel Elliott,DAL:RB;Rob Gronkowski,NE:TE;Doug Baldwin,SEA:WR;Dez Bryant,DAL:WR;Demaryius Thomas,DEN:WR;Keenan Allen,LAC:WR;Amari Cooper,OAK:WR;Brandin Cooks,NE:WR;Jordan Howard,CHI:RB;Alshon Jeffery,PHI:WR;Todd Gurley,LA:RB;Leonard Fournette,JAX:RB;Travis Kelce,KC:TE;T.Y. Hilton,IND:WR;Kareem Hunt,KC:RB;Isaiah Crowell,CLE:RB;DeAndre Hopkins,HOU:WR;Lamar Miller,HOU:RB;Larry Fitzgerald,ARI:WR;Terrelle Pryor,WAS:WR;Aaron Rodgers,GB:QB;Michael Crabtree,OAK:WR;Mark Ingram,NO:RB;Tom Brady,NE:QB;Dalvin Cook,MIN:RB;Christian McCaffrey,CAR:RB;Jordan Reed,WAS:TE;Allen Robinson,JAX:WR;Davante Adams,GB:WR;Emmanuel Sanders,DEN:WR;Golden Tate,DET:WR;Carlos Hyde,SF:RB;Greg Olsen,CAR:TE;Martavis Bryant,PIT:WR;Danny Woodhead,BAL:RB;Ty Montgomery,GB:RB;Joe Mixon,CIN:RB;Marshawn Lynch,OAK:RB;Bilal Powell,NYJ:RB;Drew Brees,NO:QB;Stefon Diggs,MIN:WR;Jamison Crowder,WAS:WR;Jimmy Graham,SEA:TE;Tevin Coleman,ATL:RB;Ameer Abdullah,DET:RB;Tyreek Hill,KC:WR;Kelvin Benjamin,CAR:WR;Jarvis Landry,MIA:WR;DeVante Parker,MIA:WR;Adrian Peterson,NO:RB;Sammy Watkins,LA:WR;Marcus Mariota,TEN:QB;Russell Wilson,SEA:QB;Matt Ryan,ATL:QB;Brandon Marshall,NYG:WR;Pierre Garcon,SF:WR;Theo Riddick,DET:RB;C.J. Anderson,DEN:RB;Kyle Rudolph,MIN:TE;Tyler Eifert,CIN:TE;Jeremy Maclin,BAL:WR;Mike Gillislee,NE:RB;Frank Gore,IND:RB;DeSean Jackson,TB:WR;Doug Martin,TB:RB;Corey Coleman,CLE:WR;James White,NE:RB;Donte Moncrief,IND:WR;Delanie Walker,TEN:TE;Zach Ertz,PHI:TE;Jordan Matthews,BUF:WR;Cam Newton,CAR:QB;Kirk Cousins,WAS:QB;Tyrell Williams,LAC:WR;Eric Decker,TEN:WR;Martellus Bennett,GB:TE;Paul Perkins,NYG:RB;Corey Davis,TEN:WR;Rishard Matthews,TEN:WR;Jameis Winston,TB:QB;Darren Sproles,PHI:RB;Kenny Britt,CLE:WR;Thomas Rawls,SEA:RB;Terrance West,BAL:RB;Willie Snead,NO:WR;Mike Wallace,BAL:WR;Hunter Henry,LAC:TE;Randall Cobb,GB:WR;Robby Anderson,NYJ:WR;Chris Hogan,NE:WR;Jack Doyle,IND:TE;Derrick Henry,TEN:RB;C.J. Prosise,SEA:RB;Derek Carr,OAK:QB;Matt Forte,NYJ:RB;Duke Johnson,CLE:RB;Ben Roethlisberger,PIT:QB;Adam Thielen,MIN:WR;Jeremy Hill,CIN:RB;Eric Ebron,DET:TE;Philip Rivers,LAC:QB;Darren McFadden,DAL:RB;Dak Prescott,DAL:QB;Zay Jones,BUF:WR;Eddie Lacy,SEA:RB;Ted Ginn,NO:WR;LeGarrette Blount,PHI:RB;Giovani Bernard,CIN:RB;Robert Kelley,WAS:RB;Jonathan Stewart,CAR:RB;Jacquizz Rodgers,TB:RB;Jamaal Williams,GB:RB;John Brown,ARI:WR;Kevin White,CHI:WR;Cooper Kupp,LA:WR;Kendall Wright,CHI:WR;Andrew Luck,IND:QB;Andy Dalton,CIN:QB;Eli Manning,NYG:QB;C.J. Fiedorowicz,HOU:TE;Coby Fleener,NO:TE;Jason Witten,DAL:TE;Matthew Stafford,DET:QB;Josh Doctson,WAS:WR;Tyrod Taylor,BUF:QB;Cameron Brate,TB:TE;Jamaal Charles,DEN:RB;Rex Burkhead,NE:RB;Antonio Gates,LAC:TE;Tyler Lockett,SEA:WR;Marvin Jones,DET:WR;Sterling Shepard,NYG:WR;Samaje Perine,WAS:RB;Alvin Kamara,NO:RB;Carson Palmer,ARI:QB;Julius Thomas,MIA:TE;Dion Lewis,NE:RB;Robert Turbin,IND:RB;Austin Hooper,ATL:TE;Cole Beasley,DAL:WR;Chris Conley,KC:WR;Chris Thompson,WAS:RB;Latavius Murray,MIN:RB;Carson Wentz,PHI:QB;Charles Sims,TB:RB;Chris Carson,SEA:RB;Jared Cook,OAK:TE;Joe Flacco,BAL:QB;Kenny Golladay,DET:WR;Kenny Stills,MIA:WR;Breshad Perriman,BAL:WR;Charcandrick West,KC:RB;Charles Clay,BUF:TE;DeAndre Washington,OAK:RB;Marlon Mack,IND:RB;Devontae Booker,DEN:RB;Marqise Lee,JAX:WR;Jesse James,PIT:TE;O.J. Howard,TB:TE;David Njoku,CLE:TE;Jay Cutler,MIA:QB;Tyler Higbee,LA:TE;Denver Broncos DST,DEN:DST;Houston Texans DST,HOU:DST;Seattle Seahawks DST,SEA:DST;Arizona Cardinals DST,ARI:DST;Minnesota Vikings DST,MIN:DST;Kansas City Chiefs DST,KC:DST;New England Patriots DST,NE:DST;New York Giants DST,NYG:DST;Carolina Panthers DST,CAR:DST;Jacksonville Jaguars DST,JAX:DST;Los Angeles Rams DST,LA:DST;Pittsburgh Steelers DST,PIT:DST;Justin Tucker,BAL:K;Stephen Gostkowski,NE:K;Dan Bailey,DAL:K;Mason Crosby,GB:K;Matt Bryant,ATL:K;Adam Vinatieri,IND:K;Brandon McManus,DEN:K;Cairo Santos,KC:K;Sebastian Janikowski,OAK:K;Steven Hauschka,BUF:K;Matt Prater,DET:K;Dustin Hopkins,WAS:K;"
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
				card = card.replace(',',' ').replace(/&nbsp;/g, ' ').trim();
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
