$Content

<% with Event %>
	<% if Title %><h1>$Title</h1><% end_if %>
	<h2>Event Date $EventDateTime.Nice</h2>
	<% end_with %>
<% with Registration %>

	<table class="table table-bordered">
		<% loop ConfirmTableRows %>
			<tr>
				<th>$Name</th>
				<td>$Val</td>
			</tr>
		<% end_loop %>
		
	</table>

<% end_with %>

<p>Sincerely Queenstown Mountain Bike Club</p>