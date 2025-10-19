<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $barber->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .swiper {
            width: 100%;
            padding: 20px 0;
        }

        .swiper-slide {
            width: auto;
        }

        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .whatsapp-float:hover {
            background-color: #128C7E;
            transform: scale(1.1);
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Banner -->
    <div class="w-full h-64 relative">
        @if ($barber->image_banner)
            <img src="{{ asset('storage/' . $barber->image_banner) }}" alt="Banner"
                class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-r from-gray-700 to-gray-900 flex items-center justify-center">
                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
        @endif
        <!-- Logo/Foto do Barbeiro -->
        <div class="absolute -bottom-16 left-8">
            @if ($barber->image_logo)
                <div class="flex flex-row justify-center">
                    <div
                        class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gray-800 flex items-center justify-center">
                        <img src="{{ asset('storage/' . $barber->image_logo) }}" alt="{{ $barber->name }}"
                            class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">

                    </div>
                    <div class="flex flex-col justify-center">
                        <p class="text-gray-200 mt-3 ml-5 text-xl font-bold">{{ $barber->name }}</p>
                        <button id="openModalBtn" class="bg-green-500 ml-5 text-white px-4 py-2 rounded-full mt-2"> <i class="fas fa-calendar-plus mr-1"></i> Agendar</button>
                    </div>
                </div>
            @else
                <div class="flex flex-row justify-center">
                    <div
                        class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gray-800 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
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
                    @foreach ($barber->services as $service)
                        <div class="swiper-slide w-72">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden h-full">
                                <div class="w-full h-48">
                                    @if ($service->image)
                                        @php
                                            $serviceImageSrc = filter_var($service->image, FILTER_VALIDATE_URL)
                                                ? $service->image
                                                : asset('storage/' . $service->image);
                                        @endphp
                                        <img src="{{ $serviceImageSrc }}" alt="{{ $service->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
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
            <div class="text-center mt-8">
                <button id="openModalBtnServices" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full font-bold transition duration-300 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <i class="fas fa-calendar-plus mr-2"></i> Agendar Serviço
                </button>
            </div>
        </div>
    </div>

    <!-- Botão Flutuante WhatsApp -->
    <a href="https://wa.me/{{ $barber->phone ?? '5511999999999' }}" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
    <!-- Modal de Agendamento -->
    <div id="appointmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">Agendar com {{ $barber->name }}</h3>
                    <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Formulário de Agendamento -->
                <form id="appointmentForm">
                    <!-- Campo oculto para o ID do barbeiro -->
                    <input type="hidden" id="barber_id" name="barber_id" value="{{ $barber->id }}">
                    
                    <!-- Seleção de Serviço -->
                    <div class="mb-4">
                        <label for="service_id" class="block text-sm font-medium text-gray-700 mb-1">Serviço</label>
                        <select id="service_id" name="service_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Selecione um serviço</option>
                            @foreach ($barber->services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }} - {{ $service->formatted_price }}</option>
                            @endforeach
                        </select>
                        <div id="service_id_error" class="text-red-500 text-sm mt-1 hidden">Por favor, selecione um serviço.</div>
                    </div>
                    
                    <!-- Seleção de Data -->
                    <div class="mb-4">
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                        <input type="date" id="date" name="date" min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <div id="date_error" class="text-red-500 text-sm mt-1 hidden">Por favor, selecione uma data.</div>
                    </div>
                    
                    <!-- Seleção de Horário -->
                    <div class="mb-4">
                        <label for="time" class="block text-sm font-medium text-gray-700 mb-1">Horário</label>
                        <input type="hidden" id="time" name="time" value="">
                        <div id="time_slots" class="grid grid-cols-3 gap-2"></div>
                        <div id="no_times" class="text-gray-600 text-sm mt-2 hidden">Nenhum horário disponível para a data selecionada.</div>
                        <div id="time_error" class="text-red-500 text-sm mt-1 hidden">Por favor, selecione um horário.</div>
                    </div>
                    
                    <!-- Informações de Contato -->
                    <div class="mb-4">
                        <label for="client_name" class="block text-sm font-medium text-gray-700 mb-1">Seu Nome</label>
                        <input type="text" id="client_name" name="client_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <div id="client_name_error" class="text-red-500 text-sm mt-1 hidden">Por favor, informe seu nome.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="client_phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="tel" id="client_phone" name="client_phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <div id="client_phone_error" class="text-red-500 text-sm mt-1 hidden">Por favor, informe seu telefone.</div>
                    </div>
                    
                    <!-- Botão de Confirmação -->
                    <button type="button" id="confirmAppointment" class="w-full bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition duration-200">
                        Confirmar Agendamento
                    </button>
                    
                    <!-- Mensagem de sucesso/erro -->
                    <div id="appointment_success" class="mt-4 p-3 bg-green-100 text-green-700 rounded-md hidden">
                        Agendamento realizado com sucesso!
                    </div>
                    <div id="appointment_error" class="mt-4 p-3 bg-red-100 text-red-700 rounded-md hidden">
                        Ocorreu um erro ao realizar o agendamento. Tente novamente.
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Inicialização do Swiper
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
        
        // Controle do Modal
        const modal = document.getElementById('appointmentModal');
        const openModalBtn = document.getElementById('openModalBtn');
        const openModalBtnServices = document.getElementById('openModalBtnServices');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const confirmBtn = document.getElementById('confirmAppointment');
        const serviceSelect = document.getElementById('service_id');
        const dateInput = document.getElementById('date');
        const timeInput = document.getElementById('time');
        const timeSlotsContainer = document.getElementById('time_slots');
        const noTimes = document.getElementById('no_times');
    
        function setConfirmState(enabled) {
            if (enabled) {
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
                confirmBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                confirmBtn.textContent = 'Confirmar Agendamento';
            } else {
                confirmBtn.disabled = true;
                confirmBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                confirmBtn.classList.add('bg-gray-400', 'cursor-not-allowed', 'opacity-50');
                confirmBtn.textContent = 'Agendando...';
            }
        }
    
        function clearTimeSelection() {
            timeInput.value = '';
            document.querySelectorAll('#time_slots .time-slot').forEach(s => s.classList.remove('bg-green-500', 'text-white'));
        }
    
        function handleTimeSlotClick(el) {
            document.querySelectorAll('#time_slots .time-slot').forEach(s => s.classList.remove('bg-green-500', 'text-white'));
            el.classList.add('bg-green-500', 'text-white');
            timeInput.value = el.getAttribute('data-time');
            setConfirmState(true);
        }
    
        function renderTimeSlots(times) {
            timeSlotsContainer.innerHTML = '';
            clearTimeSelection();
    
            if (!times || times.length === 0) {
                noTimes.classList.remove('hidden');
                setConfirmState(false);
                return;
            }
    
            noTimes.classList.add('hidden');
            setConfirmState(false);
    
            times.forEach(t => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'time-slot px-3 py-2 border border-gray-300 rounded-md text-center hover:bg-green-100';
                btn.setAttribute('data-time', t);
                btn.textContent = t;
                btn.addEventListener('click', () => handleTimeSlotClick(btn));
                timeSlotsContainer.appendChild(btn);
            });
        }
    
        function loadAvailableTimes() {
            const serviceId = serviceSelect.value;
            const date = dateInput.value;
            const barberId = document.getElementById('barber_id').value;
    
            if (!serviceId || !date) {
                renderTimeSlots([]);
                return;
            }
    
            fetch(`/barbers/${barberId}/availability?date=${encodeURIComponent(date)}&service_id=${encodeURIComponent(serviceId)}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(resp => resp.json())
            .then(data => {
                renderTimeSlots(data.available_times || []);
            })
            .catch(() => {
                renderTimeSlots([]);
            });
        }
    
        // Abrir Modal
        openModalBtn.addEventListener('click', function() {
            modal.classList.remove('hidden');
            setConfirmState(false);
            loadAvailableTimes();
        });
    
        openModalBtnServices.addEventListener('click', function() {
            modal.classList.remove('hidden');
            setConfirmState(false);
            loadAvailableTimes();
        });
    
        // Fechar Modal
        closeModalBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    
        // Atualizar horários ao mudar serviço/data
        serviceSelect.addEventListener('change', loadAvailableTimes);
        dateInput.addEventListener('change', loadAvailableTimes);

        // Função para mostrar mensagens de erro
        function showError(fieldId, show) {
            const errorElement = document.getElementById(fieldId + '_error');
            if (show) {
                errorElement.classList.remove('hidden');
            } else {
                errorElement.classList.add('hidden');
            }
        }
        
        // Função para validar o formulário
        function validateForm() {
            const serviceId = document.getElementById('service_id').value;
            const date = document.getElementById('date').value;
            const time = document.getElementById('time').value;
            const clientName = document.getElementById('client_name').value;
            const clientPhone = document.getElementById('client_phone').value;
            
            let isValid = true;
            
            // Validar serviço
            if (!serviceId) {
                showError('service_id', true);
                isValid = false;
            } else {
                showError('service_id', false);
            }
            
            // Validar data
            if (!date) {
                showError('date', true);
                isValid = false;
            } else {
                showError('date', false);
            }
            
            // Validar horário
            if (!time) {
                showError('time', true);
                isValid = false;
            } else {
                showError('time', false);
            }
            
            // Validar nome
            if (!clientName) {
                showError('client_name', true);
                isValid = false;
            } else {
                showError('client_name', false);
            }
            
            // Validar telefone
            if (!clientPhone) {
                showError('client_phone', true);
                isValid = false;
            } else {
                showError('client_phone', false);
            }
            
            return isValid;
        }
        
        // Confirmação de agendamento
        confirmBtn.addEventListener('click', function() {
            // Evita múltiplos envios
            setConfirmState(false);
    
            document.getElementById('appointment_success').classList.add('hidden');
            document.getElementById('appointment_error').classList.add('hidden');
    
            // Validar formulário
            if (!validateForm()) {
                setConfirmState(true);
                return;
            }
    
            const appointmentData = {
                barber_id: document.getElementById('barber_id').value,
                service_id: document.getElementById('service_id').value,
                date: document.getElementById('date').value,
                time: document.getElementById('time').value,
                client_name: document.getElementById('client_name').value,
                client_phone: document.getElementById('client_phone').value
            };
    
            fetch('{{ route("appointment.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(appointmentData)
            })
            .then(response => response.json().then(data => ({ ok: response.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.success) {
                    document.getElementById('appointment_success').classList.remove('hidden');
                    document.getElementById('appointmentForm').reset();
                    document.getElementById('time').value = '';
                    document.querySelectorAll('#time_slots .time-slot').forEach(s => s.classList.remove('bg-green-500', 'text-white'));
                    setTimeout(() => { document.getElementById('appointmentModal').classList.add('hidden'); }, 2000);
                } else {
                    document.getElementById('appointment_error').classList.remove('hidden');
                    document.getElementById('appointment_error').textContent = data.message || 'Horário indisponível.';
                }
            })
            .catch(() => {
                document.getElementById('appointment_error').classList.remove('hidden');
            })
            .finally(() => {
                // Reabilita o botão para nova tentativa (se necessário)
                setConfirmState(true);
            });
        });
    </script>
</body>

</html>
