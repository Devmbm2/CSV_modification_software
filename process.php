<?php
require '../vendor/autoload.php';

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

session_start();

$_SESSION['uniqueStates']=0;

$_SESSION['state_format'] = [
    'state_f1' => 'Abbreviation',              // Abbreviation
    'state_f2' => 'Complete Name',              // Complete Name
    'state_f3' => 'Abbreviation + Name',     // Abbreviation + Name
    'state_f4' => 'Name + Abbreviation'      // Name + Abbreviation
];
$_SESSION['state_format_select']=[];



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

function formatStatesArray($format_key) {
    // Fetch the states data (from the internet or static array)
    $states = fetchStatesData();

    $formatted_states = [];

    // Check if the format key exists in $_SESSION['state_format']
    if (isset($format_key)) {
        $format = $format_key;

        foreach ($states as $abbreviation => $name) {
            switch ($format) {
                case 'Abbreviation': // Abbreviation only
                    $formatted_states[$abbreviation] = $abbreviation;
                    break;

                case 'Complete Name': // Full name only
                    $formatted_states[$abbreviation] = $name;
                    break;

                case 'Abbreviation + Name': // Abbreviation + Name
                    $formatted_states[$abbreviation] = "$abbreviation, $name";
                    break;

                case 'Name + Abbreviation': // Name + Abbreviation
                    $formatted_states[$abbreviation] = "$name, $abbreviation";
                    break;

                default:
                    // Default to full name if no match
                    $formatted_states[$abbreviation] = $name;
                    break;
            }
        }
    } else {
        // If format key is invalid, return the original array
        return $states;
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


//  get the states from the column
// if (!empty($_POST['csv_column']))
// {
//             // Extract unique states and their counts from the uploaded file
//             $stateIndex = array_search($_POST['csv_column'] , $_SESSION['headers']);
//             $uniqueStates = [];
//             $usa_state_only =fetchStatesData();
//             if ($stateIndex !== false) {
//                 $stateCounts = []; // Array to store state counts
//                 foreach ($_SESSION['csvData'] as $index => $row) {
//                     if ($index === 0) continue; // Skip the header row
//                     $state = $row[$stateIndex];
//                     if (!empty($state)) {
//                         $stateCounts[$state] = ($stateCounts[$state] ?? 0) + 1;
//                     }
//                 }

//                 // Combine state names and their counts
//                 foreach ($stateCounts as $state => $count) {
//                     $uniqueStates[] = $state . " (" . $count . ")";
//                 }

           
//                 // Custom sorting logic
//                 usort($uniqueStates, function ($a, $b) {
//                     // Extract the first character for comparison
//                     $firstCharA = strtoupper($a[0] ?? ''); // Handle empty strings
//                     $firstCharB = strtoupper($b[0] ?? '');

//                     // Place alphabetic characters first
//                     if (ctype_alpha($firstCharA) && !ctype_alpha($firstCharB)) return -1;
//                     if (!ctype_alpha($firstCharA) && ctype_alpha($firstCharB)) return 1;

//                     // Sort numbers/symbols next
//                     if (ctype_alnum($firstCharA) && !ctype_alnum($firstCharB)) return -1;
//                     if (!ctype_alnum($firstCharA) && ctype_alnum($firstCharB)) return 1;

//                     // Sort empty strings at the end
//                     if ($a === "") return 1;
//                     if ($b === "") return -1;

//                     // Default string comparison
//                     return strcasecmp($a, $b);
//                 });
//                 $_SESSION['csv_column'] = $_POST['csv_column'];
//                 $_SESSION['state_format_select']=$_POST['state_format_select'];
//                 $_SESSION['uniqueStates'] = $uniqueStates;
//                 $_SESSION['States_formats'] = formatStatesArray($_POST['state_format_select']);

//             }

// }




// if (!empty($_POST['csv_column'])) {
//     // Extract unique states and their counts from the uploaded file
//     $stateIndex = array_search($_POST['csv_column'], $_SESSION['headers']);
//     $uniqueStates = [];
//     $groupedStates = []; // To store states grouped alphabetically
//     $usa_state_only = fetchStatesData(); // Fetch the predefined states data

//     if ($stateIndex !== false) {
//         $stateCounts = []; // Array to store state counts
//         foreach ($_SESSION['csvData'] as $index => $row) {
//             if ($index === 0) continue; // Skip the header row
//             $state = trim($row[$stateIndex]); // Trim whitespace
//             if (!empty($state)) {
//                 $stateCounts[$state] = ($stateCounts[$state] ?? 0) + 1;
//             }
//         }

//         // Create a lookup array for valid USA states (case-insensitive)
//         $validStatesLookup = [];
//         foreach ($usa_state_only as $key => $value) {
//             $validStatesLookup[strtolower($key)] = true; // Abbreviation (e.g., "FL")
//             $validStatesLookup[strtolower($value)] = true; // Full name (e.g., "Florida")
//             $validStatesLookup[strtolower("$value, $key")] = true; // Combined format (e.g., "Florida, FL")
//             $validStatesLookup[strtolower("$key, $value")] = true; // Reverse combined format (e.g., "FL, Florida")
//         }

//         // Group states alphabetically or into "Symbols & Numbers"
//         foreach ($stateCounts as $state => $count) {
//             $stateName = strtolower(trim($state));
//             $firstChar = strtoupper($stateName[0] ?? '');

//             // Check if the state is valid
//             $isValidState = isset($validStatesLookup[$stateName]);

//             if ($isValidState && ctype_alpha($firstChar)) {
//                 // Add to alphabetical group
//                 $groupedStates[$firstChar][] = $state . " (" . $count . ")";
//             } else {
//                 // Add to "Symbols & Numbers" group
//                 $groupedStates['symbols'][] = $state . " (" . $count . ")";
//             }
//         }

//         // Sort each group alphabetically
//         foreach ($groupedStates as $group => &$states) {
//             sort($states, SORT_STRING | SORT_FLAG_CASE);
//         }
//         unset($states); // Break reference

//         // Ensure "Symbols & Numbers" group is at the end
//         if (isset($groupedStates['symbols'])) {
//             $symbolsGroup = $groupedStates['symbols'];
//             unset($groupedStates['symbols']); // Remove symbols group temporarily
//             ksort($groupedStates); // Sort remaining groups alphabetically
//             $groupedStates['symbols'] = $symbolsGroup; // Add symbols group back at the end
//         } else {
//             ksort($groupedStates); // Sort all groups alphabetically
//         }

//         // Store the selected column, format, and grouped states in session
//         $_SESSION['csv_column'] = $_POST['csv_column'];
//         $_SESSION['state_format_select'] = $_POST['state_format_select'];
//         $_SESSION['groupedStates'] = $groupedStates; // Store grouped states
//         $_SESSION['States_formats'] = formatStatesArray($_POST['state_format_select']);
//     }
// }


if (!empty($_POST['csv_column'])) {
    // Extract unique states and their counts from the uploaded file
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

        // Encode invalid states and their rows into a JSON array
        $_SESSION['invalidStatesJson'] = json_encode($invalidStatesRows);
    }
    // print_r($_SESSION['invalidStatesJson']); die;
}




// step 2 state mapping 

if (isset($_POST['state_mapping_submit'])) {
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
        $stateIndex = array_search('primary_address_state', $headers);

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
        } else {
            echo "Failed to open the bad data file for writing.<br>";
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
    } else {
        echo "File not found.";
    }
}






// Step 2: Process Mapped Data
if (isset($_POST['process_csv']) && !empty($_POST['field_mapping']) && !empty($_SESSION['uploaded_file'])) {
    $uploadedFilePath = $_SESSION['uploaded_file'];
    $fieldMapping = $_POST['field_mapping'];
    $phone_format = $_POST['phone_number_format'] ?? [];
    $_SESSION['field_mapping'] = $fieldMapping;
    $outputCsvPath = 'uploads/mapped_data.csv';

    if (($handle = fopen($uploadedFilePath, 'r')) !== false) {
        $headers = fgetcsv($handle);
        $outputHandle = fopen($outputCsvPath, 'w');
        fputcsv($outputHandle, array_values($fieldMapping));

        while (($row = fgetcsv($handle)) !== false) {
            $mappedRow = [];
            $number_arr = [];
            foreach ($headers as $index => $header) {
                $mappedHeader = $fieldMapping[$header] ?? '';
                $value = $row[$index] ?? '';
                if (strtolower($mappedHeader) === 'phone_number' && !empty($value)) {
                    $number_arr = processPhoneNumber($value , $phone_format);
                    $mappedRow[] = $number_arr['phone_number'];
                } 
                elseif (strtolower($mappedHeader) === 'phone_number_area_code' && !empty($number_arr['phone_number_area_code'])) {
                    $mappedRow[] = $number_arr['phone_number_area_code'];
                }
                elseif (strtolower($mappedHeader) === 'phone_number_country_code' && !empty($number_arr['phone_number_country_code'])) {
                    $mappedRow[] = $number_arr['phone_number_country_code'];
                }
                elseif (strtolower($mappedHeader) === 'phone_number_extension' && !empty($number_arr['phone_number_extension'])) {
                    $mappedRow[] = $number_arr['phone_number_extension'];
                } else {
                    $mappedRow[] = $value;
                }
            }
            fputcsv($outputHandle, $mappedRow);
        }

        fclose($handle);
        fclose($outputHandle);
        $_SESSION['output_csv'] = $outputCsvPath;
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
            <div id="collapseOne" class="accordion-collapse collapse <?php echo !isset($_SESSION['headers']) ? 'show' : ''; ?>" aria-labelledby="headingOne" data-bs-parent="#csvProcessorAccordion">
                <div class="accordion-body">
                    <form method="post" enctype="multipart/form-data" class="mb-4 text-center">
                        <div class="mb-3 d-flex justify-content-center">
                            <input type="file" name="csv_file" id="csv_file" class="form-control" style="width: 300px;" required>
                        </div>
                        <button type="submit" name="upload_csv" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>



<!-- Step 2: Map Fields -->
<?php if (isset($_SESSION['headers']) && isset($_SESSION['rows'])) : ?>
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingTwo">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                Step 2: Modify CSV file
            </button>
        </h2>
        <div id="collapseTwo" class="accordion-collapse collapse <?php echo isset($_SESSION['output_csv']) ? '' : 'show'; ?>" aria-labelledby="headingTwo" data-bs-parent="#csvProcessorAccordion">
            <div class="accordion-body">

                <!-- Parent Accordion for Mapping Forms -->
                <div class="accordion" id="mappingFormsAccordion">
                    
                    <!-- State Mapping Accordion Item -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="stateMappingHeading">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#stateMappingCollapse" aria-expanded="true" aria-controls="stateMappingCollapse">
                                State Mapping
                            </button>
                        </h2>
                        <div id="stateMappingCollapse" class="accordion-collapse collapse " aria-labelledby="stateMappingHeading" data-bs-parent="#mappingFormsAccordion">
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
?>


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
                                                    <option value="mark_bad_data">Mark Bad Data</option>
                                                    <option value="enter_manually">Enter Manually</option>
                                                    <?php foreach ($_SESSION['States_formats'] as $status): ?>
                                                        <option value="<?php echo htmlspecialchars($status); ?>">
                                                            <?php echo htmlspecialchars($status); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <!-- Eye Icon for Invalid States -->
                                                <i class="bi bi-eye ms-2 cursor-pointer toggle-table" 
                                                   data-state="<?php echo htmlspecialchars($state); ?>" 
                                                   style="font-size: 1.2rem;"></i>
                                            <?php else: ?>
                                                <!-- Dropdown for other groups -->
                                                <select class="form-select" name="state_mapping[<?php echo htmlspecialchars($state); ?>]" 
                                                        aria-label="Select Correct State">
                                                    <option value="">Select Correct State</option>
                                                    <?php foreach ($_SESSION['States_formats'] as $status): ?>
                                                        <option value="<?php echo htmlspecialchars($status); ?>">
                                                            <?php echo htmlspecialchars($status); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>

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
</script>

<!-- JavaScript for dynamic behavior -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Listen for changes in dropdowns
    document.querySelectorAll('.state-select').forEach(select => {
        select.addEventListener('change', function () {
            const selectedValue = this.value;
            const parentRow = this.closest('.state-mapping-row');

            if (selectedValue === 'enter_manually') {
                // Replace the dropdown with an input field
                const inputField = document.createElement('input');
                inputField.type = 'text';
                inputField.className = 'form-control';
                inputField.name = this.name; // Preserve the name attribute
                inputField.placeholder = 'Please enter a valid state';

                // Replace the dropdown with the input field
                parentRow.querySelector('.state-select').replaceWith(inputField);
            }
        });
    });
});
// document.addEventListener('DOMContentLoaded', function () {
//     // Listen for form submission
//     const saveMappingButton = document.querySelector('button[name="state_mapping_submit"]');
//     if (saveMappingButton) {
//         saveMappingButton.addEventListener('click', function (event) {
//             // Prevent form submission
//             event.preventDefault();

//             // Validate fields in the "Symbols & Numbers" tab
//             const symbolsTab = document.getElementById('collapsesymbols');
//             if (symbolsTab) {
//                 const symbolFields = symbolsTab.querySelectorAll('.state-mapping-row');
//                 let isValid = true;

//                 symbolFields.forEach(row => {
//                     const dropdown = row.querySelector('.state-select');
//                     const inputField = row.querySelector('input[type="text"]');

//                     // Check if the dropdown or input field is empty
//                     if ((dropdown && !dropdown.value) || (inputField && !inputField.value)) {
//                         isValid = false;
//                     }
//                 });

//                 if (!isValid) {
//                     // Show alert if any field is empty
//                     alert('Please fill the "Not a USA State" field to process.');
//                 } else {
//                     // If all fields are valid, submit the form programmatically
//                     saveMappingButton.form.submit();
//                 }
//             }
//         });
//     }
// });

</script>


                                


                            </div>
                        </div>
                    </div>

                        <!-- Phone Number  Accordion Item -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="phoneHeading">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#phoneCollapse" aria-expanded="true" aria-controls="phoneCollapse">
                                Phone Number Modification
                            </button>
                        </h2>

                        <div id="phoneCollapse" class="accordion-collapse collapse " aria-labelledby="phoneHeading" data-bs-parent="#mappingFormsAccordion">
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

                                            <!-- Phone Number  Accordion Item -->
            <div class="accordion-item">
                        <h2 class="accordion-header" id="addressHeading">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#addressCollapse" aria-expanded="true" aria-controls="addressCollapse">
                                Address Mapping
                            </button>
                        </h2>

                        <div id="addressCollapse" class="accordion-collapse collapse " aria-labelledby="addressHeading" data-bs-parent="#mappingFormsAccordion">
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


                    <!-- Field Mapping Accordion Item -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="fieldMappingHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fieldMappingCollapse" aria-expanded="false" aria-controls="fieldMappingCollapse">
                                Field Mapping
                            </button>
                        </h2>
                        <div id="fieldMappingCollapse" class="accordion-collapse collapse" aria-labelledby="fieldMappingHeading" data-bs-parent="#mappingFormsAccordion">
                            <div class="accordion-body">

                                <h2 class="form-header">Field Mapping</h2>
                                <form method="post" class="mb-4 text-center">
                                    <input type="hidden" name="uploaded_file" value="<?php echo htmlspecialchars($_SESSION['uploaded_file']); ?>">
                                    <div id="mappingFields" class="mb-4">
                                        <h6 class="mb-3">Field Mapping</h6>
                                        <?php foreach ($_SESSION['headers'] as $header) : ?>
                                            <div class="row mb-3">
                                                <div class="col-md-12 d-flex">
                                                    <select class="form-select me-2" name="field_mapping[<?php echo htmlspecialchars($header); ?>]">
                                                        <option value="">Select Field</option>
                                                        <?php 
                                                        foreach ($_SESSION['options'] as $option) : 
                                                            $selected = (strtolower($header) === strtolower($option)) ? 'selected' : '';
                                                        ?>
                                                            <option value="<?php echo htmlspecialchars($option); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($option); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($header); ?>">
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="submit" name="process_csv" class="btn btn-primary">Process</button>
                                </form>

                            </div>
                        </div>
                    </div>

                </div> <!-- End Parent Accordion -->

            </div>
        </div>
    </div>
<?php endif; ?>


        <!-- Step 3: Download Processed File -->
        <?php if (isset($_SESSION['output_csv'])) : ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                        Step 3: Download Processed File
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#csvProcessorAccordion">
                    <div class="accordion-body text-center">
                        <a href="<?php echo htmlspecialchars($_SESSION['output_csv']); ?>" class="btn btn-success mb-3" download>Download Mapped File</a>
                        <form method="post">
                            <button type="submit" name="reset" class="btn btn-danger">Restart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


