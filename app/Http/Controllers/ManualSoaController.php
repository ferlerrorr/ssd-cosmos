<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

error_reporting(E_ERROR | E_PARSE);
class ManualSoaController extends Controller
{
    public function store()
    {

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

                //! This Block of Handles Data Stream Import to array

                //! Validation

                $excel_header_value = $data[0];

                if (($excel_header_value[0]) != "Date" && ($excel_header_value[1]) != "Debit" && ($excel_header_value[2]) != "Credit" && ($excel_header_value[3]) != "Status") {
                    // $message = '<div class="alert alert-danger">Invalid file: Must be a Rbank-SSDI SOA File.</div>';
                    $data = null;
                    //! Validation
                } else {

                    array_shift($data); // Remove the first row


                    foreach ($data as &$row) {
                        foreach ($row as &$value) {
                            if ($value === "" || $value === " " || $value === "  " || $value === "   ") {
                                $value = null;
                            }
                        }
                    }  // Remove all "" , " " , "  " , "   " & Replace with Null Value

                    $data = array_values($data); // Reset array keys


                    //! This Block of Handles Database Query

                    //                 $query = "
                    //       INSERT INTO manual_soa
                    //       (Date_of_transaction, Debit, Credit, Status_field, Balance) 
                    //       VALUES (:Date_of_transaction, :Debit, :Credit, :Status_field, :Balance)
                    //    ";

                    //                 $statement = $connect->prepare($query);
                    //                 $message = '';

                    //                 foreach ($data as &$row) {
                    //                     $insert_data = array(
                    //                         ':Date_of_transaction' => $row[0],
                    //                         ':Debit' => $row[1],
                    //                         ':Credit' => $row[2],
                    //                         ':Status_field' => $row[3],
                    //                         ':Balance' => $row[4]
                    //                     );
                    //                     $statement->execute($insert_data);
                    //                 }

                    //! This Block of Handles Database Query


                    //     $message = '<div class="alert alert-success">Data Imported Successfully</div>';
                    // }

                    // if (empty($data)) {
                    //     $message = '<div class="alert alert-danger">Invalid file: Must be a Rbank-SSDI SOA File.</div>';
                    // } elseif ($message === '') {
                    //     $message = '<div class="alert alert-danger">Only .xls, .csv, or .xlsx files are allowed</div>';
                }

                // echo $message;
                return response()->json(
                    $data,
                    200
                );
            }
        }
    }
}
