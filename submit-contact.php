<?php
// Contact form submission handler
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
    $firstName = sanitizeInput($_POST['firstName'] ?? '');
    $lastName = sanitizeInput($_POST['lastName'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $company = sanitizeInput($_POST['company'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    $fullName = $firstName . ' ' . $lastName;
    
    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }
    
    // Validate email
    if (!isValidEmail($email)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }
    
    $companyEmail = "mdflooringspecialist@gmail.com";
    
    // Prepare subject name for display
    $subjectNames = [
        'quote' => 'Request a Quote',
        'question' => 'General Question',
        'existing' => 'Existing Project',
        'emergency' => 'Emergency/Urgent',
        'other' => 'Other'
    ];
    $subjectName = $subjectNames[$subject] ?? $subject;
    
    // Email to company (admin notification)
    $companySubject = "New Contact Message: " . $subjectName . " - " . $fullName;
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
            .message-box { background-color: white; padding: 15px; border-left: 4px solid #ff6b35; margin-top: 10px; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            .urgent { background-color: #ffebee; border-left-color: #d32f2f; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Message</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Name:</div>
                    <div class='value'>" . $fullName . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'>" . $email . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Phone:</div>
                    <div class='value'>" . $phone . "</div>
                </div>
                " . (!empty($company) ? "
                <div class='field'>
                    <div class='label'>Company:</div>
                    <div class='value'>" . $company . "</div>
                </div>
                " : "") . "
                <div class='field'>
                    <div class='label'>Subject:</div>
                    <div class='value'>" . $subjectName . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Message:</div>
                    <div class='message-box " . ($subject === 'emergency' ? 'urgent' : '') . "'>
                        " . nl2br($message) . "
                    </div>
                </div>
            </div>
            <div class='footer'>
                <p>MD Flooring Services - Contact Management System</p>
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
    $customerSubject = "Message Received - MD Flooring Services";
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
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>MD FLOORING SERVICES</h1>
            </div>
            <div class='content'>
                <h2 style='color: #1a1a1a;'>Thank You for Contacting Us</h2>
                <p>Dear " . $firstName . ",</p>
                <p>We have received your message and appreciate you reaching out to MD Flooring Services.</p>
                
                <div class='highlight'>
                    <p style='margin: 0;'><strong>What happens next?</strong></p>
                    <p style='margin: 10px 0 0 0;'>Our team will review your message and respond within 24 hours" . ($subject === 'emergency' ? ". We understand this is urgent and will prioritize your request" : "") . ".</p>
                </div>
                
                <p><strong>Your Message Summary:</strong></p>
                <ul>
                    <li>Subject: " . $subjectName . "</li>
                    <li>Contact Email: " . $email . "</li>
                    <li>Contact Phone: " . $phone . "</li>
                </ul>
                
                <p>If you need immediate assistance, please call us at <strong>+447852911636</strong>.</p>
                
                <p>Thank you for considering MD Flooring Services for your project!</p>
                
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
            'message' => 'Thank you! Your message has been sent. Please check your email for confirmation.'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'There was an error sending your message. Please try again or call us directly.'
        ]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
