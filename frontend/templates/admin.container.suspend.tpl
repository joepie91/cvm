<form method="post" action="/admin/container/<%?id>/suspend/">
	<%if suspended == false>
		<h2><%!title-admin-vps-suspend></h2>
		<p><%!vps-admin-suspend-text></p>
		<input type="hidden" name="action" value="suspend">
		<button type="submit" name="submit" class="padded"><%!button-admin-vps-suspend></button>
	<%/if><%if suspended == true>
		<h2><%!title-admin-vps-unsuspend></h2>
		<p><%!vps-admin-unsuspend-text></p>
		<input type="hidden" name="action" value="unsuspend">
		<button type="submit" name="submit" class="padded"><%!button-admin-vps-unsuspend></button>
	<%/if>
</form>

