<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Welcome | Hospital Management System</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            overscroll-behavior: none;

        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f8fc;
            color: #263b5e;
            overflow-x: hidden;

        }

        /* Navigation */
        .navbar {
            background: white;
            padding: 18px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .logo {
            display:flex;
            align-items:center;
            gap:12px;
            color: #2563eb;
            font-size: 23px;
            font-weight: 700;
        }
        .medical-logo {
            color: #2563eb;
            flex-shrink:0;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            position: relative;
            color: #172b4d;
            text-decoration: none;
            padding: 8px 0;
            font-weight: 600;
            transition:color 0.3s ease;
        }
        .nav-links a::after {
            content: "";
            position: absolute;
            left: 0;
            bottom:0;
            width:0;
            height: 3px;
            background-color:#2563eb;
            border-radius:5px;
            transition:width 0.3s ease;
        }

        /* Hover effect */
        .nav-links a:hover {
            color: #2563eb;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* Login and Register navigation buttons */

        .nav-links a.login-button,
        .nav-links a.register-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            min-width: 85px;
            min-height: 40px;

            padding: 10px 20px;

            border-radius: 7px;

            color: white !important;
            text-decoration: none;

            font-size: 16px;
            font-weight: 600;
            line-height: 1;

            white-space: nowrap;
            transition: background-color 0.3s ease;
        }

        /* Login - Blue */

        .nav-links a.login-button {
            background-color: #2563eb;
        }

        .nav-links a.login-button:hover {
            background-color: #1d4ed8;
        }

        /* Register - Green */

        .nav-links a.register-button {
            background-color: #16a34a;
        }

        .nav-links a.register-button:hover {
            background-color: #15803d;
        }

        /* Remove blue underline from buttons */

        .nav-links a.login-button::after,
        .nav-links a.register-button::after {
            display: none;
        }
        /* Hero section */
        .hero {
            display:grid;
            grid-template-columns:1fr 1fr;
            align-items: center;
            gap:40px;
            padding:70px 8%;
            min-height:500px;
            background:linear-gradient(120deg,#e8f2ff,#f5f9ff);
            
        }

        .hero-content {
            max-width: 650px;
        }

        .hero h1 {
            font-size: 48px;
            line-height: 1.2;
            margin-bottom: 22px;
            color: #203e67;
        }

        .hero p {
            font-size: 18px;
            line-height: 1.8;
            color: #526b89;
            margin-bottom: 30px;
        }

        .hero-image img {
        width: 100%;
        height: 420px;
        object-fit: cover;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
         }


        .hero-buttons {
            display: flex;
            gap: 15px;
            margin-top:25px;
            flex-wrap: wrap;
        }

        /* Hero section Login and Register buttons */

        .hero-buttons .login-btn,
        .hero-buttons .register-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            min-height: 52px;
            padding: 14px 28px;

            border-radius: 7px;
            color: white;
            text-decoration: none;

            font-size: 16px;
            font-weight: 600;

            border: none;
            cursor: pointer;

            transition: background-color 0.3s ease;
        }

        /* Blue Login button */

        .hero-buttons .login-btn {
            background-color: #2563eb;
        }

        .hero-buttons .login-btn:hover {
            background-color: #1d4ed8;
        }

        /* Green Register button */

        .hero-buttons .register-btn {
            background-color: #16a34a;
        }

        .hero-buttons .register-btn:hover {
            background-color: #15803d;
        }

       
      

        /* Services */
        .services {
            padding: 65px 8%;
            text-align: center;
        }

        .services h2 {
            font-size: 30px;
            margin-bottom: 12px;
        }

        .section-description {
            color: #64748b;
            margin-bottom: 35px;
        }

        .service-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .service-card {
            background: white;
            padding: 30px 20px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        }

        .service-icon {
            font-size: 38px;
            margin-bottom: 15px;
        }

        .service-card h3 {
            margin-bottom: 12px;
            color: #2563eb;
        }

        .service-card p {
            color: #64748b;
            line-height: 1.6;
        }

        /* About */
        .about {
            background: white;
            padding: 65px 8%;
            text-align: center;
        }

        .about h2 {
            font-size: 30px;
            margin-bottom: 20px;
        }

        .about p {
            max-width: 800px;
            margin: auto;
            color: #64748b;
            line-height: 1.8;
        }

        /* Footer */
        footer {
            background: #263b5e;
            color: white;
            text-align: center;
            padding: 25px;
        }


        @media (max-width: 900px) {
            .service-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .navbar {
                flex-direction: column;
                gap: 18px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .hero {
                grid-template-columns:1fr;
                padding: 40px 6%;
            }
            .hero-image img {
                height:300px;
            }

            .hero h1 {
                font-size: 34px;
            }

            .service-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <header class="navbar">

        
        <div class="logo">
            <svg
                class="medical-logo"
                xmlns="http://www.w3.org/2000/svg"
                width="38"
                height="38"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8z"/>
                <path d="M3 12h5l2-4 3 8 2-4h6"/>
            </svg>

            <span>Younseii Hospital</span>
        </div>


        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#services">Services</a>
            <a href="#about">About Us</a>
            <a href="/login" class="login-button">Login</a>
            <a href="/register" class="register-button">Register</a>
        </div>

    </header>


    <!-- Home / Hero Section -->
   <section class="hero">

    <div class="hero-text">

        <h1>Your Health, Our Priority</h1>

        <p>
            Welcome to our Hospital Management System.
            We are committed to supporting quality healthcare
            through organized patient care and hospital services.
        </p>


        <div class="hero-buttons">

            <a href="/login" class="login-btn">
                Login
            </a>

            <a href="/register" class="register-btn">
                Register as a Patient
            </a>

        </div>

        

    </div>

    <div class="hero-image">

        <img
            src="/assets/images/hospital-hero.jpg"
            alt="Modern hospital and medical team"
        >

    </div>

    </section>

    <!-- Services Section -->
    <section class="services" id="services">

        <h2>Our Services</h2>

        <p class="section-description">
            Explore the healthcare services managed through our hospital.
        </p>

        <div class="service-container">

            <div class="service-card">
                <div class="service-icon">🩺</div>
                <h3>Doctor Consultation</h3>
                <p>
                    Access doctor information and manage medical appointments.
                </p>
            </div>

            <div class="service-card">
                <div class="service-icon">📅</div>
                <h3>Appointments</h3>
                <p>
                    Manage patient appointments and consultation schedules.
                </p>
            </div>

            <div class="service-card">
                <div class="service-icon">💊</div>
                <h3>Prescription</h3>
                <p>
                    Maintain prescription information and medication records.
                </p>
            </div>

            <div class="service-card">
                <div class="service-icon">🏥</div>
                <h3>Inpatient Care</h3>
                <p>
                    Manage hospital wards, rooms, beds and patient admissions.
                </p>
            </div>

        </div>

    </section>


    <!-- About Section -->
    <section class="about" id="about">

        <h2>About Our Hospital</h2>

        <p>
            Our Hospital Management System helps organize hospital
            operations, patient information, appointments, prescriptions,
            billing and inpatient services in one place.
            Patients can register for an account and access the system
            after logging in.
        </p>

    </section>


    <!-- Footer -->
    <footer>

        <p>
            &copy; <?= date('Y') ?> Hospital Management System.
            All rights reserved.
        </p>

    </footer>

</body>
</html>