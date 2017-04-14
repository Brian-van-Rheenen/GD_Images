<?php
require "../../config.php";

//Is de upload geslaagd?
if ($_FILES["bestand"]["error"] == 0)
{
	//Is het bestand een PNG?
	if ($_FILES["bestand"]['type'] == "image/jpg" ||
		$_FILES["bestand"]['type'] == "image/jpeg" ||
		$_FILES["bestand"]['type'] == "image/pjpeg")
	{
		//Maak het bestandspad
		$map = __DIR__ . "/originals/";
		
		//Maak de bestandsnaam
		$bestand = $_FILES["bestand"]["name"];
		$pad = $map . $bestand;
		$org_bestand = $bestand;
		
		//Maak een cijfer om achter de naam te plakken
		$additional = '1';

		//Zolang het bestand al bestaat
		while (file_exists($pad)) 
		{
			//Haal de informatie van het pad op
			$info = pathinfo($pad);
			
			//Verander de naam en plak het cijfer er achter
			$pad = $info['dirname'] . '/' . $info['filename'] . $additional . '.' . $info['extension'];
			$org_bestand = $info['filename'] . $additional . '.' . $info['extension'];
		}
		
		//Verplaats de upload naar de juiste map
		if (move_uploaded_file($_FILES["bestand"]["tmp_name"], $pad))
		{
			/**** Begin thumbnail maken  ****/
			
			//Hoe groot is de image?
			$info = getimagesize($pad);
			$breedte = $info[0];
			$hoogte = $info[1];
			
			//Bepaal hoe groot de thumbnail wordt
			if ($breedte >= $hoogte)
			{
				//Landscape
				$thumb_breedte = 100;
				$thumb_hoogte = round(($thumb_breedte * $hoogte) / $breedte);
				
				$water_breedte = 100;
				$water_hoogte = round(($water_breedte * $hoogte) / $breedte);
			}
			
			else
			{
				//Portrait
				$thumb_hoogte = 200;
				$thumb_breedte = round(($thumb_hoogte * $breedte) / $hoogte);
				
				$water_hoogte = 200;
				$water_breedte = round(($water_hoogte * $breedte) / $hoogte);
			}
			
			//Lees de originele image in
			$origineel = imagecreatefromjpeg($pad);
			
			//Maak een lege thumbnail
			$thumbnail = imagecreatetruecolor($thumb_breedte, $thumb_hoogte);
			
			//Kopieer het origneel verkleind naar de thumbnail
			imagecopyresampled($thumbnail, $origineel, 0, 0, 0, 0, $thumb_breedte, $thumb_hoogte, $breedte, $hoogte);
			
			//Maak het bestandspad
			$map = __DIR__ . "/thumbs/";
			
			//Bepaal het pad voor de thumbnail
			$thumb_pad = $map . "tn_" . $bestand;
			$thum_bestand = "tn_" . $bestand;
			
			//Zolang het bestand al bestaat
			while (file_exists($thumb_pad)) 
			{
				//Haal de informatie van het pad op
				$info = pathinfo($thumb_pad);

				//Verander de naam en plak het cijfer er achter
				$thumb_pad = $info['dirname'] . '/' . $info['filename'] . $additional . '.' . $info['extension'];
				$thum_bestand = $info['filename'] . $additional . '.' . $info['extension'];
			}
			
			//Sla de thumbnail op
			imagejpeg($thumbnail, $thumb_pad);
			
			/**** Eind thumbnail maken  ****/
			
			/**** Begin watermerk maken  ****/
			
			//Maak de bestandsnaam
			$bestand = $_FILES["bestand"]["name"];
			$map = __DIR__ . "/originals/";
			$pad = $map . $bestand;
			
			//Maak het pad voor het watermerk
			$map = __DIR__ . "/watermerk/";
			$wm_pad = $map . "wm_" . $bestand;
			$wm_bestand = "wm_" . $bestand;
			
			//Zolang het bestand al bestaat
			while (file_exists($wm_pad)) 
			{
				//Haal de informatie van het pad op
				$info = pathinfo($wm_pad);

				//Verander de naam en plak het cijfer er achter
				$wm_pad = $info['dirname'] . '/' . $info['filename'] . $additional . '.' . $info['extension'];
				$wm_bestand = $info['filename'] . $additional . '.' . $info['extension'];
			}
			
			//Maak de images
			$new_wm = imagecreatetruecolor($breedte, $hoogte);
			$source = imagecreatefromjpeg($pad);
			$watermerk = imagecreatefromjpeg("watermerk/watermerk.jpg");
			
			//Voeg de originele image samen met het watermerk
			imagecopyresampled($new_wm, $watermerk, 0, 0, 0, 0, $breedte, $hoogte, 200, 200);
			imagecopymerge($source, $new_wm, 0, 0, 0, 0, $breedte, $hoogte, 35);
			imagejpeg($source, $wm_pad, 100);
			
			/**** Eind watermerk maken  ****/
			
			//Lees de gegevens uit de POST
			$naam = $_POST['naam'];
			$onderschrift = $_POST['onderschrift'];
			$ip = $_SERVER['REMOTE_ADDR'];
			
			//Plaats de gegevens in de database
			$query = mysqli_query($mysqli, "INSERT INTO mphp7_fotos VALUES(NULL, '$naam','$onderschrift','$ip', '$thum_bestand', '$wm_bestand')");
			
			//Stuur de gebruiker door
			header("Location: overzicht.php");
		}
		
		else
		{
			echo "Kon het bestand niet verplaatsen.";
		}
	}
	
	else
	{
		echo "Je kunt alleen JPG's uploaden.";
	}
}

else
{
	echo "De upload is niet gelukt. Check of je wel iets aan het uploaden bent.";
}