<%-- include SideBar --%>
<div class="content-container unit size3of4 lastUnit">
	<article>
		<% if Title %><h1>$Title</h1><% end_if %>
		<div class="content">$Content</div>
 		<p>To update these details you must do this in the QMTBC app.</p>
 		<table class="table table-bordered">
 			<% with $CurrentMember %>
 			<tr>
 				<th>Name</th>
 				<td>$Name</td>
 			</tr>
 			<tr>
 				<th>Email</th>
 				<td>$Email</td>
 			</tr>
 			<tr>
 				<th>Age</th>
 				<td>$ageGroupName</td>
 			</tr>
 			<tr>
 				<th>Gender</th>
 				<td>$genderName</td>
 			</tr>
 			<tr>
 				<th>Current active membership?</th>
 				<td><% if $isActive %>Yes<% else %>No<% end_if %></td>
 			</tr>
 			<tr>
 				<th>Membership Type</th>
 				<td>$membershipPackageName</td>
 			</tr>
 			<tr>
 				<th>Address</th>
 				<td>
 					$addressLine1<br />
 					$addressLine2<br />
 					$city<br />
 					$country<br />
 					$postcode
 				</td>
 			</tr>

 			<% end_with %>
 		</table>
 		<h2>Your Event Registrations</h2>
 		<table class="table table-bordered">
 			<thead>
	 			<tr>
	 				<th>Event</th>
	 				<th>Event Date</th>
	 				<th>Registration Date</th>
	 				<th>Payment Status</th>
	 			</tr>
 			</thead>
 			<tbody>
		 		<% loop CurrentMember.Registrations %>
		 			<tr>
		 				<td>$Event.Title</td>
		 				<td>$Event.EventDateTime.Nice</td>
		 				<td>$Created.Nice</td>
		 				<td>$PaymentStatus</td>
		 				<td><a href="event-registration/payment/61d9261d7a352b7702dab3e64576f55f">View / Pay</a>
		 			</tr>
		 		<% end_loop %>
		 		</tbody>
 		</table>

	</article>

</div>