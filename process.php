<?php
include 'ParseInfoTxt.php';
include 'ParseInfoPdf.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pageType = isset($_POST['page_type']) ? $_POST['page_type'] : 'None';
    $fileType = isset($_POST['file_type']) ? $_POST['file_type'] : 'None';
    $singlePageNumber = isset($_POST['single_page_number']) ? $_POST['single_page_number'] : '';
    $startPageNumber = isset($_POST['first_page_number']) ? $_POST['first_page_number'] : '';
    $endPageNumber = isset($_POST['second_page_number']) ? $_POST['second_page_number'] : '';
    
// Check if a file has been uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Get the uploaded file details
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileTypeFromUpload = $_FILES['file']['type'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        // Validate the file type
        if (in_array($fileExtension, ['xls', 'xlsx', 'odb', 'pdf', 'txt'])) {
            if($fileType === 'txt'){
                $out= new ParseInfoTxt($fileTmpPath);
                if($pageType === 'fullpage'){
                    $out->fullRowTxt();
                }
                elseif($pageType === 'singlepage'){
                    $out->singleRowTxt($singlePageNumber);
                }
                elseif($pageType === 'multiplepage'){
                    $out->rangeRowTxt($startPageNumber,$endPageNumber);
                }
            }
            elseif($fileType === 'pdf'){
                $out= new ParseInfoPdf($fileTmpPath);
                if($pageType === 'fullpage'){
                    $out->fullRowPdf();
                }
                elseif($pageType === 'singlepage'){
                    $out->singleRowPdf($singlePageNumber);
                }
                elseif($pageType === 'multiplepage'){
                    $out->rangeRowPdf($startPageNumber,$endPageNumber);
                }
            }

            // After processing, delete the file
            if (file_exists($fileTmpPath)) {
                unlink($fileTmpPath); // Deletes the file
                echo "File has been deleted after processing.";
            }
        } else {
            echo "Invalid file type. Please upload a .xls, .xlsx, .odb, .pdf, or .txt file.";
        }
    } 
    
    else {
        echo "No file uploaded or there was an upload error.";
    }
} else {
    echo "Invalid request.";
}
?>
