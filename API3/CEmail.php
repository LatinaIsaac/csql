<?php

require_once './CStatement.php';
use Core\CStatement;

class CEmail extends CStatement{

    public function __construct($filename = null, $numberCod = null, $email = null) {
        $this->file = $filename;
        $this->numberCod = $numberCod;
        $this->email = $email;

        if (is_null($this->file)) {
            $this->sendEmail();
        } else {
            $filename = $filename . '.xls';
            $this->sendEmailExcel($filename);
        }
    }

    private function sendEmail() {
        $to = $this->email;
        $subject = "Recuperaci&oacute;n de Contrase&ntilde;a";
        $msg = "El c&oacute;digo para recuperaci&oacute;n de su contrase&ntilde;a es: <b>" . $this->numberCod."<b>";
        $headers = "From: Cervecero App <Cervecero@cerveceroapp.com>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
           
        $success = mail($to, utf8_decode($subject), utf8_decode($msg), $headers);
        if ($success)
            echo $this->response(402, "success", 1, 1);
        else
            echo $this->response(401, "Error: " . error_get_last()['message'], 1, 1);
    }

    private function sendEmailExcel($filename) {
        //yamileth.ramirez@ulatina.cr
        $to = "jostin.barrantes_10@hotmail.com";
        $subject = "Reporte de Asistencias";
        $files = $filename;

        $msg = "Se adjunta el archivo excel con el reporte de asistencias";
        $headers = "From: Sistema Control de Asistencias <sistcontrol@flutter.com>";

        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

        $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Transfer-Encoding: 7bit\n\n" . $msg . "\n\n";
        $message .= "--{$mime_boundary}\n";

        $file = fopen($files, "rb");
        $data = fread($file, filesize($files));
        fclose($file);
        $data = chunk_split(base64_encode($data));
        $message .= "Content-Disposition: attachment;\n" . " filename=\"$files\"\n" .
                "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        $message .= "--{$mime_boundary}\n";

        $bool = mail($to, $subject, $message, $headers);
        unlink($files);
        if ($bool == true) {
            echo $this->response(200, "success", 1, 1);
            //echo "Correo enviado";
        } else {
            echo $this->response(0, "not send", 0, 0);
            //echo "Correo no enviado";
        }
    }

}
