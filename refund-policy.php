<?php 
require_once 'config/connect.php';
$pageTitle = "Cancellation & Refund Policy | SS Bouncers";
include 'includes/header.php'; 
?>

<section class="page-header position-relative py-5">
    <div class="page-header-bg"></div>
    <div class="container position-relative z-2 py-4 text-center">
        <h1 class="display-4 fw-bold text-white mb-0">Cancellation & Refund Policy</h1>
    </div>
</section>

<section class="py-5 bg-light-custom">
    <div class="container py-5">
        <div class="row bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light">
            <div class="col-12 policy-content">
                <p class="text-muted mb-5"><strong>Last Updated:</strong> <?= date('F d, Y'); ?></p>

                <p class="text-muted">At <strong>SS Bouncers</strong>, we strive to deliver highly professional and uninterrupted security and manpower services. Since our operations involve human resource allocation, planning, and deployment, our cancellation and refund policies are structured accordingly.</p>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">1. Cancellation of Event/Short-Term Deployments</h4>
                <p class="text-muted">If you have booked bouncers or security guards for a specific event (1 to 5 days deployment):</p>
                <ul class="text-muted">
                    <li><strong>48 Hours Prior:</strong> Cancellations made at least 48 hours before the reporting time are eligible for a full refund of the advance amount.</li>
                    <li><strong>Within 24 Hours:</strong> Cancellations made within 24 hours of the reporting time will incur a 50% cancellation fee to cover administrative and standby manpower costs.</li>
                    <li><strong>Post-Deployment:</strong> Once personnel have reported to the location, no cancellation or refund requests will be entertained for that specific shift.</li>
                </ul>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">2. Termination of Monthly/Annual Contracts</h4>
                <p class="text-muted">For clients on monthly or annual security contracts (Corporate, Residential, Industrial), services cannot be cancelled abruptly. A formal written notice must be served <strong>30 days in advance</strong> (or as per the signed service agreement) prior to the termination of services. Any advance payments covering the active notice period are non-refundable.</p>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">3. Refund Processing</h4>
                <p class="text-muted">Approved refunds for cancelled event bookings or excess payments will be processed electronically. It typically takes <strong>5 to 7 business days</strong> for the amount to reflect in your original payment method or bank account.</p>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">4. Disputed Services</h4>
                <p class="text-muted">If you are unsatisfied with the performance or conduct of a deployed guard, please contact our control room immediately. We do not offer refunds for executed shifts, but we will initiate an immediate replacement of the personnel within 12-24 hours without any additional replacement charges.</p>

            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>