
<?php

class DashboardController extends Controller
{
    private $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new Dashboard();
    }

    public function index()
    {
        // Check whether the user is logged in
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        // Get dashboard information
        $this->view('dashboard/index', [
            'user' => Auth::user(),
            'totalStudents' => $this->dashboardModel->getTotalStudents(),
            'totalTeachers' => $this->dashboardModel->getTotalTeachers(),
            'todayPresent' => $this->dashboardModel->getTodayPresent(),
            'todayAbsent' => $this->dashboardModel->getTodayAbsent(),
            'recentAttendance' => $this->dashboardModel->getRecentAttendance()
        ]);
    }
}
