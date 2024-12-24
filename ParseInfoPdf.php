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

        $this->pdf = new FPDF('L', 'mm', 'A5');
        $this->pdf->SetLeftMargin(0);
        $this->pdf->SetTopMargin(0);
        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', '', 10);

        $this->pageWidth = 210;
        $this->pageHeight = 148;
        $this->partWidth = $this->pageWidth / 2;
        $this->partHeight = $this->pageHeight / 2;
    }

    private function printRowInSections($x, $y, $cells)
    {
        $lineHeight = 8;
        $gap = 5;
        $maxLinesPerSection = floor($this->partHeight / $lineHeight);
        
        $currentY = $y;
        
        foreach ($cells as $cell) {
            $this->pdf->SetXY($x, $currentY);
            $this->pdf->MultiCell($this->partWidth - 10, $lineHeight, $cell, 0, 'L');
            $currentY = $this->pdf->GetY() + $gap;
        }
    }

    public function singleRowPdf($rowNumber)
    {
        // Draw the 4 sections with borders
        $this->pdf->Rect(0, 0, $this->partWidth, $this->partHeight);
        $this->pdf->Rect($this->partWidth, 0, $this->partWidth, $this->partHeight);
        $this->pdf->Rect(0, $this->partHeight, $this->partWidth, $this->partHeight);
        $this->pdf->Rect($this->partWidth, $this->partHeight, $this->partWidth, $this->partHeight);

        $startX = 10;
        $startY = 10;
        $rowIndex = 0;
        $maxColumnsPerPage = 2;

        // Get the specific row by its index
        $row = $this->sheet->getRowIterator($rowNumber)->current();  // Get the row by row number

        if ($row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $cellValues = [];
            foreach ($cellIterator as $cell) {
                $cellValues[] = $cell->getValue();
            }

            $columnIndex = $rowIndex % $maxColumnsPerPage;
            $rowSectionIndex = floor($rowIndex / $maxColumnsPerPage);

            $sectionX = ($columnIndex == 1) ? $this->partWidth : 0;
            $sectionY = ($rowSectionIndex == 1) ? $this->partHeight : 0;

            // Print the row data in the correct section
            $this->printRowInSections($sectionX + $startX, $sectionY + $startY, $cellValues);

            $rowIndex++;

            // If more than 4 rows have been printed, add a new page
            if ($rowIndex >= $maxColumnsPerPage * 2) {
                $rowIndex = 0;
                $this->pdf->AddPage();
                $startY = 10;

                // Redraw the sections on the new page
                $this->pdf->Rect(0, 0, $this->partWidth, $this->partHeight);
                $this->pdf->Rect($this->partWidth, 0, $this->partWidth, $this->partHeight);
                $this->pdf->Rect(0, $this->partHeight, $this->partWidth, $this->partHeight);
                $this->pdf->Rect($this->partWidth, $this->partHeight, $this->partWidth, $this->partHeight);
            }
        }

        $this->pdf->Output('output.pdf', 'I');
    }

    public function generatePdf()
    {
        $this->pdf->Rect(0, 0, $this->partWidth, $this->partHeight);
        $this->pdf->Rect($this->partWidth, 0, $this->partWidth, $this->partHeight);
        $this->pdf->Rect(0, $this->partHeight, $this->partWidth, $this->partHeight);
        $this->pdf->Rect($this->partWidth, $this->partHeight, $this->partWidth, $this->partHeight);

        $startX = 10;
        $startY = 10;
        $rowIndex = 0;
        $maxColumnsPerPage = 2;

        foreach ($this->sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $cellValues = [];
            foreach ($cellIterator as $cell) {
                $cellValues[] = $cell->getValue();
            }

            $columnIndex = $rowIndex % $maxColumnsPerPage;
            $rowSectionIndex = floor($rowIndex / $maxColumnsPerPage);

            $sectionX = ($columnIndex == 1) ? $this->partWidth : 0;
            $sectionY = ($rowSectionIndex == 1) ? $this->partHeight : 0;

            $this->printRowInSections($sectionX + $startX, $sectionY + $startY, $cellValues);

            $rowIndex++;

            if ($rowIndex >= $maxColumnsPerPage * 2) {
                $rowIndex = 0;
                $this->pdf->AddPage();
                $startY = 10;

                $this->pdf->Rect(0, 0, $this->partWidth, $this->partHeight);
                $this->pdf->Rect($this->partWidth, 0, $this->partWidth, $this->partHeight);
                $this->pdf->Rect(0, $this->partHeight, $this->partWidth, $this->partHeight);
                $this->pdf->Rect($this->partWidth, $this->partHeight, $this->partWidth, $this->partHeight);
            }
        }

        $this->pdf->Output('output.pdf', 'I');
    }
}

?>
