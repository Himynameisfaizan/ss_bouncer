<?php 
require_once 'config/connect.php';
$pageTitle = "Service Delivery & Deployment Policy | SS Bouncers";
include 'includes/header.php'; 
?>

<section class="page-header position-relative py-5">
    <div class="page-header-bg"></div>
    <div class="container position-relative z-2 py-4 text-center">
        <h1 class="display-4 fw-bold text-white mb-0">Delivery & Deployment Policy</h1>
    </div>
</section>

<section class="py-5 bg-light-custom">
    <div class="container py-5">
        <div class="row bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light">
            <div class="col-12 policy-content">
                <p class="text-muted mb-5"><strong>Last Updated:</strong> <?= date('F d, Y'); ?></p>

                <p class="text-muted">At <strong>SS Bouncers</strong>, our "delivery" refers to the prompt and professional deployment of our verified security personnel, bouncers, and housekeeping staff to your specified location. The following policy outlines our deployment timelines and replacement protocols.</p>
                
                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">1. Deployment Timelines</h4>
                <ul class="text-muted">
                    <li><strong>Standard Corporate & Residential Security:</strong> Once the service agreement is signed and the advance payment is processed, our standard deployment time for regular security guards is <strong>24 to 48 hours</strong>.</li>
                    <li><strong>Event Bouncers (Short-term):</strong> For events, parties, and VIP protection, bouncers can be deployed within <strong>12 to 24 hours</strong> of confirmation. Emergency deployments (under 12 hours) may incur additional rapid-response charges.</li>
                    <li><strong>Bulk Manpower Deployment:</strong> For industrial setups or large educational institutions requiring more than 15 guards, deployment will be carried out in phases over <strong>3 to 5 business days</strong> to ensure proper site briefing and orientation.</li>
                </ul>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">2. Reporting and Transport</h4>
                <p class="text-muted">Our personnel will report directly to the location specified in the contract 15 minutes prior to their shift timing. For locations outside standard city limits, travel allowances or transport arrangements must be mutually agreed upon in the service contract.</p>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">3. Replacement Policy (In Lieu of Returns)</h4>
                <p class="text-muted">Since we deal in human resource services, the concept of "returns" does not apply. However, we have a strict <strong>Replacement Policy</strong>:</p>
                <ul class="text-muted">
                    <li><strong>Performance Issues:</strong> If you are not satisfied with the conduct, vigilance, or performance of an assigned security guard or bouncer, you may request a replacement by contacting our control room.</li>
                    <li><strong>Turnaround Time:</strong> We guarantee a replacement of personnel within <strong>12 to 24 hours</strong> of a formal complaint, at no additional cost to the client.</li>
                    <li><strong>Absenteeism:</strong> In the rare event that an assigned guard falls sick or is absent, our quick-response team will deploy a backup guard to ensure your premises are never left unprotected.</li>
                </ul>

                <h4 class="mt-5 text-primary-dark fw-bold border-bottom pb-2">4. Client Verification Upon Deployment</h4>
                <p class="text-muted">Upon the arrival of our security personnel at your premises, the client or facility manager is required to verify the guard's official SS Bouncers ID card and deployment letter. This ensures absolute safety and proper protocol adherence.</p>

            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>