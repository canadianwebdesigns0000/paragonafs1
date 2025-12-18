<?php
    require_once 'functions.php';
    // error_reporting(0);
    date_default_timezone_set('America/New_York'); // Set the time zone to EST
    /*
   |--------------------------------------------------------------------------
   | Database Connections
   |--------------------------------------------------------------------------
   |
   | Here are the database configuration setting of the application.
   | HOST is the hostname of the mysql. It can be IP address, localhost or 127.0.0.1
   |
   |
   | USERNAME : username of the mysql (It can be root or anything else)
   | PASSWORD : password of the mysql.
   | DATABASE : First create the database using phpmyadmin or mysql and enter the name of database
   |
   */

    $GLOBALS['HOST']     = 'localhost';
    $GLOBALS['USERNAME'] = 'paragonafs';
    $GLOBALS['PASSWORD'] = 'W6j4jCV9zJj8Tk8';
    $GLOBALS['DATABASE'] = 'paragonafs';


    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL will be used in the emails.
    |
    */

    $baseUrl = 'https://paragonafs.ca';


    /*
   |--------------------------------------------------------------------------
   | SMTP
   |--------------------------------------------------------------------------
   |
   | if smtp is set true then it will use configuration from
   | smtp.php file. Make sure you have added the details correctly
   |
   */

    $GLOBALS['SMTP'] = true;

    /*
    |--------------------------------------------------------------------------
    | SMTP False
    |--------------------------------------------------------------------------
    |
    | This email details when stmp is set to false and system sends email using mail function
    |
    */

    $fromAddress = 'paragonafs@gmail.com';
    $fromName    = 'Paragon AFS';


    /*
    |--------------------------------------------------------------------------
    | Image Path
    |--------------------------------------------------------------------------
    |
    | Path of the avatar image. This needs to have writable permission
    |
    */

    $path = 'images/avatar/';

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | Encryption key.This key is used for the encryption of the keys that are generated
    | Forget Key and Email verification Key
    |
    */

    $encryptionKey = 'o7l8Tvxdq1zE8oa45TiVNMRH05Xe6tqgPZW3+Mcghgk=';


    /*
    |--------------------------------------------------------------------------
    | Google Re captcha Keys
    |--------------------------------------------------------------------------
    |
    | Visit this to generate keys https://www.google.com/recaptcha/intro/index.html
    |
    */


    $siteKey   = '6LenUF4pAAAAAOORqRS1o8ZeTtBLOFXoSx5VMLQO';
    $secretKey = '6LenUF4pAAAAAE66AbcH8Ri-nJxW6doMRN1BCZAB';

    /*
    |--------------------------------------------------------------------------
    | Email verification
    |--------------------------------------------------------------------------
    |
    | Email verification will allow the application to use email verification. By default it is
    | not enabled. Set true ot make is enable
    |
    */

    $emailVerification = true;


    /*
    |--------------------------------------------------------------------------
    | Password encryption Type
    |--------------------------------------------------------------------------
    |
    | There are 2 types of password encryption.
    | sha1 :  Setting sha1 will set the system to use sha1 encryption type
    | md5 : Setting sha1 will set the system to use sha1 encryption type
    | none : By default the system will not use any encryption and it set to none.
    |
    |
    | Use any of the above to change the encryption type
    */

    $encryptionType = 'sha1';


    /*
    |--------------------------------------------------------------------------
    | Form Submission Email Recipient
    |--------------------------------------------------------------------------
    |
    | This email address will receive form submission notifications
    | from the tax form submission system.
    |
    */

/*     $formSubmissionEmail = 'info@paragonafs.ca';  */

    $formSubmissionEmail = ['info@paragonafs.ca', 'dev@canadianwebdesigns.com'];

    /*
    |--------------------------------------------------------------------------
    | Email CC Recipients (Optional)
    |--------------------------------------------------------------------------
    |
    | Set CC (Carbon Copy) recipients for form submission emails.
    | CC recipients will be visible to all recipients.
    |
    | Options:
    |   - Single email: $formSubmissionCC = 'cc@example.com';
    |   - Multiple emails: $formSubmissionCC = ['cc1@example.com', 'cc2@example.com'];
    |   - Disable: $formSubmissionCC = null; (or comment out)
    |
    */
    
    // Uncomment and configure one of the options below:
    
    // Option 1: Single CC recipient
    // $formSubmissionCC = 'cc@paragonafs.ca';
    
    // Option 2: Multiple CC recipients
    // $formSubmissionCC = ['cc1@paragonafs.ca', 'cc2@paragonafs.ca'];
    
    // Option 3: No CC recipients (default)
    $formSubmissionCC = null;

    /*
    |--------------------------------------------------------------------------
    | Email BCC Recipients (Optional)
    |--------------------------------------------------------------------------
    |
    | Set BCC (Blind Carbon Copy) recipients for form submission emails.
    | BCC recipients will receive the email but won't be visible to other recipients.
    |
    | Options:
    |   - Single email: $formSubmissionBCC = 'bcc@example.com';
    |   - Multiple emails: $formSubmissionBCC = ['bcc1@example.com', 'bcc2@example.com'];
    |   - Disable: $formSubmissionBCC = null; (or comment out)
    |
    */
    
    // Uncomment and configure one of the options below:
    
    // Option 1: Single BCC recipient
    // $formSubmissionBCC = 'bcc@paragonafs.ca';
    
    // Option 2: Multiple BCC recipients
    // $formSubmissionBCC = ['bcc1@paragonafs.ca', 'bcc2@paragonafs.ca'];
    
    // Option 3: No BCC recipients (default)
    $formSubmissionBCC = null;

    /*
    |--------------------------------------------------------------------------
    | Connecting Database
    |--------------------------------------------------------------------------
    |
    | connectDatabase is called to connect to the database
    |
    */


    $db = connectDatabase();
