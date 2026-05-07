<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\InvestorApplication;
use App\Models\User;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'pending');

        $query = InvestorApplication::with('user.documents')->orderBy('created_at', 'desc');

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $applications = $query->paginate(15);

        $counts = [
            'all' => InvestorApplication::count(),
            'pending' => InvestorApplication::where('status', 'pending')->count(),
            'approved' => InvestorApplication::where('status', 'approved')->count(),
            'rejected' => InvestorApplication::where('status', 'rejected')->count(),
        ];

        return view('superadmin.investors.index', compact('applications', 'counts', 'filter'));
    }

    public function approve(InvestorApplication $application)
    {
        $application->update(['status' => 'approved']);
        
        // Marcar al usuario como inversor
        $application->user->update(['is_investor' => true]);

        return back()->with('success', 'Solicitud aprobada con éxito. El usuario ahora es un inversor.');
    }

    public function reject(Request $request, InvestorApplication $application)
    {
        $application->update([
            'status' => 'rejected',
            'admin_notes' => $request->reason
        ]);

        return back()->with('success', 'Solicitud rechazada.');
    }
}
