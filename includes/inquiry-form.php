<?php
$contact_query = mysqli_query($conn, "SELECT map FROM contacts ORDER BY id DESC LIMIT 1");
$contactInfo = mysqli_fetch_assoc($contact_query);
?>

<section class="custom-inquiry-section">
    <div class="container">
        <!-- Center Header -->
        <div class="text-center mb-5">
            <span class="text-uppercase" style="color: #dfb122; font-weight: 600; letter-spacing: 1.5px; font-size: 0.9rem;">GET IN TOUCH</span>
            <h2 class="mt-2" style="font-weight: 700; font-size: 2.5rem; color: #222;">Request a Callback or Quote</h2>
            <div style="width: 60px; height: 3px; background-color: #dfb122; margin: 15px auto 0;"></div>
        </div>

        <div class="row g-0 shadow-lg rounded-3">
            
            <!-- Left Side: Dynamic Map -->
            <div class="col-lg-6">
                <div class="map-wrapper">
                    <?php 
                        // If map column has a link, put it in iframe src attribute
                        if (!empty($contactInfo['map'])) {
                            echo '<iframe src="' . htmlspecialchars($contactInfo['map']) . '" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
                        } else {
                            // Default Fallback map (Ghaziabad / Delhi NCR)
                            echo '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1m2!1s0x390cf1bb41c50fdf%3A0xe6f06fd26a7798ba!2sGhaziabad%2C%20Uttar%20Pradesh!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
                        }
                    ?>
                </div>
            </div>

            <!-- Right Side: Dark Form Section -->
            <div class="col-lg-6">
                <div class="custom-dark-card p-4 p-md-5">
                    <h3 class="text-white mb-2" style="font-weight: 600; font-size: 1.8rem;">Send Your Inquiry</h3>
                    <p class="mb-4" style="color: #94a3b8; font-size: 0.95rem;">Fill out the form below, and our export experts will get back to you promptly.</p>

                    <form action="inquiry-process.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control custom-dark-input" placeholder="Full Name *" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control custom-dark-input" placeholder="Email Address *" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="phone" class="form-control custom-dark-input" placeholder="Phone / WhatsApp *" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="subject" class="form-control custom-dark-input" placeholder="Subject / Product Name *" required>
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control custom-dark-input" placeholder="Tell us about your requirements... *" style="height: 120px; resize: none;" required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn w-100 btn-golden py-3 text-uppercase">
                                    Submit Inquiry <i class="bi bi-send-fill ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>