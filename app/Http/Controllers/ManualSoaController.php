<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

error_reporting(E_ERROR | E_PARSE);
class ManualSoaController extends Controller
{
    public function store()
    {
        $data = null;
        if ($_FILES["import_excel"]["name"] != '') {
            $allowed_extension = array('xls', 'csv', 'xlsx');
            $file_array = explode(".", $_FILES["import_excel"]["name"]);
            $file_extension = end($file_array);

            //! This Block of Handles Data Stream Validtation  
            if (!in_array($file_extension, $allowed_extension)) {
                $data = [["Invalid file: Must be a Manual SOA File."]];
                return response()->json(
                    $data,
                    403
                );
            }
            //! This Block of Handles Data Stream Validtation 

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
                    $data = null;
                    $data = [["Invalid file: Must be a Manual SOA File."]];
                    return response()->json(
                        $data,
                        403
                    );
                    //! Validation
                } else {

                    array_shift($data); //? Remove the first row


                    foreach ($data as &$row) {
                        foreach ($row as &$value) {
                            if ($value === "" || $value === " " || $value === "  " || $value === "   ") {
                                $value = null;
                            }
                        }
                    }  //? Remove all "" , " " , "  " , "   " & Replace with Null Value

                    $data = array_values($data); //? Reset array keys


                    // ! This Block of Handles Database Query

                    $query = "
                          INSERT INTO manual_soa
                          (Date_of_transaction, Debit, Credit, Status_field, Balance) 
                          VALUES (:Date_of_transaction, :Debit, :Credit, :Status_field, :Balance)
                       ";


                    foreach ($data as &$row) {
                        DB::statement($query, [
                            ':Date_of_transaction' => $row[0],
                            ':Debit' => $row[1],
                            ':Credit' => $row[2],
                            ':Status_field' => $row[3],
                            ':Balance' => $row[4]
                        ]);
                    }

                    // ! This Block of Handles Database Query


                    return response()->json(
                        $data,
                        200
                    );
                }
            }
        }
    }
}
