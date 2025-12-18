<?php

    /*
     * Function to connect to the database. If Database connection fails throw error
     */

    function connectDatabase()
    {
        try {
            $db = new PDO("mysql:dbname={$GLOBALS["DATABASE"]};host={$GLOBALS["HOST"]}", $GLOBALS["USERNAME"], $GLOBALS["PASSWORD"]);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $db;
        } catch (PDOException $ex) {
            die('Database connection failed. Please check the database configuration');
        }
    }

    /*
     * Function to resize the uploaded image
     */

    function resize($target, $w, $h, $ext)
    {
        list($w_orig, $h_orig) = getimagesize($target);

        $img = '';
        $ext = strtolower($ext);
        if ($ext == "gif") {
            $img = imagecreatefromgif($target);
        } else if ($ext == "png") {
            $img = imagecreatefrompng($target);
        } else {
            $img = imagecreatefromjpeg($target);
        }
        $tci = imagecreatetruecolor($w, $h);

        imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
        imagejpeg($tci, $target, 80);
    }


    /*
     * Function to get data from another website url
     */

    function getCurlData($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
        $curlData = curl_exec($curl);
        curl_close($curl);

        return $curlData;
    }


    function post($post)
    {
        return trim($_POST[$post]);
    }

    function get($get)
    {
        return trim($_GET[$get]);
    }

/** Return success response
 * @param $messageOrData
 * @param $email
 * @param $data
 * @return array
 */
 function responseSuccess($messageOrData, $email = '', $data = null)
{
    $response = [
        'status' => 'success',
        'email' => $email
    ];

    if (!empty($messageOrData) && !is_array($messageOrData)) {
        $response['message'] = $messageOrData;
    }

    if (is_array($data)){
        $response = array_merge($data, $response);
    }

    if (is_array($messageOrData)) {
        $response = array_merge($messageOrData, $response);
    }

    return $response;
}

function responseCheckEmailSuccess($name, $email)
{
    $response = [
        'status' => 'success',
        'email' => $email,
        'first_name' => $name,
    ];

    return $response;
}


function responseCheckEmail($messageOrData, $data)
{
    $response = [
        'status' => 'noemail',
        'email' => $data
    ];

    return $response;
}


/** Return error response
 * @param $message
 * @return array
 */

function responseError($message, $errorName = null, $errorData = [])
{
    return [
        'status' => 'fail',
        'error_name' => $errorName,
        'data' => $errorData,
        'message' => $message
    ];
}

/** Return validation error
 * @return array
 */
function responseFormErrors($input)
{
    $output = [];
    $error = false;

    foreach ($input as $key => $value) {

        if($input[$key] == '') {
            $output['errors'][$key] = array('The '.$key.' field is required.');
            $error = true;
        }
    }

    if($error) {
        return [
            'error' => $error,
            'status' => 'fail',
            'errors' => $output['errors']
        ];
    }
    else {
        return [
            'error' => $error,
            'status' => 'success',
            'errors' => $output
        ];
    }


}

/** Response with redirect action. This is meant for ajax responses and is not meant for direct redirecting
 * to the page
 * @param $url string to redirect to
 * @param null $message Optional message
 * @return array
 */
function responseRedirect($url, $message = null)
{
    if ($message) {
        return [
            'status' => 'success',
            'message' => $message,
            'action' => 'redirect',
            'url' => $url
        ];
    }
    else {
        return [
            'status' => 'success',
            'action' => 'redirect',
            'url' => $url
        ];
    }
}

/**
 * Generate a new unique file name
 * @param string $current_file_name
 * @return string new file name
 */
function generateNewFileName($currentFileName)
{
    $ext = end((explode('.', $currentFileName['name'])));
    $newName = md5(microtime());

    return $newName . '.' . $ext;
}


function getUserData($db, $email) {
    $userData = $db->prepare('SELECT * FROM tax_information WHERE email=?');
    $userData->execute(array($email));
    return $userData->fetch(PDO::FETCH_ASSOC);
}

