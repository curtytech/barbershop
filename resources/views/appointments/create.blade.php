<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Horário - Barbershop</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <!-- Header/Navbar -->
    <header class="bg-black text-white shadow-lg">
        <div class="container mx-auto px-4 py-6 flex justify-between items-center">
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold">Barbershop</a>
            </div>
            <nav class="hidden md:flex space-x-8">
                <a href="/" class="hover:text-amber-400 transition">Home</a>
                <a href="#" class="hover:text-amber-400 transition">Serviços</a>
                <a href="#" class="hover:text-amber-400 transition">Galeria</a>
                <a href="#" class="hover:text-amber-400 transition">Contato</a>
            </nav>
            <div class="md:hidden">
                <button class="text-white focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Appointment Form Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-3xl font-bold text-center mb-8">Agende seu Horário</h2>
                
                <form action="{{ route('appointments.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Barbeiro -->
                        <div>
                            <label for="barber_id" class="block text-gray-700 font-medium mb-2">Escolha o Barbeiro</label>
                            <select id="barber_id" name="barber_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" required>
                                <option value="">Selecione um barbeiro</option>
                                @foreach($barbers as $barber)
                                    <option value="{{ $barber->id }}">{{ $barber->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Serviço -->
                        <div>
                            <label for="service_id" class="block text-gray-700 font-medium mb-2">Escolha o Serviço</label>
                            <select id="service_id" name="service_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" required>
                                <option value="">Selecione um serviço</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }} - R$ {{ number_format($service->price, 2, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Data -->
                        <div>
                            <label for="date" class="block text-gray-700 font-medium mb-2">Data</label>
                            <input type="date" id="date" name="date" min="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" required>
                        </div>
                        
                        <!-- Horário -->
                        <div>
                            <label for="time" class="block text-gray-700 font-medium mb-2">Horário</label>
                            <select id="time" name="time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" required>
                                <option value="">Selecione um horário</option>
                                <option value="09:00">09:00</option>
                                <option value="09:30">09:30</option>
                                <option value="10:00">10:00</option>
                                <option value="10:30">10:30</option>
                                <option value="11:00">11:00</option>
                                <option value="11:30">11:30</option>
                                <option value="14:00">14:00</option>
                                <option value="14:30">14:30</option>
                                <option value="15:00">15:00</option>
                                <option value="15:30">15:30</option>
                                <option value="16:00">16:00</option>
                                <option value="16:30">16:30</option>
                                <option value="17:00">17:00</option>
                                <option value="17:30">17:30</option>
                                <option value="18:00">18:00</option>
                                <option value="18:30">18:30</option>
                                <option value="19:00">19:00</option>
                            </select>
                        </div>
                        
                        <!-- Nome do Cliente -->
                        <div>
                            <label for="client_name" class="block text-gray-700 font-medium mb-2">Seu Nome</label>
                            <input type="text" id="client_name" name="client_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" required>
                        </div>
                        
                        <!-- Telefone do Cliente -->
                        <div>
                            <label for="client_phone" class="block text-gray-700 font-medium mb-2">Seu Telefone</label>
                            <input type="tel" id="client_phone" name="client_phone" placeholder="(00) 00000-0000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" required>
                        </div>
                    </div>
                    
                    <div class="mt-8 text-center">
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300 ease-in-out transform hover:scale-105 inline-flex items-center">
                            <i class="fas fa-calendar-check mr-2"></i> Confirmar Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Barbershop</h3>
                    <p class="mb-4">Seu estilo, nossa especialidade. Venha conhecer o melhor serviço de barbearia da cidade.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-white hover:text-amber-400"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white hover:text-amber-400"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white hover:text-amber-400"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Horário de Funcionamento</h3>
                    <ul class="space-y-2">
                        <li>Segunda a Sexta: 9h às 20h</li>
                        <li>Sábado: 9h às 18h</li>
                        <li>Domingo: Fechado</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Contato</h3>
                    <ul class="space-y-2">
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Rua da Barbearia, 123</li>
                        <li><i class="fas fa-phone mr-2"></i> (11) 99999-9999</li>
                        <li><i class="fas fa-envelope mr-2"></i> contato@barbershop.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p>&copy; 2023 Barbershop. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>