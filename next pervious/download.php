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
.btn-close {
            background-color: #fffbfb;
            color: white; 
}   

 /* Custom Styles */
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
                                    <div class="col text-center">
                                        <p class="display-6 m-0">CSV Processing Tool</p>
                                    </div>

                                    
                                    <!-- Load Mapping Button -->
                                    <div class="col-auto text-end">
                                        <button type="button" class="btn btn-info" id="loadMappingButton">
                                            <i class="fas fa-folder-open me-2"></i>
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

                        <script src="asset/utils.js"></script>



                            
                <hr>
           <?php     if (isset($_GET['status']) && $_GET['status'] === 'success' && isset($_GET['message'])) { ?>
            <div class="alert alert-info mt-3">
                <?php echo $_GET['message'];?>
        </div>

          <?php  } ?>
            <br>
            <h4 class="form-header text-center">  Download Output file </h4>
                <br>
           
           <!--  Download Processed File -->

           <?php if (isset($_SESSION['output_csv']) ) : ?>



            <div class="accordion-body text-center">
    <!-- Download Buttons -->
    <div class="mb-4">
        <a href="<?php echo htmlspecialchars($_SESSION['output_csv']); ?>" class="btn btn-warning btn-lg" download>
            <i class="fas fa-download me-2"></i> Download Mapped File
        </a>
        <?php if (isset($_SESSION['bad_data'])) { ?>
        <a href="<?php echo htmlspecialchars($_SESSION['bad_data']); ?>" class="btn btn-success btn-lg ms-3" download>
            <i class="fas fa-exclamation-circle me-2"></i> Download Bad Data File
        </a>
        <?php } ?>
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; justify-content: center; gap: 15px;">
        <!-- Restart Button with Modal Trigger -->
        <button type="button" class="btn btn-danger btn-lg d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#restartModal">
            <i class="fas fa-redo-alt me-2"></i> Restart
        </button>

        <!-- Save Mapping Button with Modal Trigger -->
        <button type="button" class="btn btn-primary btn-lg d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#saveMappingModal">
            <i class="fas fa-save me-2"></i> Save Mapping
        </button>
    </div>
</div>

<!-- Modal for Confirmation (Restart) -->
<div class="modal fade" id="restartModal" tabindex="-1" aria-labelledby="restartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="restartModalLabel">Confirm Restart</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to restart? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="post" action="process.php" style="margin: 0;">
                    <button type="submit" name="reset" class="btn btn-danger">Restart</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Save Mapping -->
<div class="modal fade" id="saveMappingModal" tabindex="-1" aria-labelledby="saveMappingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="saveMappingModalLabel">Save Mapping</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="saveMappingForm">
                    <div class="mb-3">
                        <label for="mapping_name" class="form-label">Enter a name for this mapping:</label>
                        <input type="text" class="form-control" id="mapping_name" name="mapping_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
                <div id="ajaxResponse" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>



<!-- JavaScript for AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>






<?php endif; ?>

               
        
    </div>

<!-- Slider Buttons -->
<div class="slider-buttons">
  <!-- Left Button -->
  <a href="field_map.php" class="slider-button left">
    <i class="fas fa-chevron-left"></i> <!-- Font Awesome Icon -->
  </a>

  <!-- Right Button -->
  <a href="download.php" class="slider-button right">
    <i class="fas fa-chevron-right"></i> <!-- Font Awesome Icon -->
  </a>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>