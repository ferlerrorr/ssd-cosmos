<?php

namespace App\Http\Controllers;

error_reporting(E_ERROR | E_PARSE);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ESoaRbankController extends Controller
{
    public function store()
    { //! This Block of Handles Data Stream Import to array

        $data = null;

        if ($_FILES["import_excel"]["name"] != '') {
            $allowed_extension = array('xls', 'csv', 'xlsx');
            $file_array = explode(".", $_FILES["import_excel"]["name"]);
            $file_extension = end($file_array);

            //! This Block of Handles Data Stream Validtation  
            if (!in_array($file_extension, $allowed_extension)) {
                $data = [["Invalid file: Must be a Rbank-SSDI SOA File."]];
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

                array_shift($data); //? Remove the first row

                $excel_header_value = $data[6];

                if (($excel_header_value[0]) != "Transaction Date" && ($excel_header_value[1]) != "Transaction Type" && ($excel_header_value[2]) != "Store Code" && ($excel_header_value[3]) != "Check Number"  && ($excel_header_value[4]) != "Withdrawal"  && ($excel_header_value[5]) != "Deposit") {

                    $data = null;
                } else {

                    $rowsToRemove = [1, 5, 6]; //? Rows to be removed, indexes start from 0

                    foreach ($rowsToRemove as $indexToRemove) {
                        unset($data[$indexToRemove]);
                    }

                    $data = array_values($data); //? Reset array keys

                    foreach ($data as &$subArray) {
                        array_splice($subArray, 6, 1); //? Remove the 7th item (index starts from 0)
                    }

                    $data = array_values($data); //? Re assign and reset indexes

                    //? Assign data to varaiable
                    $statement_period = $data[0][1];
                    $client_name = $data[1][1];
                    $client_description = $data[2][1];
                    $account_number = $data[3][1];
                    //? Assign data to varaiable


                    $rowsToRemove2 = [0, 1, 2, 3]; //? Rows to be removed, indexes start from 0

                    foreach ($rowsToRemove2 as $indexToRemove2) {
                        unset($data[$indexToRemove2]);
                    }

                    $data = array_values($data); //? Re assign and reset indexes


                    foreach ($data as &$subArray) {
                        array_unshift($subArray, $statement_period, $client_name, $client_description, $account_number);
                    }  //? Add all SOA Heading details in each of the item in the array


                    foreach ($data as &$row) {
                        foreach ($row as &$value) {
                            if ($value === "" || $value === " " || $value === "  " || $value === "   ") {
                                $value = null;
                            }
                        }
                    }  //? Remove all "" , " " , "  " , "   " & Replace with Null Value


                    $data = array_slice($data, 0, -3); //? Remove the last three entries from $data array

                    $data = array_values($data); //? Re assign and reset indexes


                    //! This Block of Handles Database Query
                    $query = "
                        INSERT INTO esoa_rbank_ssdi
                        (Statement_period,Client_name,Client_description,Account_number,Transaction_date,Transaction_type,Store_code,Check_number,Withdrawal,Deposit,Remarks) 
                        VALUES (:Statement_period,:Client_name,:Client_description,:Account_number,:Transaction_date,:Transaction_type,:Store_code,:Check_number,:Withdrawal,:Deposit,:Remarks)
                     ";


                    foreach ($data as &$row) {
                        DB::statement($query, [
                            ':Statement_period' => $row[0],
                            ':Client_name' => $row[1],
                            ':Client_description' => $row[2],
                            ':Account_number' => $row[3],
                            ':Transaction_date' => $row[4],
                            ':Transaction_type' => $row[5],
                            ':Store_code' => $row[6],
                            ':Check_number' => $row[7],
                            ':Withdrawal' => $row[8],
                            ':Deposit' => $row[9],
                            ':Remarks' => $row[10],
                        ]);
                    }

                    //! This Block of Handles Database Query

                    return response()->json(
                        $data,
                        200
                    );
                }
            }
        }
    }
}
