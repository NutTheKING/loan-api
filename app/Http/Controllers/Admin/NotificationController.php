<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // In this scaffold we'll just render a simple page.
        $notifications = [];
        return view('admin.notifications.index', compact('notifications'));
    }
}
