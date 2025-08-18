<div class="content-container unit size3of4 lastUnit">
	<article>
		<h1>$Title</h1>
		<div class="content">
			<div class="owl-carousel owl-theme">
			  	<div>
					<img class="center" src="$Image.Link" />
			  	</div>
				  <% if GalleryImage %>
					  <% loop GalleryImage %>
					  	<div>
							<img class="center" src="$Me.scaleWidth(1020).Link" />
					  		
					  	</div>
					  <% end_loop %>
				  <% end_if %>
			  </div>
			$Content
		</div>
	</article>

		$Form
    
  </div>

</div>