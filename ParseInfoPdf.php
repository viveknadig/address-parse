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
        $this->pdf->SetLeftMargin(5);
        $this->pdf->SetTopMargin(5);
        $this->pdf->SetRightMargin(0);
        $this->pdf->SetAutoPageBreak(true, 10);
        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', '', 8);
        $this->pageWidth = 420;
        $this->pageHeight = 297;
        $this->partWidth = $this->pageWidth / 4;
        $this->partHeight = $this->pageHeight / 7;
    }

    private function printRowInSections($x, $y, $cells)
    {
        $lineHeight = 5;
        $maxLinesPerSection = floor($this->partHeight / $lineHeight);
        $currentY = $y;

        foreach ($cells as $index => $cell) {
            $cell = str_replace(["\r", "\n"], ' ', $cell);
            $this->pdf->SetXY($x, $currentY);
            $this->pdf->MultiCell($this->partWidth - 10, $lineHeight, $cell, 0, 'L');
            $currentY = $this->pdf->GetY();
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
                return true;
            }
        }
        return false;
    }

    public function rangeRowPdf($startingRow, $endingRow)
    {
        $topMargin = 5;
        $leftMargin = 5;
        $startX = $leftMargin;
        $startY = $topMargin;

        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $this->pdf->Rect($i * $this->partWidth, $j * $this->partHeight, $this->partWidth, $this->partHeight);
            }
        }

        $rowIndex = 0;
        $maxColumnsPerPage = 4;
        $totalRows = $endingRow - $startingRow + 1;
        $rowsPrinted = 0;
        $highestRow = $this->sheet->getHighestRow();

        for ($rowNum = $startingRow; $rowNum <= $endingRow; $rowNum++) {
            if ($this->skipFirstRowIfHeader($rowNum)) {
                continue;
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
                $rowsPrinted++;

                if ($rowsPrinted >= 28 && $rowNum < $highestRow) {
                    $rowIndex = 0;
                    $rowsPrinted = 0;
                    $this->pdf->AddPage();

                    for ($i = 0; $i < 4; $i++) {
                        for ($j = 0; $j < 7; $j++) {
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
        $topMargin = 5;
        $leftMargin = 5;
        $startX = $leftMargin;
        $startY = $topMargin;

        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $this->pdf->Rect($i * $this->partWidth, $j * $this->partHeight, $this->partWidth, $this->partHeight);
            }
        }

        $rowIndex = 0;
        $maxColumnsPerPage = 4;

        if ($this->skipFirstRowIfHeader($rowNumber)) {
            return;
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
        }

        $this->pdf->Output('output.pdf', 'D');
    }

    public function fullRowPdf()
    {
        $topMargin = 5;
        $leftMargin = 5;
        $startX = $leftMargin;
        $startY = $topMargin;

        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $this->pdf->Rect($i * $this->partWidth, $j * $this->partHeight, $this->partWidth, $this->partHeight);
            }
        }

        $rowIndex = 0;
        $maxColumnsPerPage = 4;
        $highestRow = $this->sheet->getHighestRow();
        $rowsPrinted = 0;

        foreach ($this->sheet->getRowIterator() as $row) {
            $rowNum = $row->getRowIndex();
            if ($this->skipFirstRowIfHeader($rowNum)) {
                continue;
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
            $rowsPrinted++;

            if ($rowsPrinted >= 28 && $rowNum < $highestRow) {
                $rowIndex = 0;
                $rowsPrinted = 0;
                $this->pdf->AddPage();

                for ($i = 0; $i < 4; $i++) {
                    for ($j = 0; $j < 7; $j++) {
                        $this->pdf->Rect($i * $this->partWidth, $j * $this->partHeight, $this->partWidth, $this->partHeight);
                    }
                }
            }
        }

        $this->pdf->Output('output.pdf', 'D');
    }
}
