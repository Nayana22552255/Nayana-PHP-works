
<?php

class HomeController extends Controller
{
    public function index()
    {
        // Redirect logged-in users to their role dashboard
        if (Auth::user()) {

            $role = Auth::role();

            switch ($role) {

                case 'admin':
                    header('Location: /admin/dashboard');
                    exit;

                case 'doctor':
                    header('Location: /doctor/dashboard');
                    exit;

                case 'receptionist':
                    header('Location: /receptionist/dashboard');
                    exit;

                case 'nurse':
                    header('Location: /nurse/dashboard');
                    exit;
            }
        }

        // Display public home page
        $this->view('home/index', [
            'title' => 'Welcome to Our Hospital'
        ]);
    }
}
