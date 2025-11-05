<?php

$servername = "localhost:3306";
$username = "paragonafs_dev";
$password = "Jhc+O*GM+hQ5";
$dbname = "paragonafs_clients";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";

$firstname1 = isset($_POST['firstName']) ? $_POST['firstName'] : '';
$lastName1 = isset($_POST['lastName']) ? $_POST['lastName'] : '';
$gender1 = isset($_POST['gender']) ? $_POST['gender'] : '';
$ship_address1 = isset($_POST['ship_address']) ? $_POST['ship_address'] : '';
$locality1 = isset($_POST['locality']) ? $_POST['locality'] : '';
$state1 = isset($_POST['state']) ? $_POST['state'] : '';
$postcode1 = isset($_POST['postcode']) ? $_POST['postcode'] : '';
$country1 = isset($_POST['country']) ? $_POST['country'] : '';
$birth_date1 = isset($_POST['birth_date']) ? $_POST['birth_date'] : '';
$sin_number1 = isset($_POST['sin_number']) ? $_POST['sin_number'] : '';
$phone1 = isset($_POST['phone']) ? $_POST['phone'] : '';
$email1 = isset($_POST['email']) ? $_POST['email'] : '';
$another_province1 = isset($_POST['another_province']) ? $_POST['another_province'] : '';
$move_date1 = isset($_POST['move_date']) ? $_POST['move_date'] : '';
$move_from1 = isset($_POST['move_from']) ? $_POST['move_from'] : '';
$move_to1 = isset($_POST['move_to']) ? $_POST['move_to'] : '';
$first_fillingtax1 = isset($_POST['first_fillingtax']) ? $_POST['first_fillingtax'] : '';
$canada_entry1 = isset($_POST['canada_entry']) ? $_POST['canada_entry'] : '';
$birth_country1 = isset($_POST['birth_country']) ? $_POST['birth_country'] : '';
$year11 = isset($_POST['year1']) ? $_POST['year1'] : '';
$year1_income1 = isset($_POST['year1_income']) ? $_POST['year1_income'] : '';
$year21 = isset($_POST['year2']) ? $_POST['year2'] : '';
$year2_income1 = isset($_POST['year2_income']) ? $_POST['year2_income'] : '';
$year31 = isset($_POST['year3']) ? $_POST['year3'] : '';
$year3_income1 = isset($_POST['year3_income']) ? $_POST['year3_income'] : '';
$file_paragon1 = isset($_POST['file_paragon']) ? $_POST['file_paragon'] : '';
$years_tax_return1 = isset($_POST['years_tax_return']) ? $_POST['years_tax_return'] : '';
$marital_status1 = isset($_POST['marital_status']) ? $_POST['marital_status'] : '';
$spouse_firstname1 = isset($_POST['spouse_firstname']) ? $_POST['spouse_firstname'] : '';
$spouse_lastname1 = isset($_POST['spouse_lastname']) ? $_POST['spouse_lastname'] : '';
$spouse_date_birth1 = isset($_POST['spouse_date_birth']) ? $_POST['spouse_date_birth'] : '';
$date_marriage1 = isset($_POST['date_marriage']) ? $_POST['date_marriage'] : '';
$spouse_annual_income1 = isset($_POST['spouse_annual_income']) ? $_POST['spouse_annual_income'] : '';
$residing_canada1 = isset($_POST['residing_canada']) ? $_POST['residing_canada'] : '';
$have_child1 = isset($_POST['have_child']) ? $_POST['have_child'] : "";
$marital_change1 = isset($_POST['marital_change']) ? $_POST['marital_change'] : "";
$spouse_sin1 = isset($_POST['spouse_sin']) ? $_POST['spouse_sin'] : "";
$spouse_phone1 = isset($_POST['spouse_phone']) ? $_POST['spouse_phone'] : "";
$spouse_email1 = isset($_POST['spouse_email']) ? $_POST['spouse_email'] : "";
$spouse_file_tax1 = isset($_POST['spouse_file_tax']) ? $_POST['spouse_file_tax'] : "";
$spouse_first_tax1 = isset($_POST['spouse_first_tax']) ? $_POST['spouse_first_tax'] : "";
$spouse_canada_entry1 = isset($_POST['spouse_canada_entry']) ? $_POST['spouse_canada_entry'] : "";
$spouse_birth_country1 = isset($_POST['spouse_birth_country']) ? $_POST['spouse_birth_country'] : "";
$spouse_year11 = isset($_POST['spouse_year1']) ? $_POST['spouse_year1'] : "";
$spouse_year1_income1 = isset($_POST['spouse_year1_income']) ? $_POST['spouse_year1_income'] : "";
$spouse_year21 = isset($_POST['spouse_year2']) ? $_POST['spouse_year2'] : "";
$spouse_year2_income1 = isset($_POST['spouse_year2_income']) ? $_POST['spouse_year2_income'] : "";
$spouse_year31 = isset($_POST['spouse_year3']) ? $_POST['spouse_year3'] : "";
$spouse_year3_income1 = isset($_POST['spouse_year3_income']) ? $_POST['spouse_year3_income'] : "";
$spouse_file_paragon1 = isset($_POST['spouse_file_paragon']) ? $_POST['spouse_file_paragon'] : "";
$spouse_years_tax_return1 = isset($_POST['spouse_years_tax_return']) ? $_POST['spouse_years_tax_return'] : "";
$child_first_name1 = isset($_POST['data']) ? json_encode($_POST['data']) : '';
$first_time_buyer1 = isset($_POST['first_time_buyer']) ? $_POST['first_time_buyer'] : "";
$direct_deposits = isset($_POST['direct']) ? $_POST['direct'] : '';
$id_proof1 = isset($_POST['id_proof']) ? $_POST['id_proof'] : '';
$college_receipt1 = isset($_POST['college']) ? $_POST['college'] : '';
$t_slips1 = isset($_POST['t_slips']) ? $_POST['t_slips'] : '';
$rent_address = isset($_POST['group-a']) ? json_encode($_POST['group-a']) : "";

$direct_deposit_text1 = !empty($direct_deposits) ? implode("<br>", $direct_deposits) : "";
$id_proof_text1 = !empty($id_proof1) ? implode("<br>", $id_proof1) : "";
$college_text1 = !empty($college_receipt1) ? implode("<br>", $college_receipt1) : "";
$t_slip_text1 = !empty($t_slips1) ? implode("<br>", $t_slips1) : "";

$tax_summary1 = isset($_POST['tax_summary']) ? $_POST['tax_summary'] : '';
$tax_summary_text1 = !empty($tax_summary1) ? implode("<br>", $tax_summary1) : "";

$income_delivery1 = isset($_POST['income_delivery']) ? $_POST['income_delivery'] : "";
$summary_expenses1 = isset($_POST['summary_expenses']) ? $_POST['summary_expenses'] : "";
$delivery_hst1 = isset($_POST['delivery_hst']) ? $_POST['delivery_hst'] : '';
$hst_number1 = isset($_POST['hst_number']) ? $_POST['hst_number'] : '';
$hst_access_code1 = isset($_POST['hst_access_code']) ? $_POST['hst_access_code'] : '';
$hst_start_date1 = isset($_POST['hst_start_date']) ? $_POST['hst_start_date'] : '';
$hst_end_date1 = isset($_POST['hst_end_date']) ? $_POST['hst_end_date'] : '';
$additional_docs1 = isset($_POST['additional_docs']) ? $_POST['additional_docs'] : '';
$additional_docs_text1 = !empty($additional_docs1) ? implode("<br>", $additional_docs1) : '';
$message_us1 = isset($_POST['message_us']) ? $_POST['message_us'] : '';

// SQL query to insert values into the tax_information table
$sql = "INSERT INTO tax_information (first_name, last_name, gender, ship_address, locality, state, postcode, country, birth_date, sin_number, phone, email, another_province, move_date, move_from, move_to, first_fillingtax, canada_entry, birth_country, year1, year1_income, year2, year2_income, year3, year3_income, file_paragon, years_tax_return, marital_status, spouse_first_name, spouse_last_name, spouse_date_birth, date_marriage, spouse_annual_income, residing_canada, have_child, marital_change, spouse_sin, spouse_phone, spouse_email, spouse_file_tax, spouse_first_tax, spouse_canada_entry, spouse_birth_country, spouse_year1, spouse_year1_income, spouse_year2, spouse_year2_income, spouse_year3, spouse_year3_income, spouse_file_paragon, spouse_years_tax_return, child_first_name, first_time_buyer, direct_deposits, id_proof, college_receipt, t_slips, rent_address, tax_summary, income_delivery, summary_expenses, delivery_hst, hst_number, hst_access_code, hst_start_date, hst_end_date, additional_docs, message_us)
VALUES ('$firstname1', '$lastName1', '$gender1', '$ship_address1', '$locality1', '$state1', '$postcode1', '$country1', '$birth_date1', '$sin_number1', '$phone1', '$email1', '$another_province1', '$move_date1', '$move_from1', '$move_to1', '$first_fillingtax1', '$canada_entry1', '$birth_country1', '$year11', '$year1_income1', '$year21', '$year2_income1', '$year31', '$year3_income1', '$file_paragon1', '$years_tax_return1', '$marital_status1', '$spouse_firstname1', '$spouse_lastname1', '$spouse_date_birth1', '$date_marriage1', '$spouse_annual_income1', '$residing_canada1', '$have_child1', '$marital_change1', '$spouse_sin1', '$spouse_phone1', '$spouse_email1', '$spouse_file_tax1', '$spouse_first_tax1', '$spouse_canada_entry1', '$spouse_birth_country1', '$spouse_year11', '$spouse_year1_income1', '$spouse_year21', '$spouse_year2_income1', '$spouse_year31', '$spouse_year3_income1', '$spouse_file_paragon1', '$spouse_years_tax_return1', '$child_first_name1', '$first_time_buyer1', '$direct_deposit_text1', '$id_proof_text1', '$college_text1', '$t_slip_text1', '$rent_address1', '$tax_summary_text1', '$income_delivery1', '$summary_expenses1', '$delivery_hst1', '$hst_number1', '$hst_access_code1', '$hst_start_date1', '$hst_end_date1', '$additional_docs_text1', '$message_us1')";


// Execute the SQL query
if (mysqli_query($conn, $sql)) {
  echo "New record created successfully";
} else {
  $error_message = "Error: " . mysqli_error($conn);
  echo "<script>alert('$error_message');</script>";
}
