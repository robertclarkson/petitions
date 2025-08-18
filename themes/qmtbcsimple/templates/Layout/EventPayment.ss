<%-- include SideBar --%>
<div class="content-container unit size3of4 lastUnit">
	<article>
		<% with Event %>
			<% if Title %><h1>$Title</h1><% end_if %>
			<div class="content">$Content</div>
			<h2>Event Date $EventDateTime.Nice</h2>
 		<% end_with %>
		<% with Registration %>
      <% if PaymentStatus == 'Paid' %>
        <p class="alert alert-success">This registration is paid</p>
      <% end_if %>

			<table class="table table-bordered">
				<% loop ConfirmTableRows %>
					<tr>
						<th>$Name</th>
						<td>$Val</td>
					</tr>
				<% end_loop %>
				
			</table>

      <% if PaymentStatus != 'Paid' %>
        <p>You are not registered for this event unless you have paid online in full.</p>
        <button class="btn btn-primary" type="button" id="checkout-button">Pay now to register</button>
      <% end_if %>
		<% end_with %>
	</article>

</div>

<script type="text/javascript">
    // Create an instance of the Stripe object with your publishable API key
    var stripe = Stripe("$StripePubkey");
    var checkoutButton = document.getElementById("checkout-button");
    checkoutButton.addEventListener("click", function () {
      fetch("$PaymentLink", {
        method: "POST",
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (session) {
          return stripe.redirectToCheckout({ sessionId: session.id });
        })
        .then(function (result) {
          // If redirectToCheckout fails due to a browser or network
          // error, you should display the localized error message to your
          // customer using error.message.
          if (result.error) {
            alert(result.error.message);
          }
        })
        .catch(function (error) {
          console.error("Error:", error);
        });
    });
  </script>