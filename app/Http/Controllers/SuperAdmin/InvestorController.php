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
        $search = $request->get('search');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $query = InvestorApplication::with('user.documents')->orderBy('created_at', 'desc');

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%")
                    ->orWhere('ruc', 'like', "%{$search}%");
            });
        }

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $applications = $query->paginate(10)->withQueryString();

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

        return back()->with('success', 'Interesado aprobado con éxito. El usuario ahora es un inversor.');
    }

    public function unapprove(InvestorApplication $application)
    {
        $application->update(['status' => 'pending']);

        // Quitar marca de inversor al usuario
        $application->user->update(['is_investor' => false]);

        return back()->with('success', 'Solicitud revertida. El usuario ya no figura como interesado aprobado.');
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
