<form method="post" action="/admin/vps/{%?id}/terminate/">
	{%if terminated == false}
		<h2>{%!title-admin-vps-terminate}</h2>
		<p>{%!vps-admin-terminate-text}</p>
		<input type="hidden" name="action" value="terminate">
		<button type="submit" name="submit" class="padded">{%!button-admin-vps-terminate}</button>
	{%else}
		{%if can-unterminate == true}
			<h2>{%!title-admin-vps-unterminate}</h2>
			<p>{%!vps-admin-unterminate-text}</p>
			<input type="hidden" name="action" value="unterminate">
			<button type="submit" name="submit" class="padded">{%!button-admin-vps-unterminate}</button>
		{%else}
			Cannot unterminate
		{%/if}
	{%/if}
</form>

