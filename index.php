<?php
session_start();
require_once 'includes/functions.php';

$isLoggedIn = isset($_SESSION['user_id']);
$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Don Pablo Lorenzo Memorial High School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <img src="images/logo.png" alt="School Logo" class="w-12 h-12">
                <h1 class="text-2xl font-bold text-gray-800">Don Pablo Lorenzo Memorial High School</h1>
            </div>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="#home" class="text-gray-600 hover:text-gray-800">Home</a></li>
                    <li><a href="#about" class="text-gray-600 hover:text-gray-800">About</a></li>
                    <li><a href="#announcements" class="text-gray-600 hover:text-gray-800">Announcements</a></li>
                    <li><a href="#events" class="text-gray-600 hover:text-gray-800">Events</a></li>
                    <li><a href="#contact" class="text-gray-600 hover:text-gray-800">Contact</a></li>
                </ul>
            </nav>
            <div>
                <?php if ($isLoggedIn): ?>
                    <a href="logout.php?csrf_token=<?php echo $csrf_token; ?>"
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Logout</a>
                <?php else: ?>
                    <a href="login.php"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <section id="home" class="bg-blue-600 text-white py-20">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl font-bold mb-4">Welcome to Don Pablo Lorenzo Memorial High School</h2>
                <p class="text-xl mb-8">Empowering minds, shaping futures</p>
                <a href="#about" class="bg-white text-blue-600 hover:bg-blue-100 font-bold py-2 px-4 rounded">Learn
                    More</a>
            </div>
        </section>

        <section id="about" class="py-20">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">About Us</h2>
                <div class="flex flex-col md:flex-row items-center gap-12">
                    <div class="md:w-1/2">
                        <p class="text-lg mb-6">
                            Don Pablo Lorenzo Memorial High School is committed to providing quality education and
                            fostering a nurturing environment for our students. Our dedicated faculty and staff work
                            tirelessly to ensure that each student reaches their full potential.
                        </p>
                        <ul class="space-y-2">
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Excellence in
                                academics</li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Holistic
                                development</li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Community
                                engagement</li>
                        </ul>
                    </div>
                    <div class="md:w-1/2">
                        <img src="images/About.jpg" alt="School Building" class="rounded-lg shadow-lg w-full">
                    </div>
                </div>
            </div>
        </section>

        <section id="announcements" class="bg-gray-100 py-20">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Announcements</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-2">School Reopening</h3>
                        <p>We are excited to announce that the school will reopen on August 15th, 2023. Please ensure
                            all necessary preparations are made.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-2">New Online Learning Platform</h3>
                        <p>We have launched a new online learning platform to support our students' education. Login
                            details will be sent to all parents and students soon.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-2">Parent-Teacher Conference</h3>
                        <p>The annual parent-teacher conference is scheduled for September 5th, 2023. More details will
                            be shared via email.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="events" class="py-20">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Upcoming Events</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-600 text-white p-3 rounded-lg text-center mr-4">
                                <div class="text-2xl font-bold">15</div>
                                <div class="text-sm uppercase">Aug</div>
                            </div>
                            <h3 class="text-xl font-semibold">First Day of School</h3>
                        </div>
                        <p>Welcome back all students for the new academic year!</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-600 text-white p-3 rounded-lg text-center mr-4">
                                <div class="text-2xl font-bold">05</div>
                                <div class="text-sm uppercase">Sep</div>
                            </div>
                            <h3 class="text-xl font-semibold">Parent-Teacher Conference</h3>
                        </div>
                        <p>Annual meeting for parents and teachers to discuss student progress.</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-600 text-white p-3 rounded-lg text-center mr-4">
                                <div class="text-2xl font-bold">20</div>
                                <div class="text-sm uppercase">Oct</div>
                            </div>
                            <h3 class="text-xl font-semibold">Science Fair</h3>
                        </div>
                        <p>Students showcase their innovative science projects.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="bg-gray-100 py-20">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Contact Us</h2>
                <div class="flex flex-col md:flex-row gap-12">
                    <div class="md:w-1/2">
                        <h3 class="text-2xl font-semibold mb-4">Get in Touch</h3>
                        <div class="space-y-4">
                            <p class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> 123 School Street,
                                City, State 12345</p>
                            <p class="flex items-center"><i class="fas fa-phone mr-2"></i> (123) 456-7890</p>
                            <p class="flex items-center"><i class="fas fa-envelope mr-2"></i> info@dplmhs.edu</p>
                        </div>
                        <div class="flex space-x-4 mt-6">
                            <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-blue-400 hover:text-blue-600"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-pink-600 hover:text-pink-800"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    <form class="md:w-1/2 space-y-4">
                        <input type="text" placeholder="Your Name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="email" placeholder="Your Email" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <textarea placeholder="Your Message" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 h-32"></textarea>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Send
                            Message</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p>&copy; 2023 Don Pablo Lorenzo Memorial High School. All rights reserved.</p>
                <ul class="flex space-x-4 mt-4 md:mt-0">
                    <li><a href="#" class="hover:text-gray-300">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-gray-300">Terms of Service</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-lg w-96 max-w-md mx-auto">
            <h2 class="text-2xl font-bold mb-4 text-center">Login</h2>
            <form action="index.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" name="login"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">Login</button>
                    <a href="#"
                        class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800 transition duration-300">Forgot
                        Password?</a>
                </div>
            </form>
            <?php if ($loginError): ?>
                <p class="text-red-500 text-xs italic mt-4"><?php echo htmlspecialchars($loginError); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Login Modal functionality
        const loginBtn = document.getElementById('loginBtn');
        const loginModal = document.getElementById('loginModal');

        function showLoginModal() {
            loginModal.classList.remove('hidden');
            loginModal.classList.add('flex');
        }

        loginBtn.addEventListener('click', showLoginModal);

        loginModal.addEventListener('click', (e) => {
            if (e.target === loginModal) {
                loginModal.classList.remove('flex');
                loginModal.classList.add('hidden');
            }
        });

        // Show login modal if there's an error
        <?php if ($loginError): ?>
            showLoginModal();
        <?php endif; ?>

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();

                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>