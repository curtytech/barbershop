<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbershop - Seu Estilo, Nossa Especialidade</title>
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
                <h1 class="text-2xl font-bold">Barbershop</h1>
                <img src="../../favicon.ico" class="mb-1.5 ml-3 h-20 w-auto object-contain" alt="Logo">
            </div>
            <nav class="hidden md:flex space-x-8">
                <a href="#" class="hover:text-amber-400 transition">Home</a>
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

    <!-- Hero Section -->
    <section class="relative bg-cover bg-center h-screen" style="background-image: url('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80')">
        <div class="absolute inset-0 bg-black bg-opacity-70"></div>
        <div class="container mx-auto px-4 h-full flex items-center relative z-10">
            <div class="text-white max-w-2xl">
                <h2 class="text-5xl font-bold mb-6">Estilo e Precisão em Cada Corte</h2>
                <p class="text-xl mb-8">Experimente o melhor serviço de barbearia da cidade com profissionais qualificados e ambiente exclusivo.</p>
                <a href="{{ route('appointments.create') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300 ease-in-out transform hover:scale-105 inline-flex items-center">
                    <i class="fas fa-calendar-alt mr-2"></i> Agendar Agora
                </a>
            </div>
        </div>
    </section>

    <!-- Services Preview -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">Nossos Serviços</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-gray-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="bg-amber-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-cut text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Corte de Cabelo</h3>
                    <p class="text-gray-600">Cortes modernos e clássicos para todos os estilos.</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="bg-amber-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shower text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Barba</h3>
                    <p class="text-gray-600">Modelagem e tratamento completo para sua barba.</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="bg-amber-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-spa text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Hidratação</h3>
                    <p class="text-gray-600">Tratamentos capilares para revitalizar seus cabelos.</p>
                </div>
                <div class="bg-gray-100 rounded-lg p-6 text-center hover:shadow-lg transition">
                    <div class="bg-amber-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-magic text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Combo</h3>
                    <p class="text-gray-600">Corte e barba com preço especial.</p>
                </div>
            </div>
            <div class="text-center mt-12">
                <a href="{{ route('appointments.create') }}" class="bg-black hover:bg-gray-800 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300 ease-in-out inline-flex items-center">
                    <i class="fas fa-calendar-check mr-2"></i> Agende seu Horário
                </a>
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