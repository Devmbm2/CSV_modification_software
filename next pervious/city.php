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

                                    
                                    <h4 class="form-header  text-center">City Mapping</h4>

                                    <!-- Column Selection Form -->
                                    <form method="post" action="process.php" class="mb-4 text-center ">
                                    <input type="hidden" name="page" value="city1">
                                    <div class="row mb-3">
                                    <div class="col">
                                        <label for="csvColumnSelect" class="form-label">Select Column to Map city</label>
                                        <select id="csvColumnSelect" name="city_column" class="form-select">
                                            <option value="">Select Column</option>
                                            <?php 
                                            foreach ($_SESSION['headers'] as $header): 
                                                if (stripos($header, 'city') !== false): // Case-insensitive search for "state"
                                            ?>
                                            
                                                <option value="<?php echo htmlspecialchars($header); ?>" 
                                                    <?php echo (isset($_SESSION['city_column']) && $_SESSION['city_column'] === $header) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($header); ?>
                                                </option>
                                            <?php 
                                                endif; 
                                            endforeach; 
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col">
         
                                    </div>
                                </div>
                                        
                                        <button type="submit" name="city_submit" class="btn btn-primary">Show</button>
                                    </form>




                                <?php
                                if (!isset($_SESSION['groupedStates']) || empty($_SESSION['groupedStates'])) {
                                    echo '<p class="text-center">No states available for mapping.</p>';
                                    
                                }
                                if(isset($_SESSION['show_city_mapping']) && $_SESSION['show_city_mapping']==1){ ?>
                            
                <!-- city Mapping Accordion Item -->

                        <!-- Parent Accordion for Mapping Forms -->
        <div class="accordion" id="mappingFormsAccordion">
            

                    <div class="accordion-item">
                            <h2 class="accordion-header" id="cityMappingHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cityMappingCollapse" aria-expanded="false" aria-controls="cityMappingCollapse">
                                City Mapping
                                </button>
                            </h2>
                            <div id="cityMappingCollapse" class="accordion-collapse collapse show " aria-labelledby="cityMappingHeading" data-bs-parent="#mappingFormsAccordion">
                            <div class="accordion-body">
                                <form method="post" action="process.php" class="mb-4 text-center">
                                <input type="hidden" name="page" value="city2">
                                    <div id="mappingFields" class="mb-4">
                                        <div class="accordion" id="stateDropdownAccordion">
                                            <?php
                                            $isFirstGroup = true;
                                            foreach ($_SESSION['groupedStates'] as $group => $states):
                                                $groupId = ($group === 'symbols') ? 'symbols' : "group-$group";
                                            ?>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading<?php echo $groupId; ?>">
                                                        <button class="accordion-button <?php echo $isFirstGroup ? '' : 'collapsed'; ?>" 
                                                                type="button" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#collapse<?php echo $groupId; ?>" 
                                                                aria-expanded="<?php echo $isFirstGroup ? 'true' : 'false'; ?>" 
                                                                aria-controls="collapse<?php echo $groupId; ?>">
                                                            <?php echo ($group === 'symbols') ? 'Not Valid Cities' : "Group $group"; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse<?php echo $groupId; ?>" 
                                                        class="accordion-collapse collapse <?php echo $isFirstGroup ? ' ' : ''; ?>" 
                                                        aria-labelledby="heading<?php echo $groupId; ?>" 
                                                        data-bs-parent="#stateDropdownAccordion">
                                                        <div class="accordion-body">
                                                            <?php foreach ($states as $state): ?>
                                                                <div class="row mb-3 state-mapping-row" data-state="<?php echo htmlspecialchars($state); ?>">
                                                                    <div class="col-md-12 d-flex flex-column align-items-start">
                                                                        <!-- Dropdown and Eye Icon -->
                                                                        <div class="d-flex align-items-center w-100 mb-2">
                                                                            <label for="state-<?php echo htmlspecialchars($state); ?>" class="me-2 visually-hidden">State</label>
                                                                            <input type="text" class="form-control me-2" readonly 
                                                                                id="state-<?php echo htmlspecialchars($state); ?>" 
                                                                                value="<?php echo htmlspecialchars($state); ?>">
                                                                            <?php if ($group === 'symbols'): ?>
                                                                                <!-- Dropdown for Symbols & Numbers group -->
                                                                                <select class="form-select state-select" name="city_mapping[<?php echo htmlspecialchars($state); ?>]" 
                                                                                        aria-label="Select Correct City">
                                                                                    <option value="">Select Correct City</option>
                                                                                    <option selected value="mark_bad_data">Mark Bad Data</option>
                                                                                    <option value="enter_manually">Enter Manually</option>
                                                                                    
                                                                                </select>
                                                                                <!-- <div class="dropdown d-inline-block">
                                                                                    <i class="bi bi-three-dots-vertical cursor-pointer action-icon" 
                                                                                    style="font-size: 1.2rem;" 
                                                                                    data-bs-toggle="dropdown" 
                                                                                    aria-expanded="false"></i>
                                                                                    <ul class="dropdown-menu">
                                                                                        <li>
                                                                                            <select class="form-select column-select">
                                                                                                <option value="">Select Column</option>
                                                                                                <option value="Abbreviation">Abbreviation</option>
                                                                                                <option value="Complete Name">Complete Name</option>
                                                                                                <option value="Abbreviation + Name">Abbreviation + Name</option>
                                                                                                <option value="Name + Abbreviation">Name + Abbreviation</option>
                                                                                            </select>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div> -->
                                                                
                                                                            <?php else: ?>
                                                                                <!-- Dropdown for other groups -->

                                                                                <select class="form-select state-select"  name="city_mapping[<?php echo htmlspecialchars($state); ?>]" aria-label="Select Correct State">
                                                                                    <option value="">Select Correct City</option>
                                                                                    <option  value="mark_bad_data">Mark Bad Data</option>
                                                                                    <option value="enter_manually">Enter Manually</option>
                                                                                    <?php  foreach ($states as $status): 
                                                                                          $status = preg_replace('/\s*\(\d+\)/', '', $status);
                                                                                          $state = preg_replace('/\s*\(\d+\)/', '', $state);

                                                                                                ?>
                                                                                            <option value="<?php echo htmlspecialchars($status); ?>"
                                                                                            <?php echo ( strtolower($status) == strtolower($state)  ) ? 'selected' : ''; ?>>
                                                                                            <?php echo htmlspecialchars($status); ?>  
                                                                                        </option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                                
                                                                                <!-- <div class="dropdown d-inline-block">
                                                                                    <i class="bi bi-three-dots-vertical cursor-pointer action-icon" 
                                                                                    style="font-size: 1.2rem;" 
                                                                                    data-bs-toggle="dropdown" 
                                                                                    aria-expanded="false"></i>

                                                                                    <ul class="dropdown-menu">
                                                                                        <li>
                                                                                            <select class="form-select column-select">
                                                                                                <option value="">Select Column</option>
                                                                                                <option value="Abbreviation">Abbreviation</option>
                                                                                                <option value="Complete Name">Complete Name</option>
                                                                                                <option value="Abbreviation + Name">Abbreviation + Name</option>
                                                                                                <option value="Name + Abbreviation">Name + Abbreviation</option>
                                                                                            </select>
                                                                                        </li>
                                                                                    </ul>
                                                                                    
                                                                                </div> -->
                                                                            <?php endif; ?>
                                                                        </div>

                                                                        <script>
                                                                            var statesFormats = <?php echo json_encode($_SESSION['States_formats'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
                                                                            sessionStorage.setItem("States_formats", JSON.stringify(statesFormats));

                                                                            document.addEventListener("DOMContentLoaded", function () {
                                                                                document.querySelectorAll(".column-select").forEach(select => {
                                                                                    select.addEventListener("change", function () {
                                                                                        let selectedFormat = this.value;
                                                                                        if (!selectedFormat) return;

                                                                                        let dropdownContainer = this.closest(".dropdown");
                                                                                        let selectField = dropdownContainer ? dropdownContainer.previousElementSibling : null;
                                                                                        let accordionItem = this.closest(".accordion-item");
                                                                                        let isNotValidUSAState = accordionItem && accordionItem.querySelector(".accordion-button")?.textContent.includes("Not Valid USA States");

                                                                                        if (!selectField || !selectField.classList.contains("form-select")) return;

                                                                                        let statesFormats = JSON.parse(sessionStorage.getItem("States_formats"));

                                                                                        if (statesFormats && statesFormats[selectedFormat]) {
                                                                                            let selectedValue = selectField.value;
                                                                                            selectField.innerHTML = '<option value="">Select Correct State</option>';

                                                                                            if (isNotValidUSAState) {
                                                                                                selectField.innerHTML += `
                                                                                                    <option value="mark_bad_data">Mark Bad Data</option>
                                                                                                    <option value="enter_manually">Enter Manually</option>
                                                                                                `;
                                                                                            }

                                                                                            Object.entries(statesFormats[selectedFormat]).forEach(([abbr, value]) => {
                                                                                                let option = document.createElement("option");
                                                                                                option.value = value;
                                                                                                option.textContent = value;
                                                                                                if (value === selectedValue) option.selected = true;
                                                                                                selectField.appendChild(option);
                                                                                            });
                                                                                        }
                                                                                    });
                                                                                });

                                                                                function handleManualEntry(select) {
                                                                                    console.log('seleeding');
                                                                                    if (select.value === "enter_manually") {
                                                                                        let parentDiv = select.closest(".d-flex");

                                                                                        // Save the original state of the select element
                                                                                        let originalOptions = select.innerHTML; // Save all options
                                                                                        let originalValue = select.value;       // Save the selected value

                                                                                        // Create the manual input field
                                                                                        let inputField = document.createElement("input");
                                                                                        inputField.type = "text";
                                                                                        inputField.className = "form-control manual-entry";
                                                                                        inputField.name = select.name;
                                                                                        inputField.placeholder = "Please enter a valid state";

                                                                                        // Create the reverse icon
                                                                                        let reverseIcon = document.createElement("i");
                                                                                        reverseIcon.className = "bi bi-arrow-counterclockwise cursor-pointer reverse-icon";
                                                                                        reverseIcon.style.fontSize = "1.2rem";
                                                                                        reverseIcon.style.marginLeft = "10px";

                                                                                        // Append the reverse icon to the parent div
                                                                                        parentDiv.appendChild(reverseIcon);

                                                                                        // Replace the select element with the input field
                                                                                        select.replaceWith(inputField);

                                                                                        // Add event listener to the reverse icon
                                                                                        reverseIcon.addEventListener("click", function () {
                                                                                            // Create a new select field
                                                                                            let selectField = document.createElement("select");
                                                                                            selectField.className = "form-select state-select";
                                                                                            selectField.name = inputField.name;

                                                                                            // Restore the original options and selected value
                                                                                            selectField.innerHTML = originalOptions; // Restore options
                                                                                            selectField.value = originalValue;       // Restore selected value

                                                                                            // Replace the input field with the select field
                                                                                            inputField.replaceWith(selectField);

                                                                                            // Remove the reverse icon
                                                                                            reverseIcon.remove();

                                                                                            // Reattach the event listener to the newly created select field
                                                                                            selectField.addEventListener("change", function () {
                                                                                                handleManualEntry(selectField);
                                                                                            });
                                                                                        });
                                                                                    }
                                                                                }

                                                                                // Handle manual entry selection
                                                                                document.querySelectorAll(".state-select").forEach(select => {
                                                                                    select.addEventListener("change", function () {
                                                                                        handleManualEntry(this);
                                                                                    });
                                                                                });
                                                                            });

                                                                        </script>



                                                                        <!-- Scrollable Container for Table -->
                                                                        <div class="w-100 invalid-state-table-container" id="table-container-<?php echo htmlspecialchars($state); ?>" style="display: none; max-width: 100%; overflow-x: auto;">
                                                                            <table class="table table-bordered" style="width: auto; min-width: 100%;">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <?php
                                                                                        // Extract headers from the CSV file
                                                                                        $headers = $_SESSION['csvData'][0];
                                                                                        foreach ($headers as $header):
                                                                                        ?>
                                                                                            <th><?php echo htmlspecialchars($header); ?></th>
                                                                                        <?php endforeach; ?>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php
                                                                                    // Decode the invalid states JSON and find rows for this state
                                                                                    $invalidStatesJson = json_decode($_SESSION['invalidStatesJson'], true);
                                                                                    $stateName = trim(explode(" (", $state)[0]); // Extract state name from "State (Count)"
                                                                                    if (isset($invalidStatesJson[$stateName])) {
                                                                                        foreach ($invalidStatesJson[$stateName] as $index => $row):
                                                                                            if ($index === 0) continue; // Skip header row
                                                                                    ?>
                                                                                            <tr>
                                                                                                <?php foreach ($row as $cell): ?>
                                                                                                    <td><?php echo htmlspecialchars($cell); ?></td>
                                                                                                <?php endforeach; ?>
                                                                                            </tr>
                                                                                    <?php
                                                                                        endforeach;
                                                                                    }
                                                                                    ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                                $isFirstGroup = false;
                                            endforeach;
                                            ?>
                                        </div>
                                    </div>
                                    <button type="submit" name="city_mapping_submit" class="btn btn-primary">Save Mapping</button>
                                </form>

                                        <!-- Include Bootstrap Icons CSS -->
                                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

                                        <!-- JavaScript for Toggle Functionality -->
                                        <script>
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

                                        </script>

                                        <?php } ?>
                                    


                </div>

            </div>
        </div>
    </div>


               
        
    </div>

<!-- Slider Buttons -->
<div class="slider-buttons">
  <!-- Left Button -->
  <a href="state.php" class="slider-button left">
    <i class="fas fa-chevron-left"></i> <!-- Font Awesome Icon -->
  </a>

  <!-- Right Button -->
  <a href="phone.php" class="slider-button right">
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