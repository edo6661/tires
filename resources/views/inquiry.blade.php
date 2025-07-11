<x-layouts.app>
    <div class="flex flex-col md:flex-row gap-8">
        <div class="w-full md:w-1/3 lg:w-1/4 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-800">Takanawa Gateway City</h2>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Location</h3>
                    <p class="text-gray-600">2095-8 Miyadera, Iruma-shi, Saitama-ken</p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Opening Hours</h3>
                    <ul class="text-gray-600 space-y-1">
                        <li class="flex justify-between">
                            <span>Monday</span>
                            <span>10:00 ~ 18:00</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Tuesday</span>
                            <span>10:00 ~ 18:00</span>
                        </li>
                        <li class="flex justify-between text-gray-400">
                            <span>Wednesday</span>
                            <span>Closed</span>
                        </li>
                        <li class="flex justify-between text-gray-400">
                            <span>Thursday</span>
                            <span>Closed</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Friday</span>
                            <span>10:00 ~ 18:00</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Saturday</span>
                            <span>10:00 ~ 18:00</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Sunday</span>
                            <span>10:00 ~ 18:00</span>
                        </li>
                    </ul>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <a href="#" class="text-gray-600 hover:text-green-600 transition">About Us</a>
                    <a href="#" class="text-gray-600 hover:text-green-600 transition block mt-2">Terms of Use</a>
                </div>
            </div>
        </div>
        
        <div class="flex-1 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Contact Us</h2>
            <p class="text-gray-600 mb-6">Please enter the content of your inquiry. If you have a RESERVA account, please log in from here.</p>
            
            <form class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Tokyo Taro">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                    <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="email address">
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" id="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="00-0000-0000">
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                    <input type="text" id="subject" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Inquiry Content *</label>
                    <textarea id="message" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Please enter the content of your inquiry"></textarea>
                </div>
                
                <button type="submit" class="w-full bg-primary hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                    Submit Inquiry
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
