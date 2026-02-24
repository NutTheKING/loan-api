<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\AdminRepository;

class LoginController extends Controller
{
    protected $adminRepo;

    public function __construct(AdminRepository $adminRepo)
    {
        $this->adminRepo = $adminRepo;
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string', // user_name or email
            'password' => 'required|string',
        ]);

        $identifier = $request->input('identifier');
        $password = $request->input('password');

        $admin = $this->adminRepo->findByUserName($identifier) ?? $this->adminRepo->findByEmail($identifier);

        if (!$admin || !Hash::check($password, $admin->password)) {
            return back()->withErrors(['identifier' => 'Invalid credentials'])->withInput();
        }

        if (!$admin->is_active) {
            return back()->withErrors(['identifier' => 'Account deactivated'])->withInput();
        }

        // store minimal admin info in session
        session(['admin_id' => $admin->id, 'admin_name' => $admin->name]);

        return redirect()->intended('/backend/dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_id', 'admin_name']);
        return redirect('/backend/login');
    }

    public function dashboard(Request $request)
    {
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect('/backend/login');
        }
        $admin = $this->adminRepo->find($adminId);
        return view('admin.dashboard', ['admin' => $admin]);
    }
}
