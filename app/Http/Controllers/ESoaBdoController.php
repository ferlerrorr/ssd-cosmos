<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

error_reporting(E_ERROR | E_PARSE);
class ESoaBdoController extends Controller
{
    public function store()
    {

        //! This Block of Handles Data Stream Import to array

        if ($_FILES["import_excel"]["name"] != '') {
            $allowed_extension = array('xls', 'csv', 'xlsx');
            $file_array = explode(".", $_FILES["import_excel"]["name"]);
            $file_extension = end($file_array);

            if (in_array($file_extension, $allowed_extension)) {
                $file_name = time() . '.' . $file_extension;
                move_uploaded_file($_FILES['import_excel']['tmp_name'], $file_name);
                $file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($file_name);
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file_type);

                $spreadsheet = $reader->load($file_name);

                unlink($file_name);

                $data = $spreadsheet->getActiveSheet()->toArray();

                // //! This Block of Handles Data Stream Import to array

                // array_shift($data); // Remove the first row

                $excel_header_value = $data[9];

                if (($excel_header_value[0]) != "BRANCH" && ($excel_header_value[1]) != "AMOUNT" && ($excel_header_value[2]) != "TRANSACTION DESCRIPTION" && ($excel_header_value[3]) != "DEPOSIT REFERENCE") {
                    // $message = '<div class="alert alert-danger">Invalid file: Must be a Rbank-SSDI SOA File.</div>';
                    $data = null;
                } else {

                    $rowsToRemove = [0, 1, 2, 9]; // Rows to be removed, indexes start from 0

                    foreach ($rowsToRemove as $indexToRemove) {
                        unset($data[$indexToRemove]);
                    }

                    $data = array_values($data); // Reset array keys


                    //Assign data to varaiable
                    $Transaction_date = $data[0][0];
                    $Transaction_date = str_replace("TRANSACTION DATE: ", "", $Transaction_date);
                    $Transaction_date_arr = [$Transaction_date]; // Remove string "TRANSACTION DATE: " Array -> String -> Array
                    $Requested_datetime = $data[1][0];
                    $Requested_datetime = str_replace("Requested Date and Time: ", "", $Requested_datetime);
                    $Requested_datetime_arr  = [$Requested_datetime]; // Remove string "Requested Date and Time: " Array -> String -> Array
                    $Printed_by = $data[2][0];
                    $Printed_by = str_replace("Printed By: ", "", $Printed_by);
                    $Printed_by_arr  = [$Printed_by]; // Remove string "Printed By: " Array -> String -> Array
                    $Currency = $data[3][0];
                    $Currency = str_replace("CURRENCY: ", "", $Currency);
                    $Currency_arr  = [$Currency]; // Remove string "CURRENCY: "
                    $Account_no = $data[4][0];
                    $Account_no = str_replace("ACCOUNT NO: ", "",  $Account_no);
                    $Account_no_arr  = [$Account_no]; // Remove string "ACCOUNT NO: " Array -> String -> Array
                    $Account_name = $data[5][0];
                    $Account_name = str_replace("ACCOUNT NAME: ", "",  $Account_name);
                    $Account_name_arr  = [$Account_name];   // Remove string "ACCOUNT NAME: " Array -> String -> Array
                    //Assign data to varaiable


                    $rowsToRemove = [0, 1, 2, 3, 4, 5]; // Rows to be removed, indexes start from 0

                    foreach ($rowsToRemove as $indexToRemove) {
                        unset($data[$indexToRemove]);
                    }

                    $data = array_values($data); // Reset array keys


                    foreach ($data as &$subArray) {
                        array_unshift($subArray, $Transaction_date_arr[0], $Requested_datetime_arr[0], $Printed_by_arr[0], $Currency_arr[0], $Account_no_arr[0], $Account_name_arr[0]);
                    } // Add all SOA Heading details in each of the item in the array



                    foreach ($data as &$row) {
                        foreach ($row as &$value) {
                            if ($value === "" || $value === " " || $value === "  " || $value === "   ") {
                                $value = null;
                            }
                        }
                    }  // Remove all "" , " " , "  " , "   " & Replace with Null Value

                    $data = array_values($data); // Reset array keys
                    // // //! This Block of Handles Database Query


                    // $query = "INSERT INTO esoa_bdo_ssdi
                    // (Transaction_date, Requested_datetime, Printed_by, Currency, Account_no, Account_name, Branch, Amount, Transaction_description, Deposit_reference) 
                    // VALUES (:Transaction_date, :Requested_datetime, :Printed_by, :Currency, :Account_no, :Account_name, :Branch, :Amount, :Transaction_description, :Deposit_reference)";

                    // $statement = $connect->prepare($query);
                    // $message = '';

                    // foreach ($data as &$row) {
                    //     $insert_data = [
                    //         ':Transaction_date' => $row[0],
                    //         ':Requested_datetime' => $row[1],
                    //         ':Printed_by' => $row[2],
                    //         ':Currency' => $row[3],
                    //         ':Account_no' => $row[4],
                    //         ':Account_name' => $row[5],
                    //         ':Branch' => $row[6],
                    //         ':Amount' => $row[7],
                    //         ':Transaction_description' => $row[8],
                    //         ':Deposit_reference' => $row[9]
                    //     ];
                    //     $statement->execute($insert_data);
                    // }

                    $query = "INSERT INTO esoa_bdo_ssdi
                    (Transaction_date, Requested_datetime, Printed_by, Currency, Account_no, Account_name, Branch, Amount, Transaction_description, Deposit_reference) 
                    VALUES (:Transaction_date, :Requested_datetime, :Printed_by, :Currency, :Account_no, :Account_name, :Branch, :Amount, :Transaction_description, :Deposit_reference)";


                    foreach ($data as &$row) {
                        DB::statement($query, [
                            ':Transaction_date' => $row[0],
                            ':Requested_datetime' => $row[1],
                            ':Printed_by' => $row[2],
                            ':Currency' => $row[3],
                            ':Account_no' => $row[4],
                            ':Account_name' => $row[5],
                            ':Branch' => $row[6],
                            ':Amount' => $row[7],
                            ':Transaction_description' => $row[8],
                            ':Deposit_reference' => $row[9]
                        ]);
                    }

                    // //! This Block of Handles Database Query

                    return response()->json(
                        $data,
                        200
                    );
                }
            }
        }
    }
}
