<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollToPlugin.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: url('uploads/ML.webp') no-repeat center center fixed;
            background-size: cover;
        }

        .nav-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            z-index: 1000;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
        }

        .nav-links li {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links li:hover {
            color: #ffcc00;
            transform: scale(1.1);
        }

        .moving-title {
            display: flex;
            align-items: center;
            font-size: 2rem;
            font-weight: bold;
            color: #ffcc00;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
            gap: 10px;
        }

        .moving-title span {
            display: inline-block;
            animation: waveEffect 2s infinite alternate ease-in-out;
        }

        @keyframes waveEffect {
            0% {
                transform: translateY(5px);
            }

            50% {
                transform: translateY(-5px);
            }

            100% {
                transform: translateY(5px);
            }
        }

        .moving-title span:nth-child(odd) {
            animation-delay: 0.2s;
        }

        .moving-title span:nth-child(even) {
            animation-delay: 0.4s;
        }

        /* Light Switch Styles */
        .switch {
            display: block;
            background-color: black;
            width: 60px;
            height: 80px;
            box-shadow: 0 0 10px 2px rgba(0, 0, 0, 0.2), 0 0 1px 2px black, inset 0 2px 2px -2px white, inset 0 0 2px 15px #47434c, inset 0 0 2px 22px black;
            border-radius: 5px;
            padding: 10px;
            perspective: 500px;
            cursor: pointer;
        }

        .switch input {
            display: none;
        }

        .switch input:checked+.button {
            transform: translateZ(10px) rotateX(25deg);
            box-shadow: 0 -10px 20px #ff1818;
        }

        .switch input:checked+.button .light {
            animation: flicker 0.2s infinite 0.3s;
        }

        .switch .button {
            display: block;
            transition: all 0.3s cubic-bezier(1, 0, 1, 1);
            transform-origin: center center -10px;
            transform: translateZ(10px) rotateX(-25deg);
            background: linear-gradient(#980000 0%, #6f0000 30%, #6f0000 70%, #980000 100%);
            height: 100%;
            position: relative;
        }

        .switch .light {
            opacity: 0;
            animation: light-off 1s;
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(#ffc97e, #ff1818 40%, transparent 70%);
        }

        @keyframes flicker {
            0% {
                opacity: 1;
            }

            80% {
                opacity: 0.8;
            }

            100% {
                opacity: 1;
            }
        }

        /* Footer Style */
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            font-size: 14px;
        }
    </style>
</head>

<body class="relative min-h-screen flex flex-col text-white">

    <!-- Navigation Bar -->
    <nav class="nav-bar" id="navBar">
        <div class="moving-title" id="movingTitle">
            <i class="fa-solid fa-vote-yea"></i>
            <span>Welcome</span> <span>to</span> <span>Online</span> <span>Voting</span> <span>System</span>
        </div>
        <ul class="nav-links">
            <li onclick="scrollToSection('home')"><i class="fa-solid fa-house"></i> Home</li>
            <li onclick="scrollToSection('about')"><i class="fa-solid fa-info-circle"></i> About</li>
            <li onclick="scrollToSection('guide')"><i class="fa-solid fa-book"></i> Guide</li>
            <li onclick="scrollToSection('contact')"><i class="fa-solid fa-envelope"></i> Contact Us</li>
        </ul>

        <!-- Light Switch as Login Button -->
        <label class="switch">
            <input type="checkbox" id="loginSwitch">
            <div class="button">
                <div class="light"></div>
            </div>
        </label>
    </nav>

    <!-- Home Section -->
    <section id="home" class="w-full h-screen flex items-center justify-center">
        <h2 class="text-5xl font-bold bg-black bg-opacity-60 px-6 py-4 rounded-lg">Your Vote, Your Future</h2>
        <div class="footer">© Lwannuuwatt 2025</div>
    </section>

    <!-- About Section -->
    <section id="about" class="w-full h-screen flex flex-col items-center justify-center bg-black bg-opacity-60">
        <h2 class="text-3xl font-bold text-yellow-400">How Our Voting System Works</h2>
        <ul class="text-lg mt-2 text-center">
            <li>✔️ <b>Admin creates elections</b> with multiple candidates.</li>
            <li>✔️ <b>Users log in using their unique voter ID</b> assigned by the system.</li>
            <li>✔️ <b>Results are displayed</b> instantly with live updates.</li>
            <li>✔️ <b>Graphs and visual stats</b> make it easier to understand voting trends.</li>
            <li>🔒 <b>Advanced security</b> ensures only verified users can vote.</li>
            <li>🚀 <b>Fast, reliable, and accessible</b> anytime, anywhere!</li>
        </ul>
        <div class="footer">© Lwannuuwatt 2025</div>
    </section>

    <!-- Guide Section -->
    <section id="guide" class="w-full h-screen flex flex-col items-center justify-center bg-black bg-opacity-60">
        <h2 class="text-3xl font-bold text-yellow-400">Step-by-Step Voting Guide</h2>
        <ul class="mt-4 text-lg">
            <li>✅ <b>Step 1:</b> Receive your <b>unique voter ID</b> from the admin.</li>
            <li>✅ <b>Step 2:</b> Log in using your voter ID.</li>
            <li>✅ <b>Step 3:</b> Select an active election.</li>
            <li>✅ <b>Step 4:</b> Choose your preferred candidate.</li>
            <li>✅ <b>Step 5:</b> Confirm your vote before submission.</li>
            <li>✅ <b>Step 6:</b> Votes are counted in real time.</li>
        </ul>
        <div class="footer">© Lwannuuwatt 2025</div>
    </section>

    <!-- Contact Us Section -->
    <section id="contact" class="w-full h-screen flex flex-col items-center justify-center bg-black bg-opacity-60">
        <section id="contact" class="w-full h-screen flex flex-col items-center justify-center bg-black bg-opacity-60">
            <div class="contact-container">
                <h2 class="text-3xl font-bold text-yellow-400">Contact Us</h2>
                <p><i class="fa-solid fa-envelope"></i> Email: lwannuuwatt@votingsystem.com</p>
                <p><i class="fa-solid fa-phone"></i> Phone: +95 9 423 294 656</p>
                <p><i class="fa-solid fa-map-marker-alt"></i> Address: Inle Hostel (Home)</p>
            </div>
            <div class="mt-6">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3818.3944763715363!2d96.12806807377602!3d16.856369683943072!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c194f2a6a43cd9%3A0xc214c5140fe65fa4!2sInle%20Hostel%20(Home)!5e0!3m2!1sen!2smm!4v1739123655394!5m2!1sen!2smm" width="400" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </section>
        <div class="footer">© Lwannuuwatt 2025</div>
    </section>

    <script>
        function scrollToSection(section) {
            gsap.to(window, {
                duration: 1.2,
                scrollTo: `#${section}`,
                ease: "power2.out"
            });
        }

        // Redirect to login page when switch is ON
        document.getElementById("loginSwitch").addEventListener("change", function() {
            if (this.checked) {
                window.location.href = "login.php";
            }
        });
    </script>
</body>

</html>