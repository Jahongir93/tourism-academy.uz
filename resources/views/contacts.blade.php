@extends('layouts.frontend')

@section('title', 'Kontaktlar - Tourism Academy')

@section('content')
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold gradient-text mb-4">Bog'lanish</h1>
            <p class="text-gray-600 text-lg">Biz bilan aloqaga chiqing</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Information -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold mb-6">Kontakt ma'lumotlari</h2>

                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-emerald-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1">Manzil</h3>
                            <p class="text-gray-600">Samarqand sh., Universitet xiyoboni 15</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-emerald-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1">Telefon</h3>
                            <p class="text-gray-600">+998 90 123-45-67</p>
                            <p class="text-gray-600">+998 66 234-56-78</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-emerald-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1">Email</h3>
                            <p class="text-gray-600">info@tourism.uz</p>
                            <p class="text-gray-600">admission@tourism.uz</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-emerald-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-1">Ish vaqti</h3>
                            <p class="text-gray-600">Dushanba-Juma: 9:00 - 18:00</p>
                            <p class="text-gray-600">Shanba: 9:00 - 15:00</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold mb-6">Xabar yuborish</h2>

                <form class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ism</label>
                        <input type="text" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mavzu</label>
                        <input type="text" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Xabar</label>
                        <textarea rows="4" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-emerald-500 focus:outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-3 rounded-lg font-semibold hover:from-emerald-600 hover:to-teal-700 transition">
                        <i class="fas fa-paper-plane mr-2"></i>Yuborish
                    </button>
                </form>
            </div>
        </div>

        <!-- Map Section -->
        <div class="mt-12 bg-white rounded-2xl shadow-xl p-2 h-96">
            <!-- Google Map will be added here -->
            <div class="w-full h-full bg-gray-200 rounded-xl flex items-center justify-center">
                <p class="text-gray-500">Google Map joylashadi</p>
            </div>
        </div>
    </div>
</section>
@endsection