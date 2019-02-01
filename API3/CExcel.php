<?php

namespace Core;

//require_once './excel-report/PHPExcel/Classes/PHPExcel.php';
require_once './phpspreadsheet/vendor/autoload.php';

//use PHPMailer\PHPMailer\PHPMailer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CExcel {

    //http://localhost/API3/?KEY=sdhvY6232GBE3JH@sj2&statement=e

    private $keyGEN = "sdhvY6232GBE3JH@sj2";
    private $st;

    public function __construct($key) {
        //$this->email();
        if (strcmp($key, $this->keyGEN) != 0) {
            $this->response(190, "Invalid Session $key", 0, NULL);
        }

        $this->st = "SELECT * FROM reportes_cview";
        $rs = \Core\S_DATABASE::execute($this->st);

        $len = $rs->rowCount();
        $data = array();
        for ($index = 0; $index < $len; $index++) {
            array_push($data, $rs->fetch());
        }

        $this->genExcel($data, $len);

        /*
          $string = "SELECT * FROM Reportes_tf";
          $rs = \Core\S_DATABASE::execute($string);

          $len = $rs->rowCount();
          $data = array();
          for ($index = 0; $index < $len; $index++) {
          array_push($data, $rs->fetch());
          }

          $this->llenarExcel($data, $len);
         */
    }

    public function genExcel($data, $len) {
        $documento = new Spreadsheet();
        $documento->getProperties()
                ->setCreator("Cview")
                ->setTitle('Reporte de Asistencias')
                ->setDescription('Este documento fue generado por medio de la app Cview');

        $nombreDelDocumento = "Reporte_Asistencia.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
        header('Cache-Control: max-age=0');

        $documento->getActiveSheet()
                ->setTitle("Reporte")
                ->setCellValue('A1', 'Docente')
                ->setCellValue('B1', 'Carnet Estudiante')
                ->setCellValue('C1', 'Fecha')
                ->setCellValue('D1', 'Hora Entrada')
                ->setCellValue('E1', 'Hora Salida')
                ->setCellValue('F1', 'Estado Entrada')
                ->setCellValue('G1', 'Estado Salida');

        for ($i = 2; $i < $len + 2; $i++) {
            $documento->getActiveSheet()
                    ->setCellValue('A' . $i, $data[$i - 2]["Docente"])
                    ->setCellValue('B' . $i, $data[$i - 2]["Carnet"])
                    ->setCellValue('C' . $i, $data[$i - 2]["Fecha"])
                    ->setCellValue('D' . $i, $data[$i - 2]["Hora_entrada"])
                    ->setCellValue('E' . $i, $data[$i - 2]["Hora_salida"])
                    ->setCellValue('F' . $i, $data[$i - 2]["Estado_entrada"])
                    ->setCellValue('G' . $i, $data[$i - 2]["Estado_salida"]);
        }

        foreach (range('A', 'G') as $columnID) {
            $documento->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $documento->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);

        $documento->getActiveSheet()->getStyle('A1:G1')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('92CDDC');

        $objWriter = IOFactory::createWriter($documento, 'Xlsx');
        $objWriter->save($nombreDelDocumento);
        //$objWriter->save('php://output');
        $this->sendEmail($nombreDelDocumento);
        exit;
    }

    /* public function llenarExcel($data, $len) {
      date_default_timezone_set('America/Costa_Rica');

      $filename = 'Reporte_Asistencia';
      $objPHPExcel = new PHPExcel();

      $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A1', 'Docente')
      ->setCellValue('B1', 'Carnet Estudiante')
      ->setCellValue('C1', 'Fecha')
      ->setCellValue('D1', 'Hora Entrada')
      ->setCellValue('E1', 'Hora Salida')
      ->setCellValue('F1', 'Estado Entrada')
      ->setCellValue('G1', 'Estado Salida');

      for ($i = 2; $i < $len + 2; $i++) {
      $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A' . $i, $data[$i - 2]["Docente"])
      ->setCellValue('B' . $i, $data[$i - 2]["Carnet"])
      ->setCellValue('C' . $i, $data[$i - 2]["Fecha"])
      ->setCellValue('D' . $i, $data[$i - 2]["Hora_entrada"])
      ->setCellValue('E' . $i, $data[$i - 2]["Hora_salida"])
      ->setCellValue('F' . $i, $data[$i - 2]["Estado_entrada"])
      ->setCellValue('G' . $i, $data[$i - 2]["Estado_salida"]);
      }

      foreach (range('A', 'G') as $columnID) {
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }

      $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);

      $objPHPExcel->getActiveSheet()
      ->getStyle('A1:G1')
      ->getFill()
      ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
      ->getStartColor()
      ->setARGB('00CCFF');

      $objPHPExcel->getActiveSheet()->setTitle('Reporte_Asistencia.xls');
      $objPHPExcel->setActiveSheetIndex(0);
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

      $objWriter->save($filename . '.xls');
      //$objWriter->save('php://output');
      $this->sendEmail($filename);
      exit;
      } */

    /* private function email() {
      ini_set('display_errors', 1);
      error_reporting(E_ALL);
      $from = "app.cview@gmail.com";
      $to = "jostin.barrantes_10@hotmail.com";
      $subject = "Checking PHP mail";
      $message = "PHP mail works just fine";
      $headers = "From:" . $from;

      $bool = mail($to, $subject, $message, $headers);
      if ($bool == true) {
      echo "Correo enviado";
      } else {
      echo "Correo no enviado";
      }
      } */

    /* private function sendEmail($filename) {
      $mail = new PHPMailer;
      $mail->isSMTP();
      $mail->SMTPDebug = 2;
      $mail->Host = '186.159.129.2';
      $mail->Port = 587;
      $mail->SMTPAuth = true;
      $mail->Username = 'app.cview@gmail.com';
      $mail->Password = 'IntelCorei01';
      $mail->setFrom('app.cview@gmail.com', 'Cview');
      //$mail->addReplyTo('reply-box@hostinger-tutorials.com', 'Your Name');
      $mail->addAddress('jostin.barrantes_10@hotmail.com', 'Jostin');
      $mail->Subject = 'PHPMailer SMTP message';
      $mail->msgHTML(file_get_contents('message.html'), __DIR__);
      $mail->AltBody = 'This is a plain text message body';
      $mail->addAttachment('test.txt');
      if (!$mail->send()) {
      echo 'Mailer Error: ' . $mail->ErrorInfo;
      } else {
      echo 'Message sent!';
      }
      } */

    private function sendEmail($filename) {
        //yamileth.ramirez@ulatina.cr
        $to = "jostin.barrantes_10@hotmail.com";
        $subject = "Reporte de Asistencias";
        $files = $filename;

        $msg = "Se adjunta el archivo excel con el reporte de asistencias.";
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
            echo "Correo enviado";
        } else {
            echo "Correo no enviado";
        }
    }

}
