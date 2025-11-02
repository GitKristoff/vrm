<x-guest-wide>
     <div class="relative min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-48 h-48 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
            <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-1/4 left-1/3 w-56 h-56 bg-indigo-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <div class="relative max-w-6xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Visual Section -->
                <div class="md:w-5/12 bg-gradient-to-br from-blue-600 to-indigo-700 p-8 md:p-12 flex flex-col justify-center">
                    <div class="text-center md:text-left">
                        <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto md:mx-0 mb-6">
                            <i class="fas fa-paw text-white text-3xl"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-white mb-4">Welcome to VRMS</h2>
                        <p class="text-blue-100 max-w-md">
                            Join our professional veterinary management system to get the best care for your pets.
                            Create your account in minutes and access our complete suite of services.
                        </p>

                        <div class="mt-8 space-y-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <p class="ml-3 text-blue-100">Manage all your pets in one place</p>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <p class="ml-3 text-blue-100">Schedule appointments easily</p>
                            </div>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <p class="ml-3 text-blue-100">Access medical records anytime</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="md:w-7/12 p-8 md:p-16">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900">Create Your Account</h1>
                        <p class="mt-2 text-gray-600">Fill in your details to get started</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <!-- Personal Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-l-4 border-blue-500 pl-3 py-1">
                                Personal Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="name" :value="__('Full Name')" />
                                    <x-text-input id="name" class="block w-full mt-1" type="text" name="name"
                                        :value="old('name')" required autofocus autocomplete="name"
                                        placeholder="Juan Dela Cruz" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="email" :value="__('Email Address')" />
                                    <x-text-input id="email" class="block w-full mt-1" type="email" name="email"
                                        :value="old('email')" required autocomplete="email"
                                        placeholder="juan@gmail.com" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="phone" :value="__('Phone Number')" />
                                    <x-text-input id="phone" class="block w-full mt-1" type="tel" name="phone"
                                        :value="old('phone')" required placeholder="09XXXXXXXXX"
                                        pattern="^09\d{9}$" />
                                    <p class="mt-1 text-xs text-gray-500">Format: 09XXXXXXXXX</p>
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-l-4 border-blue-500 pl-3 py-1">
                                Address Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="street" :value="__('Street')" />
                                    <x-text-input id="street" class="block w-full mt-1" type="text" name="street"
                                        :value="old('street')" required placeholder="123 Mabini St." />
                                    <x-input-error :messages="$errors->get('street')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="region" :value="__('Region')" />
                                    <select id="region" name="region" class="block w-full mt-1" required>
                                        <option value="">Select Region</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('region')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="province" :value="__('Province')" />
                                    <select id="province" name="province" class="block w-full mt-1" required>
                                        <option value="">Select Province</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('province')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="municipality" :value="__('Municipality/City')" />
                                    <select id="municipality" name="municipality" class="block w-full mt-1" required>
                                        <option value="">Select Municipality/City</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('municipality')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="barangay" :value="__('Barangay')" />
                                    <select id="barangay" name="barangay" class="block w-full mt-1" required>
                                        <option value="">Select Barangay</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('barangay')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="country" :value="__('Country')" />
                                    <x-text-input id="country" class="block w-full mt-1" type="text" name="country"
                                        :value="old('country', 'Philippines')" required placeholder="Philippines" />
                                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-l-4 border-blue-500 pl-3 py-1">
                                Account Security
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="relative">
                                    <x-input-label for="password" :value="__('Password')" />
                                    <x-text-input id="password" class="block w-full mt-1 pr-10" type="password"
                                        name="password" required autocomplete="new-password"
                                        placeholder="At least 8 characters" />
                                    <button type="button" class="absolute top-9 right-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                                        onclick="togglePassword('password', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                                <div class="relative">
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation" class="block w-full mt-1 pr-10"
                                        type="password" name="password_confirmation" required
                                        autocomplete="new-password" placeholder="Retype your password" />
                                    <button type="button" class="absolute top-9 right-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                                        onclick="togglePassword('password_confirmation', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 mt-2">
                                <p>• Use at least 8 characters</p>
                                <p>• Include uppercase and lowercase letters</p>
                                <p>• Add numbers or symbols for extra security</p>
                            </div>
                        </div>
                        <script>
                        function togglePassword(fieldId, btn) {
                            const input = document.getElementById(fieldId);
                            if (input.type === "password") {
                                input.type = "text";
                                btn.querySelector('i').classList.remove('fa-eye');
                                btn.querySelector('i').classList.add('fa-eye-slash');
                            } else {
                                input.type = "password";
                                btn.querySelector('i').classList.remove('fa-eye-slash');
                                btn.querySelector('i').classList.add('fa-eye');
                            }
                        }
                        </script>

                        <div class="flex flex-col sm:flex-row items-center justify-between mt-8">
                            <a class="text-blue-600 hover:text-blue-800 text-sm font-medium" href="{{ route('login') }}">
                                {{ __('Already have an account? Sign in') }}
                            </a>
                            <x-primary-button class="w-full sm:w-auto px-6 py-3">
                                {{ __('Create Account') }}
                                <i class="fas fa-arrow-right ml-2"></i>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    let regionSelect = document.getElementById('region');
    let provinceSelect = document.getElementById('province');
    let municipalitySelect = document.getElementById('municipality');
    let barangaySelect = document.getElementById('barangay');

    // Load regions
    fetch('/addresses/region.json')
        .then(response => response.json())
        .then(regions => {
            regions.forEach(region => {
                let opt = document.createElement('option');
                opt.value = region.region_code;
                opt.text = region.region_name;
                regionSelect.appendChild(opt);
            });
        });

    // When region changes, load provinces
    regionSelect.addEventListener('change', function () {
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        municipalitySelect.innerHTML = '<option value="">Select Municipality/City</option>';
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        if (!this.value) return;

        fetch('/addresses/province.json')
            .then(response => response.json())
            .then(provinces => {
                provinces.filter(p => p.region_code === this.value)
                    .forEach(province => {
                        let opt = document.createElement('option');
                        opt.value = province.province_code;
                        opt.text = province.province_name;
                        provinceSelect.appendChild(opt);
                    });
            });
    });

    // When province changes, load municipalities/cities
    provinceSelect.addEventListener('change', function () {
        municipalitySelect.innerHTML = '<option value="">Select Municipality/City</option>';
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        if (!this.value) return;

        fetch('/addresses/city.json')
            .then(response => response.json())
            .then(cities => {
                cities.filter(c => c.province_code === this.value)
                    .forEach(city => {
                        let opt = document.createElement('option');
                        opt.value = city.city_code;
                        opt.text = city.city_name;
                        municipalitySelect.appendChild(opt);
                    });
            });
    });

    // When municipality/city changes, load barangays
    municipalitySelect.addEventListener('change', function () {
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        if (!this.value) return;

        fetch('/addresses/barangay.json')
            .then(response => response.json())
            .then(barangays => {
                barangays.filter(b => b.city_code === this.value)
                    .forEach(barangay => {
                        let opt = document.createElement('option');
                        opt.value = barangay.brgy_name;
                        opt.text = barangay.brgy_name;
                        barangaySelect.appendChild(opt);
                    });
            });
    });
});
</script>
</x-guest-wide>
