<?php

require_once './config.php';
require_once './CEmail.php';
require_once './CExcel.php';
require_once './database.php';
require_once './CStatement.php';
require_once './image.php';

//http://localhost/API3/?KEY=sdhvY6232GBE3JH@sj2&statement=SELECT * FROM cervecero_login
//http://localhost/API3/?KEY=sdhvY6232GBE3JH@sj2&statement=SELECT user_id,passw FROM cervecero_login
//http://localhost/API3/?KEY=sdhvY6232GBE3JH@sj2&statement=SELECT user_id,passw FROM cervecero_login WHERE user_id > ? &p=2
//http://localhost/API3/?KEY=sdhvY6232GBE3JH@sj2&statement=UPDATE cervecero_login SET username=? WHERE user_id=?&p=tester,4
//http://localhost/API3/?KEY=sdhvY6232GBE3JH@sj2&statement=INSERT INTO cervecero_login(username,passw,age,email,number,acc_lvl) VALUES(?,?,?,?,?,?)&p=isaachjk,1234,24,jissaac@hotmail,123434,0

switch ($_POST["statement"]) {
    case "m":
        $mail = new CEmail($_POST["File"] ?? null, $_POST["Number"] ?? null, $_POST["Email"] ?? null);
        break;
    case "e":
        $excel = new CExcel();
        break;
    case "z":
    $targetdir = 'images/';   
        // name of the directory where the files should be stored
    $targetfile = $targetdir.$_FILES['file']['name'];
    if (file_exists($targetfile))
        unlink($targetfile);
        
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetfile)) {
    	echo "funciono";
    } else { 
        echo "fallo";
    }
    break;
    default:
        $stmt_ = new Core\CStatement($_POST["KEY"], $_POST["statement"], $_POST["p"] ?? NULL);
        break;
}

function generateImage($img)
{
    $folderPath = "images/";

    $image_parts = explode(";base64,", $img);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $file = $folderPath . uniqid() . '.png';

    file_put_contents($file, $image_base64);
}

function base64_to_jpeg($base64_string, $output_file) {
    // open the output file for writing
    $ifp = fopen( $output_file, 'wb' ); 

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    // we could add validation here with ensuring count( $data ) > 1
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // clean up the file resource
    fclose( $ifp ); 

    return $output_file; 
}