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
			.card {
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
			.pos-dst, .pos-def { background-color: #fcf; }
			.pos-k { background-color: #ccc; }
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
			
		</style>
	</head>
<body>

	<div id="content" class="container">
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
		/* Sample 2014 NFL Draft Lists */
		lists = [
			"Calvin Johnson, DET:WR;Jamaal Charles, KC:RB;LeSean McCoy, PHI:RB;Matt Forte, CHI:RB;Demaryius Thomas, DEN:WR;Dez Bryant, DAL:WR;Eddie Lacy, GB:RB;Adrian Peterson, MIN:RB;Giovani Bernard, CIN:RB;DeMarco Murray, DAL:RB;Jimmy Graham, NO:TE;Montee Ball, DEN:RB;Julio Jones, ATL:WR;Andre Ellington, ARI:RB;Le'Veon Bell, PIT:RB;C.J. Spiller, BUF:RB;Doug Martin, TB:RB;A.J. Green, CIN:WR;Alshon Jeffery, CHI:WR;Brandon Marshall, CHI:WR;Arian Foster, HOU:RB;Antonio Brown, PIT:WR;Peyton Manning, DEN:QB;Reggie Bush, DET:RB;Shane Vereen, NE:RB;Drew Brees, NO:QB;Rashad Jennings, NYG:RB;Joique Bell, DET:RB;Jordy Nelson, GB:WR;Aaron Rodgers, GB:QB;Marshawn Lynch, SEA:RB;Alfred Morris, WAS:RB;Randall Cobb, GB:WR;Keenan Allen, SD:WR;Michael Floyd, ARI:WR;Pierre Garcon, WAS:WR;Julius Thomas, DEN:TE;Ray Rice, BAL:RB;Andre Johnson, HOU:WR;Trent Richardson, IND:RB;Ben Tate, CLE:RB;Michael Crabtree, SF:WR;Roddy White, ATL:WR;Rob Gronkowski, NE:TE;Zac Stacy, STL:RB;T.Y. Hilton, IND:WR;Ryan Mathews, SD:RB;Vincent Jackson, TB:WR;Pierre Thomas, NO:RB;Jeremy Maclin, PHI:WR;Andrew Luck, IND:QB;Wes Welker, DEN:WR;DeSean Jackson, WAS:WR;Jordan Reed, WAS:TE;Percy Harvin, SEA:WR;Victor Cruz, NYG:WR;Bishop Sankey, TEN:RB;Jay Cutler, CHI:QB;Jordan Cameron, CLE:TE;Matthew Stafford, DET:QB;Matt Ryan, ATL:QB;Chris Johnson, NYJ:RB;Robert Griffin III, WAS:QB;Cordarrelle Patterson, MIN:WR;Kendall Wright, TEN:WR;Nick Foles, PHI:QB;Toby Gerhart, JAX:RB;Vernon Davis, SF:TE;Larry Fitzgerald, ARI:WR;Jason Witten, DAL:TE;Julian Edelman, NE:WR;Lamar Miller, MIA:RB;Stevan Ridley, NE:RB;Cam Newton, CAR:QB;Russell Wilson, SEA:QB;Torrey Smith, BAL:WR;Tom Brady, NE:QB;Steven Jackson, ATL:RB;Golden Tate, DET:WR;Greg Olsen, CAR:TE;Colin Kaepernick, SF:QB;Mike Wallace, MIA:WR;Emmanuel Sanders, DEN:WR;Tony Romo, DAL:QB;Dennis Pitta, BAL:TE;Eric Decker, NYJ:WR;Charles Clay, MIA:TE;Marques Colston, NO:WR;Rueben Randle, NYG:WR;Sammy Watkins, BUF:WR;Zach Ertz, PHI:TE;Kyle Rudolph, MIN:TE;Mike Evans, TB:WR;Tavon Austin, STL:WR;Markus Wheaton, PIT:WR;Dwayne Bowe, KC:WR;Seattle, SEA:Def;Carolina, CAR:Def;Darren McFadden, OAK:RB;Danny Woodhead, SD:RB;Terrance Williams, DAL:WR;St. Louis, STL:Def;Ben Roethlisberger, PIT:QB;Philip Rivers, SD:QB;Ladarius Green, SD:TE;San Francisco, SF:Def;DeAngelo Williams, CAR:RB;Martellus Bennett, CHI:TE;Carson Palmer, ARI:QB;Reggie Wayne, IND:WR;Garrett Graham, HOU:TE;Denver, DEN:Def;Bernard Pierce, BAL:RB;Kansas City, KC:Def;Knowshon Moreno, MIA:RB;Darren Sproles, PHI:RB;Andy Dalton, CIN:QB;Ryan Tannehill, MIA:QB;Brandin Cooks, NO:WR;Maurice Jones-Drew, OAK:RB;Terrance West, CLE:RB;New England, NE:Def;Lance Moore, PIT:WR;Mark Ingram, NO:RB;Delanie Walker, TEN:TE;Doug Baldwin, SEA:WR;DeAndre Hopkins, HOU:WR;Kelvin Benjamin, CAR:WR;Greg Jennings, MIN:WR;Frank Gore, SF:RB;James Jones, OAK:WR;Eric Ebron, DET:TE;LeGarrette Blount, PIT:RB;Eli Manning, NYG:QB;Lance Dunbar, DAL:RB;Chris Givens, STL:WR;Jared Cook, STL:TE;Hakeem Nicks, IND:WR;Joe Flacco, BAL:QB;Dwayne Allen, IND:TE;Cincinnati, CIN:Def;Andrew Hawkins, CLE:WR;Chicago, CHI:Def;Riley Cooper, PHI:WR;Tyler Eifert, CIN:TE;Steven Hauschka, SEA:K;Dexter McCluster, TEN:RB;Green Bay, GB:Def;Steve Smith, BAL:WR;Sam Bradford, STL:QB;Marqise Lee, JAX:WR;Anquan Boldin, SF:WR;Danny Amendola, NE:WR;Bryce Brown, BUF:RB;Aaron Dobson, NE:WR;Pittsburgh, PIT:Def;Arizona, ARI:Def;Donald Brown, SD:RB;Matt Prater, DEN:K;Jacquizz Rodgers, ATL:RB;Alex Smith, KC:QB;Cecil Shorts, JAX:WR;Travis Kelce, KC:TE;Jarrett Boykin, GB:WR;Justin Hunter, TEN:WR;Carlos Hyde, SF:RB;Rod Streater, OAK:WR;Jeremy Hill, CIN:RB;Heath Miller, PIT:TE;Stephen Gostkowski, NE:K;Jace Amaro, NYJ:TE;Phil Dawson, SF:K;Brandon LaFell, NE:WR;Jordan Matthews, PHI:WR;Richard Rodgers, GB:TE;Justin Tucker, BAL:K;Levine Toilolo, ATL:TE;Chris Ivory, NYJ:RB;Nick Folk, NYJ:K;Antonio Gates, SD:TE;Kenny Stills, NO:WR;Nick Novak, SD:K;Kenbrell Thompkins, NE:WR;Josh McCown, TB:QB;Luke Willson, SEA:TE;Austin Seferian-Jenkins, TB:TE;Coby Fleener, IND:TE;Dan Bailey, DAL:K;Kenny Britt, STL:WR;Brian Hartline, MIA:WR;Marcedes Lewis, JAX:TE;Robbie Gould, CHI:K;Nate Freese, DET:K;Mason Crosby, GB:K;Greg Zuerlein, STL:K;Jermaine Gresham, CIN:TE;Miles Austin, CLE:WR;Jerrel Jernigan, NYG:WR;Jonathan Stewart, CAR:RB;Mohamed Sanu, CIN:WR;John Carlson, ARI:TE",
			"Adrian Peterson, MIN:RB1;Jamaal Charles, KC:RB2;LeSean McCoy, PHI:RB3;Matt Forte, CHI:RB4;Eddie Lacy, GB:RB5;Peyton Manning, DEN:QB1;Jimmy Graham, NO:TE1;Calvin Johnson, DET:WR1;Montee Ball, DEN:RB6;Demaryius Thomas, DEN:WR2;Arian Foster, HOU:RB7;Dez Bryant, DAL:WR3;Drew Brees, NO:QB2;Aaron Rodgers, GB:QB3;DeMarco Murray, DAL:RB8;Marshawn Lynch, SEA:RB9;Brandon Marshall, CHI:WR4;A.J. Green, CIN:WR5;Zac Stacy, STL:RB10;Alfred Morris, WAS:RB11;Giovani Bernard, CIN:RB12;Jordy Nelson, GB:WR6;Rob Gronkowski, NE:TE2;Julio Jones, ATL:WR7;Alshon Jeffery, CHI:WR8;Antonio Brown, PIT:WR9;Le'Veon Bell, PIT:RB13;Andre Ellington, ARI:RB14;Doug Martin, TB:RB15;Julius Thomas, DEN:TE3;Pierre Garcon, WAS:WR10;Randall Cobb, GB:WR11;Victor Cruz, NYG:WR12;C.J. Spiller, BUF:RB16;Andre Johnson, HOU:WR13;Rashad Jennings, NYG:RB17;Joique Bell, DET:RB18;Vincent Jackson, TB:WR14;Roddy White, ATL:WR15;Wes Welker, DEN:WR16;Torrey Smith, BAL:WR17;Keenan Allen, SD:WR18;Larry Fitzgerald, ARI:WR19;Frank Gore, SF:RB19;Steven Jackson, ATL:RB20;Shane Vereen, NE:RB21;Cordarrelle Patterson, MIN:WR20;Michael Crabtree, SF:WR21;Toby Gerhart, JAC:RB22;Ray Rice, BAL:RB23;Lamar Miller, MIA:RB24;Ben Tate, CLE:RB25;DeSean Jackson, WAS:WR22;Matthew Stafford, DET:QB4;Percy Harvin, SEA:WR23;Michael Floyd, ARI:WR24;Reggie Bush, DET:RB26;Ryan Mathews, SD:RB27;Bishop Sankey, TEN:RB28;Maurice Jones-Drew, OAK:RB29;Chris Johnson, NYJ:RB30;Pierre Thomas, NO:RB31;Trent Richardson, IND:RB32;Andre Williams, NYG:RB33;Kendall Wright, TEN:WR25;Julian Edelman, NE:WR26;Andrew Luck, IND:QB5;Robert Griffin III, WAS:QB6;Tom Brady, NE:QB7;Cam Newton, CAR:QB8;Emmanuel Sanders, DEN:WR27;Stevan Ridley, NE:RB34;Marques Colston, NO:WR28;T.Y. Hilton, IND:WR29;Brandin Cooks, NO:WR30;Greg Olsen, CAR:TE4;Vernon Davis, SF:TE5;Nick Foles, PHI:QB9;Jay Cutler, CHI:QB10;Matt Ryan, ATL:QB11;Tony Romo, DAL:QB12;Colin Kaepernick, SF:QB13;Jordan Reed, WAS:TE6;Jordan Cameron, CLE:TE7;Jason Witten, DAL:TE8;Terrance Williams, DAL:WR31;Dennis Pitta, BAL:TE9;Reggie Wayne, IND:WR32;Riley Cooper, PHI:WR33;Sammy Watkins, BUF:WR34;Mike Wallace, MIA:WR35;Jeremy Maclin, PHI:WR36;Andrew Hawkins, CLE:WR37;Eric Decker, NYJ:WR38;Golden Tate, DET:WR39;Cecil Shorts, JAC:WR40;Josh Gordon, CLE:WR41;Chris Ivory, NYJ:RB35;Fred Jackson, BUF:RB36;Donald Brown, SD:RB37;Mike Evans, TB:WR42;LeGarrette Blount, PIT:RB38;Jeremy Hill, CIN:RB39;DeAndre Hopkins, HOU:WR43;Kelvin Benjamin, CAR:WR44;Justin Hunter, TEN:WR45;Kyle Rudolph, MIN:TE10;Zach Ertz, PHI:TE11;Seattle Seahawks:DST1;Khiry Robinson, NO:RB40;Jarrett Boykin, GB:WR46;Terrance West, CLE:RB41;Bernard Pierce, BAL:RB42;Philip Rivers, SD:QB14;Russell Wilson, SEA:QB15;Christine Michael, SEA:RB43;Danny Amendola, NE:WR47;Greg Jennings, MIN:WR48;Rueben Randle, NYG:WR49;Dwayne Bowe, KC:WR50;Darren McFadden, OAK:RB44;Ahmad Bradshaw, IND:RB45;Danny Woodhead, SD:RB46;Markus Wheaton, PIT:WR51;Lance Dunbar, DAL:RB47;Anquan Boldin, SF:WR52;Darren Sproles, PHI:RB48;Shonn Greene, TEN:RB49;DeAngelo Williams, CAR:RB50;James Jones, OAK:WR53;Kenny Stills, NO:WR54;Knowshon Moreno, MIA:RB51;Tavon Austin, STL:WR55;Jerrel Jernigan, NYG:WR56;Harry Douglas, ATL:WR57;Bryce Brown, BUF:RB52;C.J. Anderson, DEN:RB53;Chris Polk, PHI:RB54;Devonta Freeman, ATL:RB55;Tre Mason, STL:RB56;James White, NE:RB57;Marvin Jones, CIN:WR58;Mark Ingram, NO:RB58;Aaron Dobson, NE:WR59;Martellus Bennett, CHI:TE12;Jonathan Grimes, HOU:RB59;Charles Clay, MIA:TE13;Steve Smith, BAL:WR60;Hakeem Nicks, IND:WR61;Stepfan Taylor, ARI:RB60;Steve Johnson, SF:WR62;Eric Ebron, DET:TE14;Jerricho Cotchery, CAR:WR63;Miles Austin, CLE:WR64;Knile Davis, KC:RB61;Mike Williams, BUF:WR65;Carlos Hyde, SF:RB62;Jonathan Stewart, CAR:RB63;James Starks, GB:RB64;Antonio Gates, SD:TE15;Ladarius Green, SD:TE16;Bobby Rainey, TB:RB65;Mike Tolbert, CAR:RB66;Doug Baldwin, SEA:WR66;Latavius Murray, OAK:RB67;Ronnie Hillman, DEN:RB68;Dwayne Allen, IND:TE17;Theo Riddick, DET:RB69;Dexter McCluster, TEN:WR67;Marcel Reece, OAK:RB70;Jordan Todman, JAC:RB71;BenJarvus Green-Ellis, CIN:RB72;Roy Helu, WAS:RB73;Brandon Bolden, NE:RB74;Brian Hartline, MIA:WR68;Denarius Moore, OAK:WR69;Cody Latimer, DEN:WR70;Carolina Panthers:DST2;San Francisco 49ers:DST3;Cincinnati Bengals:DST4;New England Patriots:DST5;Denver Broncos:DST6;Arizona Cardinals:DST7;Kansas City Chiefs:DST8;St. Louis Rams:DST9;Houston Texans:DST10;New Orleans Saints:DST11;Baltimore Ravens:DST12;Matt Prater, DEN:K1;Stephen Gostkowski, NE:K2;Justin Tucker, BAL:K3;Adam Vinatieri, IND:K4;Steven Hauschka, SEA:K5;Dan Bailey, DAL:K6;Blair Walsh, MIN:K7;Matt Bryant, ATL:K8;Phil Dawson, SF:K9;Mason Crosby, GB:K10;Nick Novak, SD:K11;Robbie Gould, CHI:K12",
			"Jamaal Charles KC: RB;LeSean McCoy PHI: RB;Matt Forte CHI: RB;Adrian Peterson MIN: RB;Calvin Johnson DET: WR;Jimmy Graham NO: TE;Demaryius Thomas DEN: WR;DeMarco Murray DAL: RB;Montee Ball DEN: RB;Giovani Bernard CIN: RB;Eddie Lacy GB: RB;Dez Bryant DAL: WR;Brandon Marshall CHI: WR;Julio Jones ATL: WR;A.J. Green CIN: WR;Jordy Nelson GB: WR;Le'Veon Bell PIT: RB;Antonio Brown PIT: WR;Randall Cobb GB: WR;Alshon Jeffery CHI: WR;Andre Ellington ARI: RB;Zac Stacy STL: RB;Arian Foster HOU: RB;Rob Gronkowski NE: TE;Peyton Manning DEN: QB;Doug Martin TB: RB;Shane Vereen NE: RB;Marshawn Lynch SEA: RB;Alfred Morris WAS: RB;Julius Thomas DEN: TE;Aaron Rodgers GB: QB;Drew Brees NO: QB;Reggie Bush DET: RB;Andre Johnson HOU: WR;Pierre Garcon WAS: WR;Toby Gerhart JAC: RB;Michael Crabtree SF: WR;Larry Fitzgerald ARI: WR;Bishop Sankey TEN: RB;Joique Bell DET: RB;Ryan Mathews SD: RB;Roddy White ATL: WR;Keenan Allen SD: WR;Vincent Jackson TB: WR;Victor Cruz NYG: WR;C.J. Spiller BUF: RB;Michael Floyd ARI: WR;Trent Richardson IND: RB;Wes Welker DEN: WR;Cordarrelle Patterson MIN: WR;Ray Rice BAL: RB;Rashad Jennings NYG: RB;Jason Witten DAL: TE;Julian Edelman NE: WR;Frank Gore SF: RB;Kendall Wright TEN: WR;Pierre Thomas NO: RB;Chris Johnson NYJ: RB;Emmanuel Sanders DEN: WR;Percy Harvin SEA: WR;Danny Woodhead SD: RB;Golden Tate DET: WR;Jeremy Maclin PHI: WR;Lamar Miller MIA: RB;Matthew Stafford DET: QB;Dennis Pitta BAL: TE;Fred Jackson BUF: RB;Ben Tate CLE: RB;Stevan Ridley NE: RB;Vernon Davis SF: TE;Marques Colston NO: WR;Mike Wallace MIA: WR;Matt Ryan ATL: QB;DeSean Jackson WAS: WR;Torrey Smith BAL: WR;Andrew Luck IND: QB;Terrance Williams DAL: WR;Tony Romo DAL: QB;Colin Kaepernick SF: QB;Eric Decker NYJ: WR;Tom Brady NE: QB;T.Y. Hilton IND: WR;Rueben Randle NYG: WR;Brandin Cooks NO: WR;Terrance West CLE: RB;Jordan Cameron CLE: TE;Reggie Wayne IND: WR;Jordan Reed WAS: TE;Sammy Watkins BUF: WR;Steven Jackson ATL: RB;Maurice Jones-Drew OAK: RB;Darren Sproles PHI: RB;Justin Hunter TEN: WR;Devonta Freeman ATL: RB;Robert Griffin III WAS: QB;Kyle Rudolph MIN: TE;Kelvin Benjamin CAR: WR;Zach Ertz PHI: TE;Andre Williams NYG: RB;Greg Olsen CAR: TE;Jay Cutler CHI: QB;Mike Evans TB: WR;Bernard Pierce BAL: RB;Jeremy Hill CIN: RB;Nick Foles PHI: QB;Cam Newton CAR: QB;Dexter McCluster TEN: RB;Khiry Robinson NO: RB;Carlos Hyde SF: RB;Jarrett Boykin GB: WR;Christine Michael SEA: RB;Heath Miller PIT: TE;Knowshon Moreno MIA: RB;DeAngelo Williams CAR: RB;Ben Roethlisberger PIT: QB;Darren McFadden OAK: RB;Anquan Boldin SF: WR;Brian Hartline MIA: WR;Danny Amendola NE: WR;Cecil Shorts JAC: WR;Kenny Stills NO: WR;Dwayne Bowe KC: WR;Riley Cooper PHI: WR;Rod Streater OAK: WR;DeAndre Hopkins HOU: WR;Hakeem Nicks IND: WR;Tavon Austin STL: WR;Ronnie Hillman DEN: RB;Marqise Lee JAC: WR;Jordan Matthews PHI: WR;LeGarrette Blount PIT: RB;Harry Douglas ATL: WR;Lance Dunbar DAL: RB;Jonathan Grimes HOU: RB;Kenny Britt STL: WR;Mohamed Sanu CIN: WR;Doug Baldwin SEA: WR;Chris Ivory NYJ: RB;Martellus Bennett CHI: TE;Russell Wilson SEA: QB;Greg Jennings MIN: WR;Shonn Greene TEN: RB;James White NE: RB;Stepfan Taylor ARI: RB;Ladarius Green SD: TE;Roy Helu WAS: RB;Andrew Hawkins CLE: WR;Seahawks SEA: DST;Marvin Jones CIN: WR;Steve Smith BAL: WR;Knile Davis KC: RB;James Starks GB: RB;Carson Palmer ARI: QB;Tre Mason STL: RB;Jonathan Stewart CAR: RB;Markus Wheaton PIT: WR;Delanie Walker TEN: TE;Philip Rivers SD: QB;Mark Ingram NO: RB;Ahmad Bradshaw IND: RB;Aaron Dobson NE: WR;Odell Beckham NYG: WR;Rams STL: DST;James Jones OAK: WR;John Brown ARI: WR;Panthers CAR: DST;Jerricho Cotchery CAR: WR;Robert Woods BUF: WR;49ers SF: DST;Dri Archer PIT: RB;Andy Dalton CIN: QB;Andre Holmes OAK: WR;Antonio Gates SD: TE;Alex Smith KC: QB;Ryan Tannehill MIA: QB;Jeremy Kerley NYJ: WR;Sam Bradford STL: QB;Davante Adams GB: WR;Travaris Cadet NO: RB;Donald Brown SD: RB;Mike Tolbert CAR: FB;Charles Clay MIA: TE;Stephen Gostkowski NE: K;Joe Flacco BAL: QB;Dwayne Allen IND: TE;Eli Manning NYG: QB;Jerick McKinnon MIN: RB;Ka'Deem Carey CHI: RB;Phil Dawson SF: K;Charles Sims TB: RB;Eric Ebron DET: TE;Jordan Todman JAC: RB;Travis Kelce KC: TE;Jake Locker TEN: QB;Matt Prater DEN: K;Cardinals ARI: DST;Bengals CIN: DST;Steven Hauschka SEA: K;Patriots NE: DST;Justin Tucker BAL: K",
			"Jamaal Charles KC: RB;Matt Forte CHI: RB;LeSean McCoy PHI: RB;Adrian Peterson MIN: RB;Calvin Johnson DET: WR;Demaryius Thomas DEN: WR;Jimmy Graham NO: TE;DeMarco Murray DAL: RB;Giovani Bernard CIN: RB;Dez Bryant DAL: WR;Brandon Marshall CHI: WR;Julio Jones ATL: WR;A.J. Green CIN: WR;Eddie Lacy GB: RB;Jordy Nelson GB: WR;Montee Ball DEN: RB;Le'Veon Bell PIT: RB;Andre Ellington ARI: RB;Antonio Brown PIT: WR;Shane Vereen NE: RB;Rob Gronkowski NE: TE;Alshon Jeffery CHI: WR;Arian Foster HOU: RB;Doug Martin TB: RB;Zac Stacy STL: RB;Randall Cobb GB: WR;Marshawn Lynch SEA: RB;Larry Fitzgerald ARI: WR;Pierre Garcon WAS: WR;Michael Crabtree SF: WR;Julius Thomas DEN: TE;Peyton Manning DEN: QB;Andre Johnson HOU: WR;Vincent Jackson TB: WR;Toby Gerhart JAC: RB;Alfred Morris WAS: RB;Reggie Bush DET: RB;Drew Brees NO: QB;Ray Rice BAL: RB;Joique Bell DET: RB;Aaron Rodgers GB: QB;Roddy White ATL: WR;Michael Floyd ARI: WR;Wes Welker DEN: WR;Victor Cruz NYG: WR;Rashad Jennings NYG: RB;C.J. Spiller BUF: RB;Bishop Sankey TEN: RB;Ryan Mathews SD: RB;Kendall Wright TEN: WR;Keenan Allen SD: WR;Emmanuel Sanders DEN: WR;Cordarrelle Patterson MIN: WR;Vernon Davis SF: TE;Julian Edelman NE: WR;Matthew Stafford DET: QB;Jeremy Maclin PHI: WR;Eric Decker NYJ: WR;Marques Colston NO: WR;Jordan Cameron CLE: TE;Jason Witten DAL: TE;Golden Tate DET: WR;Torrey Smith BAL: WR;Mike Wallace MIA: WR;DeSean Jackson WAS: WR;Sammy Watkins BUF: WR;Chris Johnson NYJ: RB;Dennis Pitta BAL: TE;Terrance Williams DAL: WR;Pierre Thomas NO: RB;Ben Tate CLE: RB;Trent Richardson IND: RB;Fred Jackson BUF: RB;Lamar Miller MIA: RB;Frank Gore SF: RB;Danny Woodhead SD: RB;Rueben Randle NYG: WR;Reggie Wayne IND: WR;Brandin Cooks NO: WR;T.Y. Hilton IND: WR;Colin Kaepernick SF: QB;Maurice Jones-Drew OAK: RB;Mike Evans TB: WR;Percy Harvin SEA: WR;Nick Foles PHI: QB;Tom Brady NE: QB;Matt Ryan ATL: QB;Tony Romo DAL: QB;Harry Douglas ATL: WR;Andrew Luck IND: QB;Kelvin Benjamin CAR: WR;Darren Sproles PHI: RB;Brian Hartline MIA: WR;Riley Cooper PHI: WR;Kyle Rudolph MIN: TE;Zach Ertz PHI: TE;Justin Hunter TEN: WR;Greg Olsen CAR: TE;Jordan Reed WAS: TE;DeAngelo Williams CAR: RB;Stevan Ridley NE: RB;Devonta Freeman ATL: RB;Terrance West CLE: RB;Jay Cutler CHI: QB;Robert Griffin III WAS: QB;Steven Jackson ATL: RB;Dexter McCluster TEN: RB;Bernard Pierce BAL: RB;Andre Williams NYG: RB;Doug Baldwin SEA: WR;Cecil Shorts JAC: WR;Anquan Boldin SF: WR;Knowshon Moreno MIA: RB;Carlos Hyde SF: RB;Khiry Robinson NO: RB;Christine Michael SEA: RB;Dwayne Bowe KC: WR;Lance Dunbar DAL: RB;Philip Rivers SD: QB;Ben Roethlisberger PIT: QB;Seahawks SEA: DST;Cam Newton CAR: QB;Rod Streater OAK: WR;Jordan Matthews PHI: WR;Tavon Austin STL: WR;Hakeem Nicks IND: WR;Robert Woods BUF: WR;DeAndre Hopkins HOU: WR;John Brown ARI: WR;Jarrett Boykin GB: WR;Cardinals ARI: DST;Kenny Stills NO: WR;Stepfan Taylor ARI: RB;Antonio Gates SD: TE;Heath Miller PIT: TE;Patriots NE: DST;Jeremy Hill CIN: RB;Ladarius Green SD: TE;Darren McFadden OAK: RB;Tre Mason STL: RB;Travaris Cadet NO: RB;LeGarrette Blount PIT: RB;Greg Jennings MIN: WR;Jonathan Grimes HOU: RB;Rams STL: DST;Charles Clay MIA: TE;Steve Smith BAL: WR;Kenny Britt STL: WR;James Jones OAK: WR;Marvin Jones CIN: WR;Marqise Lee JAC: WR;Danny Amendola NE: WR;Mohamed Sanu CIN: WR;Markus Wheaton PIT: WR;Andrew Hawkins CLE: WR;Jerricho Cotchery CAR: WR;Broncos DEN: DST;Miles Austin CLE: WR;Odell Beckham NYG: WR;Chris Ivory NYJ: RB;49ers SF: DST;Knile Davis KC: RB;Carson Palmer ARI: QB;Steelers PIT: DST;Lance Moore PIT: WR;Andre Holmes OAK: WR;Dri Archer PIT: RB;Martellus Bennett CHI: TE;Sam Bradford STL: QB;Russell Wilson SEA: QB;Panthers CAR: DST;Roy Helu WAS: RB;Jerick McKinnon MIN: RB;James White NE: RB;Delanie Walker TEN: TE;Eric Ebron DET: TE;Stephen Gostkowski NE: K;Brian Quick STL: WR;Malcom Floyd SD: WR;Andre Roberts WAS: WR;Mike Williams BUF: WR;Andy Dalton CIN: QB;Bengals CIN: DST;Packers GB: DST;Phil Dawson SF: K;Dwayne Allen IND: TE;Jared Cook STL: TE;Garrett Graham HOU: TE;Mike Tolbert CAR: FB;Ronnie Hillman DEN: RB;Bobby Rainey TB: RB;Mark Ingram NO: RB;James Starks GB: RB;Ryan Tannehill MIA: QB;Alex Smith KC: QB;Timothy Wright TB: TE;Marcedes Lewis JAC: TE;Bears CHI: DST;Jeremy Kerley NYJ: WR;Josh McCown TB: QB",
		];

		$(lists).each(function(i, val) {
			var splits = val.split(';');
			$(splits).each(function(j, card) {
				card = card.replace(',','').replace(/&nbsp;/g, ' ').trim();
				var pos = card.substr(card.indexOf(':')+1).replace(/[0-9]/g, '').trim().toLowerCase();
				var name = card.substr(0, card.indexOf(':'));
				$('#row'+(i+1)).append('<div class="card pos-'+pos+($.inArray(name, taken)>-1?' taken':'')+($.inArray(name, star)>-1?' star':'')+'" data-name="'+name+'">'+card+'</div>');
			});
		});
		
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