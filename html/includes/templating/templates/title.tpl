	<div id="sidemenu">
		<ul>
		{section name=sidebar loop=$sidelinks}
			<li><a class="sidemenulink" href="$sidebar[sidebar].href">$sidebar[sidebar].name</a>
		{/section}
		</ul>
	</div>