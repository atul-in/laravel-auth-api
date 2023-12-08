<?php

namespace App\Exports;

use App\Models\Tasks;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TasksExport implements FromCollection, WithHeadings
{
    protected $userId;
    protected $isAdmin;

    public function __construct($userId, $isAdmin)
    {
        $this->userId = $userId;
        $this->isAdmin = $isAdmin;

    }

    public function collection()
    {
        try {
            if ($this->isAdmin){
                return Tasks::select('id', 'user_id', 'title', 'description', 'completed', 'created_at', 'updated_at')->get();
            } else{
                return Tasks::where('user_id', $this->userId)->select('id', 'user_id', 'title', 'description', 'completed', 'created_at', 'updated_at')->get();
            }
        } catch (\Exception $e) {
            Log::error('Error exporting tasks collection: ' . $e->getMessage());
            return new Collection();
        }
    }

    public function headings(): array
    {
        return [
            'id',
            'user_Id',
            'title',
            'description',
            'completed',
            'created_at',
            'updated_at'
        ];
    }
}
