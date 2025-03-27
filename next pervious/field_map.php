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
        body {
            background-color: #f8f9fa;
        }
       
/* General styling for the slider buttons container */
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
 /* Style for the popup */
 .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 8px;
        }

        /* Overlay to dim the background */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Button styling */
        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-close {
            background-color: #f44336;
            color: white;
        }

        .btn-next {
            background-color: #4caf50;
            color: white;
        }
        #mappingFields {
            max-height: 800px;
            overflow-y: auto;
        }

    </style>
</head>
<body>
    <?php
    session_start();

    // Enable error reporting
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    ?>
    <div class="container-md">
        <br>
                <p class="display-6 text-center" >CSV Processing Tool</p>
                <hr>
           <?php     if (isset($_GET['status']) && $_GET['status'] === 'success' && isset($_GET['message'])) { ?>
            <div class="alert alert-info mt-3">
                <?php echo $_GET['message'];?>
        </div>

          <?php  } ?>
            <br>
            <h4 class="form-header text-center">   Field Mapping </h4>

           
                         

<form method="post" action="process.php" class="mb-4 text-center">
    <input type="hidden" name="uploaded_file"
        value="<?php echo htmlspecialchars($_SESSION['uploaded_file']); ?>">
    <div id="mappingFields" class="mb-4">
        
        <?php 
        // Load saved mappings if available
        $savedMappings = !empty($_SESSION['selected_columns']) ? $_SESSION['selected_columns'] : $_SESSION['headers'];

        foreach ($savedMappings as $header) : 
           
        ?>
        <div class="row mb-3 mapping-row">
            <div class="col-md-12 d-flex align-items-center">
                <select class="form-select me-2"  onchange="updateFieldNames(this)" name="field_mapping[<?php echo htmlspecialchars($header); ?>]">
                <option value="">Select Field</option>
                                            <?php 
                                            foreach ($savedMappings as $option) : 
                                                $selected = (strtolower($header) === strtolower($option)) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($option); ?></option>
                                            <?php endforeach; ?>
                                            <option value="add_new_empty_column" >Add New Empty Column </option>
                </select>
                <input type="text" class="form-control me-2 custom-header-input"
                    name="custom_headers[<?php echo htmlspecialchars($header); ?>]"
                    placeholder='Previous Value Was "<?php echo empty($header) ? 'empty' : htmlspecialchars($header); ?>"'
                    value="<?php echo htmlspecialchars($header); ?>">
                <button type="button" class="btn btn-danger remove-row" onclick="showConfirmModal(this)">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div id="addFields" class="mb-4">
        <button type="button" class="btn btn-success" id="addRow">
            <i class="fas fa-plus"></i> Add Field
        </button>
    </div>

    <button type="submit" name="process_csv" class="btn btn-primary mt-3">Process</button>
</form>


<!-- Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Removal</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    Are you sure you want to remove this field?
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Remove</button>
</div>
</div>
</div>
</div>


<script>
        document.addEventListener('DOMContentLoaded', function () {
        let fieldCounter = 1; // Counter for unique field names

        // Add event listener to dynamically update input fields when a dropdown value changes
        document.querySelectorAll('.field-mapping-select').forEach(select => {
        select.addEventListener('change', function () {
        updateFieldNames(this);
        });
        });

        // Add new row button functionality
        document.getElementById('addRow').addEventListener('click', function () {
        let uniqueId = `new_field_${fieldCounter++}`;
        let rowHtml = `
        <div class="row mb-3 mapping-row">
            <div class="col-md-12 d-flex align-items-center">
                <select class="form-select me-2 field-mapping-select" name="field_mapping[${uniqueId}]" 
                    onchange="updateFieldNames(this)">
                    <option value="">Select Field</option>
                    <?php 
                    $savedMappings = !empty($_SESSION['selected_columns']) ? $_SESSION['selected_columns'] : $_SESSION['headers'];
                    foreach ($savedMappings as $option) : ?>
                    <option value="<?php echo htmlspecialchars($option); ?>">
                        <?php echo htmlspecialchars($option); ?>
                    </option>
                    <?php endforeach; ?>
                    <option value="add_new_empty_column">Add New Empty Column</option>
                </select>
                <input type="text" class="form-control me-2 custom-header-input"
                    name="custom_headers[${uniqueId}]"
                    placeholder="Enter new Column name" value="">
                <button type="button" class="btn btn-danger remove-row" onclick="showConfirmModal(this)">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>`;
        document.getElementById('mappingFields').insertAdjacentHTML('beforeend', rowHtml);
        });

        // Confirmation modal functionality for removing rows
        let removeTarget = null;

        window.showConfirmModal = function (element) {
        removeTarget = element.closest('.mapping-row');
        var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'), {
        keyboard: false
        });
        myModal.show();
        };

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (removeTarget) {
        const noteInput = removeTarget.querySelector('input[name^="custom_headers"]');
        const noteValue = noteInput ? noteInput.value : null;

        if (noteValue) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "process.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            console.log(response.message);
                        } catch (e) {
                            console.error("Invalid JSON response:", xhr.responseText);
                        }
                    } else {
                        console.error("AJAX request failed with status:", xhr.status);
                    }
                }
            };
            xhr.send(`note=${encodeURIComponent(noteValue)}`);
        }
        removeTarget.remove();
        }
        var myModalEl = document.getElementById('confirmDeleteModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        modal.hide();
        });

        // Function to update the name attributes dynamically
        window.updateFieldNames = function (selectElement) {
        const selectedValue = selectElement.value;
        const parentDiv = selectElement.closest('.col-md-12');
        const inputField = parentDiv.querySelector('.custom-header-input');

        if (selectedValue) {
        selectElement.name = `field_mapping[${selectedValue}#${fieldCounter++}]`;
        inputField.name = `custom_headers[${selectedValue}#${fieldCounter++}]`;
        }
        };
        });





</script>





               
        
    </div>

<!-- Slider Buttons -->
<div class="slider-buttons">
  <!-- Left Button -->
  <a href="size.php" class="slider-button left">
    <i class="fas fa-chevron-left"></i> <!-- Font Awesome Icon -->
  </a>

  <!-- Right Button -->
  <a href="preview.php" class="slider-button right">
    <i class="fas fa-chevron-right"></i> <!-- Font Awesome Icon -->
  </a>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>