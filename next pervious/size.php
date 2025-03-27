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
.slider-buttons {
  position: fixed; /* Fixed positioning to keep buttons on the page */
  top: 50%; /* Vertically center the buttons */
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between; /* Place buttons on opposite sides */
  transform: translateY(-50%); /* Adjust vertical alignment */
  z-index: 10; /* Ensure buttons are above other content */
}

/* Styling for individual slider buttons */
.slider-button {
  text-decoration: none; /* Remove underline from links */
  color: #000; /* Icon color */
  background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
  padding: 10px;
  border-radius: 50%; /* Circular shape */
  font-size: 20px; /* Icon size */
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Subtle shadow */
  transition: transform 0.2s ease, background-color 0.2s ease; /* Smooth hover effect */
}

/* Hover effect for buttons */
.slider-button:hover {
  background-color: rgba(0, 0, 0, 0.8); /* Darker background on hover */
  color: #fff; /* White icon color on hover */
  transform: scale(1.1); /* Slightly enlarge the button */
}

/* Positioning adjustments for left and right buttons */
.left {
  margin-left: 20px; /* Space from the left edge */
}

.right {
  margin-right: 20px; /* Space from the right edge */
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
            <br>
            <h4 class="form-header text-center">   Data size adjustments </h4>

           
                                   
                                        
                        
                                            
                                        <!-- Column Selection Form -->

                                        <form method="post" action="process.php" class="mb-4 text-center ">
                                        <div class="row mb-3">
                                        <div class="col">
                                            <label for="csvColumnSelect" class="form-label">Select Column to data size adjustments</label>
                                            <select id="csvColumnSelect" name="size_column" class="form-select">
                                                <option value="">Select Column</option>
                                                <?php 
                                                foreach ($_SESSION['headers'] as $header): 
                                                
                                                ?>
                                                    <option value="<?php echo htmlspecialchars($header); ?>" 
                                                        <?php echo (isset($_SESSION['size_column']) && $_SESSION['size_column'] === $header) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($header); ?>
                                                    </option>
                                                <?php 
                                                
                                                endforeach; 
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col">
                                            <label for="csvSecondColumnSelect" class="form-label">Add Maximum Allowed Characters</label>
                                            <input 
                                                    type="text" 
                                                    id="csvSecondColumnInput" 
                                                    name="added_size" 
                                                    class="form-control" 
                                                    value="<?php echo isset($_SESSION['added_size']) ? htmlspecialchars($_SESSION['added_size']) : ''; ?>" 
                                                    placeholder="for example 50" 
                                                
                                                ></input>
                                        </div>
                                    </div>            
                                    <button type="submit" name="size_adjust_submit" class="btn btn-primary">Show</button>
                                </form>
                                        
                                        <!-- HTML Form for Displaying Exceeding Rows -->
                                        <?php if (isset($_SESSION['exceeding_rows']) && !empty($_SESSION['exceeding_rows'])): ?>
                                        <form method="post" action="process.php" class="mt-4">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 50px;">Action</th>
                                                            <th>Selected Column Content</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $columnIndex = array_search($_SESSION['size_column'], $_SESSION['headers']);
                                                        foreach ($_SESSION['exceeding_rows'] as $index => $row): 
                                                        ?>
                                                        <tr>
                                                            <td style="width: 50px;">
                                                                <!-- Eye Icon to Show Full Record -->
                                                                <button type="button" class="btn btn-sm btn-info view-record" data-bs-toggle="modal" data-bs-target="#recordModal" data-index="<?php echo $index; ?>">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <!-- Hidden Input for ID -->
                                                                <input type="hidden" name="ids[]" value="<?php echo htmlspecialchars($row[0]); ?>">
                                                                <!-- Textarea to Edit Selected Column Content -->
                                                                <textarea name="updated_values[]" class="form-control" rows="3"><?php echo htmlspecialchars($row[$columnIndex]); ?></textarea>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <button type="submit" name="update_csv_submit" class="btn btn-success">Save Changes</button>
                                        </form>
                                        <?php endif; ?>

                                    <!-- Modal to Display Full Record -->
                                    <div class="modal fade" id="recordModal" tabindex="-1" aria-labelledby="recordModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="recordModalLabel">Full Record Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <?php foreach ($_SESSION['headers'] as $header): ?>
                                                                <th><?php echo htmlspecialchars($header); ?></th>
                                                                <?php endforeach; ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="recordDetails">
                                                            <!-- Dynamic content will be loaded here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        // Add event listener to all view-record buttons
                                        document.querySelectorAll('.view-record').forEach(button => {
                                            button.addEventListener('click', function () {
                                                const index = this.getAttribute('data-index');
                                                const row = <?php echo json_encode($_SESSION['exceeding_rows']); ?>[index];
                                                const headers = <?php echo json_encode($_SESSION['headers']); ?>;

                                                let tableContent = '<tr>';
                                                headers.forEach((header, i) => {
                                                    tableContent += `<td>${row[i]}</td>`;
                                                });
                                                tableContent += '</tr>';

                                                // Populate the modal body with the row details
                                                document.getElementById('recordDetails').innerHTML = tableContent;
                                            });
                                        });
                                    });
                                    </script>


                                        





               
        
    </div>

<!-- Slider Buttons -->
<div class="slider-buttons">
  <!-- Left Button -->
  <a href="address.php" class="slider-button left">
    <i class="fas fa-chevron-left"></i> <!-- Font Awesome Icon -->
  </a>

  <!-- Right Button -->
  <a href="field_map.php" class="slider-button right">
    <i class="fas fa-chevron-right"></i> <!-- Font Awesome Icon -->
  </a>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>