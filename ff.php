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
		/* Sample 2015 NFL Draft Lists */
		lists = [
			// fantasy pros PPR.5 2016 -- tended to ignore injuries etc and be a little inconsistent with the rest
			"Antonio Brown,PIT:WR1;Julio Jones,ATL:WR2;Odell Beckham Jr.,NYG:WR3;David Johnson,ARI:RB1;Todd Gurley,LA:RB2;DeAndre Hopkins,HOU:WR4;A.J. Green,CIN:WR5;Adrian Peterson,MIN:RB3;Ezekiel Elliott,DAL:RB4;Rob Gronkowski,NE:TE1;Lamar Miller,HOU:RB5;Dez Bryant,DAL:WR6;Allen Robinson,JAC:WR7;Jamaal Charles,KC:RB6;Brandon Marshall,NYJ:WR8;Mike Evans,TB:WR9;Devonta Freeman,ATL:RB7;Le'Veon Bell,PIT:RB8;Keenan Allen,SD:WR10;Jordy Nelson,GB:WR11;Alshon Jeffery,CHI:WR12;Mark Ingram,NO:RB9;Brandin Cooks,NO:WR13;LeSean McCoy,BUF:RB10;Amari Cooper,OAK:WR14;Doug Martin,TB:RB11;Eddie Lacy,GB:RB12;Demaryius Thomas,DEN:WR15;T.Y. Hilton,IND:WR16;C.J. Anderson,DEN:RB13;Sammy Watkins,BUF:WR17;Cam Newton,CAR:QB1;Randall Cobb,GB:WR18;Aaron Rodgers,GB:QB2;Jordan Reed,WAS:TE2;Carlos Hyde,SF:RB14;Jeremy Maclin,KC:WR19;Jarvis Landry,MIA:WR20;Julian Edelman,NE:WR21;Latavius Murray,OAK:RB15;Eric Decker,NYJ:WR22;Thomas Rawls,SEA:RB16;Matt Forte,NYJ:RB17;Golden Tate,DET:WR23;Greg Olsen,CAR:TE3;Russell Wilson,SEA:QB3;Dion Lewis,NE:RB18;Andrew Luck,IND:QB4;Doug Baldwin,SEA:WR24;Donte Moncrief,IND:WR25;Kelvin Benjamin,CAR:WR26;DeMarco Murray,TEN:RB19;Michael Floyd,ARI:WR27;Jeremy Hill,CIN:RB20;Drew Brees,NO:QB5;Jordan Matthews,PHI:WR28;Larry Fitzgerald,ARI:WR29;Ryan Mathews,PHI:RB21;Travis Kelce,KC:TE4;Jonathan Stewart,CAR:RB22;Giovani Bernard,CIN:RB23;Duke Johnson,CLE:RB24;Emmanuel Sanders,DEN:WR30;Frank Gore,IND:RB25;John Brown,ARI:WR31;Delanie Walker,TEN:TE5;Danny Woodhead,SD:RB26;Matt Jones,WAS:RB27;Ben Roethlisberger,PIT:QB6;Coby Fleener,NO:TE6;Jeremy Langford,CHI:RB28;Devante Parker,MIA:WR32;Melvin Gordon,SD:RB29;Carson Palmer,ARI:QB7;Marvin Jones,DET:WR33;Allen Hurns,JAC:WR34;Michael Crabtree,OAK:WR35;Tyler Lockett,SEA:WR36;Arian Foster,MIA:RB30;DeSean Jackson,WAS:WR37;Ameer Abdullah,DET:RB31;Eli Manning,NYG:QB8;Rashad Jennings,NYG:RB32;Gary Barnidge,CLE:TE7;Chris Ivory,JAC:RB33;Tyler Eifert,CIN:TE8;Blake Bortles,JAC:QB9;Philip Rivers,SD:QB10;Zach Ertz,PHI:TE9;Kevin White,CHI:WR38;Julius Thomas,JAC:TE10;T.J. Yeldon,JAC:RB34;Tom Brady,NE:QB11;Josh Gordon,CLE:WR39;Charles Sims,TB:RB35;Corey Coleman,CLE:WR40;Sterling Shepard,NYG:WR41;Antonio Gates,SD:TE11;DeAngelo Williams,PIT:RB36;Willie Snead,NO:WR42;Torrey Smith,SF:WR43;Stefon Diggs,MIN:WR44;Jay Ajayi,MIA:RB37;Justin Forsett,BAL:RB38;Vincent Jackson,TB:WR45;Tavon Austin,LA:WR46;Isaiah Crowell,CLE:RB39;Dwayne Allen,IND:TE12;Travis Benjamin,SD:WR47;Bilal Powell,NYJ:RB40;Theo Riddick,DET:RB41;Tyrod Taylor,BUF:QB12;Derek Carr,OAK:QB13;Kamar Aiken,BAL:WR48;Kirk Cousins,WAS:QB14;Tony Romo,DAL:QB15;Markus Wheaton,PIT:WR49;Derrick Henry,TEN:RB42;Tevin Coleman,ATL:RB43;Martellus Bennett,NE:TE13;Jameis Winston,TB:QB16;Devin Funchess,CAR:WR50;Matthew Stafford,DET:QB17;Andy Dalton,CIN:QB18;Steve Smith,BAL:WR51;Jason Witten,DAL:TE14;LeGarrette Blount,NE:RB44;Eric Ebron,DET:TE15;Marcus Mariota,TEN:QB19;Laquon Treadwell,MIN:WR52;Michael Thomas,NO:WR53;Matt Ryan,ATL:QB20;Jimmy Graham,SEA:TE16;Mohamed Sanu,ATL:WR54;Phillip Dorsett,IND:WR55;Seattle Seahawks,:DST1;Sammie Coates,PIT:WR56;Zach Miller,CHI:TE17;Ryan Tannehill,MIA:QB21;Denver Broncos,:DST2;Rishard Matthews,TEN:WR57;Ladarius Green,PIT:TE18;Ryan Fitzpatrick,NYJ:QB22;Shane Vereen,NYG:RB45;Dorial Green-Beckham,PHI:WR58;Kenneth Dixon,BAL:RB46;Jerick McKinnon,MIN:RB47;Darren Sproles,PHI:RB48;DeAndre Washington,OAK:RB49;Javorius Allen,BAL:RB50;C.J. Prosise,SEA:RB51;Kendall Wright,TEN:WR59;Jordan Howard,CHI:RB52;Arizona Cardinals,:DST3;Charles Clay,BUF:TE19;Pierre Garcon,WAS:WR60;Mike Wallace,BAL:WR61;Terrance Williams,DAL:WR62;Carolina Panthers,:DST4;Clive Walford,OAK:TE20;Devontae Booker,DEN:RB53;James Starks,GB:RB54;Jay Cutler,CHI:QB23;Josh Doctson,WAS:WR63;Joe Flacco,BAL:QB24;Jordan Cameron,MIA:TE21;Tyler Boyd,CIN:WR64;Anquan Boldin,DET:WR65;Darren McFadden,DAL:RB55;Bruce Ellington,SF:WR66;Houston Texans,:DST5;Austin Seferian-Jenkins,TB:TE22;Christine Michael,SEA:RB56;Chris Thompson,WAS:RB57;Karlos Williams,FA:RB58;Jared Cook,GB:TE23;Nelson Agholor,PHI:WR67;Chris Hogan,NE:WR68;Alex Smith,KC:QB25;Kyle Rudolph,MIN:TE24;Chris Johnson,ARI:RB59;Breshad Perriman,BAL:WR69;Will Tye,NYG:TE25;Vance McDonald,SF:TE26;Spencer Ware,KC:RB60;Los Angeles Rams,:DST6;Paul Perkins,NYG:RB61;Benjamin Watson,BAL:TE27;Ted Ginn,CAR:WR70;Tim Hightower,NO:RB62;Wendell Smallwood,PHI:RB63;Shaun Draughn,SF:RB64;Terrance West,BAL:RB65;Will Fuller,HOU:WR71;Robert Woods,BUF:WR72;Brandon LaFell,CIN:WR73;Alfred Morris,DAL:RB66;Robert Griffin,CLE:QB26;Josh Ferguson,IND:RB67;Tajae Sharpe,TEN:WR74;New England Patriots,:DST7;Davante Adams,GB:WR75;Kansas City Chiefs,:DST8;Brock Osweiler,HOU:QB27;Minnesota Vikings,:DST9;Rueben Randle,PHI:WR76;Charcandrick West,KC:RB68;Jaelen Strong,HOU:WR77;Cincinnati Bengals,:DST10;C.J. Spiller,NO:RB69;New York Jets,:DST11;Andre Ellington,ARI:RB70;Kenny Britt,LA:WR78;Cameron Artis-Payne,CAR:RB71;Buffalo Bills,:DST12;Jermaine Kearse,SEA:WR79;Richard Rodgers,GB:TE28;Ronnie Hillman,DEN:RB72;Jamison Crowder,WAS:WR80;Keith Marshall,WAS:RB73;Teddy Bridgewater,MIN:QB28;Kenyan Drake,MIA:RB74;Seth Roberts,OAK:WR81;Charles Johnson,MIN:WR82;Cameron Brate,TB:TE29;James White,NE:RB75;Terrelle Pryor,CLE:WR83;Danny Amendola,NE:WR84;Mike Gillislee,BUF:RB76;Philadelphia Eagles,:DST13;Jeff Janis,GB:WR85;Victor Cruz,NYG:WR86;Kenjon Barner,PHI:RB77;Oakland Raiders,:DST14;Ka'Deem Carey,CHI:RB78;Darrius Heyward-Bey,PIT:WR87;Justin Hardy,ATL:WR88;Sam Bradford,PHI:QB29;Chris Conley,KC:WR89;Lance Dunbar,DAL:RB79;Jace Amaro,NYJ:TE30;Austin Hooper,ATL:TE31;Benny Cunningham,LA:RB80;Mike Thomas,LA:WR90;Andre Johnson,TEN:WR91;Pittsburgh Steelers,:DST15;Zach Zenner,DET:RB81;Jesse James,PIT:TE32;Malcolm Mitchell,NE:WR92;Blaine Gabbert,SF:QB30;Tyler Ervin,HOU:RB82;Mark Sanchez,DEN:QB31;David Cobb,TEN:RB83;Kenny Stills,MIA:WR93;Khiry Robinson,NYJ:RB84;Cole Beasley,DAL:WR94;Robert Turbin,IND:RB85;Mike Davis,SF:RB86;Jacob Tamme,ATL:TE33;Tyler Higbee,LA:TE34;Virgil Green,DEN:TE35;Green Bay Packers,:DST16;New York Giants,:DST17;Stephen Gostkowski,NE:K1;Nate Washington,NE:WR95;Pharoh Cooper,LA:WR96;Hunter Henry,SD:TE36;Quinton Patton,SF:WR97;Jacksonville Jaguars,:DST18;Vernon Davis,WAS:TE37;Eddie Royal,CHI:WR98;Brian Quick,LA:WR99;TJ Jones,DET:WR100;DeAndre Smelter,SF:WR101;Jonathan Williams,BUF:RB87;Brian Hartline,FA:WR102;Jared Goff,LA:QB32;Brandon Coleman,NO:WR103;Lance Kendricks,LA:TE38;James Jones,SD:WR104;Tyrell Williams,SD:WR105;Reggie Bush,BUF:RB88;Dontrelle Inman,SD:WR106;Maxx Williams,BAL:TE39;Alfred Blue,HOU:RB89;Albert Wilson,KC:WR107;Owen Daniels,FA:TE40;Jeff Heuerman,DEN:TE41;Steven Hauschka,SEA:K2;Justin Tucker,BAL:K3;Larry Donnell,NYG:TE42;Dexter McCluster,TEN:RB90;Dan Bailey,DAL:K4;MyCole Pruitt,MIN:TE43;Mason Crosby,GB:K5;Antonio Andrews,TEN:RB91;Miami Dolphins,:DST19;Washington Redskins,:DST20;Chandler Catanzaro,ARI:K6;Marques Colston,FA:WR108;Brandon McManus,DEN:K7;Paxton Lynch,DEN:QB33;Adam Vinatieri,IND:K8;Graham Gano,CAR:K9;Nick Foles,KC:QB34;Blair Walsh,MIN:K10;Colin Kaepernick,SF:QB35;Cairo Santos,KC:K11;Andre Williams,NYG:RB92;Cody Latimer,DEN:WR109;Jimmy Garoppolo,NE:QB36;Philly Brown,CAR:WR110;Cecil Shorts,HOU:WR111;Leonte Carroo,MIA:WR112;Trevor Siemian,DEN:QB37;Stevan Ridley,DET:RB93;Tre Mason,LA:RB94;J.J. Nelson,ARI:WR113;Ryan Griffin,HOU:TE44;Braxton Miller,HOU:WR114;Crockett Gillmore,BAL:TE45;Geno Smith,NYJ:QB38;Ty Montgomery,GB:WR115;Alex Collins,SEA:RB95;Darren Fells,ARI:TE46;Josh McCown,CLE:QB39;Devin Smith,NYJ:WR116;Leonard Hankerson,FA:WR117;Carson Wentz,PHI:QB40;Josh Hill,NO:TE47;Luke Willson,SEA:TE48;Garrett Celek,SF:TE49;Rashard Higgins,CLE:WR118;New Orleans Saints,:DST21;Roberto Aguayo,TB:K12;Denard Robinson,JAC:RB96;Josh Brown,NYG:K13;Matt Bryant,ATL:K14;Detroit Lions,:DST22;Baltimore Ravens,:DST23;Chris Boswell,PIT:K15;Cleveland Browns,:DST24;Matt Prater,DET:K16;Nick Novak,HOU:K17;Chicago Bears,:DST25;Indianapolis Colts,:DST26;Tampa Bay Buccaneers,:DST27;Robbie Gould,CHI:K18;Atlanta Falcons,:DST28;Dallas Cowboys,:DST29;San Francisco 49ers,:DST30;San Diego Chargers,:DST31;Tennessee Titans,:DST32;",
			// espn mike clay PPR 2016
			"Antonio Brown,PIT:WR; Julio Jones,ATL:WR; Odell Beckham Jr.,NYG:WR; DeAndre Hopkins,HOU:WR; David Johnson,ARI:RB; Todd Gurley,LA:RB; A.J. Green,CIN:WR; Ezekiel Elliott,DAL:RB; Adrian Peterson,MIN:RB; Dez Bryant,DAL:WR; Allen Robinson,JAC:WR; Le'Veon Bell,PIT:RB; Devonta Freeman,ATL:RB; Rob Gronkowski,NE:TE; Lamar Miller,HOU:RB; Brandon Marshall,NYJ:WR; Alshon Jeffery,CHI:WR; Keenan Allen,SD:WR; Jordy Nelson,GB:WR; Sammy Watkins,BUF:WR; Mike Evans,TB:WR; Jamaal Charles,KC:RB; Mark Ingram,NO:RB; LeSean McCoy,BUF:RB; Brandin Cooks,NO:WR; Jarvis Landry,MIA:WR; Amari Cooper,OAK:WR; Jordan Reed,WAS:TE; Danny Woodhead,SD:RB; Doug Martin,TB:RB; Eddie Lacy,GB:RB; Carlos Hyde,SF:RB; Eric Decker,NYJ:WR; Julian Edelman,NE:WR; T.Y. Hilton,IND:WR; Jeremy Maclin,KC:WR; C.J. Anderson,DEN:RB; Jonathan Stewart,CAR:RB; Thomas Rawls,SEA:RB; DeMarco Murray,TEN:RB; Demaryius Thomas,DEN:WR; Cam Newton,CAR:QB; Golden Tate,DET:WR; Randall Cobb,GB:WR; Emmanuel Sanders,DEN:WR; Donte Moncrief,IND:WR; Jordan Matthews,PHI:WR; Greg Olsen,CAR:TE; Russell Wilson,SEA:QB; Aaron Rodgers,GB:QB; Michael Floyd,ARI:WR; Larry Fitzgerald,ARI:WR; John Brown,ARI:WR; Doug Baldwin,SEA:WR; Latavius Murray,OAK:RB; Ryan Mathews,PHI:RB; Frank Gore,IND:RB; Dion Lewis,NE:RB; Matt Forte,NYJ:RB; Delanie Walker,TEN:TE; Andrew Luck,IND:QB; Duke Johnson Jr.,CLE:RB; Giovani Bernard,CIN:RB; Arian Foster,MIA:RB; Melvin Gordon,SD:RB; Theo Riddick,DET:RB; Rashad Jennings,NYG:RB; Ben Roethlisberger,PIT:QB; Drew Brees,NO:QB; DeVante Parker,MIA:WR; Marvin Jones,DET:WR; Kelvin Benjamin,CAR:WR; Ameer Abdullah,DET:RB; Matt Jones,WAS:RB; Jeremy Hill,CIN:RB; Jeremy Langford,CHI:RB; Bilal Powell,NYJ:RB; Tom Brady,NE:QB; Carson Palmer,ARI:QB; Michael Crabtree,OAK:WR; Tyler Lockett,SEA:WR; Sterling Shepard,NYG:WR; Willie Snead,NO:WR; Torrey Smith,SF:WR; Tavon Austin,LA:WR; Kevin White,CHI:WR; DeSean Jackson,WAS:WR; Eli Manning,NYG:QB; Philip Rivers,SD:QB; Tyrod Taylor,BUF:QB; Coby Fleener,NO:TE; Travis Kelce,KC:TE; Tyler Eifert,CIN:TE; Charles Sims,TB:RB; T.J. Yeldon,JAC:RB; Stefon Diggs,MIN:WR; Corey Coleman,CLE:WR; Devin Funchess,CAR:WR; Mohamed Sanu,ATL:WR; Allen Hurns,JAC:WR; DeAngelo Williams,PIT:RB; Chris Ivory,JAC:RB; Derrick Henry,TEN:RB; Tevin Coleman,ATL:RB; Darren Sproles,PHI:RB; LeGarrette Blount,NE:RB; Shane Vereen,NYG:RB; James Starks,GB:RB; Zach Ertz,PHI:TE; Gary Barnidge,CLE:TE; Antonio Gates,SD:TE; Vincent Jackson,TB:WR; Michael Thomas,NO:WR; Josh Gordon,CLE:WR; Travis Benjamin,SD:WR; Markus Wheaton,PIT:WR; Will Fuller,HOU:WR; Kamar Aiken,BAL:WR; Phillip Dorsett,IND:WR; Steve Smith Sr.,BAL:WR; Rishard Matthews,TEN:WR; Bruce Ellington,SF:WR; Justin Forsett,BAL:RB; Isaiah Crowell,CLE:RB; DeAndre Washington,OAK:RB; Jerick McKinnon,MIN:RB; Sammie Coates,PIT:WR; Jaelen Strong,HOU:WR; Shaun Draughn,SF:RB; Jay Ajayi,MIA:RB; Kirk Cousins,WAS:QB; Matthew Stafford,DET:QB; Tony Romo,DAL:QB; Blake Bortles,JAC:QB; Javorius Allen,BAL:RB; Devontae Booker,DEN:RB; Julius Thomas,JAC:TE; Pierre Garcon,WAS:WR; Terrance Williams,DAL:WR; Martellus Bennett,NE:TE; Seattle Seahawks,SEA:DST; Cincinnati Bengals,CIN:DST; Denver Broncos,DEN:DST; Carolina Panthers,CAR:DST; Houston Texans,HOU:DST; Arizona Cardinals,ARI:DST; Kansas City Chiefs,KC:DST; Stephen Gostkowski,NE:K; New York Jets,NYJ:DST; Green Bay Packers,GB:DST; Oakland Raiders,OAK:DST; Steven Hauschka,SEA:K; Dan Bailey,DAL:K; Justin Tucker,BAL:K; Adam Vinatieri,IND:K; Chris Boswell,PIT:K; Mason Crosby,GB:K; Graham Gano,CAR:K; Blair Walsh,MIN:K; Chandler Catanzaro,ARI:K; Christine Michael,SEA:RB; C.J. Prosise,SEA:RB; Chris Thompson,WAS:RB; Mike Wallace,BAL:WR; Eric Ebron,DET:TE; Jason Witten,DAL:TE; Zach Miller,CHI:TE; Nelson Agholor,PHI:WR; Andy Dalton,CIN:QB; Jameis Winston,TB:QB; Derek Carr,OAK:QB; Terrance West,BAL:RB; Tim Hightower,NO:RB; Charles Clay,BUF:TE; Clive Walford,OAK:TE; Benjamin Cunningham,LA:RB; C.J. Spiller,NO:RB; Ka'Deem Carey,CHI:RB; Dwayne Allen,IND:TE; Jimmy Graham,SEA:TE; Anquan Boldin,DET:WR; Tyler Boyd,CIN:WR; Jamison Crowder,WAS:WR; Tajae Sharpe,TEN:WR; Laquon Treadwell,MIN:WR; Spencer Ware,KC:RB; Dorial Green-Beckham,PHI:WR; Davante Adams,GB:WR; Minnesota Vikings,MIN:DST; New England Patriots,NE:DST; Matt Prater,DET:K; Roberto Aguayo,TB:K; Ryan Fitzpatrick,NYJ:QB; Marcus Mariota,TEN:QB; Ryan Tannehill,MIA:QB; Chris Hogan,NE:WR; Josh Doctson,WAS:WR; Chris Conley,KC:WR; Josh Ferguson,IND:RB; Wendell Smallwood,PHI:RB; Jordan Howard,CHI:RB; Kenyan Drake,MIA:RB; Chris Johnson,ARI:RB; Paul Perkins,NYG:RB; Alfred Morris,DAL:RB; Charcandrick West,KC:RB; Cameron Artis-Payne,CAR:RB; Kenneth Dixon,BAL:RB; Benjamin Watson,BAL:TE; Jared Cook,GB:TE; Kyle Rudolph,MIN:TE; Kenny Britt,LA:WR; Kendall Wright,TEN:WR; Breshad Perriman,BAL:WR; Charles Johnson,MIN:WR; Ted Ginn Jr.,CAR:WR; Cole Beasley,DAL:WR; Malcolm Mitchell,NE:WR; Joe Flacco,BAL:QB; Matt Ryan,ATL:QB; Buffalo Bills,BUF:DST; Los Angeles Rams,LA:DST; Josh Brown,NYG:K; Cairo Santos,KC:K; Robert Woods,BUF:WR; Seth Roberts,OAK:WR; Kenny Stills,MIA:WR; Marqise Lee,JAC:WR; Jermaine Kearse,SEA:WR; Terrelle Pryor,CLE:WR; Justin Hardy,ATL:WR; J.J. Nelson,ARI:WR; Braxton Miller,HOU:WR; Quinton Patton,SF:WR; Andre Ellington,ARI:RB; Karlos Williams,BUF:RB; James White,NE:RB; Keith Marshall,WAS:RB; Tyler Ervin,HOU:RB; Branden Oliver,SD:RB; Lance Dunbar,DAL:RB; Zach Zenner,DET:RB; Alex Smith,KC:QB; Brock Osweiler,HOU:QB; Jay Cutler,CHI:QB; Jordan Cameron,MIA:TE; Austin Seferian-Jenkins,TB:TE; Jesse James,PIT:TE; Will Tye,NYG:TE; Khiry Robinson,NYJ:RB; Tyrell Williams,SD:WR; Leonte Carroo,MIA:WR; Jacksonville Jaguars,JAC:DST; Tampa Bay Buccaneers,TB:DST; Robbie Gould,CHI:K; Brandon McManus,DEN:K; Vance McDonald,SF:TE; Jacob Tamme,ATL:TE; Jace Amaro,NYJ:TE; Quincy Enunwa,NYJ:WR; Pharoh Cooper,LA:WR; Brandon LaFell,CIN:WR; Victor Cruz,NYG:WR; Darren McFadden,DAL:RB; Reggie Bush,BUF:RB; Teddy Bridgewater,MIN:QB; Robert Griffin,CLE:QB; Sam Bradford,PHI:QB; Colin Kaepernick,SF:QB; Jared Goff,LA:QB; Rashad Greene,JAC:WR; Albert Wilson,KC:WR; Eddie Royal,CHI:WR; Rashard Higgins,CLE:WR; Ty Montgomery,GB:WR; Jacquizz Rodgers,CHI:RB; Malcolm Brown,LA:RB; Dexter McCluster,TEN:RB; Robert Turbin,IND:RB; Alfred Blue,HOU:RB; Andre Williams,NYG:RB; Mike Tolbert,CAR:RB; Mike Gillislee,BUF:RB; Ronnie Hillman,DEN:RB; Indianapolis Colts,IND:DST; Baltimore Ravens,BAL:DST; Matt Bryant,ATL:K; Nick Novak,HOU:K; Jeff Heuerman,DEN:TE; Larry Donnell,NYG:TE; Ladarius Green,PIT:TE; Cameron Brate,TB:TE; Tyler Higbee,LA:TE; Andrew Hawkins,CLE:WR; Brian Quick,LA:WR; Blaine Gabbert,SF:QB; Chicago Bears,CHI:DST; Washington Redskins,WAS:DST; Philadelphia Eagles,PHI:DST; Pittsburgh Steelers,PIT:DST;",
			// cbs jamey eisenberg PPR 2016
			"Antonio Brown,PIT:WR ;Julio Jones,ATL:WR ;Odell Beckham,NYG:WR ;A.J. Green,CIN:WR;David Johnson,ARI:RB ;Dez Bryant,DAL:WR ;DeAndre Hopkins,HOU:WR ;Todd Gurley,LAR:RB ;Lamar Miller,HOU:RB ;Rob Gronkowski,NE:TE ;Ezekiel Elliott,DAL:RB  ;Adrian Peterson,MIN:RB ;Jordy Nelson,GB:WR  ;Jamaal Charles,KC:RB  ;Le'Veon Bell,PIT:RB ;Keenan Allen,SD:WR ;Mike Evans,TB:WR ;Allen Robinson,JAC:WR ;Alshon Jeffery,CHI:WR ;Brandon Marshall,NYJ:WR;Brandin Cooks,NO:WR ;Amari Cooper,OAK:WR ;Eddie Lacy,GB:RB ;T.Y. Hilton,IND:WR ;Sammy Watkins,BUF:WR ;Mark Ingram,NO:RB;Devonta Freeman,ATL:RB ;Demaryius Thomas,DEN:WR;LeSean McCoy,BUF:RB ;Doug Martin,TB:RB ;Randall Cobb,GB:WR ;Latavius Murray,OAK:RB ;C.J. Anderson,DEN:RB;Aaron Rodgers,GB:QB ;Cam Newton,CAR:QB;Jarvis Landry,MIA:WR ;Jordan Reed,WAS:TE  ;Julian Edelman,NE:WR ;Kelvin Benjamin,CAR:WR ;Dion Lewis,NE:RB  ;Danny Woodhead,SD:RB ;Donte Moncrief,IND:WR ;Michael Floyd,ARI:WR;Thomas Rawls,SEA:RB  ;Carlos Hyde,SF:RB ;John Brown,ARI:WR  ;Andrew Luck,IND:QB ;Eric Decker,NYJ:WR ;Jeremy Maclin,KC:WR ;Russell Wilson,SEA:QB ;Doug Baldwin,SEA:WR ;Melvin Gordon,SD:RB ;DeMarco Murray,TEN:RB ;Greg Olsen,CAR:TE ;Tyler Lockett,SEA:WR;Golden Tate,DET:WR ;Matt Forte,NYJ:RB  ;DeVante Parker,MIA:WR  ;Larry Fitzgerald,ARI:WR  ;Drew Brees,NO:QB ;Coby Fleener,NO:TE ;Giovani Bernard,CIN:RB;Marvin Jones,DET:WR ;Emmanuel Sanders,DEN:WR;Jeremy Hill,CIN:RB ;Sterling Shepard,NYG:WR ;Kevin White,CHI:WR ;Ryan Mathews,PHI:RB ;Jeremy Langford,CHI:RB ;Carson Palmer,ARI:QB;Frank Gore,IND:RB;Duke Johnson,CLE:RB ;Delanie Walker,TEN:TE ;Jonathan Stewart,CAR:RB ;Ameer Abdullah,DET:RB ;Charles Sims,TB:RB  ;DeSean Jackson,WAS:WR ;Travis Kelce,KC:TE ;Allen Hurns,JAC:WR ;Rashad Jennings,NYG:RB;Willie Snead,NO:WR ;Jordan Matthews,PHI:WR ;Matt Jones,WAS:RB  ;Arian Foster,MIA:RB ;Tom Brady,NE:QB ;Josh Gordon,CLE:WR ;Chris Ivory,JAC:RB;Zach Ertz,PHI:TE ;Theo Riddick,DET:RB ;Michael Crabtree,OAK:WR ;Kamar Aiken,BAL:WR;Stefon Diggs,MIN:WR;Justin Forsett,BAL:RB;Derrick Henry,TEN:RB ;DeAngelo Williams,PIT:RB ;Eli Manning,NYG:QB ;Antonio Gates,SD:TE ;T.J. Yeldon,JAC:RB;Gary Barnidge,CLE:TE ;Torrey Smith,SF:WR ;Jay Ajayi,MIA:RB ;Philip Rivers,SD:QB ;Julius Thomas,JAC:TE ;Ben Roethlisberger,PIT:QB ;Corey Coleman,CLE:WR  ;Vincent Jackson,TB:WR  ;Phillip Dorsett,IND:WR ;Tony Romo,DAL:QB ;Michael Thomas,NO:WR;Bilal Powell,NYJ:RB;LeGarrette Blount,NE:RB ;Devin Funchess,CAR:WR ;Tevin Coleman,ATL:RB ;Devontae Booker,DEN:RB ;Matthew Stafford,DET:QB;Isaiah Crowell,CLE:RB ;Jerick McKinnon,MIN:RB ;Dwayne Allen,IND:TE ;Christine Michael,SEA:RB ;Tavon Austin,LAR:WR ;Tyler Boyd,CIN:WR ;Spencer Ware,KC:RB ;Markus Wheaton,PIT:WR ;Sammie Coates,PIT:WR ;Tyrod Taylor,BUF:QB ;Tajae Sharpe,TEN:WR ;Tyler Eifert,CIN:TE ;DeAndre Washington,OAK:RB;Darren Sproles,PHI:RB;Blake Bortles,JAC:QB;Josh Ferguson,IND:RB;Jameis Winston,TB:QB ;Kenneth Dixon,BAL:RB ;Martellus Bennett,NE:TE ;Derek Carr,OAK:QB ;Karlos Williams,BUF:RB ;Cardinals ,ARI:DST ;Terrance Williams,DAL:WR ;Mohamed Sanu,ATL:WR ;Bruce Ellington,SF:WR ;Paul Perkins,NYG:RB ;Chris Hogan,NE:WR ;Travis Benjamin,SD:WR ;Kirk Cousins,WAS:QB ;Laquon Treadwell,MIN:WR ;Anquan Boldin,DET:WR ;Broncos ,DEN:DST ;Seahawks ,SEA:DST ;Eric Ebron,DET:TE  ;Ryan Tannehill,MIA:QB ;Jimmy Graham,SEA:TE  ;Javorius Allen,BAL:RB;Zach Miller,CHI:TE  ;Stephen Gostkowski,NE:K;Panthers ,CAR:DST ;Matt Ryan,ATL:QB ;Steve Smith,BAL:WR  ;Clive Walford,OAK:TE ;Andy Dalton,CIN:QB;Shane Vereen,NYG:RB;Alfred Morris,DAL:RB ;Ryan Fitzpatrick,NYJ:QB;James Starks,GB:RB ;Kendall Wright,TEN:WR  ;Mike Wallace,BAL:WR ;Tim Hightower,NO:RB;Texans ,HOU:DST ;Shaun Draughn,SF:RB;Chris Thompson,WAS:RB;Vikings ,MIN:DST ;Chris Johnson,ARI:RB;C.J. Prosise,SEA:RB  ;Pharoh Cooper,LAR:WR;Cameron Artis-Payne,CAR:RB;Jason Witten,DAL:TE ;Rishard Matthews,TEN:WR ;Darren McFadden,DAL:RB ;Jordan Howard,CHI:RB ;Josh Doctson,WAS:WR  ;Breshad Perriman,BAL:WR  ;Charcandrick West,KC:RB ;Benny Cunningham,LAR:RB ;Mike Gillislee,BUF:RB ;Jay Cutler,CHI:QB;Jared Cook,GB:TE ;Davante Adams,GB:WR ;Jordan Cameron,MIA:TE ;Marcus Mariota,TEN:QB ;Pierre Garcon,WAS:WR ;Will Fuller,HOU:WR ;Tyrell Williams,SD:WR ;Tyreek Hill,KC:WR ;Terrelle Pryor,CLE:WR ;Charles Clay,BUF:TE ;Vance McDonald,SF:TE ;Terrance West,BAL:RB;James White,NE:RB ;Kyle Rudolph,MIN:TE ;Kenjon Barner,PHI:RB ;Jaelen Strong,HOU:WR;",
			// cbs dave richard PPR 2016
			"Antonio Brown,PIT:WR ;Odell Beckham,NYG:WR ;Julio Jones,ATL:WR ;DeAndre Hopkins,HOU:WR ;David Johnson,ARI:RB ;Dez Bryant,DAL:WR ;A.J. Green,CIN:WR;Todd Gurley,LAR:RB ;Adrian Peterson,MIN:RB ;Lamar Miller,HOU:RB ;Jordy Nelson,GB:WR  ;Ezekiel Elliott,DAL:RB  ;Rob Gronkowski,NE:TE ;Allen Robinson,JAC:WR ;Brandin Cooks,NO:WR ;Mike Evans,TB:WR ;Brandon Marshall,NYJ:WR;Keenan Allen,SD:WR ;Jamaal Charles,KC:RB  ;Le'Veon Bell,PIT:RB ;Amari Cooper,OAK:WR ;T.Y. Hilton,IND:WR ;Sammy Watkins,BUF:WR ;Eddie Lacy,GB:RB ;Devonta Freeman,ATL:RB ;Doug Martin,TB:RB ;Mark Ingram,NO:RB;Alshon Jeffery,CHI:WR ;Demaryius Thomas,DEN:WR;LeSean McCoy,BUF:RB ;Latavius Murray,OAK:RB ;Randall Cobb,GB:WR ;C.J. Anderson,DEN:RB;Kelvin Benjamin,CAR:WR ;Jordan Reed,WAS:TE  ;Greg Olsen,CAR:TE ;Jeremy Maclin,KC:WR ;Carlos Hyde,SF:RB ;DeMarco Murray,TEN:RB ;Danny Woodhead,SD:RB ;Aaron Rodgers,GB:QB ;Michael Floyd,ARI:WR;Jarvis Landry,MIA:WR ;Doug Baldwin,SEA:WR ;Cam Newton,CAR:QB;Julian Edelman,NE:WR ;John Brown,ARI:WR  ;Thomas Rawls,SEA:RB  ;Dion Lewis,NE:RB  ;Melvin Gordon,SD:RB ;Jeremy Hill,CIN:RB ;Andrew Luck,IND:QB ;Eric Decker,NYJ:WR ;DeVante Parker,MIA:WR  ;Donte Moncrief,IND:WR ;Jeremy Langford,CHI:RB ;Matt Forte,NYJ:RB  ;Matt Jones,WAS:RB  ;Drew Brees,NO:QB ;Russell Wilson,SEA:QB ;Giovani Bernard,CIN:RB;Duke Johnson,CLE:RB ;Tyler Lockett,SEA:WR;Larry Fitzgerald,ARI:WR  ;Golden Tate,DET:WR ;Allen Hurns,JAC:WR ;Jordan Matthews,PHI:WR ;Frank Gore,IND:RB;Jonathan Stewart,CAR:RB ;Chris Ivory,JAC:RB;Ryan Mathews,PHI:RB ;Rashad Jennings,NYG:RB;Sterling Shepard,NYG:WR ;Charles Sims,TB:RB  ;Arian Foster,MIA:RB ;Delanie Walker,TEN:TE ;Coby Fleener,NO:TE ;Marvin Jones,DET:WR ;Ameer Abdullah,DET:RB ;DeAngelo Williams,PIT:RB ;Emmanuel Sanders,DEN:WR;Tom Brady,NE:QB ;DeSean Jackson,WAS:WR ;Kevin White,CHI:WR ;Jay Ajayi,MIA:RB ;Justin Forsett,BAL:RB;Derrick Henry,TEN:RB ;Carson Palmer,ARI:QB;Travis Kelce,KC:TE ;Kamar Aiken,BAL:WR;Michael Crabtree,OAK:WR ;Willie Snead,NO:WR ;Devontae Booker,DEN:RB ;DeAndre Washington,OAK:RB;Theo Riddick,DET:RB ;T.J. Yeldon,JAC:RB;Antonio Gates,SD:TE ;Zach Ertz,PHI:TE ;Josh Gordon,CLE:WR ;Tajae Sharpe,TEN:WR ;Corey Coleman,CLE:WR  ;Isaiah Crowell,CLE:RB ;LeGarrette Blount,NE:RB ;Gary Barnidge,CLE:TE ;Blake Bortles,JAC:QB;Philip Rivers,SD:QB ;Eli Manning,NYG:QB ;Kenneth Dixon,BAL:RB ;Julius Thomas,JAC:TE ;Derek Carr,OAK:QB ;Jerick McKinnon,MIN:RB ;Bilal Powell,NYJ:RB;Tevin Coleman,ATL:RB ;Ben Roethlisberger,PIT:QB ;Tyler Eifert,CIN:TE ;Sammie Coates,PIT:WR ;Torrey Smith,SF:WR ;Spencer Ware,KC:RB ;Devin Funchess,CAR:WR ;Tavon Austin,LAR:WR ;Martellus Bennett,NE:TE ;Stefon Diggs,MIN:WR;Phillip Dorsett,IND:WR ;Cardinals ,ARI:DST ;Christine Michael,SEA:RB ;Broncos ,DEN:DST ;Seahawks ,SEA:DST ;Vincent Jackson,TB:WR  ;Tyler Boyd,CIN:WR ;Mohamed Sanu,ATL:WR ;Terrance Williams,DAL:WR ;Michael Thomas,NO:WR;Dwayne Allen,IND:TE ;Chris Johnson,ARI:RB;Tim Hightower,NO:RB;Javorius Allen,BAL:RB;Alfred Morris,DAL:RB ;Darren Sproles,PHI:RB;Tony Romo,DAL:QB ;Josh Ferguson,IND:RB;James Starks,GB:RB ;Terrance West,BAL:RB;Andy Dalton,CIN:QB;Vikings ,MIN:DST ;Texans ,HOU:DST ;Bengals ,CIN:DST ;Travis Benjamin,SD:WR ;Rishard Matthews,TEN:WR ;Chris Hogan,NE:WR ;Markus Wheaton,PIT:WR ;Eric Ebron,DET:TE  ;Shane Vereen,NYG:RB;Panthers ,CAR:DST ;Jimmy Graham,SEA:TE  ;C.J. Prosise,SEA:RB  ;Laquon Treadwell,MIN:WR ;Jameis Winston,TB:QB ;Shaun Draughn,SF:RB;Jordan Howard,CHI:RB ;Chris Thompson,WAS:RB;Matthew Stafford,DET:QB;Keith Marshall,WAS:RB;Tyrod Taylor,BUF:QB ;Pharoh Cooper,LAR:WR;Wendell Smallwood,PHI:RB ;Darren McFadden,DAL:RB ;Cameron Artis-Payne,CAR:RB;Benny Cunningham,LAR:RB ;Paul Perkins,NYG:RB ;Jared Cook,GB:TE ;Anquan Boldin,DET:WR ;Steelers ,PIT:DST ;Steve Smith,BAL:WR  ;Will Fuller,HOU:WR ;Kirk Cousins,WAS:QB ;Tyreek Hill,KC:WR ;Zach Miller,CHI:TE  ;Jaelen Strong,HOU:WR ;Ryan Tannehill,MIA:QB ;Josh Doctson,WAS:WR  ;Kendall Wright,TEN:WR  ;Jason Witten,DAL:TE ;Charcandrick West,KC:RB ;Jonathan Williams,BUF:RB ;Kenyan Drake,MIA:RB ;Rams ,LAR:DST ;Jeff Janis,GB:WR  ;Khiry Robinson,NYJ:RB ;Andre Ellington,ARI:RB ;Danny Amendola,NE:WR  ;Pierre Garcon,WAS:WR ;Braxton Miller,HOU:WR ;Jordan Cameron,MIA:TE ;Chiefs ,KC:DST;Charles Clay,BUF:TE ;Ronnie Hillman,DEN:RB ;Marqise Lee,JAC:WR ;Breshad Perriman,BAL:WR  ;Matt Ryan,ATL:QB ;Clive Walford,OAK:TE;",
			// cbs dave richard regular 2016
			"Todd Gurley,LAR:RB ;David Johnson,ARI:RB ;Adrian Peterson,MIN:RB ;Odell Beckham,NYG:WR ;Antonio Brown,PIT:WR ;Julio Jones,ATL:WR ;Rob Gronkowski,NE:TE ;Lamar Miller,HOU:RB ;DeAndre Hopkins,HOU:WR ;Ezekiel Elliott,DAL:RB  ;Dez Bryant,DAL:WR ;Jamaal Charles,KC:RB  ;A.J. Green,CIN:WR;Jordy Nelson,GB:WR  ;Eddie Lacy,GB:RB ;Allen Robinson,JAC:WR ;Le'Veon Bell,PIT:RB ;Brandin Cooks,NO:WR ;Doug Martin,TB:RB ;Amari Cooper,OAK:WR ;Mike Evans,TB:WR ;Devonta Freeman,ATL:RB ;Mark Ingram,NO:RB;Brandon Marshall,NYJ:WR;Keenan Allen,SD:WR ;T.Y. Hilton,IND:WR ;LeSean McCoy,BUF:RB ;Sammy Watkins,BUF:WR ;Aaron Rodgers,GB:QB ;Latavius Murray,OAK:RB ;Alshon Jeffery,CHI:WR ;C.J. Anderson,DEN:RB;Cam Newton,CAR:QB;Demaryius Thomas,DEN:WR;Thomas Rawls,SEA:RB  ;DeMarco Murray,TEN:RB ;Carlos Hyde,SF:RB ;Jeremy Hill,CIN:RB ;Randall Cobb,GB:WR ;Andrew Luck,IND:QB ;Kelvin Benjamin,CAR:WR ;Michael Floyd,ARI:WR;John Brown,ARI:WR  ;Jordan Reed,WAS:TE  ;Jeremy Maclin,KC:WR ;Greg Olsen,CAR:TE ;Drew Brees,NO:QB ;Russell Wilson,SEA:QB ;Melvin Gordon,SD:RB ;Doug Baldwin,SEA:WR ;Jeremy Langford,CHI:RB ;DeVante Parker,MIA:WR  ;Julian Edelman,NE:WR ;Jarvis Landry,MIA:WR ;Eric Decker,NYJ:WR ;Tyler Lockett,SEA:WR;Donte Moncrief,IND:WR ;Matt Jones,WAS:RB  ;Dion Lewis,NE:RB  ;Matt Forte,NYJ:RB  ;Allen Hurns,JAC:WR ;Frank Gore,IND:RB;Danny Woodhead,SD:RB ;Jonathan Stewart,CAR:RB ;Chris Ivory,JAC:RB;Golden Tate,DET:WR ;Ryan Mathews,PHI:RB ;Rashad Jennings,NYG:RB;Larry Fitzgerald,ARI:WR  ;Giovani Bernard,CIN:RB;Duke Johnson,CLE:RB ;Ameer Abdullah,DET:RB ;Coby Fleener,NO:TE ;Arian Foster,MIA:RB ;DeAngelo Williams,PIT:RB ;Tom Brady,NE:QB ;Carson Palmer,ARI:QB;Jordan Matthews,PHI:WR ;Sterling Shepard,NYG:WR ;Charles Sims,TB:RB  ;Derrick Henry,TEN:RB ;DeSean Jackson,WAS:WR ;Marvin Jones,DET:WR ;Delanie Walker,TEN:TE ;Emmanuel Sanders,DEN:WR;Jay Ajayi,MIA:RB ;Devontae Booker,DEN:RB ;T.J. Yeldon,JAC:RB;Kevin White,CHI:WR ;DeAndre Washington,OAK:RB;Tevin Coleman,ATL:RB ;Isaiah Crowell,CLE:RB ;Travis Kelce,KC:TE ;Antonio Gates,SD:TE ;Zach Ertz,PHI:TE ;Blake Bortles,JAC:QB;Philip Rivers,SD:QB ;Eli Manning,NYG:QB ;LeGarrette Blount,NE:RB ;Jerick McKinnon,MIN:RB ;Willie Snead,NO:WR ;Kamar Aiken,BAL:WR;Josh Gordon,CLE:WR ;Corey Coleman,CLE:WR  ;Kenneth Dixon,BAL:RB ;Derek Carr,OAK:QB ;Justin Forsett,BAL:RB;Ben Roethlisberger,PIT:QB ;Spencer Ware,KC:RB ;Bilal Powell,NYJ:RB;Christine Michael,SEA:RB ;Gary Barnidge,CLE:TE ;Michael Crabtree,OAK:WR ;Tajae Sharpe,TEN:WR ;Torrey Smith,SF:WR ;Julius Thomas,JAC:TE ;Tyler Eifert,CIN:TE ;Devin Funchess,CAR:WR ;Sammie Coates,PIT:WR ;Tavon Austin,LAR:WR ;Stefon Diggs,MIN:WR;Cardinals ,ARI:DST ;Phillip Dorsett,IND:WR ;Martellus Bennett,NE:TE ;Broncos ,DEN:DST ;Seahawks ,SEA:DST ;Theo Riddick,DET:RB ;Chris Johnson,ARI:RB;Vincent Jackson,TB:WR  ;Tyler Boyd,CIN:WR ;Tim Hightower,NO:RB;Michael Thomas,NO:WR;Andy Dalton,CIN:QB;Alfred Morris,DAL:RB ;Javorius Allen,BAL:RB;Tony Romo,DAL:QB ;Josh Ferguson,IND:RB;Darren Sproles,PHI:RB;C.J. Prosise,SEA:RB  ;Vikings ,MIN:DST ;Texans ,HOU:DST ;Jameis Winston,TB:QB ;James Starks,GB:RB ;Dwayne Allen,IND:TE ;Bengals ,CIN:DST ;Panthers ,CAR:DST ;Terrance West,BAL:RB;Matthew Stafford,DET:QB;Keith Marshall,WAS:RB;Jordan Howard,CHI:RB ;Terrance Williams,DAL:WR ;Markus Wheaton,PIT:WR ;Mohamed Sanu,ATL:WR ;Travis Benjamin,SD:WR ;Steelers ,PIT:DST ;Tyrod Taylor,BUF:QB ;Chris Hogan,NE:WR ;Laquon Treadwell,MIN:WR ;Eric Ebron,DET:TE  ;Jimmy Graham,SEA:TE  ;Paul Perkins,NYG:RB ;Cameron Artis-Payne,CAR:RB;Shaun Draughn,SF:RB;Wendell Smallwood,PHI:RB ;Rams ,LAR:DST ;Chiefs ,KC:DST;Jared Cook,GB:TE ;Kirk Cousins,WAS:QB ;Darren McFadden,DAL:RB ;Pharoh Cooper,LAR:WR;Ryan Tannehill,MIA:QB ;Anquan Boldin,DET:WR ;Andre Ellington,ARI:RB ;Jonathan Williams,BUF:RB ;Rishard Matthews,TEN:WR ;Steve Smith,BAL:WR  ;Tyreek Hill,KC:WR ;Jaelen Strong,HOU:WR ;Will Fuller,HOU:WR ;Kendall Wright,TEN:WR  ;Josh Doctson,WAS:WR  ;Zach Miller,CHI:TE  ;Matt Ryan,ATL:QB ;Jay Cutler,CHI:QB;Khiry Robinson,NYJ:RB ;Charcandrick West,KC:RB ;Benny Cunningham,LAR:RB ;Kenyan Drake,MIA:RB ;Jordan Cameron,MIA:TE ;Jeff Janis,GB:WR  ;Pierre Garcon,WAS:WR ;Danny Amendola,NE:WR  ;Patriots ,NE:DST ;Bills ,BUF:DST ;Ka'Deem Carey,CHI:RB  ;Victor Cruz,NYG:WR  ;Ryan Fitzpatrick,NYJ:QB;Marcus Mariota,TEN:QB ;Nelson Agholor,PHI:WR ;Charles Clay,BUF:TE;"
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
