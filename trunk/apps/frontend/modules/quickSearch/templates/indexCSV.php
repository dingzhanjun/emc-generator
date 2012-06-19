<?php
	echo "Website;Age;Pick-up Date;Truck Type;Full/Partial;DH(O);Origin;Trip;Destination;DH(D);Contact;Credit Score;Ft;Klbs;Company\n";
	foreach ($loads as $jobboard_name => $data) {
		foreach ($data as $index => $loads) {
			echo $jobboard_name.';';
			foreach ($loads as $key => $value)
				echo $value.';';
			echo "\n";
		}
	}
?>