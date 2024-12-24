<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Tool to parse excel to pdf and txt">
    <meta name="keywords" content="Pdf,txt,parse">
    <meta name="author" content="Vivek A Nadig">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elixir Content</title>
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }

        .form-section {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            color: #333;
        }

        input[type="file"] {
            display: none;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        input[type="number"] {
            padding: 8px;
            margin-top: 8px;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .drag-area {
            background-color: #f0f0f0;
            padding: 20px;
            text-align: center;
            border: 2px dashed #4CAF50;
            margin-bottom: 20px;
            cursor: pointer;
            border-radius: 8px;
        }

        .drag-area.hover {
            background-color: #e8f5e9;
        }

        .drag-area p {
            margin: 0;
            font-size: 16px;
            color: #555;
        }

        .form-section h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

    </style>
    <script>
        // Function to handle the visibility of additional inputs based on selected page type
        function updatePageTypeFields() {
            const pageType = document.querySelector('input[name="page_type"]:checked').value;
            const singlePageInputs = document.getElementById('single-page-inputs');
            const multiplePageInputs = document.getElementById('multiple-page-inputs');

            // Hide all additional fields first
            singlePageInputs.style.display = 'none';
            multiplePageInputs.style.display = 'none';

            // Show the appropriate input fields based on selected page type
            if (pageType === 'singlepage') {
                singlePageInputs.style.display = 'block';
            } else if (pageType === 'multiplepage') {
                multiplePageInputs.style.display = 'block';
            }
        }

        // Drag-and-drop handling
        document.addEventListener('DOMContentLoaded', function() {
            const dropArea = document.getElementById('drop-area');
            const fileInput = document.getElementById('file');

            // Highlight drop area when file is dragged over
            dropArea.addEventListener('dragover', function(e) {
                e.preventDefault(); // Prevent default behavior (open as link for example)
                dropArea.classList.add('hover'); // Add hover effect
            });

            // Remove hover effect when drag leaves the drop area
            dropArea.addEventListener('dragleave', function() {
                dropArea.classList.remove('hover');
            });

            // Handle the drop event
            dropArea.addEventListener('drop', function(e) {
                e.preventDefault();
                dropArea.classList.remove('hover'); // Remove hover effect after drop

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files; // Assign the dropped files to the input element
                    alert('File ready for upload: ' + files[0].name);
                }
            });

            // Open file selector on drop area click
            dropArea.addEventListener('click', function() {
                fileInput.click();
            });

            // Handle file selection via the file input
            fileInput.addEventListener('change', function(e) {
                const selectedFiles = e.target.files;
                if (selectedFiles.length > 0) {
                    alert('File selected: ' + selectedFiles[0].name);
                }
            });
        });
    </script>
</head>
<body>

    
    <!-- File Upload Form -->
    <form action="process.php" method="POST" enctype="multipart/form-data">
        <!-- Drag and Drop Section -->
        <div id="drop-area" class="drag-area">
            <p>Drag and drop .xlsx, .xls, or .odb files here</p>
            <input type="file" name="file" id="file" accept=".xls, .xlsx, .odb" multiple required>
            <br><br>
            <label for="file">Or click here to select file</label>
        </div>

        <!-- Page Type Selection -->
        <div class="form-section">
            <h3>Page Type</h3>
            <label>
                <input type="radio" name="page_type" value="fullpage" required onclick="updatePageTypeFields()"> Full Page
            </label>
            <br>
            <label>
                <input type="radio" name="page_type" value="singlepage" onclick="updatePageTypeFields()"> Single Page with Single Input
            </label>
            <br>
            <label>
                <input type="radio" name="page_type" value="multiplepage" onclick="updatePageTypeFields()"> Multiple Pages with Two Inputs
            </label>
        </div>

        <!-- Additional Inputs for Page Type -->
        <div id="single-page-inputs" style="display:none;">
            <h3>Enter Page Number</h3>
            <label for="single_page_number">Page Number:</label>
            <input type="number" id="single_page_number" name="single_page_number" min="1">
        </div>

        <div id="multiple-page-inputs" style="display:none;">
            <h3>Enter Two Page Numbers</h3>
            <label for="first_page_number">Start Page:</label>
            <input type="number" id="first_page_number" name="first_page_number" min="1">
            <br><br>
            <label for="second_page_number">End Page:</label>
            <input type="number" id="second_page_number" name="second_page_number" min="1">
        </div>

        <!-- File Type Selection -->
        <div class="form-section">
            <h3>File Type</h3>
            <label>
                <input type="radio" name="file_type" value="pdf" required> PDF
            </label>
            <br>
            <label>
                <input type="radio" name="file_type" value="txt" required> TXT
            </label>
        </div>

        <!-- Submit Button -->
        <input type="submit" value="Upload and Process File" class="submit-btn">
    </form>

    <script>
        // Initialize the form by checking which page type is selected
        updatePageTypeFields();
    </script>
</body>
</html>
