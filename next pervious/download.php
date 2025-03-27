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
            <h4 class="form-header text-center">  Download Output file </h4>
                <br>
           
           <!--  Download Processed File -->

           <?php if (isset($_SESSION['output_csv']) ) : ?>

                    <div class="accordion-body text-center">
                        <a href="<?php echo htmlspecialchars($_SESSION['output_csv']); ?>" class="btn btn-warning mb-3" download>Download Mapped File</a>
                        <?php if(isset($_SESSION['bad_data'])) { ?>
                        <a href="<?php echo htmlspecialchars($_SESSION['bad_data']); ?>" class="btn btn-success mb-3" download>Download Bad Data File</a>
                        <?php  } ?> 
                        <form method="post" action="process.php">
                            <button type="submit" name="reset" class="btn btn-danger">Restart</button>
                        </form>
                    </div>
                

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