<div class="sidebar">
	<a class="sidebutton" id="button_overview" href="/{%?id}/">{%!menu-overview}</a>
	<!-- <a class="sidebutton" id="button_statistics" href="/{%?id}/statistics/">{%!menu-statistics}</a> -->
	<a class="sidebutton" id="button_reinstall" href="/{%?id}/reinstall/">{%!menu-reinstall}</a>
	<!-- <a class="sidebutton" id="button_backup" href="/{%?id}/backup/">{%!menu-backups}</a> -->
	<a class="sidebutton" id="button_webshell" href="/{%?id}/console/">{%!menu-console}</a>
	<a class="sidebutton" id="button_password" href="/{%?id}/password/">{%!menu-password}</a>
	<a class="sidebutton" id="button_ip" href="/{%?id}/ip/">{%!menu-ip}</a>
	<!-- <a class="sidebutton" id="button_alerts" href="/{%?id}/alerts/">{%!menu-alerts}</a>
	<a class="sidebutton" id="button_api" href="/{%?id}/api/">{%!menu-api}</a> -->
</div>

{%if isset|error}
	{%?error}
{%/if}

{%?contents}

<div class="clear"></div>
