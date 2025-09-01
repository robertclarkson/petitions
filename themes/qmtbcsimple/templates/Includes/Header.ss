<header class="header" role="banner">
	<div class="inner">
		<div class="size4of4 lastUnit">
			<a href="$BaseHref" class="brand" rel="home">
				$SiteConfig.Logo.ScaleWidth(300)
				<h1>$SiteConfig.Title</h1>
			</a>
			<div class="clearfix"></div>
		</div>
		<div class="size4of4 lastUnit">
			
			<% if $SearchForm %>
				<span class="search-dropdown-icon">L</span>
				<div class="search-bar">
					$SearchForm
				</div>
			<% end_if %>
			<% include Navigation %>
		</div>
	</div>
</header>
