<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tax Information</title>
    <style>
        /* Add your custom CSS styles here */
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 1200px;
            margin: auto;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        td,
        th {
            line-height: 1.5;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        h1 {
            text-align: center;
            margin-top: 50px;
        }
        .hide-row {
            display: none;
        }
    </style>

    <link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
</head>

<body onload="checkLoggedIn()">
    <table id="myTable" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Gender</th>
                <th>Ship Address</th>
                <th>Locality</th>
                <th>State</th>
                <th>Postcode</th>
                <th>Country</th>
                <th>Birth Date</th>
                <th>SIN Number</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Another Province</th>
                <th>Move Date</th>
                <th>Move From</th>
                <th>Move To</th>
                <th>First Filling Tax</th>
                <th>Canada Entry</th>
                <th>Birth Country</th>
                <th>Year 1</th>
                <th>Year 1 Income</th>
                <th>Year 2</th>
                <th>Year 2 Income</th>
                <th>Year 3</th>
                <th>Year 3 Income</th>
                <th>File Paragon</th>
                <th>Years Tax Return</th>
                <th>Marital Status</th>
                <th>Spouse First Name</th>
                <th>Spouse Last Name</th>
                <th>Spouse Date of Birth</th>
                <th>Date of Marriage</th>
                <th>Spouse Annual Income</th>
                <th>Residing Canada</th>
                <th>Have Child</th>
                <th>Marital Change</th>
                <th>Spouse SIN</th>
                <th>Spouse Phone</th>
                <th>Spouse Email</th>
                <th>Spouse File Tax</th>
                <th>Spouse First Tax</th>
                <th>Spouse Canada Entry</th>
                <th>Spouse Birth Country</th>
                <th>Spouse Year 1</th>
                <th>Spouse Year 1 Income</th>
                <th>Spouse Year 2</th>
                <th>Spouse Year 2 Income</th>
                <th>Spouse Year 3</th>
                <th>Spouse Year 3 Income</th>
                <th>Spouse File Paragon</th>
                <th>Spouse Years Tax Return</th>
                <th>Child First Name</th>
                <th>First Time Buyer</th>
                <th>Direct Deposits</th>
                <th>ID Proof</th>
                <th>College Receipt</th>
                <th>T Slips</th>
                <th>Rent Address</th>
                <th>Tax Summary</th>
                <th>Income Delivery</th>
                <th>Summary Expenses</th>
                <th>Delivery HST</th>
                <th>HST Number</th>
                <th>HST Access Code</th>
                <th>HST Start Date</th>
                <th>HST End Date</th>
                <th>Additional Docs</th>
                <th>Message Us</th>
                <th>Created At</th>
            </tr>
        </thead>
    </table>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: true,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "fetch_data.php",
                    "type": "POST"
                },
                "createdRow": function (row, data, dataIndex) {
                    let hasData = false;
                    for (let i = 0; i < data.length; i++) {
                        if (data[i] !== "" && data[i] !== null) {
                            hasData = true;
                            break;
                        }
                    }

                    if (!hasData) {
                        $(row).addClass('hide-row');
                    }
                },
                "columns": [
                    { "data": 0 },
                    { "data": 1 },
                    { "data": 2 },
                    { "data": 3 },
                    { "data": 4 },
                    { "data": 5 },
                    { "data": 6 },
                    { "data": 7 },
                    { "data": 8 },
                    { "data": 9 },
                    { "data": 10 },
                    { "data": 11 },
                    { "data": 12 },
                    { "data": 13 },
                    { "data": 14 },
                    { "data": 15 },
                    { "data": 16 },
                    { "data": 17 },
                    { "data": 18 },
                    { "data": 19 },
                    { "data": 20 },
                    { "data": 21 },
                    { "data": 22 },
                    { "data": 23 },
                    { "data": 24 },
                    { "data": 25 },
                    { "data": 26 },
                    { "data": 27 },
                    { "data": 28 },
                    { "data": 29 },
                    { "data": 30 },
                    { "data": 31 },
                    { "data": 32 },
                    { "data": 33 },
                    { "data": 34 },
                    { "data": 35 },
                    { "data": 36 },
                    { "data": 37 },
                    { "data": 38 },
                    { "data": 39 },
                    { "data": 40 },
                    { "data": 41 },
                    { "data": 42 },
                    { "data": 43 },
                    { "data": 44 },
                    { "data": 45 },
                    { "data": 46 },
                    { "data": 47 },
                    { "data": 48 },
                    { "data": 49 },
                    { "data": 50,
                        "render": function (data, type, row, meta) {
                            return type === 'display' ? data : data.replace(/<br>/g, ', ');
                        }
                    },
                    { "data": 51 },
                    { "data": 52 },
                    { "data": 53 },
                    { "data": 54,
                        "render": function (data, type, row, meta) {
                            if (data === "" || data === null) {
                                return '<input type="file" class="id-proof-input" multiple> <button class="insert-id-proof-btn" data-row-id="' + row.unique_id + '">Insert</button>';
                            } else {
                                return data;
                            }
                        }
                    },
                    { "data": 55 },
                    { "data": 56 },
                    { "data": 57 },
                    { "data": 58 },
                    { "data": 59 },
                    { "data": 60,
                    "render": function (data, type, row, meta) {
                        return type === 'display' ? data : data.replace(/<br>/g, ', ');
                    }
                },
                    { "data": 61 },
                    { "data": 62 },
                    { "data": 63 },
                    { "data": 64 },
                    { "data": 65 },
                    { "data": 66 },
                    { "data": 67 },
                    { "data": 68 }
                ],
                "pagingType": "full_numbers",
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "order": [[68, "desc"]],
            });

            // Event delegation for table row click
            $('#myTable tbody').on('click', 'tr', function() {
                const table = $("#myTable").DataTable();
                const rowData = table.row(this).data();
                const headers = table.columns().header().toArray().map(header => $(header).text());
                
                localStorage.setItem("rowData", JSON.stringify(rowData));
                localStorage.setItem("tableHeaders", JSON.stringify(headers));
                window.location.href = "display_row_data.php";
            });

            function displayRowData(rowData) {
                const rowDataText = rowData.join(", ");
                alert("Row data:\n" + rowDataText);
            }


            $(document).on('click', '.insert-id-proof-btn', function() {
                // Get the file input element, the files, and the row ID
                const fileInput = $(this).siblings('.id-proof-input')[0];
                const files = fileInput.files;
                const rowId = $(this).data('row-id');

                // Log the row ID to the console
                console.log("Row ID:", rowId);

                // Upload the files and insert the file names into the database
                uploadAndInsertFiles(files, rowId);
            });


            function uploadAndInsertFiles(files, rowId) {

                // Create a FormData object to store the files
                const formData = new FormData();

                // Add the files to the FormData object
                for (let i = 0; i < files.length; i++) {
                    formData.append('idProofFiles[]', files[i]);
                }

                // Add the row ID to the FormData object
                formData.append('rowId', rowId);

                // Upload the files and insert the file names into the database
                $.ajax({
                    url: 'upload_and_insert_files.php', // The PHP file responsible for uploading the files and inserting the file names into the database
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Files uploaded and file names inserted, refresh the DataTable
                            $('#myTable').DataTable().ajax.reload();
                        } else {
                            // Handle error
                            console.error('Error uploading and inserting files:', response.error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            }
        });
    </script>
    <script>
        function checkLoggedIn() {
            if (localStorage.getItem('loggedin') !== 'true') {
                window.location.href = 'login.php'; // Change 'login.html' to your login page's file name if different
            }
        }
    </script>
</body>

</html>