<section class="py-5 bg-light" id="contact">
    <div class="container py-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Hubungi Kami</h2>
                <p class="text-muted mb-4">Ada pertanyaan atau ingin menjalin kerjasama? Jangan ragu untuk menghubungi
                    kami!</p>

                <div class="d-flex align-items-center mb-4">
                    <div class="bg-white rounded-circle p-3 shadow-sm me-4">
                        <i class="fas fa-map-marker-alt text-primary-cacha"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Alamat</h5>
                        <p class="mb-0 text-muted">Jl. Pantai Barat No. 123, Pangandaran, Jawa Barat</p>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="bg-white rounded-circle p-3 shadow-sm me-4">
                        <i class="fas fa-phone-alt text-primary-cacha"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Telepon</h5>
                        <p class="mb-0 text-muted">+62 812-3456-7890</p>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="bg-white rounded-circle p-3 shadow-sm me-4">
                        <i class="fas fa-envelope text-primary-cacha"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Email</h5>
                        <p class="mb-0 text-muted">info@cachasnack.id</p>
                    </div>
                </div>

                <div class="mt-5">
                    <h5 class="fw-bold mb-3">Ikuti Kami</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="social-icon">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-tiktok text-white"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-youtube text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 rounded-4 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="fw-bold mb-4">Kirim Pesan</h3>
                        <div id="email-status"></div>
                        <form id="contact-form">
                            <input type="hidden" name="store_name" value="Toko Cacha">
                            <input type="hidden" name="store_email" value="amaralfatah.me@gmail.com">

                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium">Nama Lengkap</label>
                                <input type="text" class="form-control form-control-lg rounded-pill border-0 bg-light"
                                       id="name" name="name" placeholder="Masukkan nama lengkap">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium">Email</label>
                                <input type="email" class="form-control form-control-lg rounded-pill border-0 bg-light"
                                       id="email" name="email" placeholder="Masukkan email">
                            </div>
                            <div class="mb-4">
                                <label for="message" class="form-label fw-medium">Pesan</label>
                                <textarea class="form-control border-0 bg-light rounded-4" id="message" name="message" rows="5"
                                          placeholder="Tulis pesan Anda di sini..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary-cacha btn-lg rounded-pill w-100">
                                Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
<script>
    (function() {
        emailjs.init("QdkV99AroXCn9Zeji");
    })();

    document.addEventListener('DOMContentLoaded', function() {
        const contactForm = document.getElementById('contact-form');
        const statusDiv = document.getElementById('email-status');

        contactForm.addEventListener('submit', function(event) {
            event.preventDefault();

            // Show loading state
            statusDiv.innerHTML = '<div class="alert alert-info">Mengirim pesan...</div>';

            // Prepare template parameters
            const templateParams = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                message: document.getElementById('message').value,
                site_name: contactForm.store_name.value,
                our_email: contactForm.store_email.value,
            };

            // Send email using EmailJS
            emailjs.send('service_6sfihlo', 'template_22525qd', templateParams)
                .then(function(response) {
                    console.log('SUCCESS!', response.status, response.text);
                    statusDiv.innerHTML = '<div class="alert alert-success">Terima kasih! Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.</div>';
                    contactForm.reset();
                }, function(error) {
                    console.log('FAILED...', error);
                    statusDiv.innerHTML = '<div class="alert alert-danger">Maaf, terjadi kesalahan saat mengirim pesan. Silakan coba lagi nanti.</div>';
                });
        });
    });
</script>
