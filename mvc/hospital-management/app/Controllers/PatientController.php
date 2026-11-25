<?php
require_once "../app/Models/Patient.php";

class PatientController extends Controller 
{
    /** @var Patient */
    private $patient;

    public function __construct($db)
    {
        $this->patient=new Patient($db);
    }

    //Display patient
    public function index()
    {
        $patients= $this->patient->getAllPatients();
        $this->view('patients/index',[
            'patients'=> $patients
        ]);
    }

    //display registration form
    public function create()
    {
        $this->view('patients/create');
    }
    //save patients
    public function Store()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $data =[
                    'patient_name' => trim($_POST['patient_name']?? ' '),
                    'gender' =>$_POST['gender']?? '',
                    'date_of_birth'=>$_POST['date_of_birth']?? '',
                    'phone'=>trim($_POST['phone']?? ''),
                    'email'=>trim($_POST['email']?? ''),
                    'address'=>trim($_POST['address']?? '')
                ];
            

        if($data['patient_name'] == '')
            {
                die("Patient name is required");
            }

        $this->patient->addPatients($data);

        header("Location: " . APP_BASE_URL . "/patients");
        exit;
        }
    }
}
