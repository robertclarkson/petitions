<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style>
	body {
        margin:0px;
		font-family: 'DejaVu Sans', sans-serif;
	}
    @page { 
        margin-left: 10px;
        margin-bottom: 20px;
	   margin-right: 10px;
    }
    .invoice-box {
        max-width: 1000px;
        margin: auto;
        padding: 30px;
        font-size: 12px;
        line-height: 16px;
        color: #555;
    }
    
    .invoice-box table {
        width: 100%;
        text-align: left;
    }
    
    .invoice-box table td,
    .invoice-box table th {
        padding: 5px 10px;
        vertical-align: top;
    }
    
    .invoice-box table tr.top td:nth-child(2),
    .invoice-box table tr.information td:nth-child(2),
    .invoice-box table tr.heading th:nth-child(4),
    .invoice-box table tr.item td:nth-child(4),
    .invoice-box table tr.total td:nth-child(3) {
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
    
    .invoice-box table tr.heading th {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    
    .invoice-box table tr.total td.bold {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
    
	h1 {
		text-align: center;
	}

    table td, table th {
    	text-align: left;
    }

    footer {
        position: absolute; 
        bottom: 20px; 
        left: 20px; 
        right: 0px;
        height: 35px; 
        line-height: 35px;
        text-align: center;
        /** Extra personal styles **/
    }
    </style>
</head>
	<body>
		<div class="invoice-box">
			<h1>Submission Form</h1>
            <p style="text-align: center;">Draft Otago Conservation Management Strategy partial review</p>

			<h2>Submitter Details</h2>
			<table cellpadding="0" cellspacing="0">
                <tr>
                	<th>
                		Name
                	</th>
                    <td>
                    	$Data.Name
                    </td>
                </tr>
                <tr>
                	<th>
                		Organisation Name
                	</th>
                    <td>
                    	$Data.OrganisationName
                    </td>
                </tr>
                <tr>
                	<th>
                		Phone
                	</th>
                    <td>
                    	$Data.Phone
                    </td>
                </tr>
                <tr>
                	<th>
                		Email
                	</th>
                    <td>
                    	$Data.Email
                    </td>
                </tr>
                <tr>
                	<th>
                		Postal Address
                	</th>
                    <td>
                    	$Data.AddressLine1, $Data.AddressLine2
                    </td>
                </tr>
                <tr>
                    <th>
                        Suburb
                    </th>
                    <td>
                        $Data.Suburb
                    </td>
                </tr>
                <tr>
                    <th>
                        City
                    </th>
                    <td>
                        $Data.City
                    </td>
                </tr>
                <tr>
                	<th>
                		Postcode
                	</th>
                    <td>
                    	$Data.Postcode
                    </td>
                </tr>
	        </table>
	               

	        <h2>Submission</h2>

			<table cellpadding="0" cellspacing="0">
                
                <tr>
                	<th>
                        <% if $Data.Heard == 'Heard' %>
                		  I do wish to be heard in support of my submission
                        <% else %>
                            I do not wish to be heard in support of my submission
                        <% end_if %>
                	</th>
                   
                </tr>

            </table>


            <h2>My submission is</h2>
			<p>$Data.MySubmissionIs</p>
                    
	        <h2>Signature</h2>
	        <br />
	        <br />
	        <br />
			<img src="data:image/jpg;base64,$Signature" />
			
			<p>$Data.Name</p>
			<p>Date: $Data.Created.Format('{o} MMMM Y')</p>
		</div>
	</body>
</html>