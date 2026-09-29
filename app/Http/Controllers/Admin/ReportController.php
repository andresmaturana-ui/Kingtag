<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Los reportes pendientes primero, después los ya revisados.
     */
    public function index(): View
    {
        return view('admin.reports', [
            'reports' => Report::query()
                ->with(['user', 'photo.graffiti.tag', 'comment.user'])
                ->orderByRaw('resolved_at is not null')
                ->latest('id')
                ->paginate(30),
        ]);
    }

    public function resolve(Report $report): RedirectResponse
    {
        $report->forceFill(['resolved_at' => $report->resolved_at ? null : now()])->save();

        return back();
    }
}
