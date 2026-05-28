<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi Pembangunan Tulungagung</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 50%, #90CAF9 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Indonesia Map Pattern Background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.3) 2px, transparent 2px),
                radial-gradient(circle at 60% 50%, rgba(255, 255, 255, 0.3) 2px, transparent 2px),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.3) 2px, transparent 2px),
                radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.3) 2px, transparent 2px),
                radial-gradient(circle at 15% 60%, rgba(255, 255, 255, 0.3) 2px, transparent 2px),
                radial-gradient(circle at 70% 20%, rgba(255, 255, 255, 0.3) 2px, transparent 2px),
                radial-gradient(circle at 50% 40%, rgba(255, 255, 255, 0.3) 2px, transparent 2px),
                radial-gradient(circle at 30% 10%, rgba(255, 255, 255, 0.3) 2px, transparent 2px);
            background-size: 200px 200px, 150px 150px, 180px 180px, 160px 160px, 170px 170px, 190px 190px, 140px 140px, 200px 200px;
            background-position: 0 0, 50px 50px, 100px 100px, 150px 150px, 200px 200px, 250px 250px, 300px 300px, 350px 350px;
            opacity: 0.4;
            pointer-events: none;
            animation: patternMove 20s linear infinite;
        }

        @keyframes patternMove {
            0% { background-position: 0 0, 50px 50px, 100px 100px, 150px 150px, 200px 200px, 250px 250px, 300px 300px, 350px 350px; }
            100% { background-position: 200px 200px, 250px 250px, 300px 300px, 350px 350px, 400px 400px, 450px 450px, 500px 500px, 550px 550px; }
        }

        /* Floating Particles */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 0.3;
            }
            25% {
                transform: translateY(-100px) translateX(50px) rotate(90deg);
                opacity: 0.6;
            }
            50% {
                transform: translateY(-200px) translateX(-30px) rotate(180deg);
                opacity: 0.4;
            }
            75% {
                transform: translateY(-100px) translateX(80px) rotate(270deg);
                opacity: 0.7;
            }
        }

        .container {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .button-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
        }

        .top-buttons {
            display: none;
            gap: 30px;
            flex-direction: row;
            align-items: center;
            justify-content: center;
        }

        .top-buttons.show {
            display: flex;
        }

        .top-buttons.show .button-top:nth-child(1) {
            animation: slideInLeft 0.6s ease-out 0.1s both;
        }

        .top-buttons.show .button-top:nth-child(2) {
            animation: slideInRight 0.6s ease-out 0.2s both;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px) translateY(-20px) scale(0.8);
            }
            to {
                opacity: 1;
                transform: translateX(0) translateY(0) scale(1);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px) translateY(-20px) scale(0.8);
            }
            to {
                opacity: 1;
                transform: translateX(0) translateY(0) scale(1);
            }
        }

        .button {
            background-color: #F5F5DC;
            border: 2px solid #DC143C;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #DC143C;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.2);
            position: relative;
            overflow: hidden;
        }

        .button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(220, 20, 60, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .button:hover::before {
            width: 300px;
            height: 300px;
        }

        .button:hover {
            transform: scale(1.08) translateY(-5px);
            box-shadow: 0 8px 30px rgba(220, 20, 60, 0.4);
            background-color: #FFF8DC;
            border-color: #B71C1C;
        }

        .button:active {
            transform: scale(0.95) translateY(0);
        }

        .button-center {
            animation: pulseGlow 2s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 4px 15px rgba(220, 20, 60, 0.2), 0 0 0 0 rgba(220, 20, 60, 0.4);
            }
            50% {
                box-shadow: 0 4px 15px rgba(220, 20, 60, 0.2), 0 0 0 10px rgba(220, 20, 60, 0);
            }
        }

        .button-center {
            width: 200px;
            height: 200px;
            font-size: 24px;
        }

        .button-top {
            width: 150px;
            height: 150px;
            font-size: 14px;
            text-align: center;
            padding: 10px;
        }

        .button-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .button-center .button-icon {
            width: 80px;
            height: 80px;
        }

        .button-text {
            font-size: 14px;
            line-height: 1.2;
            text-align: center;
        }

        .button-center .button-text {
            font-size: 20px;
            font-weight: 700;
            margin-top: 10px;
            position: relative;
            animation: textShimmer 3s ease-in-out infinite;
        }

        @keyframes textShimmer {
            0%, 100% {
                color: #DC143C;
            }
            50% {
                color: #B71C1C;
            }
        }

        /* Icon Styles */
        .icon-logo {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .icon-logo svg {
            width: 100%;
            height: 100%;
            animation: iconPulse 2s ease-in-out infinite;
        }

        .icon-logo svg circle {
            animation: dotPulse 1.5s ease-in-out infinite;
        }

        .icon-logo svg .dot-center { animation-delay: 0s; }
        .icon-logo svg .dot-side:nth-of-type(1) { animation-delay: 0.15s; }
        .icon-logo svg .dot-side:nth-of-type(2) { animation-delay: 0.3s; }
        .icon-logo svg .dot-side:nth-of-type(3) { animation-delay: 0.45s; }
        .icon-logo svg .dot-side:nth-of-type(4) { animation-delay: 0.6s; }
        .icon-logo svg .dot-corner:nth-of-type(1) { animation-delay: 0.75s; }
        .icon-logo svg .dot-corner:nth-of-type(2) { animation-delay: 0.9s; }
        .icon-logo svg .dot-corner:nth-of-type(3) { animation-delay: 1.05s; }
        .icon-logo svg .dot-corner:nth-of-type(4) { animation-delay: 1.2s; }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes dotPulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.2);
            }
        }

        .icon-construction {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-construction svg {
            width: 100%;
            height: 100%;
            fill: #DC143C;
            transition: all 0.3s ease;
            animation: iconBounce 2s ease-in-out infinite;
        }

        .button-top:hover .icon-construction svg {
            transform: translateY(-5px) scale(1.1);
            fill: #B71C1C;
        }

        @keyframes iconBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        .icon-rfk {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 32px;
            color: #DC143C;
            transition: all 0.3s ease;
            animation: textGlow 2s ease-in-out infinite;
        }

        .button-top:hover .icon-rfk {
            transform: scale(1.15);
            color: #B71C1C;
            text-shadow: 0 0 10px rgba(220, 20, 60, 0.5);
        }

        @keyframes textGlow {
            0%, 100% {
                text-shadow: 0 0 5px rgba(220, 20, 60, 0.3);
            }
            50% {
                text-shadow: 0 0 15px rgba(220, 20, 60, 0.6);
            }
        }

        /* E-ASB Button Styles */
        .button-easb {
            width: 150px;
            height: 150px;
        }

        .button-text-easb {
            font-size: 32px;
            font-weight: 700;
            color: #DC143C;
            position: relative;
            z-index: 1;
        }

        /* Logo Section Styles */
        .logo-section {
            margin-top: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
            animation: fadeInUp 1s ease-out 0.8s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-img {
            width: 150px;
            height: auto;
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.2));
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .logo-text {
            text-align: center;
            color: #1a1a1a;
        }

        .logo-text h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            line-height: 1.4;
            text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.8);
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {
            .button-center {
                width: 150px;
                height: 150px;
                font-size: 18px;
            }

            .button-top {
                width: 120px;
                height: 120px;
                font-size: 12px;
            }

            .button-easb {
                width: 120px !important;
                height: 120px !important;
            }

            .button-text-easb {
                font-size: 24px;
            }

            .top-buttons {
                gap: 20px;
                flex-wrap: wrap;
            }

            .button-center .button-icon {
                width: 60px;
                height: 60px;
            }

            .button-center .button-text {
                font-size: 16px;
            }

            .logo-section {
                margin-top: 40px;
                gap: 15px;
            }

            .logo-img {
                width: 100px;
            }

            .logo-text h2 {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="particles" id="particles"></div>
    <div class="container">
        <div class="button-container">
            <div class="top-buttons" id="topButtons">
                <a href="{{ env('EASB_URL') }}" target="_blank" class="button button-top button-easb">
                    <div class="button-text-easb">E-ASB</div>
                </a>
                <a href="{{ route('login') }}" class="button button-top">
                    <div class="button-icon">
                        <div class="icon-construction">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <!-- Building/Construction Icon -->
                                <path d="M12 3L2 12H5V20H19V12H22L12 3ZM17 18H7V10.19L12 5.69L17 10.19V18Z" fill="currentColor"/>
                                <rect x="9" y="13" width="2" height="5" fill="currentColor"/>
                                <rect x="13" y="13" width="2" height="5" fill="currentColor"/>
                            </svg>
                        </div>
                    </div>
                    <div class="button-text">Pekerjaan<br>Konstruksi</div>
                </a>
                <a href="{{ route('login') }}" class="button button-top">
                    <div class="button-icon">
                        <div class="icon-rfk">RFK</div>
                    </div>
                    <div class="button-text">RFK</div>
                </a>
            </div>
            <button class="button button-center" id="centerButton">
                <div class="button-icon">
                    <div class="icon-logo">
                        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <!-- 3x3 Grid Dots Pattern: Center red, surrounding blue, corners red -->
                            <!-- Top row -->
                            <circle cx="30" cy="30" r="6" fill="#DC143C" class="dot-corner"/>
                            <circle cx="50" cy="30" r="6" fill="#1E88E5" class="dot-side"/>
                            <circle cx="70" cy="30" r="6" fill="#DC143C" class="dot-corner"/>
                            <!-- Middle row -->
                            <circle cx="30" cy="50" r="6" fill="#1E88E5" class="dot-side"/>
                            <circle cx="50" cy="50" r="6" fill="#DC143C" class="dot-center"/>
                            <circle cx="70" cy="50" r="6" fill="#1E88E5" class="dot-side"/>
                            <!-- Bottom row -->
                            <circle cx="30" cy="70" r="6" fill="#DC143C" class="dot-corner"/>
                            <circle cx="50" cy="70" r="6" fill="#1E88E5" class="dot-side"/>
                            <circle cx="70" cy="70" r="6" fill="#DC143C" class="dot-corner"/>
                        </svg>
                    </div>
                </div>
                <div class="button-text">SIAP</div>
            </button>
        </div>

        <!-- Logo and Text Section -->
        <div class="logo-section">
            <img src="{{ asset('logo-tulungagung.png') }}" alt="Logo Tulungagung" class="logo-img">
            <div class="logo-text">
                <h2>SEKRETARIAT DAERAH</h2>
                <h2>BAGIAN ADMINISTRASI</h2>
                <h2>PEMBANGUNAN</h2>
            </div>
        </div>
    </div>

    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 15;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                const size = Math.random() * 8 + 4;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        // Center button click handler
        document.getElementById('centerButton').addEventListener('click', function() {
            const topButtons = document.getElementById('topButtons');
            if (!topButtons.classList.contains('show')) {
                topButtons.classList.add('show');
                // Add ripple effect
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'pulseGlow 2s ease-in-out infinite';
                }, 10);
            }
        });

        // Initialize particles on load
        window.addEventListener('load', createParticles);
    </script>
</body>
</html>
