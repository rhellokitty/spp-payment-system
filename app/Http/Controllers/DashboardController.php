<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\ClassRoomDashboardResource;
use App\Interfaces\DashboardRepoositoriesInterface;
use Exception;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private DashboardRepoositoriesInterface $dashboardRepositories;

    public function __construct(DashboardRepoositoriesInterface $dashboardRepositories)
    {
        $this->dashboardRepositories = $dashboardRepositories;
    }

    public function getDashboardData()
    {
        try {
            $data = $this->dashboardRepositories->getDashboardData();

            $data['classes_breakdown'] = ClassRoomDashboardResource::collection(
                $data['classes_breakdown']
            );

            return ResponseHelper::jsonResponse(true, 'Data Dashboard Berhasil Diambil', $data, 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                false,
                'Data Dashboard Gagal Diambil',
                [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
                500
            );
        }
    }
}
