<?php

require_once './CStatement.php';
require_once './excel-report/PHPExcel/Classes/PHPExcel.php';

use Core\CStatement;

class CExcel extends CStatement {
    
    //http://localhost/API2/?KEY=73461234dgvbsv2e18r5rt&statement=e

    public function __construct() {
        $string = "SELECT * FROM reportes_cview";
        $rs = \Core\S_DATABASE::execute($string);

        $len = $rs->rowCount();
        $data = array();
        for ($index = 0; $index < $len; $index++) {
            array_push($data, $rs->fetch());
        }

        $this->llenarExcel($data, $len);
    }

    public function llenarExcel($data, $len) {
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

        for ($i = 2; $i < $len+2; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $data[$i-2]["Docente"])
                    ->setCellValue('B'.$i, $data[$i-2]["Carnet"])
                    ->setCellValue('C'.$i, $data[$i-2]["Fecha"])
                    ->setCellValue('D'.$i, $data[$i-2]["Hora_entrada"])
                    ->setCellValue('E'.$i, $data[$i-2]["Hora_salida"])
                    ->setCellValue('F'.$i, $data[$i-2]["Estado_entrada"])
                    ->setCellValue('G'.$i, $data[$i-2]["Estado_salida"]);
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
        /*header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;Filename=$filename.xls");
        header('Cache-Control: max-age=0');*/
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        
        $objWriter->save($filename.'.xls');
        //$objWriter->save('php://output');
        
        $this->sendEmail($filename);
        exit;   
    }
    
    private function sendEmail($filename){
        $mail = new CEmail($filename);        
    }

}
