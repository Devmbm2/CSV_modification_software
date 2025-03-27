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
            <br><br>
          <h4 class="form-header text-center">Phone Number Format Setting</h4>
          <br>
                                                                <!-- Column Selection Form -->
                                        <form method="post" action="process.php" class="mb-4 text-center">
                                        <div class="mb-3">
                                        <div class="row">
                                        
                                            <div class="col-md-6">
                                            <label for="csvPhoneColumnSelect" class="form-label">Select Phone number Column</label>
                                                <select id="csvPhoneColumnSelect" name="phone_col" class="form-select">
                                                    <option value="">Select Phone Column</option>
                                                    <?php 
                                                    foreach ($_SESSION['headers'] as $header): 
                                                        if (stripos($header, 'phone') !== false): // Case-insensitive search for "phone"
                                                    ?>
                                                        <option value="<?php echo htmlspecialchars($header); ?>" 
                                                            <?php echo (isset($_SESSION['phone_col']) && $_SESSION['phone_col'] === $header) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($header); ?>
                                                        </option>
                                                    <?php 
                                                        endif; 
                                                    endforeach; 
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                            <label for="csvStateColumnSelect" class="form-label">Select Phone number format</label>
                                                <select id="csvStateColumnSelect" name="phone_format" class="form-select">
                                                    <option value="">Select Number Format</option>
                                                    <?php 
                                                    foreach ($_SESSION['phone_format'] as $index =>$header): 
                                                        // Case-insensitive search for "state"
                                                    ?>
                                                        <option value="<?php echo htmlspecialchars($index); ?>" 
                                                            <?php echo (isset($_SESSION['phone_f']) && $_SESSION['phone_f'] == $index) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($header); ?>
                                                        </option>
                                                    <?php 
                                                      
                                                    endforeach; 
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                   
                                    <button type="submit" name="phone_mapping_submit" class="btn btn-primary">Save Format</button>
                                </form>

               
        
    </div>

<!-- Slider Buttons -->
<div class="slider-buttons">
  <!-- Left Button -->
  <a href="city.php" class="slider-button left">
    <i class="fas fa-chevron-left"></i> <!-- Font Awesome Icon -->
  </a>

  <!-- Right Button -->
  <a href="address.php" class="slider-button right">
    <i class="fas fa-chevron-right"></i> <!-- Font Awesome Icon -->
  </a>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Toggle table visibility on eye icon click
            document.querySelectorAll(".toggle-table").forEach(icon => {
                icon.addEventListener("click", function () {
                    const state = this.getAttribute("data-state");
                    const tableContainer = document.getElementById(`table-container-${state}`);
                    tableContainer.style.display = tableContainer.style.display === "none" ? "block" : "none";
                });
            });
        

        document.addEventListener('DOMContentLoaded', function () {
            // Add click event listeners to all eye icons
            document.querySelectorAll('.toggle-table').forEach(icon => {
                icon.addEventListener('click', function () {
                    const state = this.getAttribute('data-state');
                    const tableContainerId = `table-container-${state}`;
                    const tableContainer = document.getElementById(tableContainerId);

                    // Toggle table visibility
                    if (tableContainer.style.display === 'none' || tableContainer.style.display === '') {
                        tableContainer.style.display = 'block';
                        this.classList.remove('bi-eye');
                        this.classList.add('bi-eye-slash'); // Change icon to "eye-slash"
                    } else {
                        tableContainer.style.display = 'none';
                        this.classList.remove('bi-eye-slash');
                        this.classList.add('bi-eye'); // Change icon back to "eye"
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Listen for form submission
            const saveMappingButton = document.querySelector('button[name="state_mapping_submit"]');
            if (saveMappingButton) {
                saveMappingButton.addEventListener('click', function (event) {
                    // Prevent form submission
                    event.preventDefault();

                    // Validate fields in the "Symbols & Numbers" tab
                    if ( document.getElementById('collapsesymbols')) {
                        const symbolsTab = document.getElementById('collapsesymbols');
                        const symbolFields = symbolsTab.querySelectorAll('.state-mapping-row');
                        let isValid = true;

                        symbolFields.forEach(row => {
                            const dropdown = row.querySelector('.state-select');
                            const inputField = row.querySelector('input[type="text"]');

                            // Check if the dropdown or input field is empty
                            if ((dropdown && !dropdown.value) || (inputField && !inputField.value)) {
                                isValid = false;
                            }
                        });

                        if (!isValid) {
                            // Show alert if any field is empty
                            alert('Please fill the "Not a USA State" field to process.');
                        } else {
                            // If all fields are valid, submit the form programmatically
                            saveMappingButton.form.submit();
                        }
                    }
                    else{
                        saveMappingButton.form.submit();
                    }
                });
            }
        });
    });
</script>

</body>
</html>