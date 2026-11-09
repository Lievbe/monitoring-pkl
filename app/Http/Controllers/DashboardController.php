<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $dashboard = match ($request->user()->role) {
            'admin_hubin' => 'admin.dashboard',
            'siswa_pkl' => 'siswa.dashboard',
            'guru_pembimbing' => 'guru.dashboard',
            'pembimbing_industri' => 'industri.dashboard',
            default => abort(403),
        };

        return redirect()->route($dashboard);
    }
}
