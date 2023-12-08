<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Log;
use App\Models\Tasks;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class TasksImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = new Tasks([
                "user_id" => $row["user_id"],
                "title" => $row["title"],
                "description" => $row["description"],
                "completed" => !$row["completed"]?false:true,
            ]);
            $data->save();
         
        }
    }
}