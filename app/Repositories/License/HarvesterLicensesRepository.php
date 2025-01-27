<?php

namespace App\Repositories\License;

use App\Models\License\HarvesterLicense;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;
use DB;

class HarvesterLicensesRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return HarvesterLicense::class;
    }

    /**
     * Get data for DataTable with optional search and sorting.
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @param bool $trashed
     * @return Collection
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc', $trashed = false): Collection
    {
        $query = $this->getModelInstance()
            ->with(['applicant', 'island', 'groupMembers'])
            ->select('harvester_licenses.*')
            // Add these lines to select and concatenate names
            ->selectRaw("CONCAT(harvester_applicants.first_name, ' ', harvester_applicants.last_name) as applicant_name")
            ->selectRaw("islands.name as island_name")
            ->selectRaw("harvester_licenses.status as license_status")  // Add status field to the query
            ->join('harvester_applicants', 'harvester_licenses.harvester_applicant_id', '=', 'harvester_applicants.id')
            ->join('islands', 'harvester_licenses.island_id', '=', 'islands.id');
    
        if ($trashed) {
            $query->withTrashed();
        }
    
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function($q) use ($search) {
                $q->whereHas('applicant', function($q) use ($search) {
                    $q->where(DB::raw('LOWER(first_name)'), 'LIKE', $search)
                      ->orWhere(DB::raw('LOWER(last_name)'), 'LIKE', $search);
                })
                ->orWhere(DB::raw('LOWER(payment_receipt_no)'), 'LIKE', $search)
                ->orWhereHas('island', function($q) use ($search) {
                    $q->where(DB::raw('LOWER(name)'), 'LIKE', $search);
                });
            });
        }
    
        return $query->orderBy($order_by, $sort)->get();
    }
    

    /**
     * Save group members for a license.
     *
     * @param int $licenseId
     * @param array $members
     * @return void
     */
    public function saveGroupMembers(int $licenseId, array $members): void
    {
        foreach ($members as $member) {
            DB::table('group_members')->insert([
                'harvester_license_id' => $licenseId,
                'name' => $member['name'],
                'national_id' => $member['national_id'],
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Update group members for a license.
     *
     * @param int $licenseId
     * @param array $members
     * @return void
     */
    public function updateGroupMembers(int $licenseId, array $members): void
    {
        // Delete existing group members
        DB::table('group_members')
            ->where('harvester_license_id', $licenseId)
            ->delete();

        // Add new group members
        $this->saveGroupMembers($licenseId, $members);
    }

    /**
     * Get license with related data.
     *
     * @param mixed $id
     * @param array $columns
     * @return Model|null
     */
    public function getById($id, array $columns = ['*']): ?Model
    {
        return $this->getModelInstance()
            ->with(['applicant', 'island', 'groupMembers'])
            ->find($id, $columns);
    }

    /**
     * Create a new license with optional group members.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        $license = parent::create($data);

        if (isset($data['group_members'])) {
            $this->saveGroupMembers($license->id, $data['group_members']);
        }

        return $license;
    }

    /**
     * Update a license with optional group members.
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $license = parent::update($id, $data);

        if (isset($data['group_members'])) {
            $this->updateGroupMembers($license->id, $data['group_members']);
        }

        return $license;
    }

    /**
     * Get a list of harvester licenses for dropdown.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck()
{
    return $this->getModelInstance()
        ->select('id', 'first_name', 'last_name', 'is_group', 'group_size')
        ->get()
        ->map(function($applicant) {
            return [
                'id' => $applicant->id,
                'name' => $applicant->first_name . ' ' . $applicant->last_name,
                'is_group' => $applicant->is_group,
                'group_size' => $applicant->group_size
            ];
        });
}

}