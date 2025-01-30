<?php

namespace App\Repositories\License;

use App\Models\License\Applicant;
use App\Models\License\License;
use App\Models\License\LicenseType;
use App\Models\License\Species;
use App\Models\License\LicenseItem;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;

class ApplicantsDetailsRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return Applicant::class; // Ensuring correct model
    }

    /**
     * Get data for DataTables with optional search and sorting.
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @param bool $trashed
     *
     * @return Collection
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc', $trashed = false): Collection
    {
        // Get the authenticated user
        $user = auth()->user();

        // Ensure the authenticated user is a valid applicant
        if (!$user || !$user->applicant) {
            return collect(); // Return empty collection instead of aborting
        }

        // Initialize query builder for licenses linked to this applicant
        $query = License::query()->where('applicant_id', $user->applicant->id);

        // Include soft-deleted records if requested
        if ($trashed) {
            $query->withTrashed();
        }

        // Apply search filter
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(license_number) LIKE ?', [$search])
                    ->orWhereHas('licenseType', function ($query) use ($search) {
                        $query->whereRaw('LOWER(name) LIKE ?', [$search]);
                    });
            });
        }

        // Ensure ordering by valid columns
        $validOrderBy = ['id', 'license_number', 'created_at'];
        if (in_array($order_by, $validOrderBy)) {
            $query->orderBy($order_by, $sort);
        }

        // Return results as collection
        return $query->distinct()->get();
    }
}
