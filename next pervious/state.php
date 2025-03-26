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
                <h4 class="form-header text-center">States Mapping</h4> 
                                    <!-- Column Selection Form -->
                                    <form method="post" action="process.php" class="mb-4 text-center">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="csvColumnSelect" class="form-label">Select Column to Map States</label>
                                                <select id="csvColumnSelect" name="csv_column" class="form-select">
                                                    <option value="">Select Column</option>
                                                    <?php
                                                    foreach ($_SESSION['headers'] as $header):
                                                        if (stripos($header, 'state') !== false): // Case-insensitive search for "state"
                                                            ?>
                                                            <option value="<?php echo htmlspecialchars($header); ?>"
                                                                <?php echo (isset($_SESSION['csv_column']) && $_SESSION['csv_column'] === $header) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($header); ?>
                                                            </option>
                                                        <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label for="csvSecondColumnSelect" class="form-label">Select Output State Format</label>
                                                <select id="csvSecondColumnSelect" name="state_format_select" class="form-select">
                                                    <option value="">Select Format</option>
                                                    <?php
                                                    foreach ($_SESSION['state_format'] as $format):
                                                        ?>
                                                        <option value="<?php echo htmlspecialchars($format); ?>"
                                                            <?php echo (isset($_SESSION['state_format_select']) && $_SESSION['state_format_select'] === $format) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($format); ?>
                                                        </option>
                                                    <?php
                                                    endforeach;
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <button type="submit" name="column_submit" class="btn btn-primary">Show</button>
                                    </form>
                                                    <hr>
            <!-- State Mapping Accordion Item -->
            <form method="post"  action="process.php" class="mb-4 text-center">
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
                                                            <?php echo ($group === 'symbols') ? 'Not Valid USA States' : "Group $group"; ?>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse<?php echo $groupId; ?>" 
                                                        class="accordion-collapse collapse <?php echo $isFirstGroup ? 'show' : ''; ?>" 
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
                                                                                <select class="form-select state-select" name="state_mapping[<?php echo htmlspecialchars($state); ?>]" 
                                                                                        aria-label="Select Correct State">
                                                                                    <option value="">Select Correct State</option>
                                                                                    <option selected value="mark_bad_data">Mark Bad Data</option>
                                                                                    <option value="enter_manually">Enter Manually</option>
                                                                                    <?php   foreach ($_SESSION['States_formats'][$_SESSION['state_format_select']] as $status): ?>
                                                                                        <option value="<?php echo htmlspecialchars($status); ?>">
                                                                                            <?php echo htmlspecialchars($status); ?>
                                                                                        </option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                                         <!-- Eye Icon for Invalid States -->
                                                                                        <i class="bi bi-eye ms-2 cursor-pointer toggle-table" 
                                                                                        data-state="<?php echo htmlspecialchars($state); ?>" 
                                                                                        style="font-size: 1.2rem;"></i>


                                                                                <div class="dropdown d-inline-block">
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
                                                                                </div>
                                                                
                                                                            <?php else: ?>
                                                                                <!-- Dropdown for other groups -->

                                                                                <?php
                                                                       

                                                                                // Use regex to extract only the state abbreviation
                                                                                preg_match('/([A-Za-z]+)/', $state, $matches);
                                                                                $clean_state = isset($matches[1]) ? strtolower(trim($matches[1])) : ''; 

                                                                                // Dropdown
                                                                                ?>

                                                                                <select class="form-select" name="state_mapping[<?php echo htmlspecialchars($state); ?>]" 
                                                                                        aria-label="Select Correct State">
                                                                                    <option value="">Select Correct State</option>
                                                                                    <?php foreach ($_SESSION['States_formats'][$_SESSION['state_format_select']] as $key => $status): ?>
                                                                                        <option value="<?php echo htmlspecialchars($status); ?>" 
                                                                                            <?php echo ( strtolower($clean_state) == strtolower(trim($key)) || strtolower($clean_state) == strtolower(trim($status))) ? 'selected' : ''; ?>>
                                                                                            <?php echo htmlspecialchars($status); ?>
                                                                                        </option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                                
                                                                                <div class="dropdown d-inline-block">
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
                                                                                </div>
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
                                                                                    if (select.value === "enter_manually") {
                                                                                        let parentDiv = select.closest(".d-flex");
                                                                                        let actionIcon = parentDiv.querySelector(".action-icon");

                                                                                        let inputField = document.createElement("input");
                                                                                        inputField.type = "text";
                                                                                        inputField.className = "form-control manual-entry";
                                                                                        inputField.name = select.name;
                                                                                        inputField.placeholder = "Please enter a valid state";
                                                                                        
                                                                                        let reverseIcon = document.createElement("i");
                                                                                        reverseIcon.className = "bi bi-arrow-counterclockwise cursor-pointer reverse-icon";
                                                                                        reverseIcon.style.fontSize = "1.2rem";
                                                                                        reverseIcon.style.marginLeft = "10px";

                                                                                        actionIcon.style.display = "none"; // Hide action icon
                                                                                        parentDiv.appendChild(reverseIcon); // Add reverse icon

                                                                                        select.replaceWith(inputField);

                                                                                        reverseIcon.addEventListener("click", function () {
                                                                                            let selectField = document.createElement("select");
                                                                                            selectField.className = "form-select state-select";
                                                                                            selectField.name = inputField.name;

                                                                                            let statesFormats = JSON.parse(sessionStorage.getItem("States_formats"));
                                                                                            let defaultOptions = '<option value="">Select Correct State</option>';
                                                                                            defaultOptions += ` <option value="mark_bad_data">Mark Bad Data</option>
                                                                                                <option value="enter_manually">Enter Manually</option>`;
                                                                                            Object.values(statesFormats).forEach(group => {
                                                                                                Object.values(group).forEach(value => {
                                                                                                    defaultOptions += `<option value="${value}">${value}</option>`;
                                                                                                });
                                                                                            });

                                                                                            selectField.innerHTML = defaultOptions;
                                                                                            inputField.replaceWith(selectField);
                                                                                            actionIcon.style.display = "inline-block";
                                                                                            reverseIcon.remove(); // Remove reverse icon
                                                                                            
                                                                                            // Reattach event listener to newly created select field
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
                                    <button type="submit" name="state_mapping_submit" class="btn btn-primary">Save Mapping</button>
                                </form>
        
    </div>

<!-- Slider Buttons -->
<div class="slider-buttons">
  <!-- Left Button -->
  <a href="index.php" class="slider-button left">
    <i class="fas fa-chevron-left"></i> <!-- Font Awesome Icon -->
  </a>

  <!-- Right Button -->
  <a href="city.php" class="slider-button right">
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