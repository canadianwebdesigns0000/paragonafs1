<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

function sendEmail($to = [], $subject = '', $body = '', $attachments = []) {
    $mail = new PHPMailer(true);
    
    try {
        // DEBUG: Output to screen AND log
        echo "<pre style='background:#f0f0f0; padding:10px; border:1px solid #ccc;'>";
        echo "===== EMAIL DEBUG START =====\n";
        echo "Attachments received: " . print_r($attachments, true) . "\n";
        echo "Attachments count: " . count($attachments) . "\n";
        
        error_log("===== EMAIL DEBUG START =====");
        error_log("Attachments received: " . print_r($attachments, true));
        error_log("Attachments count: " . count($attachments));
        
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'mail.mnd.uvd.mybluehost.me';
        $mail->SMTPAuth = true;
        $mail->Username = 'testemail@mnd.uvd.mybluehost.me';
        $mail->Password = '@Nexus369';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->Timeout = 300; // 5 minutes timeout for large attachments

        // Sender & recipient
        $mail->setFrom('testemail@mnd.uvd.mybluehost.me', 'Paragon AFS');
        
        foreach ($to as $recipient) {
            $mail->addAddress($recipient);
        }

        // Handle attachments - support both simple paths and structured arrays
        $attachmentCount = 0;
        $totalSize = 0;
        if (!empty($attachments)) {
            foreach ($attachments as $index => $attachment) {
                debugLog("Processing attachment #{$index}:", $attachment);
                error_log("Processing attachment #{$index}: " . print_r($attachment, true));
                
                // Check if it's a structured array with 'path' and 'name'
                if (is_array($attachment)) {
                    $filePath = $attachment['path'] ?? '';
                    $fileName = $attachment['name'] ?? basename($filePath);
                    
                    debugLog("File path: {$filePath}");
                    debugLog("File name: {$fileName}");
                    debugLog("File exists: " . (file_exists($filePath) ? 'YES' : 'NO'));
                    
                    if (file_exists($filePath)) {
                        $fileSize = filesize($filePath);
                        $totalSize += $fileSize;
                        debugLog("File size: " . number_format($fileSize) . " bytes (" . round($fileSize/1024/1024, 2) . " MB)");
                    }
                    
                    error_log("File path: {$filePath}");
                    error_log("File exists: " . (file_exists($filePath) ? 'YES' : 'NO'));
                    
                    if (!empty($filePath) && file_exists($filePath)) {
                        try {
                            $mail->addAttachment($filePath, $fileName);
                            $attachmentCount++;
                            debugLog("✓ ATTACHED: {$fileName}");
                            error_log("✓ Attached: {$fileName} from {$filePath}");
                        } catch (Exception $attachEx) {
                            debugLog("✗ FAILED to attach {$fileName}: " . $attachEx->getMessage());
                            error_log("✗ Failed to attach {$fileName}: " . $attachEx->getMessage());
                        }
                    } else {
                        debugLog("✗ NOT ATTACHED - File not found: {$filePath}");
                        error_log("✗ Attachment not found: {$filePath}");
                    }
                } 
                // Handle simple string path (backward compatibility)
                elseif (is_string($attachment) && file_exists($attachment)) {
                    try {
                        $mail->addAttachment($attachment);
                        $attachmentCount++;
                        $fileSize = filesize($attachment);
                        $totalSize += $fileSize;
                        debugLog("✓ ATTACHED (string): {$attachment}");
                        error_log("✓ Attached (string): {$attachment}");
                    } catch (Exception $attachEx) {
                        debugLog("✗ FAILED to attach {$attachment}: " . $attachEx->getMessage());
                        error_log("✗ Failed to attach: " . $attachEx->getMessage());
                    }
                } else {
                    debugLog("✗ INVALID attachment", $attachment);
                    error_log("✗ Invalid attachment: " . print_r($attachment, true));
                }
            }

            debugLog('Attachments:' . print_r($mail->getAttachments(), true));
            debugLog("Finished processing attachments.");
        } else {
            debugLog("⚠ No attachments provided to sendEmail()");
            error_log("No attachments provided to sendEmail()");
        }
        
        debugLog("===== ATTACHMENT SUMMARY =====");
        debugLog("Total attachments added: {$attachmentCount}");
        debugLog("Total size: " . number_format($totalSize) . " bytes (" . round($totalSize/1024/1024, 2) . " MB)");
        debugLog("Server limit check: " . (($totalSize/1024/1024) > 25 ? "⚠ WARNING: Over 25MB!" : "✓ Within limits"));
        
        error_log("Total attachments added: {$attachmentCount}");
        error_log("===== EMAIL DEBUG END =====");

        // Content - use provided HTML body
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body); // fallback plain text

        debugLog("Attempting to send email...");
        debugLog("To: " . implode(', ', $to));
        debugLog("Subject: {$subject}");
        
        $mail->send();

        debugLog("✓✓✓ EMAIL SENT SUCCESSFULLY ✓✓✓");
        error_log("Email sent successfully to: " . implode(', ', $to));
        return true;
        
    } catch (Exception $e) {
        debugLog("✗✗✗ EMAIL FAILED ✗✗✗");
        debugLog("Error: {$mail->ErrorInfo}");
        debugLog("Exception: " . $e->getMessage());
        
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}
?>