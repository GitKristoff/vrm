<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinary Records Management with AI-Powered Diagnosis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-cyan-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-paw text-blue-600 text-3xl mr-2"></i>
                        <span class="text-xl font-bold text-gray-900">VetAI Assistant</span>
                    </div>
                </div>
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="open = !open" class="text-gray-700 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
                <!-- Desktop Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-md font-medium hover:underline hover:decoration-2 hover:underline-offset-4 transition">Features</a>
                    <a href="#how-it-works" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-md font-medium hover:underline hover:decoration-2 hover:underline-offset-4 transition">How It Works</a>
                    <a href="#about" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-md font-medium hover:underline hover:decoration-2 hover:underline-offset-4 transition">About</a>
                    <a href="{{ route('login') }}" class="text-blue-600 hover:bg-blue-100 px-4 py-2 rounded-md text-md font-medium hover:underline hover:decoration-2 hover:underline-offset-4 transition">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-md font-medium hover:underline hover:decoration-2 hover:underline-offset-4 transition">Register</a>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="open" class="md:hidden bg-white shadow-lg" x-transition>
            <div class="px-4 pt-2 pb-4 space-y-2">
                <a href="#features" class="block text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-md font-medium hover:underline hover:decoration-2 hover:underline-offset-4 transition">Features</a>
                <a href="#how-it-works" class="block text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-md font-medium hover:underline hover:decoration-2 hover:underline-offset-4 transition">How It Works</a>
                <a href="#about" class="block text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-md font-medium hover:underline hover:decoration-2 hover:underline-offset-4 transition">About</a>
                <a href="{{ route('login') }}" class="block text-blue-600 hover:bg-blue-100 px-4 py-2 rounded-md text-md font-medium hover:underline hover:decoration-2 hover:underline-offset-4 transition">Login</a>
                <a href="{{ route('register') }}" class="block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-md font-medium hover:underline hover:decoration-2 hover:underline-offset-4 transition">Register</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
                        Veterinary Records Management with <span class="text-blue-600">AI-Powered</span> Diagnosis Assistant
                    </h1>
                    <p class="mt-6 text-xl text-gray-600">
                        Transforming veterinary care through intelligent record management and AI-assisted diagnostics for Bulan Veterinary Clinic and beyond.
                    </p>
                    <div class="mt-10 flex space-x-4">
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg hover:bg-blue-700 text-base md:text-lg font-semibold w-full md:w-auto text-center">
                            Get Started <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#features" class="border-2 border-blue-600 text-blue-600 px-6 py-3 md:px-8 md:py-4 rounded-lg hover:bg-blue-400 hover:text-white text-base md:text-lg font-semibold w-full md:w-auto text-center mt-2 md:mt-0">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-blue-100 rounded-2xl p-6 shadow-xl transform rotate-3">
                        <div class="bg-white rounded-xl shadow-lg p-6 transform -rotate-3">
                            <div class="flex items-center">
                                <img
                                    src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQo3AgcJtwVpAUZ6sSXtDKRTl70jiFKXczw4Q&s"
                                    alt="Golden Retriever"
                                    class="rounded-full w-16 h-16 object-cover border-2 border-blue-200 mx-auto md:mx-0"
                                >
                                <div class="ml-4">
                                    <h3 class="font-semibold">Max (Golden Retriever)</h3>
                                    <p class="text-gray-600 text-sm">Age: 5 years | Weight: 32kg</p>
                                </div>
                            </div>
                            <div class="mt-6">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <div class="flex">
                                        <i class="fas fa-robot text-blue-600 mt-1 mr-3"></i>
                                        <div>
                                            <h4 class="font-semibold text-blue-700">AI Diagnosis Suggestion</h4>
                                            <p class="mt-1">Based on symptoms, possible conditions: Allergic dermatitis (85% confidence)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-sm text-gray-500">Last Vaccination</p>
                                    <p class="font-medium">Rabies - 10/15/2024</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <p class="text-sm text-gray-500">Next Appointment</p>
                                    <p class="font-medium">07/25/2025 - 10:30 AM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-yellow-100 rounded-full opacity-70"></div>
                    <div class="absolute -top-8 -right-8 w-24 h-24 bg-green-100 rounded-full opacity-70"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Advanced Features for Modern Veterinary Care</h2>
                <p class="mt-4 text-xl text-gray-600 max-w-3xl mx-auto">
                    Our system combines comprehensive record management with AI-powered diagnostic assistance
                </p>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">
                <!-- Feature 1 -->
                <div class="bg-blue-50 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-brain text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-gray-900">AI-Powered Diagnosis</h3>
                    <p class="mt-3 text-gray-600">
                        Intelligent symptom analysis and diagnostic suggestions based on pet medical history and breed-specific factors.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-green-50 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-medical text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-gray-900">Comprehensive Records</h3>
                    <p class="mt-3 text-gray-600">
                        Centralized digital storage for medical histories, treatments, vaccination schedules, and lab results.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-purple-50 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-check text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-gray-900">Smart Scheduling</h3>
                    <p class="mt-3 text-gray-600">
                        Automated appointment management with reminders and follow-up notifications for pet owners.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-yellow-100 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="w-16 h-16 bg-yellow-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-comments text-amber-600 text-2xl"></i>
                    </div>
                    <h3 class="mt-6 text-xl font-semibold text-gray-900">Live Communication</h3>
                    <p class="mt-3 text-gray-600">
                        Real-time chat between pet owners and veterinary staff for quick consultations and follow-ups.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">How Our System Works</h2>
                <p class="mt-4 text-xl text-gray-600 max-w-3xl mx-auto">
                    A seamless workflow designed to enhance veterinary care through technology
                </p>
            </div>

            <div class="mt-16 flex flex-col md:flex-row items-center justify-between">
                <div class="w-full md:w-5/12">
                    <div class="relative">
                        <div class="absolute -top-6 -left-6 w-48 h-48 bg-blue-100 rounded-full opacity-50"></div>
                        <div class="relative bg-white rounded-2xl shadow-lg p-8 z-10">
                            <div class="flex items-center mb-8">
                                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">1</span>
                                </div>
                                <h3 class="ml-4 text-xl font-semibold">Pet Registration & Records</h3>
                            </div>
                            <p class="text-gray-600">
                                Create comprehensive digital profiles for pets with medical history, vaccination records, and owner information.
                            </p>
                        </div>
                    </div>

                    <div class="relative mt-12">
                        <div class="absolute -bottom-6 -right-6 w-48 h-48 bg-green-100 rounded-full opacity-50"></div>
                        <div class="relative bg-white rounded-2xl shadow-lg p-8 z-10">
                            <div class="flex items-center mb-8">
                                <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">3</span>
                                </div>
                                <h3 class="ml-4 text-xl font-semibold">AI-Assisted Diagnosis</h3>
                            </div>
                            <p class="text-gray-600">
                                Our AI analyzes symptoms and medical history to provide diagnostic suggestions to veterinarians.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- <div class="hidden md:block w-2/12 flex justify-center my-12">
                    <div class="h-full flex items-center">
                        <div class="h-64 w-1 bg-blue-200 full-rounded"></div>
                    </div>
                </div> --}}

                <div class="w-full md:w-5/12 mt-12 md:mt-0">
                    <div class="relative">
                        <div class="absolute -top-6 -right-6 w-48 h-48 bg-purple-100 rounded-full opacity-50"></div>
                        <div class="relative bg-white rounded-2xl shadow-lg p-8 z-10">
                            <div class="flex items-center mb-8">
                                <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">2</span>
                                </div>
                                <h3 class="ml-4 text-xl font-semibold">Appointment Scheduling</h3>
                            </div>
                            <p class="text-gray-600">
                                Easily book, manage, and track appointments with automated reminders for pet owners.
                            </p>
                        </div>
                    </div>

                    <div class="relative mt-12">
                        <div class="absolute -bottom-6 -left-6 w-48 h-48 bg-amber-100 rounded-full opacity-50"></div>
                        <div class="relative bg-white rounded-2xl shadow-lg p-8 z-10">
                            <div class="flex items-center mb-8">
                                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">4</span>
                                </div>
                                <h3 class="ml-4 text-xl font-semibold">Treatment & Follow-up</h3>
                            </div>
                            <p class="text-gray-600">
                                Create treatment plans, track progress, and schedule follow-ups with integrated communication tools.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">About Our Research & System</h2>
                    <p class="mt-6 text-gray-600 leading-relaxed">
                        Developed as part of the research paper "Veterinary Records Management with AI-Powered Diagnosis Assistant in Bulan Veterinary Clinic", this system represents a significant advancement in veterinary healthcare technology.
                    </p>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        Our solution addresses the challenges of traditional veterinary record-keeping by providing a centralized, secure platform that enhances diagnostic accuracy through artificial intelligence while streamlining clinic operations.
                    </p>
                    <div class="mt-8 grid grid-cols-2 gap-6">
                        <div class="flex">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span>Developed at Sorsogon State University</span>
                        </div>
                        <div class="flex">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span>Cloud-based for accessibility</span>
                        </div>
                        <div class="flex">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span>Data security & privacy compliance</span>
                        </div>
                        <div class="flex">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span>Built with Laravel & MySQL</span>
                        </div>
                    </div>
                </div>
                <div class="bg-blue-50 rounded-2xl p-8">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full">
                            <i class="fas fa-lightbulb text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="mt-6 text-xl font-semibold">Research Highlights</h3>
                    </div>
                    <div class="mt-6 space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-robot text-blue-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-900">AI-Powered Assistance</h4>
                                <p class="mt-1 text-gray-600 text-sm">
                                    Machine learning algorithms provide diagnostic suggestions based on symptoms and medical history.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-green-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-900">Secure Data Management</h4>
                                <p class="mt-1 text-gray-600 text-sm">
                                    Encrypted storage and role-based access control protect sensitive veterinary records.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-sync-alt text-purple-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-900">Workflow Optimization</h4>
                                <p class="mt-1 text-gray-600 text-sm">
                                    Automated scheduling, reminders, and reporting reduce administrative burden.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-cyan-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white">Ready to Transform Veterinary Care?</h2>
            <p class="mt-4 text-xl text-blue-100 max-w-3xl mx-auto">
                Join Bulan Veterinary Clinic and experience the future of veterinary record management and AI-assisted diagnostics.
            </p>
            <div class="mt-10 flex flex-col items-center space-y-4 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-4">
                <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg hover:bg-blue-50 text-lg font-semibold inline-flex items-center justify-center w-full sm:w-auto">
                    Get Started Now <i class="fas fa-arrow-right ml-3"></i>
                </a>
                <a href="{{ route('login') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg hover:bg-blue-700 text-lg font-semibold">
                    Login to Your Account
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Footer Columns -->
            </div>

            <div class="mt-12 pt-8 border-t border-gray-800 text-center text-gray-400">
                <p>&copy; 2025 Veterinary Records Management with AI-Powered Diagnosis Assistant. All rights reserved.</p>
                <p class="mt-2">Developed as part of research at Sorsogon State University</p>
            </div>
        </div>
    </footer>
</body>
</html>
