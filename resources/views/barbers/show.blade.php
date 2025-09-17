<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $barber->name }} - Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <style>
        .swiper {
            width: 100%;
            padding: 20px 0;
        }
        .swiper-slide {
            width: auto;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Banner -->
    <div class="w-full h-64 relative">
        @if($barber->image_banner)
            <img src="{{ asset('storage/' . $barber->image_banner) }}" alt="Banner" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-r from-gray-700 to-gray-900 flex items-center justify-center">
                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
        <!-- Logo/Foto do Barbeiro -->
        <div class="absolute -bottom-16 left-8">
            @if($barber->image_logo)
             <div class="flex flex-row justify-center">
                <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gray-800 flex items-center justify-center">
                                   <img src="{{ asset('storage/' . $barber->image_logo) }}" alt="{{ $barber->name }}" class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">

                </div>
                <p class="text-gray-200 mt-3 ml-5 text-xl font-bold">{{ $barber->name }}</p>
            </div>
            @else
            <div class="flex flex-row justify-center">
                <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gray-800 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <p class="text-gray-200 mt-3 ml-5 text-xl font-bold">{{ $barber->name }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Informações do Barbeiro -->
    <div class="container mx-auto px-4 mt-20">
        <!-- Serviços -->
        <div class="mt-8">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Serviços</h2>
            <div class="swiper servicesSwiper">
                <div class="swiper-wrapper">
                    @foreach($barber->services as $service)
                        <div class="swiper-slide w-72">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden h-full">
                                <div class="w-full h-48">
                                    @if($service->image)
                                        <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold text-gray-800">{{ $service->name }}</h3>
                                    <p class="text-gray-600 mt-2">{{ $service->description }}</p>
                                    <p class="text-lg font-bold text-gray-800 mt-2">{{ $service->formatted_price }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
    <script>
        new Swiper('.servicesSwiper', {
            slidesPerView: 'auto',
            spaceBetween: 24,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                }
            }
        });
    </script>
</body>
</html>