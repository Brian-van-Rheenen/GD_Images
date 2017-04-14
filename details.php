<?php
//Dit is de connectie naar de database.
require "../../config.php";

//Haal het id uit de url
$id = $_GET['id'];

//Dit is de query.
$opdracht = "SELECT * FROM mphp7_fotos WHERE `ID` = $id";
$overzicht = mysqli_query($mysqli, $opdracht);

//Loop door alle rijen data heen
while ($rij = mysqli_fetch_array($overzicht))
{
	//Echo de naam van de afbeelding
	echo "<p><strong><label>Naam afbeelding:</label></strong></p>";
	echo $rij['Naam'];
	
	//Echo de afbeelding met watermerk
	echo "<p><strong><label>Afbeelding met watermerk:</label></strong></p>";
	echo "<img src='watermerk/" . $rij['Watermerk']. "'/>";
	
	//Echo het onderschrift van de afbeelding
	echo "<p><strong><label>Onderschrift:</label></strong></p>";
	echo $rij['Onderschrift'];
	
	//Echo het IP adres van de gebruiker die de afbeelding geüpload heeft
	echo "<p><strong><label>IP adres:</label></strong></p>";
	echo $rij['IP'];
}
?>