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
			
			.form-field-group {
				clear: both;
				display: block;
				vertical-align: top;
			}
			.form-field-group label {
				display: inline-block;
				vertical-align: top;
				width: 25%;
			}
			.detail {
				clear: both;
				color: gray;
				display: inline-block;
				font-weight: normal;
				text-decoration: italic;
			}
			
		</style>
	</head>
	<body>
		
		<div id="content">
		
			<h1>Flea Flicker</h1>
			<h3>Fantasy Draft List Keeper</h3>

			<div id="star">
				<form method="POST" action="ff.php">
					<div class="form-field-group">
						<label>Favorites:<span class="detail">Put each player on a separate line followed by team abbreviation (eg: Dez Bryant DAL).</span></label>
						<textarea rows="10" cols="50" name="star"></textarea>
					</div>
					<div class="form-button-group">
						<input type="submit" name="submit" value="Go!"/>
					</div>
				</form>
			</div>
			 
		</div>
		
	</body>
</html>
			