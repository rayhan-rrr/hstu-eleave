<?php  

$files = glob("../bootstrap/sys/modules/base/*xml");
var_dump($files);

if (is_array($files)) {

	echo "got::";

     foreach($files as $filename) {
        $xml_file = file_get_contents($filename, FILE_TEXT);
        // and proceed with your code

        print_r($xml_file);
     }

}

die();


?>