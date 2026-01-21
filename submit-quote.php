<?php
// Quote form submission handler
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $service = sanitizeInput($_POST['service'] ?? '');
    $projectDate = sanitizeInput($_POST['projectDate'] ?? '');
    $location = sanitizeInput($_POST['location'] ?? '');
    $sqFootage = sanitizeInput($_POST['sqFootage'] ?? '');
    $additionalInfo = sanitizeInput($_POST['additionalInfo'] ?? '');
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($service) || empty($projectDate) || empty($location)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }
    
    // Validate email
    if (!isValidEmail($email)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }
    
    $companyEmail = "mdflooringspecialist@gmail.com";
    
    // Prepare service name for display
    $serviceNames = [
        'industrial-concrete' => 'Industrial Concrete Pours',
        'concrete-flooring' => 'Concrete Flooring',
        'epoxy-flooring' => 'Epoxy Flooring',
        'pouring-preparation' => 'Pouring Preparation',
        'multiple' => 'Multiple Services',
        'not-sure' => 'Not Sure / Need Consultation'
    ];
    $serviceName = $serviceNames[$service] ?? $service;
    
    // Email to company (admin notification)
    $companySubject = "New Quote Request from " . $name;
    $companyMessage = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #ff6b35; color: white; padding: 20px; text-align: center; }
            .content { background-color: #f5f5f0; padding: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #1a1a1a; }
            .value { margin-top: 5px; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Quote Request</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Name:</div>
                    <div class='value'>" . $name . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'>" . $email . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Phone:</div>
                    <div class='value'>" . $phone . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Service Requested:</div>
                    <div class='value'>" . $serviceName . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Desired Start Date:</div>
                    <div class='value'>" . $projectDate . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Project Location:</div>
                    <div class='value'>" . $location . "</div>
                </div>
                " . (!empty($sqFootage) ? "
                <div class='field'>
                    <div class='label'>Square Footage:</div>
                    <div class='value'>" . $sqFootage . "</div>
                </div>
                " : "") . "
                " . (!empty($additionalInfo) ? "
                <div class='field'>
                    <div class='label'>Additional Information:</div>
                    <div class='value'>" . nl2br($additionalInfo) . "</div>
                </div>
                " : "") . "
            </div>
            <div class='footer'>
                <p>MD Flooring Services - Quote Management System</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Email headers for company
    $companyHeaders = "MIME-Version: 1.0" . "\r\n";
    $companyHeaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $companyHeaders .= "From: MD Flooring Website <noreply@mdflooring.com>" . "\r\n";
    $companyHeaders .= "Reply-To: " . $email . "\r\n";
    
    // Email to customer (confirmation)
    $customerSubject = "Quote Request Received - MD Flooring Services";
    $customerMessage = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #ff6b35; color: white; padding: 30px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; letter-spacing: 2px; }
            .content { background-color: #f5f5f0; padding: 30px; }
            .content p { margin-bottom: 15px; }
            .highlight { background-color: #fff; padding: 15px; border-left: 4px solid #ff6b35; margin: 20px 0; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            .button { display: inline-block; background-color: #ff6b35; color: white; padding: 12px 30px; text-decoration: none; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>MD FLOORING SERVICES</h1>
            </div>
            <div class='content'>
                <h2 style='color: #1a1a1a;'>Thank You for Your Quote Request</h2>
                <p>Dear " . $name . ",</p>
                <p>We have received your quote request for <strong>" . $serviceName . "</strong> and appreciate your interest in MD Flooring Services.</p>
                
                <div class='highlight'>
                    <p style='margin: 0;'><strong>What happens next?</strong></p>
                    <p style='margin: 10px 0 0 0;'>Our team will review your request and contact you within 24 hours to discuss your project details and provide a detailed quote.</p>
                </div>
                
                <p><strong>Your Request Summary:</strong></p>
                <ul>
                    <li>Service: " . $serviceName . "</li>
                    <li>Desired Start Date: " . $projectDate . "</li>
                    <li>Location: " . $location . "</li>
                </ul>
                
                <p>If you have any immediate questions or need to speak with someone urgently, please call us at <strong>+447852911636</strong>.</p>
                
                <p>We look forward to working with you!</p>
                
                <p style='margin-top: 30px;'>
                    Best regards,<br>
                    <strong>MD Flooring Services Team</strong><br>
                    Email: mdflooringspecialist@gmail.com<br>
                    Phone: +447852911636
                </p>
            </div>
            <div class='footer'>
                <p>&copy; 2026 MD Flooring Services. All rights reserved.</p>
                <p>Licensed & Insured | Industrial Concrete & Epoxy Specialists</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Email headers for customer
    $customerHeaders = "MIME-Version: 1.0" . "\r\n";
    $customerHeaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $customerHeaders .= "From: MD Flooring Services <mdflooringspecialist@gmail.com>" . "\r\n";
    $customerHeaders .= "Reply-To: mdflooringspecialist@gmail.com" . "\r\n";
    
    // Send emails
    $companyEmailSent = mail($companyEmail, $companySubject, $companyMessage, $companyHeaders);
    $customerEmailSent = mail($email, $customerSubject, $customerMessage, $customerHeaders);
    
    if ($companyEmailSent && $customerEmailSent) {
        echo json_encode([
            'success' => true, 
            'message' => 'Thank you! Your quote request has been received. Please check your email for confirmation.'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'There was an error sending your request. Please try again or call us directly.'
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
