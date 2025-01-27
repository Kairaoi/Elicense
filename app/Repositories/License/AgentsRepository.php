<?php

namespace App\Repositories\License;

use App\Models\License\Agent;
use App\Repositories\CustomBaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use DB;

class AgentsRepository extends CustomBaseRepository
{
    public function model(): string
    {
        return Agent::class;
    }

    /**
     * Create a new Agent.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->getModelInstance()->create($data);
    }

    /**
     * Update an existing Agent.
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $model = $this->getModelInstance()->findOrFail($id);
        $model->update($data);
        return $model;
    }

    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc', $trashed = false): Collection
{
    $query = $this->getModelInstance()->newQuery();

    if ($trashed) {
        $query->withTrashed();
    }

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $searchLower = strtolower($search);
            $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchLower . '%']) // Replace with valid column names
              ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(phone_number) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(status) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(notes) LIKE ?', ['%' . $searchLower . '%']);
        });
    }

    return $query->orderBy($order_by, $sort)
                 ->with(['applicant', 'creator:id,name', 'updater:id,name']) // Replace with actual relationships
                 ->distinct()
                 ->get();
}

    public function pluck(): \Illuminate\Support\Collection
    {
        return DB::table('agents')
            ->select(
                'id',
                DB::raw("CONCAT(first_name, ' ', last_name, ' - ', email) AS display_text")
            )
            ->pluck('display_text', 'id');
    }
}
