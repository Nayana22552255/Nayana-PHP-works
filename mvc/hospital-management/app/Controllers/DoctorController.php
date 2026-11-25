<?php

require_once __DIR__. "/../Models/Doctor.php";

class DoctorController extends Controller 
{
    private $doctor;

    public function __construct($db)
    {
        $this->doctor=new Doctor($db);
    }

    public function index()
    {
        $doctors=$this->doctor->getAllDoctors();

        $this->view('doctors/index',['doctors'=>$doctors]);
    }

    public function create()
    {
        $departments=$this->doctor->getDepartments();
        $this->view('doctors/create',[
            'departments'=>$departments,
            'errors'=>[],
            'data'=>[]
        ]);
    }

    public function store()
    {
        if($_SERVER['REQUEST_METHOD']!== 'POST')
            {
                http_response_code(405);
                exit('Method Not Allowed');
            }

            $data=[
                'doctor_name'=>trim($_POST['doctor_name']?? ''),
                'specialization'=>trim($_POST['specialization']?? ''),
                'phone'=>trim($_POST['phone']?? ''),
                'email'=>trim($_POST['email']?? ''),
                'department_id'=> $_POST['department_id']?? ''
            ];

        $errors=$this->validateDoctor($data);

        if(!empty($errors))
            {
                $departments=$this->doctor->getDepartments();
                $this->view('doctors/create',[
                    'departments'=>$departments,
                    'errors'=>$errors,
                    'data'=>$data
                ]);

                return;
            }

            $this->doctor->addDoctors($data);
            header("Location: /doctors");
            exit;
    }

    public function show()
    {
        $id=(int)($_GET['id']?? 0);
        $doctor= $this->doctor->getDoctorById($id);

        if(!$doctor)
            {
                http_response_code(404);
                exit("Doctor Not Found");
            }

        $this->view('doctors/show',[
            'doctor'=>$doctor
        ]);
    }

    public function edit()
    {
        $id=(int)($_GET['id']?? 0);
        $doctor=$this->doctor->getDoctorById($id);

        if(!$doctor)
            {
                http_response_code(404);
                exit("Doctor Not Found");
            }

        $departments=$this->doctor->getDepartments();
        $this->view('doctors/edit',[
            'doctor'=>$doctor,
            'departments'=>$departments,
            'errors'=>[]
        ]);
    }

    public function update()
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST')
            {
                http_response_code(405);
                exit("Method Not Allowed");
            }
        $id=(int)($_GET['id']?? 0);

        $doctor=$this->doctor->getDoctorById($id);

        if(!$doctor)
            {
                http_response_code(404);
                exit("Doctor Not Found");
            }
        $data=[
            'doctor_name'=>trim($_POST['doctor_name']?? ''),
            'specialization'=>trim($_POST['specialization']?? ''),
            'phone'=>trim($_POST['phone']?? ''),
            'email'=>trim($_POST['email']?? ''),
            'department_id'=>$_POST['department_id']?? ''
        ];

        $errors=$this->validateDoctor($data);
        if(!empty($errors))
            {
                $departments=$this->doctor->getDepartments();

                $this->view('doctors/edit',[
                    'doctor'=>array_merge($doctor,$data),
                    'departments'=>$departments,
                    'errors'=>$errors
                ]);
                return;
            }

            $this->doctor->updateDoctor($id,$data);

            header("Location: /doctors");
            exit;
    }


    public function delete()
    {
        if($_SERVER['REQUEST_METHOD']!== 'POST')
            {
                http_response_code(405);
                exit('Method Not Allowed');
            }

        $id =(int)($_GET['id']?? 0);
        try {
            $this->doctor->deleteDoctor($id);
            header("Location: /doctors");
            exit;
        }catch(PDOException $e)
        {
            http_response_code(409);
            echo "This doctor cannot be deleted because related records may exist.";
        }
    }
    public function search()
    {
        $keyword =trim($_GET['keyword']?? '');
        $doctors=$this->doctor->searchDoctors($keyword);
        $this->view('doctors/index',[
            'doctors'=>$doctors,
            'keyword'=>$keyword
        ]);
    }
    private function validateDoctor($data)
    {
        $errors=[];
        if($data['doctor_name'] === '')
            {
                $errors['doctor_name']="Doctor name is required.";
            }
        if($data['specialization'] === '')
            {
                $errors['specialization']="Specialization is required.";
            }
        elseif (strlen($data['specialization']) > 100) {
            $errors['specialization'] = "Specialization must be 100 characters or fewer.";
        }
        if ($data['phone'] !== '' &&
            !preg_match('/^[0-9+\-\s()]{7,20}$/', $data['phone'])) {
            $errors['phone'] = "Enter a valid phone number.";
        }
         if ($data['email'] !== '' &&
            !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Enter a valid email address.";
        }
          if (!ctype_digit((string)$data['department_id']) ||
            (int)$data['department_id'] <= 0) {
            $errors['department_id'] = "Select a department.";
        }
        return $errors;

    }

}
?>