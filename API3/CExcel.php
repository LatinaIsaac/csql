<?php

namespace Core;

require_once './phpspreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require("./phpmailer/PHPMailer.php");
require("./phpmailer/SMTP.php");

class CExcel {

    //http://localhost/API3/?KEY=sdhvY6232GBE3JH@sj2&statement=e&correo=jostin.barrantes_10@hotmail.com&carnet=2018

    private $keyGEN = "sdhvY6232GBE3JH@sj2";
    private $st;

    public function __construct($key, $correo, $carnet) {
        //$this->removeFile();
        //$this->email($correo, $carnet);

        if (strcmp($key, $this->keyGEN) != 0) {
            $this->response(190, "Invalid Session $key", 0, NULL);
        }

        $this->st = "SELECT * FROM reportes_cview order by Fecha";
        $rs = \Core\S_DATABASE::execute($this->st);

        $len = $rs->rowCount();
        $data = array();
        for ($index = 0; $index < $len; $index++) {
            array_push($data, $rs->fetch());
        }

        $this->genExcel($data, $len, $correo, $carnet);
    }

    public function genExcel($data, $len, $correo, $carnet) {
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
        $this->email($nombreDelDocumento, $correo, $carnet);
        exit;
    }

    private function email($nombreDelDocumento, $correo, $carnet) {
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->IsHTML(true);
        $mail->Username = "app.cview@gmail.com";
        $mail->Password = "IntelCorei01";
        $mail->SetFrom("app.cview@gmail.com");
        $mail->Subject = "Reporte de Asistencias";
        $mail->Body = "Estimado/a usuario <strong>$carnet</strong>, <br /> <br /> <br />";
        $mail->Body .= "Se adjunta el archivo excel con el reporte de asistencias.";
        $mail->AddAddress($correo);
        $mail->addAttachment("./$nombreDelDocumento");
        $boolean = $mail->validateAddress($correo);

        if ($boolean == 1) {
            if (!$mail->Send()) {
                echo $this->response(0, "not send: " . $mail->ErrorInfo, 0, 0);
                //echo "Mailer Error: " . $mail->ErrorInfo;
            } else {
                echo $this->response(202, "success", 1, 1);
                //echo "Message has been sent";
            }
        } else {
            echo $this->response(0, "correo no valido", 1, 1);
        }
    }

    private function removeFile() {
        unlink("Reporte_Asistencia.xlsx");
    }

    protected function response($status, $status_message, $data_lenght, $data) {
        header("HTTP/1.1 " . $status);
        header('Content-Type: application/json');

        $response['status'] = $status;
        $response['status_message'] = $status_message;
        $response['columnas'] = $data_lenght;
        $response['data'] = $data;

        $json_response = json_encode($response, JSON_PRETTY_PRINT);
        echo $json_response;
        exit;
    }

}
