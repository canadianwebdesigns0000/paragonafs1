<!DOCTYPE html>
<html>

<head>
    <title>Paragon Accounting</title>
    <style>
        /* Add your custom CSS styles here */
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: auto;
        }

        img {
            display: block;
            max-width: 250px;
            margin: 20px auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 14px;
            margin: 20px 0;
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ccc;
        }

        th {
            text-align: left;
        }

        tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        @media print {
            body {
                font-size: 12px;
            }

            th,
            td {
                padding: 5px;
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <img src="assets/img/paragon_logo.png" alt="Paragon Logo">
    <table id="rowDataDisplay">
        <tbody>
            <!-- Headers and data will be added dynamically from the original table -->
        </tbody>
    </table>
    <button onclick="window.print()">Print</button>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script>
        const rowData = JSON.parse(localStorage.getItem('rowData'));

        if (rowData) {
            const headers = JSON.parse(localStorage.getItem('tableHeaders'));
            populateTable(headers, rowData);
        } else {
            document.body.innerHTML = "<h1>No row data found!</h1>";
        }

        function populateTable(headers, rowData) {
        const table = document.getElementById("rowDataDisplay");

        headers.forEach((header, index) => {
            if (header && rowData[index]) {
                const tr = document.createElement("tr");

                const th = document.createElement("th");
                th.innerText = header;
                tr.appendChild(th);

                const td = document.createElement("td");

                if (index === 54) { // Assuming that the ID Proof column has an index of 58
                    if (rowData[index].trim() === '') {
                        const button = document.createElement("button");
                        button.innerText = "Reupload";
                        button.addEventListener("click", () => {
                            window.location.href = "reupload.php"; // Replace with the correct page URL
                        });
                        td.appendChild(button);
                    } else {
                        td.innerText = rowData[index];
                    }
                } else {
                    td.innerText = rowData[index];
                }

                tr.appendChild(td);
                table.appendChild(tr);
            }
        });
    }
    </script>
</body>

</html>