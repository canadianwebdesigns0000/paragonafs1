<?php
/**
 * Client confirmation email template (Jejy)
 *
 * Expects at least:
 * - $firstname
 * - $lastName
 */
?>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0284c7; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9fafb; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
        ul { padding-left: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Your Submission</h1>
        </div>
        <div class="content">
            <p>Dear <?= htmlspecialchars($firstname ?? '') ?> <?= htmlspecialchars($lastName ?? '') ?>,</p>
            <p>We have successfully received your tax information submission. Our team will review your documents and get back to you soon.</p>
            <p><strong>What happens next?</strong></p>
            <ul>
                <li>Our tax professionals will review your submission</li>
                <li>We will contact you within 48 hours if we need any additional information</li>
                <li>You will receive updates on the status of your tax filing</li>
            </ul>
            <p><strong>Important Notes:</strong></p>
            <ul>
                <li>Please check your SPAM folder if you don't see our emails</li>
                <li>Keep this confirmation for your records</li>
                <li>If you have any questions, please contact us using the information below</li>
            </ul>
            <p><strong>Contact Information:</strong></p>
            <p>
                Email: info@paragonafs.ca<br>
                Phone: +1 (416) 477 3359<br>
                Website: https://paragonafs.ca
            </p>
            <p>Thank you for choosing Paragon AFS!</p>
            <p>Best regards,<br>The Paragon AFS Team</p>
        </div>
        <div class="footer">
            <p>This is an automated confirmation email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>


