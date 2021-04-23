<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <title>A simple, clean, and responsive HTML invoice template</title>

    <!-- Favicon -->
    <link rel="icon" href="./images/favicon2.png" type="image/x-icon"/>

    <!-- Invoice styling -->
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'poppins', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }
    </style>
</head>

<body>


<div class="invoice-box">
    <table>
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <img src="./images/logo.png" alt="Company logo" style="width: 45%; max-width: 300px"/>
                        </td>

                        <td>
                            Invoice #: 123<br/>
                            Created: January 1, 2015<br/>
                            Due: February 1, 2015
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            Biller : SLT Mobitel<br/>
                            Category : Telephone<br/>
                            Transaction ID : trx66564363214efg
                        </td>

                        <td>
                            Kasun anuranga<br/>
                            0716358522<br/>
                            23/04/2021 | 03:23 PM
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


        <!--<td class="title">
            <img src="images/icons/trueGIF.gif" alt="Company logo"
                 style="width: 50%; max-width: 200px"/>
            <h3>Payment Successfull!</h3>
        </td> -->


        <tr class="heading">
            <td>Paid using</td>

            <td></td>
        </tr>

        <tr class="details">
            <td><img src="images/icons/visa.png" width="9%" align="center"></td>

            <td>xxxx-xxxx-xxxx-1257</td>
        </tr>

        <tr class="heading">
            <td>Service</td>

            <td></td>
        </tr>

        <tr class="item">
            <td>Account number - 0713040514 (SLT Mobitel -Postpaid)</td>

            <td>1500.00 LKR</td>
        </tr>


        <tr class="item">
            <td>Biller category - Telephone</td>

            <td></td>
        </tr>

        <tr class="item">
            <td>Status</td>

            <td>Success</td>
        </tr>

        <tr class="item">
            <td>Reference</td>

            <td>-</td>
        </tr>

        <tr class="item">
            <td>Description</td>

            <td>Monthly bill payment</td>
        </tr>


        <br><br><br>
        <tr class="total">

            <td></td>

            <td>Amount : 1500.00</td>
        </tr>
    </table>
<br><br><br>
</div>
</body>
</html>
