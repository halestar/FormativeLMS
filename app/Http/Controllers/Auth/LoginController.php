<?php

namespace App\Http\Controllers\Auth;

use App\Classes\Settings\AuthSettings;
use App\Http\Controllers\Controller;
use App\Models\Integrations\Connections\AuthConnection;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public static function middleware()
    {
        return
            [
                new Middleware('guest', except: ['logout', 'impersonate', 'unimpersonate', 'viewChild']),
                new Middleware('auth', only: ['logout', 'impersonate', 'unimpersonate', 'viewChild']),
            ];
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        Auth::guard()
            ->logout();
        $request->session()
            ->invalidate();
        $request->session()
            ->regenerateToken();

        return redirect('/');
    }

    public function impersonate(Person $person)
    {
        session(['impersonating_from' => redirect()
            ->back()
            ->getTargetUrl()]);
        auth()
            ->user()
            ->impersonate($person);

        return redirect(route('home'));
    }

    public function unimpersonate()
    {
        $manager = app('impersonate');
        if ($manager->isImpersonating()) {
            auth()
                ->user()
                ->leaveImpersonation();
            $url = session()->pull('impersonating_from', route('home'));

            return redirect($url);
        }

        return redirect(route('home'));
    }

    public function viewChild(StudentRecord $student)
    {
        $user = auth()->user();
        if ($user->isParent() && $user->isParentOfPerson($student->person)) {
            $user->student_id = $student->id;
            $user->save();
        }

        return redirect(route('home'));
    }

	public function linkLogin(Request $request, AuthSettings $authSettings)
	{
		$person = Person::where('school_id', $request->person)->first();
		if($person)
			return redirect(AuthConnection::completeLogin($person));
		abort(404);
	}
}
