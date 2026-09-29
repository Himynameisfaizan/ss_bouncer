<?php
// Ensure $conn is available before this file is included
$inq_contact_query = $conn->query("SELECT map FROM contacts ORDER BY id DESC LIMIT 1");
$inq_contactInfo = $inq_contact_query ? $inq_contact_query->fetch_assoc() : null;
?>

<section class="custom-inquiry-section py-5 bg-light-custom border-top border-bottom">
    <div class="container py-4">
        <!-- Center Header -->
        <div class="text-center mb-5">
            <span class="sub-heading text-secondary-accent fw-bold text-uppercase tracking-wider">Get In Touch</span>
            <h2 class="main-heading text-primary-dark fw-bold mt-2">Request a Callback or Quote</h2>
            <p class="text-muted mt-3">Fill out the form below, and our export experts will get back to you promptly.</p>
        </div>

        <div class="row g-0 bg-white shadow-lg rounded-4 overflow-hidden">
            
            <!-- Left Side: Dynamic Map -->
            <div class="col-lg-6 map-wrapper p-0">
                <?php 
                    if (!empty($inq_contactInfo['map'])) {
                        $mapData = trim($inq_contactInfo['map']);
                        if (strpos($mapData, '<iframe') !== false) {
                            $mapIframe = preg_replace('/width="[^"]+"/', 'width="100%"', $mapData);
                            $mapIframe = preg_replace('/height="[^"]+"/', 'height="100%"', $mapIframe);
                            echo $mapIframe;
                        } else {
                            echo '<iframe src="' . htmlspecialchars($mapData) . '" width="100%" height="100%" style="border:0; min-height: 450px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
                        }
                    } else {
                        echo '<div class="d-flex align-items-center justify-content-center h-100 bg-light" style="min-height: 450px;"><span class="text-muted">Map not available</span></div>';
                    }
                ?>
            </div>

            <!-- Right Side: Clean Form Section -->
            <div class="col-lg-6 p-4 p-md-5">
                <form action="contact.php" method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="name" id="inq_name" class="form-control custom-input" placeholder="Full Name" required>
                                <label for="inq_name">Full Name *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" name="email" id="inq_email" class="form-control custom-input" placeholder="Email Address" required>
                                <label for="inq_email">Email Address *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="tel" name="phone" id="inq_phone" class="form-control custom-input" placeholder="Phone / WhatsApp" required>
                                <label for="inq_phone">Phone / WhatsApp *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="interest" id="inq_subject" class="form-control custom-input" placeholder="Subject / Service" required>
                                <label for="inq_subject">Service Type *</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="message" id="inq_message" class="form-control custom-input" placeholder="Tell us about your requirements..." style="height: 120px;" required></textarea>
                                <label for="inq_message">Tell us about your requirements... *</label>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" name="submit_inquiry" class="btn btn-premium w-100 py-3 text-uppercase fw-bold rounded-3">
                                Submit Inquiry <i class="fa-solid fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>