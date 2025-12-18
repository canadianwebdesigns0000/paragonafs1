<?php
/*
 * Script:    DataTables server-side script for PHP and MySQL
 * Copyright: 2010 - Allan Jardine, 2012 - Chris Wright
 * License:   GPL v2 or BSD (3-point)
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

/* Array of database columns which should be read and sent back to DataTables. Use a space where
 * you want to insert a non-database field (for example a counter or static image)
 */
// $aColumns = array( 'id','name', 'avatar','username', 'email', 'status','id' );
// $asColumns = array( 'id','name', 'avatar','username', 'email', 'status','id' );
// $aaColumns = array( 'id','name', 'avatar','username', 'email', 'status','id');

$aColumns = array( 'id','first_name', 'last_name','phone', 'email', 'email_verified','id' );
$asColumns = array( 'id','first_name', 'last_name','phone', 'email', 'email_verified','id' );
$aaColumns = array( 'id','first_name', 'last_name','phone', 'email', 'email_verified','id');

/* Indexed column (used for fast and accurate table cardinality) */
$sIndexColumn = "id";

/* DB table to use */
$sTable = "users";

/* Database connection information */

require_once("../../config.php");

// Function to Encrypt Decrypt String
function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    // Update your secret key before use
    $secret_key = '$7PHKqGt$yRlPjyt89rds4ioSDsglpk/';
    // Update your secret iv before use
    $secret_iv = '$QG8$hj7TRE2allPHPlBbrthUtoiu23bKJYi/';
    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
 * no need to edit below this line
 */

/*
 * Local functions
 */
function fatal_error ( $sErrorMessage = '' )
{
    header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
    die( $sErrorMessage );
}


/*
 * MySQL connection
 */
if ( ! $gaSql['link'] = mysqli_connect( $GLOBALS["HOST"] , $GLOBALS["USERNAME"] , $GLOBALS["PASSWORD"]  ,$GLOBALS["DATABASE"]  ) )
{
    fatal_error( 'Could not open connection to server' );
}



/*
 * Paging
 */
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
    $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
        intval( $_GET['iDisplayLength'] );
}


/*
 * Ordering
 */
$sOrder = "";
if ( isset( $_GET['iSortCol_0'] ) )
{
    $sOrder = "ORDER BY  ";
    for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
    {
        if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
        {
            $sOrder .= $aaColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                    ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
        }
    }

    $sOrder = substr_replace( $sOrder, "", -2 );
    if ( $sOrder == "ORDER BY" )
    {
        $sOrder = "";
    }
}


/*
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */
$sWhere =  "";
if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
    $searchQuery = $_GET['sSearch'];
    $encryptedSearchQuery = encrypt_decrypt('encrypt', $searchQuery);
    $sWhere .= "WHERE (";
    for ($i = 0; $i < count($asColumns); $i++) {
        if (isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true") {
            if ($asColumns[$i] == 'phone' || $asColumns[$i] == 'email') {
                // Use the decrypted search query for phone and email fields
                $sWhere .= $asColumns[$i]." LIKE '%" . mysqli_real_escape_string($gaSql['link'], $encryptedSearchQuery) . "%' OR ";            
            } else {
                // Use the original column value for comparison
                $sWhere .= $asColumns[$i]." LIKE '%" . mysqli_real_escape_string($gaSql['link'], $searchQuery) . "%' OR ";
            }            
        }
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}

///* Individual column filtering */
for ( $i=0 ; $i<count($asColumns) ; $i++ )
{
    if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
    {
        if ( $sWhere == "" )
        {
            $sWhere = "WHERE ";
        }
        else
        {
            $sWhere .= " AND ";
        }
        $sWhere .= $asColumns[$i]." LIKE '%".mysqli_real_escape_string($gaSql['link'],$_GET['sSearch_'.$i])."%' ";
    }
}


/*
 * SQL queries
 * Get data to display SELECT * FROM users
 */
$sQuery = "
        SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
    ";
$rResult = mysqli_query($gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysql_errno() );



/* Data set length after filtering */
$sQuery = "
        SELECT FOUND_ROWS()
    ";
$rResultFilterTotal = mysqli_query($gaSql['link'] , $sQuery) or fatal_error( 'MySQL Error: ' . mysql_errno() );
$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
$iFilteredTotal = $aResultFilterTotal[0];

/* Total data set length */
$sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM   $sTable WHERE status = 'enable'
    ";
$rResultTotal = mysqli_query($gaSql['link'] ,$sQuery) or fatal_error( 'MySQL Error: ' . mysql_errno() );
$aResultTotal = mysqli_fetch_array($rResultTotal);
$iTotal = $aResultTotal[0];


/*
 * Output
 */
$output = array(
    "sEcho" => intval($_GET['sEcho']),
    "iTotalRecords" => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData" => array()
);

while ( $aRow = mysqli_fetch_array( $rResult ) )
{
    $row = array();
    for ( $i=0 ; $i<count($aaColumns) ; $i++ )
    {
//        print_r($aaColumns[$i]);
        if ( $aaColumns[$i] == "version" )
        {
            /* Special output formatting for 'version' column */
            $row[] = ($aRow[ $aaColumns[$i] ]=="0") ? '-' : $aRow[ $aaColumns[$i] ];
        }
        else if ( $aaColumns[$i] != ' ' )
        {
            // Edit status column
            if ($aaColumns[$i] == 'email_verified') {
                if($aRow[$aaColumns[$i]] == 'yes') {
                    $row[] = '<span class="label label-success">Yes</span>';
                } else {
                    $row[] = '<span class="label label-danger">No</span>';
                }
            } else if ($aaColumns[$i] == 'phone' || $aaColumns[$i] == 'email') {
                // Apply encrypt_decrypt function to phone and email fields
                $row[] = encrypt_decrypt('decrypt', $aRow[$aaColumns[$i]]);
            } else {
                /* General output */
                $row[] = $aRow[ $aaColumns[$i] ];
            }
        }
    }
    $row[] = '<a type="button" href="javascript:;" onclick="viewModal(\'' . $row[4] . '\')" class="btn btn green btn-outline btn-xs margin-top-10">View</a>&nbsp;';

    $row[] = '<a type="button" href="javascript:;" onclick="editModal('.$row[0].')" class="btn btn green btn-outline btn-xs margin-top-10">	<i class="fa fa-edit"></i> Edit</a>&nbsp;
                <a href="javascript:;" onclick="del('.$row[0].',\''.addslashes($row[1]).'\')" class="btn btn-outline red btn-xs margin-top-10">	<i class="fa fa-trash-o"></i> Delete</a>';

    $output['aaData'][] = $row;
}

echo json_encode( $output );
?>