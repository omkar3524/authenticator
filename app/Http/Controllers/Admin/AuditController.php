<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginAudit;
use Inertia\Inertia;

class AuditController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Audits/Index', [
            'audits' => LoginAudit::with('user')->latest()->paginate(50)
        ]);
    }
}
