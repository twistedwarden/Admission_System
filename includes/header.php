<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'light-purple': '#D3B3F2',
                        'slight-purple': '#C394F2',
                        'purple': '#8243D9',
                        'medium-dark-purple': '#5B26A6',
                        'dark-purple': '#3B1273',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #C394F2;
            min-height: 100vh;
        }
        
        .gradient-bg {
            background-image: linear-gradient(135deg, #5B26A6, #8243D9);
        }
        
        .header-gradient {
            background-image: linear-gradient(to right, #3B1273, #5B26A6);
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(59, 18, 115, 0.2);
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(59, 18, 115, 0.3);
        }
        
        /* Form styles */
        .form-input {
            width: 100%;
            border-radius: 0.375rem;
            border-width: 2px;
            border-color: #d1d5db;
            padding: 0.5rem 0.75rem;
            transition-property: all;
            transition-duration: 200ms;
        }
        
        .form-input:focus {
            border-color: #8243D9;
            outline: none;
            box-shadow: 0 0 0 2px rgba(130, 67, 217, 0.5);
        }
        
        .form-select {
            width: 100%;
            border-radius: 0.375rem;
            border-width: 2px;
            border-color: #d1d5db;
            padding: 0.5rem 0.75rem;
            transition-property: all;
            transition-duration: 200ms;
        }
        
        .form-select:focus {
            border-color: #8243D9;
            outline: none;
            box-shadow: 0 0 0 2px rgba(130, 67, 217, 0.5);
        }
        
        .form-checkbox {
            width: 1.25rem;
            height: 1.25rem;
            color: #8243D9;
            border-width: 2px;
            border-color: #d1d5db;
            border-radius: 0.25rem;
        }
        
        .form-checkbox:focus {
            --tw-ring-color: #8243D9;
        }
        
        .form-radio {
            width: 1.25rem;
            height: 1.25rem;
            color: #8243D9;
            border-width: 2px;
            border-color: #d1d5db;
            border-radius: 9999px;
        }
        
        .form-radio:focus {
            --tw-ring-color: #8243D9;
        }
        
        .btn-primary {
            background-color: #5B26A6;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition-property: all;
            transition-duration: 300ms;
        }
        
        .btn-primary:hover {
            background-color: #3B1273;
        }
        
        .btn-secondary {
            background-color: #C394F2;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition-property: all;
            transition-duration: 300ms;
        }
        
        .btn-secondary:hover {
            background-color: #8243D9;
        }
        
        .btn-outline {
            border-width: 2px;
            border-color: #5B26A6;
            color: #5B26A6;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition-property: all;
            transition-duration: 300ms;
        }
        
        .btn-outline:hover {
            background-color: #5B26A6;
            color: white;
        }
    </style>
</head>
<body class="font-sans text-gray-800">
    <!-- Navigation -->
    <nav class="header-gradient text-white shadow-md">
        <div class="container mx-auto px-4 md:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <a href="index.php" class="flex items-center space-x-3">
                    <span class="text-2xl font-bold">Admission System</span>
                </a>
                
                <!-- Mobile Toggle -->
                <button id="mobile-menu-toggle" class="md:hidden block text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="../index.php" class="text-white hover:text-light-purple transition duration-300">Home</a>
                    <a href="../programs.php" class="text-white hover:text-light-purple transition duration-300">Programs</a>
                    <a href="../contact.php" class="text-white hover:text-light-purple transition duration-300">Contact</a>
                    <a href="../faq.php" class="text-white hover:text-light-purple transition duration-300">FAQ</a>
                    <a href="../status.php" class="bg-white text-dark-purple px-4 py-2 rounded-md font-medium hover:bg-gray-100 transition duration-300">Check Status</a>
                </div>
            </div>
            
            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="md:hidden hidden pb-4 animate-fade-in">
                <div class="flex flex-col space-y-3">
                    <a href="index.php" class="text-white hover:text-light-purple transition duration-300">Home</a>
                    <a href="programs.php" class="text-white hover:text-light-purple transition duration-300">Programs</a>
                    <a href="contact.php" class="text-white hover:text-light-purple transition duration-300">Contact</a>
                    <a href="faq.php" class="text-white hover:text-light-purple transition duration-300">FAQ</a>
                    <a href="status.php" class="bg-white text-dark-purple px-4 py-2 rounded-md font-medium hover:bg-gray-100 transition duration-300 inline-block w-full text-center">Check Status</a>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="container mx-auto px-4 py-8 md:px-6 lg:px-8">