<?php
require '../vendor/autoload.php';

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

session_start();


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
if (isset($_POST['upload_csv']) && !empty($_FILES['csv_file'])) {
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
                $_SESSION['step']=1;
                $_SESSION['csvData']=$csvData;
                $_SESSION['headers'] = $csvData[0];
                $_SESSION['rows'] = array_slice($csvData, 1);
                $_SESSION['uploaded_file'] = $uploadedFilePath;
                $_SESSION['options'] =$fields = [
                                            'id',
                                            'first_name',
                                            'last_name',
                                            'middle_name',
                                            'nickname',
                                            'initials',
                                            'birthdate',
                                            'extra_suffix',
                                            'ssn',
                                            'date_of_death',
                                            'contact_type_id',
                                            'contact_type_name',
                                            'articulation_id',
                                            'articulation',
                                            'dress_id',
                                            'dress',
                                            'education_id',
                                            'education',
                                            'gender_id',
                                            'gender',
                                            'language_id',
                                            'language',
                                            'marital_status_id',
                                            'marital_status',
                                            'parent_id',
                                            'parent',
                                            'primary_address_id',
                                            'primary_address_street',
                                            'primary_address_street_number',
                                            'primary_address_nickname',
                                            'primary_address_po_box',
                                            'primary_address_suite',
                                            'primary_address_is_registered_agent',
                                            'primary_address_city_id',
                                            'primary_address_city',
                                            'primary_address_country_id',
                                            'primary_address_country',
                                            'primary_address_county_id',
                                            'primary_address_county',
                                            'primary_address_state_id',
                                            'primary_address_state',
                                            'primary_address_zip_code_id',
                                            'primary_address_zip_code',
                                            'contact_email_id',
                                            'contact_email',
                                            'phone_number_id',
                                            'phone_number',
                                            'phone_number_area_code',
                                            'phone_number_country_code',
                                            'phone_number_extension',
                                            'phone_number_extension_label',
                                            'phone_number_nickname',
                                            'phone_number_is_ada',
                                            'phone_number_is_registered_agent',
                                            'additional_fields_data',
                                            'is_archived',
                                            'data',
                                            'note'
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
}



// Extract unique states and their counts from the uploaded file

if (!empty($_POST['csv_column'])) {   
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

        // print_r($_SESSION['States_formats']);  die;  
    }
    // print_r($_SESSION['invalidStatesJson']); die;
}


// Extract unique citys and their counts from the uploaded file
if (isset($_POST['city_submit'])) {   
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
    }
}






// step 2 state mapping 

if (isset($_POST['state_mapping']) && !empty($_POST['state_mapping'])) {
    // Get the mappings from the form
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
            echo "State column not found in the CSV file.";
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
            echo "Updated file saved as: $newFileName<br>";

            $_SESSION['uploaded_file']=$newFileName;
            update_file($newFileName);
            $_SESSION['step']=1.1;
        } else {
            echo "Failed to open the working file for writing.<br>";
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
            echo "Bad data file saved as: $badDataFileName<br>";
            $_SESSION['bad_data'] = $badDataFileName;
        } else {
            echo "Failed to open the bad data file for writing.<br>";
        }
    }
}


//   city mapping saved by user


if (isset($_POST['city_mapping']) && !empty($_POST['city_mapping'])) {
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
            echo "State column not found in the CSV file.";
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
            echo "Updated file saved as: $newFileName<br>";

            $_SESSION['uploaded_file'] = $newFileName;
            update_file($newFileName);
        } else {
            echo "Failed to open the working file for writing.<br>";
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
                echo "Bad data file created: $badDataFileName<br>";
            } else {
                echo "Failed to open the bad data file for writing.<br>";
            }
        } else {
            // If a bad data file already exists, merge the new bad data into it
            if (($handle = fopen($badDataFileName, 'a')) !== false) {
                foreach ($badData as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
                echo "New bad data merged into existing file: $badDataFileName<br>";
            } else {
                echo "Failed to open the existing bad data file for appending.<br>";
            }
        }
    }
}





//  fromat number 

if (isset($_POST['phone_mapping_submit'])) {
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
        echo "Error opening the CSV file.";
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
        echo "Updated file saved as: $newFileName";

        $_SESSION['uploaded_file']=$newFileName;
        update_file($newFileName);
        $_SESSION['step']=3;
    } else {
        echo "Failed to save the updated CSV file.";
    }
}




//  address mapping 

if (isset($_POST['address_mapping_submit'])) {


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
        
  
        echo "Updated file saved as: $newFileName";

        $_SESSION['uploaded_file']=$newFileName;
        update_file($newFileName);
        $_SESSION['step']=4;
    } else {
        echo "File not found.";
    }
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
        echo "Please select a valid column and provide a valid maximum character limit.";
    }
}

if (isset($_POST['update_csv_submit'])) {
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
        echo "Updated file saved as: $newFileName";
        $_SESSION['uploaded_file']=$newFileName;
        update_file($newFileName);
        $_SESSION['step']=5;
    } else {
        echo "Failed to save the updated CSV file.";
    }
}





if (isset($_POST['size_adjust_auto'])) {
    if (!isset($_POST['size_column_2'], $_POST['size_data_2'], $_SESSION['csvData'], $_SESSION['headers'])) {
        die("Missing required data.");
    }

    $selectedColumn = $_POST['size_column_2'];
    $selectedOption = $_POST['size_data_2'];

    // Validate inputs
    if (empty($selectedColumn) || empty($selectedOption)) {
        die("Please select both a column and an adjustment option.");
    }

    // Ensure the selected column exists
    $columnIndex = array_search($selectedColumn, $_SESSION['headers']);
    if ($columnIndex === false) {
        die("Invalid column selection.");
    }

    // Function to extract the first word
    function getFirstWord($text) {
        $words = explode(' ', trim($text));
        return $words[0] ?? $text; // Return first word or original text if no spaces
    }

    // Function to extract initials (first letter of each word in uppercase)
    function getInitials($text) {
        $words = explode(' ', trim($text));
        $initials = array_map(function($word) {
            return strtoupper($word[0] ?? '');
        }, $words);
        return implode('', $initials);
    }

    // Apply transformation based on selected option
    foreach ($_SESSION['csvData'] as &$row) {
        if (!isset($row[$columnIndex])) continue; // Skip if column index is not set

        if ($selectedOption === 'First word only') {
            $row[$columnIndex] = getFirstWord($row[$columnIndex]);
        } elseif ($selectedOption === 'Initials only') {
            $row[$columnIndex] = getInitials($row[$columnIndex]);
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
        echo "Updated file saved as: $newFileName";
        $_SESSION['uploaded_file']=$newFileName;
        update_file($newFileName);
        
    } else {
        echo "Failed to save the updated CSV file.";
    }
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

if (isset($_POST['process_csv']) && !empty($_POST['field_mapping']) && !empty($_SESSION['uploaded_file'])) {
    $uploadedFilePath = $_SESSION['uploaded_file'];
    $fieldMapping = $_POST['field_mapping']; // Generic field mappings
    $customHeaders = $_POST['custom_headers'] ?? []; // Custom headers
    $_SESSION['field_mapping'] = $fieldMapping;
    $_SESSION['custom_headers'] = $customHeaders;

    $outputCsvPath = 'uploads/mapped_data.csv';

    if (($handle = fopen($uploadedFilePath, 'r')) !== false) {
        $headers = fgetcsv($handle);
        $validColumns = [];
        $selectedColumns = [];

        // Identify valid columns (non-empty headers)
        foreach ($headers as $index => $header) {
            // Check if the header exists in the field mapping
            if (!empty(trim($header)) && isset($fieldMapping[$header]) && !empty($fieldMapping[$header])) {
                $validColumns[$index] = $header;
                $selectedColumns[] = $customHeaders[$header] ?? $fieldMapping[$header]; // Use custom header if available
            }
        }

        // Store selected columns in session
        $_SESSION['selected_columns'] = $selectedColumns;

        $outputHandle = fopen($outputCsvPath, 'w');

        // Write mapped headers
        fputcsv($outputHandle, $selectedColumns);

        // Process CSV rows
        while (($row = fgetcsv($handle)) !== false) {
            $mappedRow = [];
            foreach ($validColumns as $index => $header) {
                $mappedRow[] = $row[$index] ?? ''; // Add only selected columns
            }
            fputcsv($outputHandle, $mappedRow);
        }

        fclose($handle);
        fclose($outputHandle);
        $_SESSION['output_csv'] = $outputCsvPath;
        $_SESSION['step'] = 6;
    } else {
        echo "Error: Failed to process file.";
    }
}



// Step 3: Reset
if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
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
}


// Reset session if page is reloaded
if (isset($_POST['page_reload']) && $_POST['page_reload'] === 'true') {
    session_destroy();
    session_start(); // Restart the session to avoid errors
    $_SESSION = []; // Clear all session variables
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to clear POST data
    exit;
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

        <!-- Parent Accordion for Mapping Forms -->
        <div class="accordion" id="mappingFormsAccordion">
            
            <!-- State Mapping Accordion Item -->
        <div class="accordion-item">
                            <h2 class="accordion-header" id="stateMappingHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stateMappingCollapse" aria-expanded="false" aria-controls="stateMappingCollapse">
                                    State Mapping
                                </button>
                            </h2>
                            <div id="stateMappingCollapse" class="accordion-collapse collapse <?php echo ($_SESSION['step']==1) ? 'show' : ''; ?> " aria-labelledby="stateMappingHeading" data-bs-parent="#mappingFormsAccordion">
                                <div class="accordion-body">
                                    
                                    <h4 class="form-header">States Mapping</h4>

                                    <!-- Column Selection Form -->
                                    <form method="post" action="process.php" class="mb-4 text-center ">
                                    <div class="row mb-3">
                                    <div class="col">
                                        <label for="csvColumnSelect" class="form-label">Select Column to Map States</label>
                                        <select id="csvColumnSelect" name="csv_column" class="form-select">
                                            <option value="">Select Column</option>
                                            <?php 
                                            foreach ($_SESSION['headers'] as $header): 
                                                if (stripos($header, 'state') !== false ): // Case-insensitive search for "state"
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
                                            <option value="">Select Column</option>
                                            <?php 
                                            foreach ($_SESSION['state_format'] as $header): 
                                            ?>
                                                <option value="<?php echo htmlspecialchars($header); ?>" 
                                                    <?php echo (isset($_SESSION['state_format_select']) && $_SESSION['state_format_select'] === $header) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($header); ?>
                                                </option>
                                            <?php 
                                            endforeach; 
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                        
                                        <button type="submit" name="column_submit" class="btn btn-primary">Show</button>
                                    </form>




                                <?php
                                if (!isset($_SESSION['groupedStates']) || empty($_SESSION['groupedStates'])) {
                                    echo '<p class="text-center">No states available for mapping.</p>';
                                    return;
                                }
                                if($_SESSION['show_states_mapping']==1){ ?>

                                <form method="post" class="mb-4 text-center">
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




                    <!-- city Mapping Accordion Item -->
                    <div class="accordion-item">
                            <h2 class="accordion-header" id="cityMappingHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cityMappingCollapse" aria-expanded="false" aria-controls="cityMappingCollapse">
                                City Mapping
                                </button>
                            </h2>
                            <div id="cityMappingCollapse" class="accordion-collapse collapse <?php echo ($_SESSION['step']==1.1) ? ' ' : ''; ?> " aria-labelledby="cityMappingHeading" data-bs-parent="#mappingFormsAccordion">
                                <div class="accordion-body">
                                    
                                    <h4 class="form-header">City Mapping</h4>

                                    <!-- Column Selection Form -->
                                    <form method="post" action="process.php" class="mb-4 text-center ">
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
                                    return;
                                }
                                if($_SESSION['show_city_mapping']==1){ ?>

                                <form method="post" class="mb-4 text-center">
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
                                                                                                ?>
                                                                                            <option value="<?php echo htmlspecialchars($status); ?>" >
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



                        <!-- Phone Number  Accordion Item -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="phoneHeading">
                            <button class="accordion-button collapsed " type="button" data-bs-toggle="collapse" data-bs-target="#phoneCollapse" aria-expanded="false" aria-controls="phoneCollapse">
                                Phone Number Modification
                            </button>
                        </h2>

                        <div id="phoneCollapse" class="accordion-collapse collapse <?php echo ($_SESSION['step']==2) ? '' : ''; ?> " aria-labelledby="phoneHeading" data-bs-parent="#mappingFormsAccordion">
                            <div class="accordion-body">
                                
                                <h4 class="form-header">Phone Number Format Setting</h4>
                                                                <!-- Column Selection Form -->
                                        <form method="post" action="process.php" class="mb-4 text-center">
                                        <div class="mb-3">
                                        <label class="form-label">Select Phone number Column and Phone number format</label>
                                        <div class="row">
                                        
                                            <div class="col-md-6">
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
                        </div>


                    </div>

                                            <!-- Address  Accordion Item -->
            <div class="accordion-item">
                        <h2 class="accordion-header" id="addressHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#addressCollapse" aria-expanded="false" aria-controls="addressCollapse">
                                Address Mapping
                            </button>
                        </h2>

                        <div id="addressCollapse" class="accordion-collapse collapse <?php echo ($_SESSION['step']==3) ? ' ' : ''; ?> " aria-labelledby="addressHeading" data-bs-parent="#mappingFormsAccordion">
                            <div class="accordion-body">
                                
                                <h4 class="form-header"> Address Mapping </h4>
                                                                <!-- Column Selection Form -->
                                        <form method="post" action="process.php" class="mb-4 text-center">
                                        <div class="mb-3">
                                        <label class="form-label">Select Address Column and Phone number format</label>
                                      
                                        <?php 
                                        // Create read-only fields based on the session variable count
                                        if (isset($_SESSION['address_format']) && is_array($_SESSION['address_format'])): 
                                            $phoneFormatCount = count($_SESSION['address_format']);
                                            for ($i = 0; $i < $phoneFormatCount; $i++):
                                        ?>
                                        <div class="row mb-3">
                                      
                                            <div class="col-md-6">
                                                <input type="text" value="<?php echo htmlspecialchars($_SESSION['address_format'][$i]); ?>" class="form-control" readonly>
                                            </div>


                                        <div class="col-md-6">
                                            <select id="csvAddressColumnSelect"   name="field_mapping[<?php echo htmlspecialchars($_SESSION['address_format'][$i]); ?>]" class="form-select">
                                                <option value="">Select Address Column</option>
                                                <?php 
                                                foreach ($_SESSION['headers'] as $header): 
                                                    if (stripos($header, 'address') !== false): // Case-insensitive search for "address"
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

                                    </div>
                                    
                                    <?php 
                                            endfor;
                                        endif;
                                        ?>

                                </div>

                                   
                                    <button type="submit" name="address_mapping_submit" class="btn btn-primary">Save</button>
                                </form>

                            </div>
                        </div>


            </div>



            
                                                         
            <!-- Size  Accordion Item -->
            
            <div class="accordion-item">
                        <h2 class="accordion-header" id="size_data_adjust">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#size_data_adjustCollapse" aria-expanded="false" aria-controls="size_data_adjustCollapse">
                            Data size adjustments
                            </button>
                        </h2>
                        <div id="size_data_adjustCollapse" class="accordion-collapse collapse <?php echo ($_SESSION['step']==4) ? ' ' : ''; ?> " aria-labelledby="size_data_adjust" data-bs-parent="#mappingFormsAccordion">
                            
                            <div class="accordion-body">

               
               
                            <div class="accordion" id="data_DropdownAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="sizeHeading">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sizeCollapse" aria-expanded="true" aria-controls="sizeCollapse">
                                        Data size adjustments Manually
                                    </button> 
                                </h2>

                                <div id="sizeCollapse" class="accordion-collapse collapse  <?php echo ($_SESSION['step']==4) ? ' ' : ''; ?>" aria-labelledby="sizeHeading" data-bs-parent="#data_DropdownAccordion">
                                    <div class="accordion-body">
                                        
                                        <h4 class="form-header"> Data size adjustments </h4>
                                            
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
                                </div>

                                

                            </div>

                            </div>
                        </div>
                    </div>     
            </div>





            <div class="accordion-item">
    <h2 class="accordion-header" id="fieldMappingHeading">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#fieldMappingCollapse" aria-expanded="false" aria-controls="fieldMappingCollapse">
            Field Mapping
        </button>
    </h2>
    <div id="fieldMappingCollapse"
        class="accordion-collapse collapse <?php echo ($_SESSION['step']==5) ? '' : ''; ?>"
        aria-labelledby="fieldMappingHeading" data-bs-parent="#mappingFormsAccordion">
        <div class="accordion-body">
            <h2 class="form-header">Field Mapping</h2>
            <form method="post" class="mb-4 text-center">
                <input type="hidden" name="uploaded_file"
                    value="<?php echo htmlspecialchars($_SESSION['uploaded_file']); ?>">
                <div id="mappingFields" class="mb-4">
                    <h6 class="mb-3">Field Mapping</h6>
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
        </div>
    </div>
</div>

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
    // Add event listener to dynamically update input fields when a dropdown value changes
    document.querySelectorAll('.field-mapping-select').forEach(select => {
        select.addEventListener('change', function () {
            const parentRow = this.closest('.mapping-row');
            const inputField = parentRow.querySelector('.custom-header-input');
            inputField.value = this.value; // Update input field with selected value
        });
    });

    // Add new row button functionality
    document.getElementById('addRow').addEventListener('click', function () {
        let newRowId = 'new_field_' + Date.now();
        let rowHtml = `
            <div class="row mb-3 mapping-row">
                <div class="col-md-12 d-flex align-items-center">
                    <select class="form-select me-2 field-mapping-select" name="field_mapping[${newRowId}]" 
                        onchange="updateFieldNames(this)"
                          >
                        <option value="">Select Field</option>
                        <?php 
                        $savedMappings = !empty($_SESSION['selected_columns']) ? $_SESSION['selected_columns'] : $_SESSION['headers'];
                        foreach ($savedMappings as $option) : ?>
                        <option value="<?php echo htmlspecialchars($option); ?>">
                            <?php echo htmlspecialchars($option); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" class="form-control me-2 custom-header-input"
                        name="custom_headers[${newRowId}]"
                        placeholder="Enter new field name" value="">
                    <button type="button" class="btn btn-danger remove-row" onclick="showConfirmModal(this)">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>`;
        document.getElementById('mappingFields').insertAdjacentHTML('beforeend', rowHtml);

        // Add change listener to newly added select elements
        document.querySelectorAll('.field-mapping-select').forEach(select => {
            select.addEventListener('change', function () {
                const parentRow = this.closest('.mapping-row');
                const inputField = parentRow.querySelector('.custom-header-input');
                inputField.value = this.value; // Update input field with selected value
            });
        });
    });

    // Confirmation modal functionality for removing rows
    let removeTarget = null;

    window.showConfirmModal = function (element) {
        removeTarget = element.closest('.mapping-row'); // Store reference to the row
        var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'), {
            keyboard: false
        });
        myModal.show();
    };

    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (removeTarget) {
            removeTarget.remove(); // Remove the targeted row
        }
        var myModalEl = document.getElementById('confirmDeleteModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        modal.hide(); // Hide the modal after removal
        });
        });

    // Function to update the name attributes dynamically
    function updateFieldNames(selectElement) {
        // Get the selected value
        const selectedValue = selectElement.value;

        // Find the parent container
        const parentDiv = selectElement.closest('.col-md-12');

        // Update the name attribute of the select field
        if (selectedValue) {
            selectElement.name = `field_mapping[${selectedValue}]`;
        } else {
            selectElement.name = 'field_mapping[new_field_1741863378426]';
        }

        // Find the input field and update its name attribute
        const inputField = parentDiv.querySelector('.custom-header-input');
        if (selectedValue) {
            inputField.name = `custom_headers[${selectedValue}]`;
        } else {
            inputField.name = 'custom_headers[new_field_1741863378426]';
        }
    }

</script>




            </div> <!-- End Parent Accordion -->

        </div>
    </div>
    </div>
    <?php endif; ?>







<!-- Step 3: preview for the bad data -->
<?php if (isset($_SESSION['bad_data']) && $_SESSION['step'] == 6) : ?>
<div class="accordion-item">
    <h2 class="accordion-header" id="headingfour">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsefour" aria-expanded="true" aria-controls="collapsefour">
            Step 4: Edit Processed File
        </button>
    </h2>
    <div id="collapsefour" class="accordion-collapse collapse show" aria-labelledby="headingfour" data-bs-parent="#csvProcessorAccordion">
        <div class="accordion-body">
            <!-- Single Table with Sticky Header -->
            <form method="post" id="csvEditForm">
                <div style="max-height: 400px; overflow-y: auto;" class="table-responsive">
                    <table class="table table-bordered">
                        <!-- Fixed Header -->
                        <thead style="position: sticky; top: 0; background-color: white; z-index: 1;">
                            <tr>
                                <?php
                                // Fetch and parse the CSV file
                                $csvFile = fopen($_SESSION['bad_data'], 'r');
                                $headers = fgetcsv($csvFile);
                                foreach ($headers as $header) {
                                    echo "<th>" . htmlspecialchars($header) . "</th>";
                                }
                                fclose($csvFile);
                                ?>
                            </tr>
                        </thead>

                        <!-- Scrollable Table Body -->
                        <tbody>
                            <?php
                            // Reopen the file to read all rows
                            $csvFile = fopen($_SESSION['bad_data'], 'r');
                            $headers = fgetcsv($csvFile); // Get headers
                            $rowNumber = 0; // Initialize row counter
                            while (($row = fgetcsv($csvFile)) !== false) {
                                echo "<tr>";
                                foreach ($headers as $index => $header) {
                                    // Use column names in the input field names
                                    echo "<td><input type='text' name='csv_data[" . $rowNumber . "][" . htmlspecialchars($header) . "]' value='" . htmlspecialchars($row[$index]) . "' class='form-control' style='width: auto; min-width: 100px;' oninput='this.style.width = ((this.value.length + 1) * 8) + \"px\";'></td>";
                                }
                                echo "</tr>";
                                $rowNumber++; // Increment row counter
                            }
                            fclose($csvFile);
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Fixed Save Button -->
                <div style="position: sticky; bottom: 0; background-color: white; z-index: 1; padding: 10px 0; text-align: center;">
                    <button type="submit" name="save_changes" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
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


