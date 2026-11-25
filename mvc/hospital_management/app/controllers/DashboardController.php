
<?php

class DashboardController extends Controller
{
    // Admin dashboard
    public function admin()
    {
        Auth::requireRole('admin');

        $this->showDashboard('Admin Dashboard');
    }
   
    // Doctor dashboard
    
        public function doctor()
        {
            Auth::requireRole('doctor');

            $user = Auth::user();

            $appointmentModel = new Appointment();

            // Find the logged-in doctor's profile
            $doctorId = $appointmentModel->getDoctorIdByUserId(
                $user['id']
            );

            // Get appointments belonging to this doctor
            $appointments = [];

            if ($doctorId) {
                $appointments = $appointmentModel->getDoctorAppointments(
                    $doctorId
                );
            }

            $this->view('dashboard/doctor', [
                'title' => 'Doctor Dashboard',
                'user' => $user,
                'doctorId' => $doctorId,
                'appointments' => $appointments,
                'doctorProfileFound' => (bool)$doctorId,
                'csrfToken' => Auth::csrfToken()
            ]);
        }


    // Receptionist dashboard
    public function receptionist()
    {
        Auth::requireRole('receptionist');

        $this->showDashboard('Receptionist Dashboard');
    }

    // Nurse dashboard
    public function nurse()
    {
        Auth::requireRole('nurse');

        $this->showDashboard('Nurse Dashboard');
    }

    // Patient dashboard

public function patient()
{
    Auth::requireRole('patient');

    $user = Auth::user();

    $doctorModel = new Doctor();
    $appointmentModel = new Appointment();

    // Get active doctors
    $doctors = $doctorModel->getActiveDoctors();

    // Find the patient record linked to the logged-in user
    $patientId = $appointmentModel->getPatientIdByUserId($user['id']);

    // Get appointments belonging to this patient
    $appointments = [];

    if ($patientId) {
        $appointments = $appointmentModel->getPatientAppointments($patientId);
    }

    $this->view('dashboard/patient', [
        'title' => 'Patient Dashboard',
        'user' => $user,
        'doctors' => $doctors,
        'appointments' => $appointments,
        'csrfToken' => Auth::csrfToken()
    ]);
}

        
    // Common dashboard view
    private function showDashboard($title, $extraData = [])
    {
        $data = array_merge([
            'title' => $title,
            'user' => Auth::user(),
            'csrfToken' => Auth::csrfToken(),
            'appointments' => [],
            'doctorProfileFound' => true
        ], $extraData);

        $this->view('dashboard/index', $data);
    }

}
