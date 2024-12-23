<?php
// Include the PhpSpreadsheet library
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Path to your .xls file
$filePath = 'data.xlsx';

// Load the Excel file
$spreadsheet = IOFactory::load($filePath);

// Get the active sheet
$sheet = $spreadsheet->getActiveSheet();

// Loop through each row and column to read data
foreach ($sheet->getRowIterator() as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false); // Loop through all cells, even if empty
    
    foreach ($cellIterator as $cell) {
        echo $cell->getValue() . "<br>"; // Print cell value
    }
    echo "<br><br>"; // New line after each row
}
?>
