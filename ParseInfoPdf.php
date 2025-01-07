<?php

require_once 'vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\IOFactory;
include 'fpdf.php';

class ParseInfoPdf
{
    private $spreadsheet;
    private $pdf;
    private $sheet;
    private $partWidth;
    private $partHeight;
    private $pageWidth;
    private $pageHeight;

    public function __construct($filePath)
    {
        $this->spreadsheet = IOFactory::load($filePath);
        $this->sheet = $this->spreadsheet->getActiveSheet();

        $this->pdf = new FPDF('L', 'mm', 'A3');
        $this->pdf->SetLeftMargin(10);
        $this->pdf->SetTopMargin(10);
        $this->pdf->SetRightMargin(10);
        $this->pdf->SetAutoPageBreak(true, 10);
        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', '', 12);

        $this->pageWidth = 420;
        $this->pageHeight = 297;
        $this->partWidth = $this->pageWidth / 3;
        $this->partHeight = $this->pageHeight / 4;
    }

    private function printRowInSections($x, $y, $cells)
    {
        $lineHeight = 12;
        $maxLinesPerSection = floor($this->partHeight / $lineHeight);
        $currentY = $y;

        foreach ($cells as $index => $cell) {
            if ($index == 1) {
                $cell = str_replace(["\r", "\n"], ' ', $cell);
                $this->pdf->SetXY($x, $currentY);
                $this->pdf->MultiCell($this->partWidth - 10, $lineHeight, $cell, 0, 'L');
                $currentY = $this->pdf->GetY();
            } else {
                $this->pdf->SetXY($x, $currentY);
                $this->pdf->MultiCell($this->partWidth - 10, $lineHeight, $cell, 0, 'L');
                $currentY = $this->pdf->GetY() + 5;
            }
        }
    }

    private function skipFirstRowIfHeader($rowNum)
    {
        $row = $this->sheet->getRowIterator($rowNum)->current();
        if ($row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cellValues = [];
            foreach ($cellIterator as $cell) {
                $cellValues[] = $cell->getValue();
            }

            $firstRowHeaders = ['name', 'address', 'phone number'];
            $headerCheck = array_map('strtolower', $cellValues);
            if (array_intersect($firstRowHeaders, $headerCheck)) {
                return true; // Skip this row
            }
        }
        return false; // Proceed with row
    }

    public function rangeRowPdf($startingRow, $endingRow)
    {
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $this->pdf->Rect($i * $this->partWidth, $j * $this->partHeight, $this->partWidth, $this->partHeight);
            }
        }

        $startX = 10;
        $startY = 10;
        $rowIndex = 0;
        $maxColumnsPerPage = 3;

        for ($rowNum = $startingRow; $rowNum <= $endingRow; $rowNum++) {
            if ($this->skipFirstRowIfHeader($rowNum)) {
                continue; // Skip the first row if it contains 'name', 'address', or 'phone number'
            }

            $row = $this->sheet->getRowIterator($rowNum)->current();

            if ($row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $cellValues = [];
                foreach ($cellIterator as $cell) {
                    $cellValues[] = $cell->getValue();
                }

                $cellValues[0] = strtoupper($cellValues[0]);

                $columnIndex = $rowIndex % $maxColumnsPerPage;
                $rowSectionIndex = floor($rowIndex / $maxColumnsPerPage);

                $sectionX = $columnIndex * $this->partWidth;
                $sectionY = $rowSectionIndex * $this->partHeight;

                $this->printRowInSections($sectionX + $startX, $sectionY + $startY, $cellValues);

                $rowIndex++;

                if ($rowIndex >= 12) {
                    $rowIndex = 0;
                    $this->pdf->AddPage();

                    for ($i = 0; $i < 3; $i++) {
                        for ($j = 0; $j < 4; $j++) {
                            $this->pdf->Rect($i * $this->partWidth, $j * $this->partHeight, $this->partWidth, $this->partHeight);
                        }
                    }
                }
            }
        }

        $this->pdf->Output('output.pdf', 'D');
    }

    public function singleRowPdf($rowNumber)
    {
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $this->pdf->Rect($i * $this->partWidth, $j * $this->partHeight, $this->partWidth, $this->partHeight);
            }
        }

        $startX = 10;
        $startY = 10;
        $rowIndex = 0;
        $maxColumnsPerPage = 3;

        if ($this->skipFirstRowIfHeader($rowNumber)) {
            return; // Skip the first row if it contains 'name', 'address', or 'phone number'
        }

        $row = $this->sheet->getRowIterator($rowNumber)->current();

        if ($row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $cellValues = [];
            foreach ($cellIterator as $cell) {
                $cellValues[] = $cell->getValue();
            }

            $cellValues[0] = strtoupper($cellValues[0]);

            $columnIndex = $rowIndex % $maxColumnsPerPage;
            $rowSectionIndex = floor($rowIndex / $maxColumnsPerPage);

            $sectionX = $columnIndex * $this->partWidth;
            $sectionY = $rowSectionIndex * $this->partHeight;

            $this->printRowInSections($sectionX + $startX, $sectionY + $startY, $cellValues);

            $rowIndex++;

            if ($rowIndex >= 12) {
                $rowIndex = 0;
                $this->pdf->AddPage();

                for ($i = 0; $i < 3; $i++) {
                    for ($j = 0; $j < 4; $j++) {
                        $this->pdf->Rect($i * $this->partWidth, $j * $this->partHeight, $this->partWidth, $this->partHeight);
                    }
                }
            }
        }

        $this->pdf->Output('output.pdf', 'D');
    }

    public function fullRowPdf()
    {
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $this->pdf->Rect($i * $this->partWidth, $j * $this->partHeight, $this->partWidth, $this->partHeight);
            }
        }

        $startX = 10;
        $startY = 10;
        $rowIndex = 0;
        $maxColumnsPerPage = 3;

        foreach ($this->sheet->getRowIterator() as $row) {
            $rowNum = $row->getRowIndex();
            if ($this->skipFirstRowIfHeader($rowNum)) {
                continue; // Skip the first row if it contains 'name', 'address', or 'phone number'
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $cellValues = [];
            foreach ($cellIterator as $cell) {
                $cellValues[] = $cell->getValue();
            }

            $cellValues[0] = strtoupper($cellValues[0]);

            $columnIndex = $rowIndex % $maxColumnsPerPage;
            $rowSectionIndex = floor($rowIndex / $maxColumnsPerPage);

            $sectionX = $columnIndex * $this->partWidth;
            $sectionY = $rowSectionIndex * $this->partHeight;

            $this->printRowInSections($sectionX + $startX, $sectionY + $startY, $cellValues);

            $rowIndex++;

            if ($rowIndex >= 12) {
                $rowIndex = 0;
                $this->pdf->AddPage();

                for ($i = 0; $i < 3; $i++) {
                    for ($j = 0; $j < 4; $j++) {
                        $this->pdf->Rect($i * $this->partWidth, $j * $this->partHeight, $this->partWidth, $this->partHeight);
                    }
                }
            }
        }

        $this->pdf->Output('output.pdf', 'D');
    }
}
?>

