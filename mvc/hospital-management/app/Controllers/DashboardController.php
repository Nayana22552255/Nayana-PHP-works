<?php

require_once __DIR__ . '/../Models/Dashboard.php';

class DashboardController extends Controller
{
    private $dashboard;

    public function __construct($db)
    {
        $this->dashboard = new Dashboard($db);
    }

    public function index()
    {
        $this->view('dashboard/index', [
            'summary' => $this->dashboard->getSummary(),
            'appointments' => $this->dashboard->getUpcomingAppointments()
        ]);
    }
}
