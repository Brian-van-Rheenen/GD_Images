<?php
//Dit is de connectie naar de database.
require "../../config.php";

//Dit is de query.
$opdracht = "SELECT * FROM mphp7_fotos";
$overzicht = mysqli_query($mysqli, $opdracht);

//Zet count op 0
$count = 0;

//Maak de cellen voor de kopjes.
echo "<table border='0' cellpadding='20'>";

//Header kopje in de tabel
echo "<p><strong>Alle Thumbnails:</strong></p>";

//Loop door alle rijen data heen
while ($rij = mysqli_fetch_array($overzicht))
{
	//Als count gelijk is aan 0
	if ($count == 0)
	{	
		//Start een tabelrij
		echo "<tr>";
			
		//count + 1
		$count++;
	}

	//Als count niet 0 is en kleiner dan 6 is
	if ($count != 0 && $count < 6)
	{
		//Maak de cellen voor de gegevens
		echo "<td><a href='details.php?id=" . $rij['ID'] . "'><img src='thumbs/" . $rij['Thumbnail']. "'/></a><br>&nbsp;" . $rij['Naam'] . "</td>";

		//count + 1
		$count++;
			
		//Als count gelijk is aan 6
		if ($count == 6)
		{
			//Zet count op 0
			$count = 0;
		}
	}
	
	//Als count gelijk is aan 0
	if ($count == 0)
	{	
		//Eindig een tabelrij
		echo "</tr>";
	}
}

//Sluit de tabel
echo "</table>";
?>