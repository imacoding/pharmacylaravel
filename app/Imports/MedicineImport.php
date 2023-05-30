<?php

namespace App\Imports;

use App\Models\Medicine;

use Maatwebsite\Excel\Concerns\ToModel;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;

class MedicineImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $aAllMedcines = Medicine::select ('item_name')->get ()->toArray ();
		$available_medicines = array_column ($aAllMedcines , 'item_name');
		$availableMed = array_map ('trim' , $available_medicines);
        $itemName = ((isset($row['item_name']) && !empty($row['item_name'])) ? trim ($row['item_name']) : '');
        
        if ($itemName || !in_array ($itemName , $availableMed))
        {
            return new Medicine([
                'item_code' => isset($row['item_code']) ? $row['item_code'] : NULL,
                'item_name' => $itemName,
                'batch_no' => isset($row['batch_no']) ? $row['batch_no'] : NULL,
                'quantity' => isset($row['quantity']) ? $row['quantity'] : 0,
                'cost_price' => isset($row['cost_price']) ? $row['cost_price'] : NULL,
                'purchase_price' => isset($row['purchase_price']) ? $row['purchase_price'] : 0.00,
                'rack_number' => isset($row['rack']) ? $row['rack'] : NULL,
                'selling_price' => isset($row['selling_price']) ? $row['selling_price'] : 0.00,
                'expiry' => date ('Y-m-d' , strtotime (isset($row['expiry']) ? $row['expiry'] : date('Y-m-d', strtotime('+2 months')))),
                'tax' => isset($row['tax']) ? $row['tax'] : 0.00,
                'composition' => isset($row['composition']) ? $row['composition'] : NULL,
                'discount' => isset($row['discount']) ? $row['discount'] : 0.00,
                'manufacturer' => isset($row['manufacturer']) ? $row['manufacturer'] : NULL,
                'marketed_by' => isset($row['marketed_by']) ? $row['marketed_by'] : NULL,
                'group' => isset($row['group']) ? $row['group'] : NULL,
                'created_at' => isset($row['created_at']) ? $row['created_at'] : date ('Y-m-d H:i:s'),
                'created_by' => Auth::user()->id,
            ]);
            
            $availableMed[] = $itemName;
        }
    }
}
