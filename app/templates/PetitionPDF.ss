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
			<h1>Form 13: Submission</h1>
			<h1>on a resource consent application</h1>
            <p style="text-align: center;">Resource Management Act 1991 Section 95, 96, 127(3), 136(4), 137(5)(c), 234(4) & 41D</p>
			<h2>To: 	Queenstown Lakes District Council</h2>


			<h2>Your Details</h2>
            <p>QLDC's preferred method of correspondence is by email and phone.</p>
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
                		Address
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
	               
			<h2>Applicant Details</h2>

			<table cellpadding="0" cellspacing="0">
                <tr>
                	<th>
                		Applicants Name
                	</th>
                    <td>
                       $SubmissionData.ApplicantsName
                    </td>
                </tr>
                <tr>
                	<th>
                		Application Reference Number
                	</th>
                    <td>
                       $SubmissionData.ApplicationReferenceNumber
                    </td>
                </tr>
                <tr>
                	<th>
                		Application Details
                	</th>
                    <td>
                       $SubmissionData.ApplicationDetails
                    </td>
                </tr>
                <tr>
                	<th>
                		Application Location
                	</th>
                    <td>
                       $SubmissionData.ApplicationLocation
                    </td>
                </tr>
	        </table>

	        <h2>Submission</h2>

			<table cellpadding="0" cellspacing="0">
                <tr>
                	<th>
                		<% if $Data.Submission == 'support' %>
                            I support the submission
                        <% else %>
                            I oppose the submission
                        <% end_if %>
                	</th>
                   
                </tr>
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
                    

            <h2>The reasons for my submission are</h2>
			<p>$Data.Reasons</p>

            <h2>My submission would be met by the Queenstown Lakes District Council making the following decision</h2>
			<p>$Data.Decision</p>

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