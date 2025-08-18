<header class="header" role="banner">
	<div class="inner">
		<div class="size4of4 lastUnit">
			<a href="$BaseHref" class="brand" rel="home">
				$SiteConfig.Logo.ScaleWidth(300)
			</a>
			<div class="clearfix"></div>
		</div>
		<div class="size4of4 lastUnit">
			<% if $SiteConfig.Tagline %>
			<p class="tagline"><br/>$SiteConfig.Tagline</p>
			<% end_if %>
			<p style="color:#fff;">
				<% if $CurrentMember %>
					Logged in as: <a href="profile">$CurrentMember.Email</a> | 
					<a href="Security/logout">Log out</a>
				<% else %>
					<!-- <a href="firebaselogin">Log in</a> -->
					<a href="firebaselogin?mode=select&signInSuccessUrl=$Link">Log in</a>
				<% end_if %>
			</p>
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
