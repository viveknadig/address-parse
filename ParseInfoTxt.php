<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class ParseInfoTxt
{
Public $filePath;
Public $spreadsheet;
function __construct($filePath){
    $this->filePath=$filePath;
    if (!file_exists($filePath)) {
        die("File not found.");
    }
    try {
        $this->spreadsheet = IOFactory::load($filePath);
    } catch (Exception $e) {
        die('Error loading file: ' . $e->getMessage());
    }
}


function fullRowTxt(){
$sheet = $this->spreadsheet->getActiveSheet();
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="output.txt"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w'); // 'php://output' is a special file that writes directly to the browser
foreach ($sheet->getRowIterator() as $row) {
    $line = '';
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false); 

    foreach ($cellIterator as $cell) {
        $line .= $cell->getValue() . "\n"; 
    }
    fwrite($output, rtrim($line) . "\n\n"); 
}

fclose($output);
exit;

}

function rangeRowTxt($startingRow,$endingRow){
$sheet = $this->spreadsheet->getActiveSheet();

// Set the headers to force the download of the file
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="output.txt"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w'); // 'php://output' writes directly to the browser

for ($rowNum = $startingRow; $rowNum <= $endingRow; $rowNum++) {
    $row = $sheet->getRowIterator($rowNum)->current(); 
    $line = '';
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false); 
    foreach ($cellIterator as $cell) {
        $line .= $cell->getValue() . "\n";
    }
    fwrite($output, rtrim($line) . "\n\n");
}

fclose($output);
exit;
}


function singleRowTxt($rowNumber){
$sheet = $this->spreadsheet->getActiveSheet();
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="output.txt"');
header('Pragma: no-cache');
header('Expires: 0');
$output = fopen('php://output', 'w'); // 'php://output' writes directly to the browser
// Specify the row number to print (e.g., row 5)
$row = $sheet->getRowIterator($rowNumber)->current();
$line = ''; // Start an empty line for this row
$cellIterator = $row->getCellIterator();
$cellIterator->setIterateOnlyExistingCells(false); // Include empty cells

// Loop through each cell in the row
foreach ($cellIterator as $cell) {
    $line .= $cell->getValue() . "\n";
}
fwrite($output, rtrim($line) . "\n\n");
fclose($output);
exit;
}

}

?>
