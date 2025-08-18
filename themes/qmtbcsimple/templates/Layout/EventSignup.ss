<%-- include SideBar --%>
<div class="content-container unit size3of4 lastUnit">
	<article>
		<% with Event %>
			<% if Title %><h1>$Title <small> - $EventDateTime.Nice</small></h1><% end_if %>
			<div class="my-slider">
			  <div>$Image.scaleWidth(1200)</div>
			  <% loop Slideshow %>
			  <div><img src="$scaleWidth(1200).URL" /></div>
			  <% end_loop %>
			</div>
			<div class="content">$Content</div>
			<h2>Event Date $EventDateTime.Nice</h2>
			<h3>Registration</h3>
			<table class="table table-bordered">
				<tr>
					<th>Registration Closes</th>
					<td>$RegistrationClose.Nice</td>
				</tr>
			</table>
			<h3>Event Categories</h3>
			<ul style="list-style: disc; padding-left: 20px">
				<% loop SportIdentCategories %>
				<li>$Title</li>
				<% end_loop %>
			</ul>
 		<% end_with %>
 		<% if $Event.CanRegister %>
 			<% if not CurrentMember %>
	 			<div class="alert alert-info">
	 				<a class="" href="firebaselogin?mode=select&signInSuccessUrl=$Link/signup/$Event.Hash">Log in as an active member</a> to prefill your details and get your member discount
	 			</div>
	 		<% else %>
	 			<div class="alert alert-info">
	 				You are signed in as $CurrentMember.Name, if this registration is for someone else please <a href="Security/logout?BackURL=$Link/signup/$Event.Hash">log out</a>
	 			</div>
 			<% end_if %>
 			$EventForm
 		<% else %>
 			<p>Registration for this event is not open yet.</p>
 		<% end_if %>

 		<% if Event.RelatedEvents %>
 		<div class="pt-5">
	 		<p>You might also like...</p>
	 		<div class="related-events-slider">
	 			<% loop Event.RelatedEvents.Limit(3) %>
	 			<div class="event">
	 				<% include EventSummary %>
	 			</div>
	 			<% end_loop %>
	 		</div>
 		</div>
 		<% end_if %>
	</article>

    

</div>