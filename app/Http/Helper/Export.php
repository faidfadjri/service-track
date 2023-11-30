<?php

namespace App\Http\Helper;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export
{
    public static function excel($data, $file) {
        try {
            $filename  = $file;
            $dataArray = json_decode($data, true);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
    
            # Set headers 
            $headers = array_keys($dataArray[0]);
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(Export::getColumnLetter($index + 2) . '2', $header);
    
                # Style header cell
                $headerCell = $sheet->getCell(Export::getColumnLetter($index + 2) . '2');
                $headerCell->getStyle()
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $headerCell->getStyle()->getFont()->setBold(true);
                $headerCell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getRowDimension(2)->setRowHeight(25);

                # Set header color
                $sheet->getStyle(Export::getColumnLetter($index + 2) . '2')
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('92D050');
            }
    
            # Fill data
            foreach ($dataArray as $rowIndex => $row) {
                foreach ($headers as $columnIndex => $header) {
                    $sheet->setCellValue(Export::getColumnLetter($columnIndex + 2) . ($rowIndex + 3), $row[$header]);
                    
                    # Style data cell
                    $dataCell = $sheet->getCell(Export::getColumnLetter($columnIndex + 2) . ($rowIndex + 3));
                    $dataCell->getStyle()
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                    $dataCell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }
            }
    
            # Autosize width for all columns
            foreach (range('B', Export::getColumnLetter(count($headers) + 1)) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
    
            # Set excel file header
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');
    
            # Save to excel
            $writer = new Xlsx($spreadsheet);
            $writer->save('php:#output');
    
            # Stop continuous action
            exit();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    # Convert numbers of array to alphabet (EX: 1 -> A, 2 -> B, etc.)
    private static function getColumnLetter($columnNumber) {
        $letter = '';
        while ($columnNumber > 0) {
            $remainder = ($columnNumber - 1) % 26;
            $letter = chr(65 + $remainder) . $letter;
            $columnNumber = ($columnNumber - $remainder - 1) / 26;
        }
        return $letter;
    }
}
