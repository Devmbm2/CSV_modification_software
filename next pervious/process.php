<?php
require '../../vendor/autoload.php';

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

session_start();
$_SESSION['step']=0;


// Initialize session variables if they don't exist
if (!isset($_SESSION['headers'])) {
    $_SESSION['headers'] = []; // Default empty array for headers
}
if (!isset($_SESSION['options'])) {
    $_SESSION['options'] = []; // Default empty array for options
}
if (!isset($_SESSION['field_mapping'])) {
    $_SESSION['field_mapping'] = []; // Default empty array for field mappings
}
if (!isset($_SESSION['custom_headers'])) {
    $_SESSION['custom_headers'] = []; // Default empty array for custom headers
}



$_SESSION['show_states_mapping'] = 0;
$_SESSION['show_city_mapping'] = 0;

$_SESSION['data_size_options']=[
    'First word only' => 'Fist word only ',
    'Initials only' =>  'Initials only '
];

$_SESSION['uniqueStates']=0;

$_SESSION['state_format'] = [
    'state_f1' => 'Abbreviation',              // Abbreviation
    'state_f2' => 'Complete Name',              // Complete Name
    'state_f3' => 'Abbreviation + Name',     // Abbreviation + Name
    'state_f4' => 'Name + Abbreviation'      // Name + Abbreviation
];



$usStates = array(
    "AL" => "Alabama",
    "AK" => "Alaska",
    "AS" => "American Samoa",
    "AZ" => "Arizona",
    "AR" => "Arkansas",
    "CA" => "California",
    "CO" => "Colorado",
    "CT" => "Connecticut",
    "DE" => "Delaware",
    "DC" => "District Of Columbia",
    "FM" => "Federated States Of Micronesia",
    "FL" => "Florida",
    "GA" => "Georgia",
    "GU" => "Guam",
    "HI" => "Hawaii",
    "ID" => "Idaho",
    "IL" => "Illinois",
    "IN" => "Indiana",
    "IA" => "Iowa",
    "KS" => "Kansas",
    "KY" => "Kentucky",
    "LA" => "Louisiana",
    "ME" => "Maine",
    "MH" => "Marshall Islands",
    "MD" => "Maryland",
    "MA" => "Massachusetts",
    "MI" => "Michigan",
    "MN" => "Minnesota",
    "MS" => "Mississippi",
    "MO" => "Missouri",
    "MT" => "Montana",
    "NE" => "Nebraska",
    "NV" => "Nevada",
    "NH" => "New Hampshire",
    "NJ" => "New Jersey",
    "NM" => "New Mexico",
    "NY" => "New York",
    "NC" => "North Carolina",
    "ND" => "North Dakota",
    "MP" => "Northern Mariana Islands",
    "OH" => "Ohio",
    "OK" => "Oklahoma",
    "OR" => "Oregon",
    "PW" => "Palau",
    "PA" => "Pennsylvania",
    "PR" => "Puerto Rico",
    "RI" => "Rhode Island",
    "SC" => "South Carolina",
    "SD" => "South Dakota",
    "TN" => "Tennessee",
    "TX" => "Texas",
    "UT" => "Utah",
    "VT" => "Vermont",
    "VI" => "Virgin Islands",
    "VA" => "Virginia",
    "WA" => "Washington",
    "WV" => "West Virginia",
    "WI" => "Wisconsin",
    "WY" => "Wyoming"
);




$_SESSION['phone_format']=
[
    1 => '222-2222', 
    2 => '813.222.2222',
    3 => '813-222-2222',
    4 => '8132222222',
    5 => '+1 (813) 222-2222 ext 123',
    6 => '1-813-222-2222X123'

];

$_SESSION['address_format'] = [
    'first_line' ,
    'second_line',
    'city',
    'state' ,
    'zip_code',
    'plus_5' ,
    'name_of_address' ,
];


function fetchStatesData() {
    $url = "https://gist.githubusercontent.com/mshafrir/2646763/raw/8b0dbb93521f5d6889502305335104218454c2bf/states_hash.json";

    // Attempt to fetch data from the internet
    $response = @file_get_contents($url);

    if ($response !== false) {
        // Decode the JSON response into an associative array
        $states = json_decode($response, true);

        // Validate the fetched data
        if (is_array($states) && !empty($states)) {
            return $states;
        }
    }
        $usStates = array(
            "AL" => "Alabama",
            "AK" => "Alaska",
            "AS" => "American Samoa",
            "AZ" => "Arizona",
            "AR" => "Arkansas",
            "CA" => "California",
            "CO" => "Colorado",
            "CT" => "Connecticut",
            "DE" => "Delaware",
            "DC" => "District Of Columbia",
            "FM" => "Federated States Of Micronesia",
            "FL" => "Florida",
            "GA" => "Georgia",
            "GU" => "Guam",
            "HI" => "Hawaii",
            "ID" => "Idaho",
            "IL" => "Illinois",
            "IN" => "Indiana",
            "IA" => "Iowa",
            "KS" => "Kansas",
            "KY" => "Kentucky",
            "LA" => "Louisiana",
            "ME" => "Maine",
            "MH" => "Marshall Islands",
            "MD" => "Maryland",
            "MA" => "Massachusetts",
            "MI" => "Michigan",
            "MN" => "Minnesota",
            "MS" => "Mississippi",
            "MO" => "Missouri",
            "MT" => "Montana",
            "NE" => "Nebraska",
            "NV" => "Nevada",
            "NH" => "New Hampshire",
            "NJ" => "New Jersey",
            "NM" => "New Mexico",
            "NY" => "New York",
            "NC" => "North Carolina",
            "ND" => "North Dakota",
            "MP" => "Northern Mariana Islands",
            "OH" => "Ohio",
            "OK" => "Oklahoma",
            "OR" => "Oregon",
            "PW" => "Palau",
            "PA" => "Pennsylvania",
            "PR" => "Puerto Rico",
            "RI" => "Rhode Island",
            "SC" => "South Carolina",
            "SD" => "South Dakota",
            "TN" => "Tennessee",
            "TX" => "Texas",
            "UT" => "Utah",
            "VT" => "Vermont",
            "VI" => "Virgin Islands",
            "VA" => "Virginia",
            "WA" => "Washington",
            "WV" => "West Virginia",
            "WI" => "Wisconsin",
            "WY" => "Wyoming"
        );
    
    return $usStates;
}

function formatStatesArray($format_ke) {
       // Fetch the states data (from the internet or static array)
       $states = fetchStatesData();

       $formatted_states = [
           'Abbreviation' => [],
           'Complete Name' => [],
           'Abbreviation + Name' => [],
           'Name + Abbreviation' => []
       ];
   
       foreach ($states as $abbreviation => $name) {
           $formatted_states['Abbreviation'][$abbreviation] = $abbreviation;
           $formatted_states['Complete Name'][$abbreviation] = $name;
           $formatted_states['Abbreviation + Name'][$abbreviation] = "$abbreviation, $name";
           $formatted_states['Name + Abbreviation'][$abbreviation] = "$name, $abbreviation";
       }
   
       return $formatted_states;
}
    



function processPhoneNumber($phone , $phone_format)
{   
    // Check if the phone number has fewer than 7 digits
    if (strlen(preg_replace('/\D/', '', $phone)) < 7) {
        // Return the phone number as is if it has fewer than 7 digits
        return [
            'phone_number' => $phone,
            'phone_number_area_code' => '',
            'phone_number_country_code' => '',
            'phone_number_extension' => '',
        ];
    }

    $phoneUtil = PhoneNumberUtil::getInstance();

    // Try to handle extension manually by splitting the string
    $extension = '';
    $exten ='';
    if (preg_match('/\s*[xX]\s*(\d+)$/', $phone, $matches)) {
        $extension = $matches[1];
        $exten = $matches[1];

        $phone = preg_replace('/\s*[xX]\s*\d+$/', '', $phone); // Remove the extension part
    }

    try {
        // Add a hyphen after the first 3 digits if the number is in the format 2650100
        if (preg_match('/^\d{7}$/', $phone)) {
            $phone = substr($phone, 0, 3) . '-' . substr($phone, 3);
        }

        $number = $phoneUtil->parse($phone, "US");
        $formattedNational = $phoneUtil->format($number, PhoneNumberFormat::NATIONAL);

        // Extract the area code, local number, and extension if present
        preg_match('/\(?\s*(\d{3})?\s*\)?[-.\s]?(\d{3})[-.\s]?(\d{4})/', $formattedNational, $matches);

        // Default area code and country code if missing
        $areaCode = $matches[1] ?? ''; // Default area code if none is provided
        $localNumber = ($matches[2] ?? '') . '-' . ($matches[3] ?? '');
        $countryCode = $number->getCountryCode() ?: '1'; // Default country code (e.g., US: 1)

        // Handle phone numbers without area code (e.g., 699-6677)
        if (empty($matches[1])) {
            $areaCode = ''; // Assign a default area code
        }

    // Return the phone number in the selected format
    if (!empty($phone_format)) {
        $format = $phone_format;
        switch ($format) {
            case 1:
                // Format: 222-2222
                $localNumber = ($matches[2] ?? '') . '-' . ($matches[3] ?? '');
                break;
            case 2:
                // Format: 813.222.2222
                $localNumber = ($matches[1] ?? '') . '.' . ($matches[2] ?? '') . '.' . ($matches[3] ?? '');
                break;
            case 3:
                // Format: 813-222-2222
                $localNumber = ($matches[1] ?? '') . '-' . ($matches[2] ?? '') . '-' . ($matches[3] ?? '');
                break;
            case 4:
                // Format: 8132222222
                $localNumber = ($matches[1] ?? '') . ($matches[2] ?? '') . ($matches[3] ?? '');
                break;
            case 5:
                // Format: +1 (813) 222-2222 ext 123, without parentheses if area code is empty
                if (!empty($extension)) {
                    $extension = ' ext ' . $extension;
                }
                if (!empty($matches[1])) {
                    $localNumber = '+1 (' . ($matches[1] ?? '') . ') ' . ($matches[2] ?? '') . '-' . ($matches[3] ?? '') . $extension;
                } else {
                    $localNumber = '+1 ' . ($matches[2] ?? '') . '-' . ($matches[3] ?? '') . $extension;
                }
                break;
            case 6:
                // Format: 1-813-222-2222X123
                if (!empty($extension)) {
                    $extension = 'X' . $extension;
                }
                $localNumber = '1-' . ($matches[1] ?? '') . '-' . ($matches[2] ?? '') . '-' . ($matches[3] ?? '') . $extension;
                break;
            default:
                // Default format (just in case)
                $localNumber = ($matches[2] ?? '') . '-' . ($matches[3] ?? '');
                break;
        }
    }


            return [
                'phone_number' => $localNumber,
                'phone_number_area_code' => $areaCode,
                'phone_number_country_code' => $countryCode,
                'phone_number_extension' => $exten,
            ];
        } catch (\libphonenumber\NumberParseException $e) {
            return [
                'phone_number' => '',
                'phone_number_area_code' => '',
                'phone_number_country_code' => '',
                'phone_number_extension' => '',
            ];
        }
}




// Step 1: Upload CSV
if (isset($_POST['upload_csv']) && !empty($_FILES['csv_file']) && empty($_POST['csv_column'])
 && $_POST['page']=="upload" ) {

    $uploadDir = 'uploads/';
    $uploadedFilePath = $uploadDir . basename($_FILES['csv_file']['name']);

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['csv_file']['tmp_name'], $uploadedFilePath)) {
        if (($handle = fopen($uploadedFilePath, 'r')) !== false) {
            $csvData = [];
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $csvData[] = $row;
            }
            fclose($handle);

            if (!empty($csvData)) {
                $_SESSION['step'] = 1;
                $_SESSION['csvData'] = $csvData;
                
                // Process headers and replace empty ones with "empty"
                $headers = $csvData[0];
                foreach ($headers as $key => $header) {
                    if (trim($header) === '') {
                        $headers[$key] = 'empty';
                    }
                }

                $_SESSION['headers'] = $headers;
                $_SESSION['rows'] = array_slice($csvData, 1);

                // Create a new CSV file with updated headers
                $updatedFilePath = $uploadDir . 'updated_' . basename($_FILES['csv_file']['name']);
                if (($handle = fopen($updatedFilePath, 'w')) !== false) {
                    fputcsv($handle, $headers);
                    foreach ($_SESSION['rows'] as $row) {
                        fputcsv($handle, $row);
                    }
                    fclose($handle);
                }

                $_SESSION['uploaded_file'] = $updatedFilePath; // Store updated file path in session
                $_SESSION['options'] = [
                    'id', 'first_name', 'last_name', 'middle_name', 'nickname', 'initials', 'birthdate',
                    'extra_suffix', 'ssn', 'date_of_death', 'contact_type_id', 'contact_type_name',
                    'articulation_id', 'articulation', 'dress_id', 'dress', 'education_id', 'education',
                    'gender_id', 'gender', 'language_id', 'language', 'marital_status_id', 'marital_status',
                    'parent_id', 'parent', 'primary_address_id', 'primary_address_street',
                    'primary_address_street_number', 'primary_address_nickname', 'primary_address_po_box',
                    'primary_address_suite', 'primary_address_is_registered_agent', 'primary_address_city_id',
                    'primary_address_city', 'primary_address_country_id', 'primary_address_country',
                    'primary_address_county_id', 'primary_address_county', 'primary_address_state_id',
                    'primary_address_state', 'primary_address_zip_code_id', 'primary_address_zip_code',
                    'contact_email_id', 'contact_email', 'phone_number_id', 'phone_number',
                    'phone_number_area_code', 'phone_number_country_code', 'phone_number_extension',
                    'phone_number_extension_label', 'phone_number_nickname', 'phone_number_is_ada',
                    'phone_number_is_registered_agent', 'additional_fields_data', 'is_archived', 'data', 'note'
                ];
            } else {
                echo "Error: Empty CSV file.";
            }
        } else {
            echo "Error: Failed to read the file.";
        }
    } else {
        echo "Error: Failed to upload file.";
    }

    header('Location:state.php');
    exit();
}


// Extract unique states and their counts from the uploaded file

if (!empty($_POST['csv_column']) && isset($_POST['column_submit']) 
&& $_POST['page']=="state1") {   
   
    $stateIndex = array_search($_POST['csv_column'], $_SESSION['headers']);
    $uniqueStates = [];
    $groupedStates = []; // To store states grouped alphabetically
    $invalidStatesRows = []; // To store rows for invalid states
    $usa_state_only = fetchStatesData(); // Fetch the predefined states data

    if ($stateIndex !== false) {
        // Get the header row (first row of the CSV)
        $headerRow = $_SESSION['csvData'][0];

        $stateCounts = []; // Array to store state counts
        foreach ($_SESSION['csvData'] as $index => $row) {
            if ($index === 0) continue; // Skip the header row
            $state = trim($row[$stateIndex]); // Trim whitespace
            if (!empty($state)) {
                $stateCounts[$state] = ($stateCounts[$state] ?? 0) + 1;

                // Check if the state is valid
                $stateName = strtolower(trim($state));
                $validStatesLookup = [];
                foreach ($usa_state_only as $key => $value) {
                    $validStatesLookup[strtolower($key)] = true; // Abbreviation (e.g., "FL")
                    $validStatesLookup[strtolower($value)] = true; // Full name (e.g., "Florida")
                    $validStatesLookup[strtolower("$value, $key")] = true; // Combined format (e.g., "Florida, FL")
                    $validStatesLookup[strtolower("$key, $value")] = true; // Reverse combined format (e.g., "FL, Florida")
                }

                // If the state is not valid, add the entire row to invalidStatesRows
                if (!isset($validStatesLookup[$stateName])) {
                    // Add the header row first if this is the first invalid row for this state
                    if (!isset($invalidStatesRows[$state])) {
                        $invalidStatesRows[$state] = [$headerRow];
                    }
                    $invalidStatesRows[$state][] = $row;
                }
            }
        }

        // Group valid states alphabetically or into "Symbols & Numbers"
        foreach ($stateCounts as $state => $count) {
            $stateName = strtolower(trim($state));
            $firstChar = strtoupper($stateName[0] ?? '');
            // Check if the state is valid
            $isValidState = isset($validStatesLookup[$stateName]);
            if ($isValidState && ctype_alpha($firstChar)) {
                // Add to alphabetical group
                $groupedStates[$firstChar][] = $state . " (" . $count . ")";
            } else {
                // Add to "Symbols & Numbers" group
                $groupedStates['symbols'][] = $state . " (" . $count . ")";
            }
        }

        // Sort each group alphabetically
        foreach ($groupedStates as $group => &$states) {
            sort($states, SORT_STRING | SORT_FLAG_CASE);
        }
        unset($states); // Break reference

        // Ensure "Symbols & Numbers" group is at the end
        if (isset($groupedStates['symbols'])) {
            $symbolsGroup = $groupedStates['symbols'];
            unset($groupedStates['symbols']); // Remove symbols group temporarily
            ksort($groupedStates); // Sort remaining groups alphabetically
            $groupedStates['symbols'] = $symbolsGroup; // Add symbols group back at the end
        } else {
            ksort($groupedStates); // Sort all groups alphabetically
        }

        // Store the selected column, format, and grouped states in session
        $_SESSION['csv_column'] = $_POST['csv_column'];
        $_SESSION['state_format_select'] = $_POST['state_format_select'];
        $_SESSION['groupedStates'] = $groupedStates; // Store grouped states
        $_SESSION['States_formats'] = formatStatesArray($_POST['state_format_select']);
        $_SESSION['show_states_mapping'] = 1;
       // print_r($_POST['state_format_select']); die; 

        // Encode invalid states and their rows into a JSON array
        $_SESSION['invalidStatesJson'] = json_encode($invalidStatesRows);
        $_SESSION['show_states_mapping']=1;
        //header('location:state.php');
        // print_r($_SESSION['States_formats']);  die; 
        header('Location:state.php'); 
    }
    // print_r($_SESSION['invalidStatesJson']); die;
}








// step 2 state mapping 

if (isset($_POST['state_mapping']) && !empty($_POST['state_mapping'])
&& $_POST['page']=="state2") {
  
    // Get the mappings from the form
    $message = "State Mapping Comleted successfully!";
    $stateMapping = $_POST['state_mapping'];
    $stateMapping_clean = [];
    
    // Clean the array
    foreach ($stateMapping as $key => $value) {
        // Remove the space before the parenthesis and the parenthesis with the count
        $cleanedValue = preg_replace('/\s\(\d+\)$/', '', $key);
        if (!empty($value)) {
            $stateMapping_clean[$cleanedValue] = $value; // Update the array with the cleaned value
        }
    }

    // Path to the uploaded CSV file
    $csvFilePath = $_SESSION['uploaded_file'];
    // Open the CSV file for reading
    $csvData = [];
    $badData = []; // Array to store rows marked as bad data

    if (($handle = fopen($csvFilePath, 'r')) !== false) {
        // Get the headers
        $headers = fgetcsv($handle, 0, ',');
        // Determine the index of the "state" column
        $stateIndex = array_search($_SESSION['csv_column'], $headers);

        if ($stateIndex !== false) {
            // Read the rest of the CSV data
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                // Check if the state has a mapping
                if (isset($stateMapping_clean[$row[$stateIndex]])) {
                    $mappedState = $stateMapping_clean[$row[$stateIndex]];
                    if ($mappedState === 'mark_bad_data') {
                        // Add this row to the bad data array
                        $badData[] = $row;
                    } else {
                        // Update the state column with the mapped value
                        $row[$stateIndex] = $mappedState;
                        // Add the updated row to the main CSV data
                        $csvData[] = $row;
                    }
                } else {
                    // If no mapping exists, keep the row as is
                    $csvData[] = $row;
                }
            }
        } else {
            $message = "State column not found in the CSV file.";
        }
        fclose($handle);
    }

    // Save the updated file in the `working` folder with versioned name
    $workingDir = 'working/';
    if (!is_dir($workingDir)) {
        mkdir($workingDir, 0777, true); // Create the directory if it doesn't exist
    }

    // Get the next version number
    $files = glob($workingDir . 'working-*.csv');
    $version = 1;
    if (!empty($files)) {
        $highestVersion = 0;
        foreach ($files as $file) {
            // Extract the version number using regex
            if (preg_match('/working-(\d+)\.csv$/', $file, $matches)) {
                $highestVersion = max($highestVersion, (int)$matches[1]);
            }
        }
        $version = $highestVersion + 1;
    }

    $newFileName = $workingDir . "working-$version.csv";
    $badDataFileName = $workingDir . "bad_data_$version.csv";

    // Write the updated data to the new working file
    if (!empty($csvData)) {
        if (($handle = fopen($newFileName, 'w')) !== false) {
            // Write the headers
            fputcsv($handle, $headers);
            // Write the updated rows
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
             $message = "Updated file saved as: $newFileName<br>";

            $_SESSION['uploaded_file']=$newFileName;
            update_file($newFileName);
            $_SESSION['step']=1.1;
        } else {
             $message = "Failed to open the working file for writing.<br>";
        }
    }

    // Write the bad data to the new bad_data file
    if (!empty($badData)) {
        if (($handle = fopen($badDataFileName, 'w')) !== false) {
            // Write the headers
            fputcsv($handle, $headers);
            // Write the bad data rows
            foreach ($badData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
            $message = "Bad data file saved as: $badDataFileName<br>";
            $_SESSION['bad_data'] = $badDataFileName;
        } else {
            $message = "Failed to open the bad data file for writing.<br>";
        }
    }

    $stateIndex = array_search($_SESSION['csv_column'], $_SESSION['headers']);
    $uniqueStates = [];
    $groupedStates = []; // To store states grouped alphabetically
    $invalidStatesRows = []; // To store rows for invalid states
    $usa_state_only = fetchStatesData(); // Fetch the predefined states data

    if ($stateIndex !== false) {
        // Get the header row (first row of the CSV)
        $headerRow = $_SESSION['csvData'][0];

        $stateCounts = []; // Array to store state counts
        foreach ($_SESSION['csvData'] as $index => $row) {
            if ($index === 0) continue; // Skip the header row
            $state = trim($row[$stateIndex]); // Trim whitespace
            if (!empty($state)) {
                $stateCounts[$state] = ($stateCounts[$state] ?? 0) + 1;

                // Check if the state is valid
                $stateName = strtolower(trim($state));
                $validStatesLookup = [];
                foreach ($usa_state_only as $key => $value) {
                    $validStatesLookup[strtolower($key)] = true; // Abbreviation (e.g., "FL")
                    $validStatesLookup[strtolower($value)] = true; // Full name (e.g., "Florida")
                    $validStatesLookup[strtolower("$value, $key")] = true; // Combined format (e.g., "Florida, FL")
                    $validStatesLookup[strtolower("$key, $value")] = true; // Reverse combined format (e.g., "FL, Florida")
                }

                // If the state is not valid, add the entire row to invalidStatesRows
                if (!isset($validStatesLookup[$stateName])) {
                    // Add the header row first if this is the first invalid row for this state
                    if (!isset($invalidStatesRows[$state])) {
                        $invalidStatesRows[$state] = [$headerRow];
                    }
                    $invalidStatesRows[$state][] = $row;
                }
            }
        }

        // Group valid states alphabetically or into "Symbols & Numbers"
        foreach ($stateCounts as $state => $count) {
            $stateName = strtolower(trim($state));
            $firstChar = strtoupper($stateName[0] ?? '');
            // Check if the state is valid
            $isValidState = isset($validStatesLookup[$stateName]);
            if ($isValidState && ctype_alpha($firstChar)) {
                // Add to alphabetical group
                $groupedStates[$firstChar][] = $state . " (" . $count . ")";
            } else {
                // Add to "Symbols & Numbers" group
                $groupedStates['symbols'][] = $state . " (" . $count . ")";
            }
        }

        // Sort each group alphabetically
        foreach ($groupedStates as $group => &$states) {
            sort($states, SORT_STRING | SORT_FLAG_CASE);
        }
        unset($states); // Break reference

        // Ensure "Symbols & Numbers" group is at the end
        if (isset($groupedStates['symbols'])) {
            $symbolsGroup = $groupedStates['symbols'];
            unset($groupedStates['symbols']); // Remove symbols group temporarily
            ksort($groupedStates); // Sort remaining groups alphabetically
            $groupedStates['symbols'] = $symbolsGroup; // Add symbols group back at the end
        } else {
            ksort($groupedStates); // Sort all groups alphabetically
        }

        // Store the selected column, format, and grouped states in session
        
        $_SESSION['groupedStates'] = $groupedStates; // Store grouped states
        $_SESSION['States_formats'] = formatStatesArray($_POST['state_format_select']);
        $_SESSION['show_states_mapping'] = 1;
       // print_r($_POST['state_format_select']); die; 

        // Encode invalid states and their rows into a JSON array
        $_SESSION['invalidStatesJson'] = json_encode($invalidStatesRows);
        $_SESSION['show_states_mapping']=1;
        header('location:state.php'  );
        // print_r($_SESSION['States_formats']);  die; 
    }

       
        header("Location: state.php?status=success&message=". urlencode($message));
        exit();





}

// Extract unique citys and their counts from the uploaded file
if (isset($_POST['city_submit']) && $_POST['page']=="city1") {   
    $stateIndex = array_search($_POST['city_column'], $_SESSION['headers']);
    $groupedStates = []; // To store states grouped alphabetically
    $invalidStatesRows = []; // To store rows for invalid states

    if ($stateIndex !== false) {
        // Get the header row (first row of the CSV)
        $headerRow = $_SESSION['csvData'][0];
        $stateCounts = []; // Array to store state counts

        foreach ($_SESSION['csvData'] as $index => $row) {
            if ($index === 0) continue; // Skip the header row
            
            $state = trim($row[$stateIndex]); // Trim whitespace
            if (!empty($state)) {
                $stateCounts[$state] = ($stateCounts[$state] ?? 0) + 1;
            } else {
                // If the state is empty, add the row to invalidStatesRows
                if (!isset($invalidStatesRows['empty'])) {
                    $invalidStatesRows['empty'] = [$headerRow];
                }
                $invalidStatesRows['empty'][] = $row;
            }
        }

        // Group states alphabetically or into "Symbols & Numbers"
        foreach ($stateCounts as $state => $count) {
            $firstChar = strtoupper($state[0] ?? '');
            if (ctype_alpha($firstChar)) {
                // Add to alphabetical group
                $groupedStates[$firstChar][] = $state . " (" . $count . ")";
            } else {
                // Add to "Symbols & Numbers" group
                if (!isset($groupedStates['symbols'])) {
                    $groupedStates['symbols'] = [];
                }
                $groupedStates['symbols'][] = $state . " (" . $count . ")";
            }
        }

        // Sort each group alphabetically
        foreach ($groupedStates as &$states) {
            sort($states, SORT_STRING | SORT_FLAG_CASE);
        }
        unset($states); // Break reference

        // Ensure "Symbols & Numbers" group is at the end
        ksort($groupedStates); // Sort all groups alphabetically
        if (isset($groupedStates['symbols'])) {
            $symbolsGroup = $groupedStates['symbols'];
            unset($groupedStates['symbols']); // Remove symbols group temporarily
            $groupedStates['symbols'] = $symbolsGroup; // Add symbols group back at the end
        }

        // Store the selected column and grouped states in session
        $_SESSION['city_column'] = $_POST['city_column'];
        $_SESSION['groupedStates'] = $groupedStates; // Store grouped states
        $_SESSION['show_city_mapping'] = 1;

        // Encode invalid states and their rows into a JSON array
        $_SESSION['invalidStatesJson'] = json_encode($invalidStatesRows);
        header('location:city.php'  );
    }
}


//   city mapping saved by user

if (isset($_POST['city_mapping']) && !empty($_POST['city_mapping']) && $_POST['page']=="city2") {
    $message = "City Mapping completed successfully!";
    // Get the mappings from the form
    $stateMapping = $_POST['city_mapping'];
    $stateMapping_clean = [];
    
    // Clean the array
    foreach ($stateMapping as $key => $value) {
        // Remove the space before the parenthesis and the parenthesis with the count
        $cleanedValue = preg_replace('/\s\(\d+\)$/', '', $key);
        if (!empty($value)) {
            $stateMapping_clean[$cleanedValue] = $value; // Update the array with the cleaned value
        }
    }

    // Path to the uploaded CSV file
    $csvFilePath = $_SESSION['uploaded_file'];
    // Open the CSV file for reading
    $csvData = [];
    $badData = []; // Array to store rows marked as bad data

    if (($handle = fopen($csvFilePath, 'r')) !== false) {
        // Get the headers
        $headers = fgetcsv($handle, 0, ',');
        // Determine the index of the "state" column
        $stateIndex = array_search($_SESSION['city_column'], $headers);

        if ($stateIndex !== false) {
            // Read the rest of the CSV data
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                // Check if the state has a mapping
                if (isset($stateMapping_clean[$row[$stateIndex]])) {
                    $mappedState = $stateMapping_clean[$row[$stateIndex]];
                    if ($mappedState === 'mark_bad_data') {
                        // Add this row to the bad data array
                        $badData[] = $row;
                    } else {
                        // Update the state column with the mapped value
                        $row[$stateIndex] = $mappedState;
                        // Add the updated row to the main CSV data
                        $csvData[] = $row;
                    }
                } else {
                    // If no mapping exists, keep the row as is
                    $csvData[] = $row;
                }
            }
        } else {
            $message = "State column not found in the CSV file.";
        }
        fclose($handle);
    }

    // Save the updated file in the `working` folder with versioned name
    $workingDir = 'working/';
    if (!is_dir($workingDir)) {
        mkdir($workingDir, 0777, true); // Create the directory if it doesn't exist
    }

    // Get the next version number
    $files = glob($workingDir . 'working-*.csv');
    $version = 1;
    if (!empty($files)) {
        $highestVersion = 0;
        foreach ($files as $file) {
            // Extract the version number using regex
            if (preg_match('/working-(\d+)\.csv$/', $file, $matches)) {
                $highestVersion = max($highestVersion, (int)$matches[1]);
            }
        }
        $version = $highestVersion + 1;
    }

    $newFileName = $workingDir . "working-$version.csv";

    // Write the updated data to the new working file
    if (!empty($csvData)) {
        if (($handle = fopen($newFileName, 'w')) !== false) {
            // Write the headers
            fputcsv($handle, $headers);
            // Write the updated rows
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
             $message = "Updated file saved as: $newFileName<br>";

            $_SESSION['uploaded_file'] = $newFileName;
            update_file($newFileName);
        } else {
             $message = "Failed to open the working file for writing.<br>";
        }
    }

    // Handle bad data merging
    if (!empty($badData)) {
        $badDataFileName = $_SESSION['bad_data'] ?? null; // Check if a bad data file is stored in the session

        // If no bad data file exists, create a new one
        if (!$badDataFileName || !file_exists($badDataFileName)) {
            $badDataFileName = $workingDir . "bad_data_$version.csv";
            $_SESSION['bad_data'] = $badDataFileName;

            // Write the headers and bad data rows to the new file
            if (($handle = fopen($badDataFileName, 'w')) !== false) {
                fputcsv($handle, $headers);
                foreach ($badData as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
                $message = "Bad data file created: $badDataFileName<br>";
            } else {
                $message = "Failed to open the bad data file for writing.<br>";
            }
        } else {
            // If a bad data file already exists, merge the new bad data into it
            if (($handle = fopen($badDataFileName, 'a')) !== false) {
                foreach ($badData as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
                $message = "New bad data merged into existing file: $badDataFileName<br>";
            } else {
                $message = "Failed to open the existing bad data file for appending.<br>";
            }
        }
    }

    

    // Redirect to city.php with the success message as a query parameter
    header("Location: city.php?status=success&message=" . urlencode($message));
    exit();
}





//  fromat number 

if (isset($_POST['phone_mapping_submit'])) {
    $message = "Phone number formatting operation completed successfully!";
    // Get the mappings from the form
    $_SESSION['phone_f'] = $_POST['phone_format'];
    $_SESSION['phone_col'] = $_POST['phone_col'];

    // Path to the uploaded CSV file
    $csvFilePath = $_SESSION['uploaded_file'];

    // Open the CSV file for reading
    $csvData = [];
    if (($handle = fopen($csvFilePath, 'r')) !== false) {
        // Get the headers
        $headers = fgetcsv($handle, 0, ',');

        // Find the index of the selected phone column
        $phoneColIndex = array_search($_SESSION['phone_col'], $headers);
        if ($phoneColIndex === false) {
            echo "Phone column not found in the CSV.";
            exit;
        }

        // Read and process the CSV data
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $phoneValue = $row[$phoneColIndex] ?? ''; // Get the phone value from the selected column

            // Process the phone number using the selected format
            $processedPhone = processPhoneNumber($phoneValue, $_SESSION['phone_f']);

            // Update the corresponding columns with the processed values
            foreach ($processedPhone as $key => $value) {
                // Check if the key exists in the headers, if not, add it to the row
                $columnIndex = array_search($key, $headers);
                if ($columnIndex !== false) {
                    $row[$columnIndex] = $value;
                } else {
                    // Add the new column to the headers and row if it doesn't exist
                    $headers[] = $key;
                    $row[] = $value;
                }
            }

            // Add the processed row to the CSV data
            $csvData[] = $row;
        }

        fclose($handle);
    } else {
        $message = "Error opening the CSV file.";
        exit;
    }

    // Save the updated file in the `working` folder with a versioned name
    $workingDir = 'working/';
    if (!is_dir($workingDir)) {
        mkdir($workingDir, 0777, true); // Create the directory if it doesn't exist
    }

    // Determine the next version number for the file
    $files = glob($workingDir . 'working_phone-*.csv');
    $version = 1;
    if (!empty($files)) {
        $highestVersion = 0;
        foreach ($files as $file) {
            if (preg_match('/working_phone-(\d+)\.csv$/', $file, $matches)) {
                $highestVersion = max($highestVersion, (int)$matches[1]);
            }
        }
        $version = $highestVersion + 1;
    }

    $newFileName = $workingDir . "working_phone-$version.csv";

    // Write the updated CSV data to the new file
    if (($handle = fopen($newFileName, 'w')) !== false) {
        // Write the headers
        fputcsv($handle, $headers);

        // Write the processed rows
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
        $message = "Updated file saved as: $newFileName";

        $_SESSION['uploaded_file']=$newFileName;
        update_file($newFileName);
        $_SESSION['step']=3;
    } else {
        $message = "Failed to save the updated CSV file.";
    }

    // Redirect to city.php with the success message as a query parameter   
    header("Location: phone.php?status=success&message=". urlencode($message));
    exit();
}




//  address mapping 

if (isset($_POST['address_mapping_submit'])) {

    $message = "Address mapping operation completed successfully!";

    // Get the uploaded file path and field mapping
    $uploadedFilePath = $_SESSION['uploaded_file'];
    $fieldMapping = $_POST['field_mapping']; // Contains the user's selected column mappings
    $fieldMapping = array_flip($fieldMapping);

    // Specify the path for the output CSV file
    $workingDir = 'working/';
    if (!is_dir($workingDir)) {
        mkdir($workingDir, 0777, true); // Create the directory if it doesn't exist
    }

    // Get the next version number for the file
    $files = glob($workingDir . 'address-working-*.csv');
    $version = 1;
    if (!empty($files)) {
        $highestVersion = 0;
        foreach ($files as $file) {
            // Extract the version number using regex
            if (preg_match('/address-working-(\d+)\.csv$/', $file, $matches)) {
                $highestVersion = max($highestVersion, (int)$matches[1]);
            }
        }
        $version = $highestVersion + 1;
    }

    $newFileName = $workingDir . "address-working-$version.csv";

    // Open the uploaded file for reading
    if (($handle = fopen($uploadedFilePath, 'r')) !== false) {
        // Get the headers from the uploaded CSV
        $headers = fgetcsv($handle);

        // Create the output CSV file and write the headers (using field mapping)
        $outputHandle = fopen($newFileName, 'w');

        // Create the header for the output CSV based on the field mapping
        $mappedHeaders = [];
        foreach ($headers as $header) {
            // If this header has a mapping and the mapping value is not empty, replace it
            if (isset($fieldMapping[$header]) && !empty($fieldMapping[$header])) {
                // Replace the header with the corresponding field mapping key
                $mappedHeaders[] = $fieldMapping[$header];
            } else {
                // If the mapping is empty or doesn't exist, keep the original header
                $mappedHeaders[] = $header;
            }
        }

        // Write the mapped headers to the output CSV
        fputcsv($outputHandle, $mappedHeaders);

        // Read each row from the CSV, map the data, and write to the output file
        while (($row = fgetcsv($handle)) !== false) {
            $mappedRow = [];
            foreach ($headers as $index => $header) {
                // If the header has a non-empty mapping, replace the value with the mapped header
                if (isset($fieldMapping[$header]) && !empty($fieldMapping[$header])) {
                    // Add the value corresponding to the mapped header
                    $mappedRow[] = $row[$index];
                } else {
                    // Otherwise, keep the value as is for the unmapped column
                    $mappedRow[] = $row[$index];
                }
            }

            // Write the mapped row to the output file
            fputcsv($outputHandle, $mappedRow);
        }

        fclose($handle);
        fclose($outputHandle);
        
  
      $message = "Updated file saved as: $newFileName";

        $_SESSION['uploaded_file']=$newFileName;
        update_file($newFileName);
        $_SESSION['step']=4;
    } else {
      $message = "File not found.";
    }

    // Redirect to city.php with the success message as a query parameter
    header("Location: address.php?status=success&message=". urlencode($message));
    exit();
}




// size adjustments

// Check if the size adjustment form is submitted
if (isset($_POST['size_adjust_submit'])) {
    $selectedColumn = $_POST['size_column'];
    $allowedSize = (int)$_POST['added_size'];

    // Validate inputs
    if (!empty($selectedColumn) && $allowedSize > 0) {
        $_SESSION['size_column'] = $selectedColumn;
        $_SESSION['added_size'] = $allowedSize;

        // Get the index of the selected column
        $columnIndex = array_search($selectedColumn, $_SESSION['headers']);
        $exceedingRows = [];

        // Loop through the CSV data to find rows exceeding the allowed size
        foreach ($_SESSION['csvData'] as $index => $row) {
            if ($index === 0) continue; // Skip header row
            $value = $row[$columnIndex] ?? '';
            if (strlen($value) > $allowedSize) {
                $exceedingRows[] = $row; // Store the entire row
            }
        }

        // Store the exceeding rows in the session
        $_SESSION['exceeding_rows'] = $exceedingRows;
    } else {
       $message = "Please select a valid column and provide a valid maximum character limit.";
    }
   
    header("Location: size.php");
    exit();
}

if (isset($_POST['update_csv_submit'])) {
    $message = "Size adjustment operation completed successfully!";

    $updatedValues = $_POST['updated_values'];
    $ids = $_POST['ids'];

    // Update the CSV data with the new values provided by the user
    foreach ($_SESSION['csvData'] as &$row) {
        $id = $row[0]; // Assuming the first column is the ID
        if (in_array($id, $ids)) {
            $index = array_search($id, $ids);
            $columnIndex = array_search($_SESSION['size_column'], $_SESSION['headers']);
            $row[$columnIndex] = $updatedValues[$index];
        }
    }

    // Save the updated CSV file
    $workingDir = 'working/';
    if (!is_dir($workingDir)) {
        mkdir($workingDir, 0777, true);
    }

    $files = glob($workingDir . 'working_size-*.csv');
    $version = 1;
    if (!empty($files)) {
        $highestVersion = 0;
        foreach ($files as $file) {
            if (preg_match('/working_size-(\d+)\.csv$/', $file, $matches)) {
                $highestVersion = max($highestVersion, (int)$matches[1]);
            }
        }
        $version = $highestVersion + 1;
    }

    $newFileName = $workingDir . "working_size-$version.csv";

    if (($handle = fopen($newFileName, 'w')) !== false) {
        fputcsv($handle, $_SESSION['headers']); // Write headers first

        // Ensure headers are not duplicated in data
        $data = $_SESSION['csvData'];
        if ($data[0] === $_SESSION['headers']) {
            array_shift($data); // Remove headers from data if already present
        }

        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
       $message = "Updated file saved as: $newFileName";
        $_SESSION['uploaded_file']=$newFileName;
        update_file($newFileName);
        $_SESSION['step']=5;
    } else {
       $message = "Failed to save the updated CSV file.";
    }
    // Redirect to city.php with the success message as a query parameter
    header("Location: size.php?status=success&message=". urlencode($message));
    exit();
}









if (!isset($_SESSION['field_mapping'])) {
    // Auto-select the best matching options for headers on the first load
    $headers = $_SESSION['headers'];
    $options = $_SESSION['options'];
    $fieldMapping = [];
    $customHeaders = [];

    foreach ($headers as $header) {
        $bestMatch = '';
        $maxSimilarity = 0;

        // Find the best match based on string similarity
        foreach ($options as $option) {
            similar_text(strtolower($header), strtolower($option), $similarity);
            if ($similarity > $maxSimilarity && $similarity > 70) { // Threshold of 70% similarity
                $maxSimilarity = $similarity;
                $bestMatch = $option;
            }
        }

        // If a good match is found, use it; otherwise, leave it blank
        $fieldMapping[$header] = $bestMatch;
        $customHeaders[$header] = $header; // Default custom header is the original header
    }

    // Save the initial mappings in the session
    $_SESSION['field_mapping'] = $fieldMapping;
    $_SESSION['custom_headers'] = $customHeaders;
}



if (isset($_POST['process_csv']) && !empty($_POST['custom_headers']) && !empty($_SESSION['uploaded_file'])) {
    $uploadedFilePath = $_SESSION['uploaded_file'];
    $fieldMapping = $_POST['field_mapping'];
    $customHeaders = $_POST['custom_headers'] ?? [];
    $_SESSION['field_mapping'] = $fieldMapping;
    $_SESSION['custom_headers'] = $customHeaders;

    // Process the custom headers
    $processedArray = [];
    foreach ($customHeaders as $originalKey => $originalValue) {
        $processedValue = preg_replace('/#\d+/', '', $originalKey);
        $processedArray[$originalValue] = $processedValue;
    }

    $outputCsvPath = 'uploads/mapped_data.csv';

    // Read all data from input file first
    $inputData = [];
    $originalHeaders = [];
    if (($inputHandle = fopen($uploadedFilePath, 'r')) !== false) {
        // Read headers
        $originalHeaders = fgetcsv($inputHandle);
        if ($originalHeaders === false) {
            fclose($inputHandle);
            die("Error reading CSV headers.");
        }
        
        // Read all rows
        while (($row = fgetcsv($inputHandle)) !== false) {
            $inputData[] = $row;
        }
        fclose($inputHandle);
    } else {
        die("Error: Failed to open input file.");
    }

    // Process and write to output file
    if (($outputHandle = fopen($outputCsvPath, 'w')) !== false) {
        // Create header map
        $originalHeaderIndices = array_flip($originalHeaders);
        
        // Write new headers
        fputcsv($outputHandle, array_keys($processedArray));

        // Process each row
        foreach ($inputData as $row) {
            $newRow = [];
            foreach ($processedArray as $newHeader => $originalHeader) {
                // Use the index to get the actual value from the row
                $index = $originalHeaderIndices[$originalHeader] ?? null;
                $newRow[] = $index !== null && isset($row[$index]) ? $row[$index] : ''; // Add the actual value or empty string
            }
            fputcsv($outputHandle, $newRow);
        }

        fclose($outputHandle);
        $_SESSION['output_csv'] = $outputCsvPath;
        update_file($outputCsvPath);
        $_SESSION['step'] = 6;
    } else {
        die("Error: Failed to open output file.");
    }

    // Redirect to city.php with success message
    $message = "Fields Mapping completed successfully!";
    header("Location: field_map.php?status=success&message=". urlencode($message));
    exit();

}







// Step 3: Reset
if (isset($_POST['reset'])) {
    session_destroy();
    header("Location:index.php" );
    exit;
}

function update_file($file){
  $uploadedFilePath=$file;
    if ($uploadedFilePath) {
        if (($handle = fopen($uploadedFilePath, 'r')) !== false) {
            $csvData = [];
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $csvData[] = $row;
            }
            fclose($handle);

            if (!empty($csvData)) {
                $_SESSION['csvData']=$csvData;
                $_SESSION['headers'] = $csvData[0];
                $_SESSION['rows'] = array_slice($csvData, 1);
                $_SESSION['uploaded_file'] = $uploadedFilePath;
                    if(isset($_POST['address_mapping_submit'])){
                        $_SESSION['options'] = array(
                            "id",
                            "first_name",
                            "last_name",
                            "middle_name",
                            "nickname",
                            "initials",
                            "birthdate",
                            "extra_suffix",
                            "ssn",
                            "date_of_death",
                            "contact_type_id",
                            "contact_type_name",
                            "articulation_id",
                            "articulation",
                            "dress_id",
                            "dress",
                            "education_id",
                            "education",
                            "gender_id",
                            "gender",
                            "language_id",
                            "language",
                            "marital_status_id",
                            "marital_status",
                            "parent_id",
                            "parent",
                            "first_line",
                            "first_line",
                            "second_line",
                            "second_line",
                            "plus_5",
                            "city",
                            "zip_code",
                            "primary_address_city_id",
                            "city",
                            "primary_address_country_id",
                            "plus_5",
                            "primary_address_county_id",
                            "primary_address_county",
                            "name_of_address",
                            "state",
                            "primary_address_zip_code_id",
                            "zip_code",
                            "contact_email_id",
                            "contact_email",
                            "phone_number_id",
                            "phone_number",
                            "phone_number_area_code",
                            "phone_number_country_code",
                            "phone_number_extension",
                            "phone_number_extension_label",
                            "phone_number_nickname",
                            "phone_number_is_ada",
                            "phone_number_is_registered_agent",
                            "additional_fields_data",
                            "is_archived",
                            "data",
                            "note"
                        );
                    }

            } else {
                echo "Error: Empty CSV file.";
            }
        } else {
            echo "Error: Failed to read the file.";
        }


}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_changes'])) {
    // Ensure the output CSV file exists in the session
    if (!isset($_SESSION['output_csv']) || !file_exists($_SESSION['output_csv'])) {
        die("Output CSV file not found.");
    }

    // Read the headers of the bad_data CSV file
    $badDataFile = $_SESSION['bad_data'];
    if (!file_exists($badDataFile)) {
        die("Bad data file not found.");
    }

    $badDataHeaders = [];
    if (($handle = fopen($badDataFile, 'r')) !== false) {
        $badDataHeaders = fgetcsv($handle);
        fclose($handle);
    }

    // Read the headers of the output CSV file
    $outputCsvFile = $_SESSION['output_csv'];
    $outputHeaders = [];
    if (($handle = fopen($outputCsvFile, 'r')) !== false) {
        $outputHeaders = fgetcsv($handle);
        fclose($handle);
    }

    // Create a mapping between bad_data headers and output CSV headers
    $headerMap = [];
    foreach ($badDataHeaders as $header) {
        $headerMap[$header] = array_search($header, $outputHeaders);
    }

    // Read the submitted form data
    $submittedData = $_POST['csv_data'] ?? [];

    // Prepare the updated rows for the output CSV
    $updatedRows = [];
    foreach ($submittedData as $rowNumber => $rowData) {
        $updatedRow = array_fill(0, count($outputHeaders), ''); // Initialize with empty values
        foreach ($rowData as $columnName => $value) {
            if (isset($headerMap[$columnName]) && $headerMap[$columnName] !== false) {
                $updatedRow[$headerMap[$columnName]] = $value; // Map the value to the correct column
            }
        }
        $updatedRows[] = $updatedRow;
    }

    // Write the updated rows back to the output CSV file
    if (($handle = fopen($outputCsvFile, 'a')) !== false) {
        foreach ($updatedRows as $row) {
            fputcsv($handle, $row); // Write each row to the output CSV file
        }
        fclose($handle);
    }

    // Update the session's output_csv with the updated file path
    $_SESSION['output_csv'] = $outputCsvFile;

    // Unset the bad_data file from the session
    unset($_SESSION['bad_data']);

    echo "Data successfully added to the output CSV file. Bad data file has been cleared.";

    // Redirect to city.php with success message
    $message = "Data successfully added to the output CSV file. Bad data file has been cleared.";
    header("Location: preview.php?status=success&message=". urlencode($message));
    exit();
}


// Reset session if page is reloaded
if (isset($_POST['page_reload']) && $_POST['page_reload'] === 'true') {
    session_destroy();
    session_start(); // Restart the session to avoid errors
    $_SESSION = []; // Clear all session variables
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to clear POST data
    exit;
}



function processCsvAndRemoveColumn($note) {
    // Step 1: Check if the uploaded file path exists in the session
    if (!isset($_SESSION['uploaded_file'])) {
        return json_encode(['status' => 'error', 'message' => 'No uploaded file found in the session.']);
    }

    $uploadedFilePath = $_SESSION['uploaded_file'];

    // Step 2: Read the CSV file content
    if (($handle = fopen($uploadedFilePath, 'r')) !== false) {
        $csvData = [];
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $csvData[] = $row;
        }
        fclose($handle);

        if (empty($csvData)) {
            return json_encode(['status' => 'error', 'message' => 'The uploaded file is empty or could not be read.']);
        }

        // Step 3: Extract headers and rows
        $headers = $csvData[0];
        $rows = array_slice($csvData, 1);

        // Step 4: Find the index of the header matching the note value
        $headerIndex = array_search($note, $headers);

        if ($headerIndex !== false) {
            // Step 5: Remove the header from the headers array
            unset($headers[$headerIndex]);
            $headers = array_values($headers); // Reindex the headers array

            // Step 6: Remove the corresponding column values from each row
            foreach ($rows as &$row) {
                unset($row[$headerIndex]);
                $row = array_values($row); // Reindex the row array
            }

            // Step 7: Combine the updated headers and rows back into CSV format
            $updatedCsvData = array_merge([$headers], $rows);

            // Step 8: Write the updated CSV data back to the file
            if (($handle = fopen($uploadedFilePath, 'w')) !== false) {
                foreach ($updatedCsvData as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);

                // Step 9: Update the session variables
                $_SESSION['headers'] = $headers;
                $_SESSION['rows'] = $rows;
                $_SESSION['csvData'] = $updatedCsvData;

                return json_encode(['status' => 'success', 'message' => "Header '$note' and its column removed successfully."]);
            } else {
                return json_encode(['status' => 'error', 'message' => 'Failed to write the updated CSV data back to the file.']);
            }
        } else {
            return json_encode(['status' => 'error', 'message' => "Header '$note' not found in the CSV file."]);
        }
    } else {
        return json_encode(['status' => 'error', 'message' => 'Failed to open the uploaded file for reading.']);
    }
}

// Handle AJAX request to remove a header and column
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'])) {
    $note = trim($_POST['note']);

    if (!empty($note)) {
        echo processCsvAndRemoveColumn($note);
        die;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No note value received.']);
        die;
    }
}






?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Processor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
   
   <style>
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }


        
    </style>
</head>
<body class="container mt-5">
    <h3 class="mb-4 text-center">CSV Processing Tool</h3>
    <div class="accordion" id="csvProcessorAccordion">
                <!-- Step 1: Upload CSV -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Step 1: Upload CSV File
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse <?php echo ($_SESSION['step']==0) ? 'show' : ''; ?>" aria-labelledby="headingOne" data-bs-parent="#csvProcessorAccordion">
                        <div class="accordion-body">
                            <form method="post" enctype="multipart/form-data" class="mb-4 text-center">
                                <div class="mb-3 d-flex justify-content-center">
                                    <input type="file" name="csv_file" id="csv_file" class="form-control" style="width: 300px;" required>
                                </div>

                                  <!-- Display the attached file if it exists -->
                                    <?php if (isset($_SESSION['uploaded_file'])): ?>
                                        <div class="mb-3 text-success">
                                            File Attached: <?php echo basename($_SESSION['uploaded_file']); ?>
                                        </div>
                                    <?php endif; ?>



                                <button type="submit" name="upload_csv" class="btn btn-primary">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>


                <script>


    // Check if the page is being reloaded
$(document).ready(function () {
    // Check if the page was reloaded
    if (performance.navigation.type === 1) { // 1 means the page was reloaded
        // Trigger a hidden form submission to notify the server
        const reloadForm = document.createElement('form');
        reloadForm.method = 'POST';
        reloadForm.action = window.location.href;

        const reloadInput = document.createElement('input');
        reloadInput.type = 'hidden';
        reloadInput.name = 'page_reload';
        reloadInput.value = 'true';

        reloadForm.appendChild(reloadInput);
        document.body.appendChild(reloadForm);
        reloadForm.submit();
    }
});

</script>

<script>


if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    document.addEventListener('DOMContentLoaded', function() {
    
    // Restore accordion states
    const savedState = localStorage.getItem('accordionState');
    if (savedState) {
        const states = JSON.parse(savedState);
        states.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                const bsCollapse = new bootstrap.Collapse(element, { toggle: false });
                bsCollapse.show();
            }
        });
    }

    // Save accordion states
    function saveState() {
        const openAccordions = [];
        document.querySelectorAll('.accordion-collapse.show').forEach(collapse => {
            openAccordions.push(collapse.id);
        });
        localStorage.setItem('accordionState', JSON.stringify(openAccordions));
    }

    // Event listeners for accordion changes
    document.querySelectorAll('.accordion-collapse').forEach(collapse => {
        collapse.addEventListener('shown.bs.collapse', saveState);
        collapse.addEventListener('hidden.bs.collapse', saveState);
    });
});


</script>


<!-- Step 2: Map Fields -->
<?php if (isset($_SESSION['headers']) && isset($_SESSION['rows'])) : ?>
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingTwo">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                Step 2: Modify CSV file
            </button>
        </h2>
        <div id="collapseTwo" 
        class="accordion-collapse collapse <?php if($_SESSION['step']==1 || $_SESSION['step']==1.1 || $_SESSION['step']==2 || $_SESSION['step']==3 || $_SESSION['step']==4 || $_SESSION['step']==5 ){ echo "show";} ?>"
         aria-labelledby="headingTwo" data-bs-parent="#csvProcessorAccordion">
            <div class="accordion-body">


        </div>
    </div>
    </div>
    <?php endif; ?>










        <!--  Download Processed File -->

        <?php if (isset($_SESSION['output_csv']) ) : ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                        Step 4: Download Processed File
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#csvProcessorAccordion">
                    <div class="accordion-body text-center">
                        <a href="<?php echo htmlspecialchars($_SESSION['output_csv']); ?>" class="btn btn-warning mb-3" download>Download Mapped File</a>
                        <?php if(isset($_SESSION['bad_data'])) { ?>
                        <a href="<?php echo htmlspecialchars($_SESSION['bad_data']); ?>" class="btn btn-success mb-3" download>Download Bad Data File</a>
                        <?php  } ?> 
                        <form method="post">
                            <button type="submit" name="reset" class="btn btn-danger">Restart</button>
                        </form>
                    </div>
                </div>
            </div>

        <?php endif; ?>



    </div>





  

</body>
</html>


