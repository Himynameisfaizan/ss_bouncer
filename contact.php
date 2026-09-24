<?php
include "admin/db-conn.php";
$pageTitle = "Contact Us";
include 'includes/header.php';
include 'includes/breadcrumb.php';

// 1. Form Submission Handle Karna
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_contact'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Inquiries table me data insert karna[cite: 1]
    $insert_query = "INSERT INTO inquiries (name, email, phone, subject, message) VALUES ('$name', '$email', '$phone', '$subject', '$message')";

    if (mysqli_query($conn, $insert_query)) {
        $success_msg = "Your message has been sent successfully. Our team will contact you shortly!";
    } else {
        $error_msg = "Something went wrong while sending your message. Please try again.";
    }
}

// 2. Contact Details Fetch Karna[cite: 1]
$contact_query = mysqli_query($conn, "SELECT * FROM contacts LIMIT 1");
$contact_data = mysqli_fetch_assoc($contact_query);

// Fallback values agar DB me data blank ho
$db_address = !empty($contact_data['address']) ? htmlspecialchars($contact_data['address']) : 'No.2, M.G. Road, Thiruvanmiyur, Chennai, Tamil Nadu - 600041.';
$db_email = !empty($contact_data['email']) ? htmlspecialchars($contact_data['email']) : 'srisaiss505@gmail.com';
$db_phone = !empty($contact_data['phone']) ? htmlspecialchars($contact_data['phone']) : '+91 72008 64976';
?>

<!-- Contact Info Section -->
<section class="section-padding bg-light-gray pb-0">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Get In <span>Touch</span></h2>
            <p class="text-muted">We are available 24/7 to answer your queries and provide the best security solutions.
            </p>
        </div>

        <div class="row g-4">
            <!-- Office Address -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="contact-info-card h-100">
                    <div class="contact-icon-wrapper">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4 class="fw-bold text-secondary mb-3">Office Location</h4>
                    <p class="text-muted mb-0"><?= $db_address; ?></p>
                </div>
            </div>

            <!-- Email Address -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-info-card h-100">
                    <div class="contact-icon-wrapper">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4 class="fw-bold text-secondary mb-3">Email Address</h4>
                    <p class="text-muted mb-0">
                        <a href="mailto:<?= $db_email; ?>" class="text-muted text-decoration-none"><?= $db_email; ?></a>
                        <br>Response within 24 hours.
                    </p>
                </div>
            </div>

            <!-- Phone Number -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="contact-info-card h-100">
                    <div class="contact-icon-wrapper">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h4 class="fw-bold text-secondary mb-3">Phone Number</h4>
                    <p class="text-muted mb-0">
                        <a href="tel:<?= $db_phone; ?>" class="text-muted text-decoration-none"><?= $db_phone; ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form & Map Section -->
<section class="section-padding bg-light-gray">
    <div class="container">
        <div class="row g-4 align-items-stretch">

            <!-- Map Area -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="map-container h-100">
                    <!-- Google Maps Embed -->
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3887.6108380011115!2d80.25389767505176!3d12.99672451432701!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a5267f36527872d%3A0x804b2dc6c6b4d6d9!2s2%2C%20Mahatma%20Gandhi%20Rd%2C%20Subramaniam%20Colony%2C%20Thiruvanmiyur%2C%20Chennai%2C%20Greater%20Chennai%2C%20Tamil%20Nadu%20600041!5e0!3m2!1sen!2sin!4v1787312799695!5m2!1sen!2sin"
                        width="100%" height="100%" style="border:0; min-height: 400px; border-radius: 8px;"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

            <!-- Form Area -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="quote-card p-4 p-md-5 h-100 bg-white rounded shadow-sm">
                    <h3 class="mb-4 fw-bold">Send a <span>Message</span></h3>

                    <!-- Success/Error Message Display -->
                    <?php if (!empty($success_msg)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> <?= $success_msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= $error_msg; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control p-3" placeholder="Full Name"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control p-3" placeholder="Email Address"
                                    required>
                            </div>
                            <div class="col-md-12">
                                <input type="text" name="phone" class="form-control p-3" placeholder="Phone Number"
                                    required pattern="[0-9+\-\s]+">
                            </div>
                            <div class="col-md-12">
                                <input type="text" name="subject" class="form-control p-3" placeholder="Subject"
                                    required>
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control p-3" rows="5"
                                    placeholder="Write your message here..." required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" name="submit_contact"
                                    class="btn btn-primary-custom w-100 fs-5 py-3">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>