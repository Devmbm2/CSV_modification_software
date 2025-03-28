<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Upload & Mapping</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* CSS remains the same as in your original code */

        .slider-button {
        position: fixed;
        top: 50%;
        transform: translateY(-50%);
        /* Existing styles */
        text-decoration: none;
        color: #000;
        background-color: rgba(255, 255, 255, 0.8);
        padding: 10px;
        border-radius: 50%;
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s ease, background-color 0.2s ease;
        z-index: 10;
        }

        .left {
        left: 20px; /* Position left button */
        }

        .right {
        right: 20px; /* Position right button */
        }
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .drag-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }
        .drag-area:hover {
            border-color: #0d6efd;
        }
        .drag-area.active {
            border-color: #0d6efd;
        }
        .file-input {
            display: none;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<div class="container-fluid">
                                <style>
                                    /* Custom Styles */

                                    .btn-close {
                                                background-color: #fffbfb;
                                                color: white; 
                                    }  
                                    .load-mapping-btn {
                                                margin-left: 10px;
                                            }
                                            .modal-body ul {
                                                list-style-type: none;
                                                padding: 0;
                                            }
                                            .modal-body li {
                                                margin-bottom: 10px;
                                            }
                                            
                                </style>
                                <div class="row align-items-center mb-3">
                                    <!-- Title -->
                                    <div class="col text-center" style="padding-left: 450px;">
                                        <p class="display-5 m-0">CSV Processing Tool</p>
                                    </div>

                                    
                                    <!-- Load Mapping Button -->
                                    <div class="col-auto text-end" style="padding-right:200px;">
                                        <button type="button" class="btn btn-info" id="loadMappingButton">
                                            <i class="fas fa-folder-open me-2"></i>Saved Mappings
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="container-fluid">
                                <!-- Modal for Loading Mappings -->
                                <div class="modal fade" id="loadMappingModal" tabindex="-1" aria-labelledby="loadMappingModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title" id="loadMappingModalLabel">Available Mappings</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Mappings will be dynamically loaded here -->
                                                <p>Loading mappings...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                  <!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

                        <script src="asset/utils.js"></script>
                        <!-- JavaScript for AJAX -->




    <div id="csvUploadSection" class="container d-flex justify-content-center">
        <button id="uploadButton" class="btn btn-primary btn-lg">Upload File</button>

        <!-- Popup -->
        <form id="csvForm" action="process.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="page" value="upload">
            <div id="popupOverlay" class="popup-overlay">
                <div class="popup-content">
                    <h4>Upload File</h4>
                    <p>Drag & Drop your file here or click to browse</p>
                    <div id="dragArea" class="drag-area">
                        <p>Drag & Drop or <span style="color: #0d6efd;">Browse File</span></p>
                    </div>
                    <input id="fileInput" name="csv_file" type="file" class="file-input" accept=".csv">
                    <button id="uploadFileButton" name="upload_csv" type="submit" class="btn btn-primary">Upload File</button>
                </div>
            </div>
        </form>

    </div>

    <!-- Slider Buttons -->
<div class="slider-buttons">
  <!-- Right Button -->
  <a href="state.php" class="slider-button right">
    <i class="fas fa-chevron-right"></i> <!-- Font Awesome Icon -->
  </a>
</div>

    <script>
        const uploadButton = document.getElementById('uploadButton');
        const popupOverlay = document.getElementById('popupOverlay');
        const dragArea = document.getElementById('dragArea');
        const fileInput = document.getElementById('fileInput');

        uploadButton.addEventListener('click', () => {
            popupOverlay.style.display = 'flex';
        });

        popupOverlay.addEventListener('click', (e) => {
            if (e.target === popupOverlay) {
                popupOverlay.style.display = 'none';
            }
        });

        dragArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dragArea.classList.add('active');
        });

        dragArea.addEventListener('dragleave', () => {
            dragArea.classList.remove('active');
        });

        dragArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dragArea.classList.remove('active');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });

        dragArea.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file) {
                handleFile(file);
            }
        });

        function handleFile(file) {
            if (file.type !== 'text/csv') {
                alert('Only CSV files are allowed.');
                return;
            }
            dragArea.innerHTML = `<p>Selected File: ${file.name}</p>`;
        }
    </script>


                               <!-- Display the attached file if it exists -->
           <?php     if (isset($_SESSION['uploaded_file'])): ?>
        <div class="mb-3 text-success">
            File Attached: <?php echo basename($_SESSION['uploaded_file']); ?>
        </div>
    <?php endif; ?>



</body>
</html>