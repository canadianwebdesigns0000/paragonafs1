<?php
/**
 * Spouse confirmation email template (Jejy)
 *
 * Expects at least:
 * - $spouse_firstname
 * - $spouse_lastname
 * - $firstname (applicant's first name)
 * - $lastName (applicant's last name)
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
            <h1>Thank you for your submission</h1>
        </div>
        <div class="content">
            <p>Dear <?= htmlspecialchars($spouse_firstname ?? '') ?> <?= htmlspecialchars($spouse_lastname ?? '') ?>,</p>
            <p>We have received <?= htmlspecialchars($firstname ?? '') ?> <?= htmlspecialchars($lastName ?? '') ?>'s tax submission, which includes your information and documents. Thank you for providing your details as part of this joint tax filing.</p>
            <p>Our experienced tax professionals will carefully review all submitted information, including yours, to ensure accuracy and completeness. We understand the importance of handling your tax matters with the utmost care and attention to detail.</p>
            <p><strong>What happens next?</strong></p>
            <ul>
                <li>Our tax professionals will thoroughly review all submitted information, including your documents and details</li>
                <li>We will contact you or <?= htmlspecialchars($firstname ?? 'the applicant') ?> within 48 hours if we need any additional information</li>
                <li>You will receive updates on the status of your tax filing</li>
            </ul>
            <p><strong>Important Notes:</strong></p>
            <ul>
                <li>Please check your SPAM folder if you don't see our emails</li>
                <li>Keep this confirmation for your records</li>
                <li>If you have any questions or concerns, please don't hesitate to contact us using the information below</li>
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
