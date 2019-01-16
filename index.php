<!doctype html>
<html lang="en">
	<head>
		
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		
		<link rel="stylesheet" href="style.css">
		
		<title>Charlie Hickling - Portfolio</title>
		
	</head>
	<body>
		
		<div class="container" style="margin-top: 30px">
			
			<div class="jumbotron">
				<center><h1>Vehicle Lookup</h1></center><br />
				<p>Enter a vehicle registration number to find out information on it via the UKVD API.<br />Due to free account limitations, vehicle registration number must contain the letter 'A'.</p>
				<br /><center><label for="reg-number"><h2>Registration Number</h2></label></center>
				<form class="input-group" action="index.php" method="POST">
					<input type="text" class="form-control reg" id="reg-number" name="reg-number" value="<?php
						if(isset($_POST["reg-number"])) {
							echo $_POST["reg-number"];
						} else {
							echo "AJ17 OHB";
						}
					?>">
					<br />
					<span class="input-group-btn"><button type="submit" class="btn btn-warning mb-2 reg-btn">Search</button></span>
				</form>
				
				<?php
					if(isset($_POST["reg-number"])) {
						$reg = $_POST["reg-number"];
						$reg = str_replace(" ", "", $reg);
						if($reg == "") return;
						
						// Init cURL session
						$curl = curl_init();

						// Set API Key
						$ApiKey = "6996c11e-2635-4aa7-b0ff-1c2e5d6933cb";

						// Construct URL String
						$url = "https://uk1.ukvehicledata.co.uk/api/datapackage/%s?v=2&api_nullitems=1&key_vrm=%s&auth_apikey=%s";
						$url = sprintf($url, "VehicleData", $reg, $ApiKey); // Syntax: sprintf($url, "PackageName", "VRM", ApiKey);
						// Note your package name here. There are 5 standard packagenames. Please see your control panel > weblookup or contact your account manager

						// Create array of options for the cURL session
						curl_setopt_array($curl, array(
						  CURLOPT_URL => $url,
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_SSL_VERIFYPEER => false,
						  CURLOPT_TIMEOUT => 30,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => "GET"
						));

						// Execute cURL session and store the response in $response
						$response = curl_exec($curl);

						// If the operation failed, store the error message in $error
						$error = curl_error($curl);

						// Close cURL session
						curl_close($curl);

						// If there was an error, print it to screen. Otherwise, unserialize response and print to screen.
						if ($error) {
							echo "cURL Error: " . $error;
						} else {
							$json = json_decode($response, true);
							if(!$json || (!isset($json["Response"]["StatusCode"]) || $json["Response"]["StatusCode"] != "Success")) {
								if(isset($json["Response"]["StatusMessage"])) {
									echo "<div class='jumbotron'>Error (".$reg."): ".$json["Response"]["StatusMessage"]."</div>"; 
								} else {
									echo "<div class='jumbotron'>Unknown error (".$reg.").</div>";
								}
							} else {
								$reginfo = $json["Response"]["DataItems"]["VehicleRegistration"];
								echo '<div class="jumbotron"><center><h2>'.$reginfo["Make"]." ".$json["Response"]["DataItems"]["SmmtDetails"]["Range"].'</h2></center><div class="table-responsive"><table class="table">
									  <thead>
										<tr>
										  <th scope="col">Make</th>
										  <th scope="col">Model</th>
										  <th scope="col">Variant</th>
										  <th scope="col">Manufactured</th>
										  <th scope="col">Colour</th>
										  <th scope="col">Body</th>
										  <th scope="col">Engine</th>
										  <th scope="col">Transmission</th>
										  <th scope="col">Fuel Type</th>
										</tr>
									  </thead>
									  <tbody>
										<tr>
										  <td>'.$reginfo["Make"].'</td>
										  <td>'.$json["Response"]["DataItems"]["SmmtDetails"]["Range"].'</td>
										  <td>'.$json["Response"]["DataItems"]["SmmtDetails"]["ModelVariant"].'</td>
										  <td>'.$reginfo["YearMonthFirstRegistered"].'</td>
										  <td>'.$reginfo["Colour"].'</td>
										  <td>'.$reginfo["DoorPlanLiteral"].'</td>
										  <td>'.$reginfo["EngineCapacity"].'cc</td>
										  <td>'.$reginfo["Transmission"].'</td>
										  <td>'.$reginfo["FuelType"].'</td>
										</tr>
									  </tbody>
									</table></div></div>
								';
								//var_dump(json_decode($response, true)); // For demonstration purposes - Unserialize response & dump array contents to screen
							}
						}
					}
				?>
			</div>
			
		</div>

		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>