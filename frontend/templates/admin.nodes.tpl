<h2>{%!title-admin-nodelist}</h2>

<div class="toolbar">
	<a href="/admin/nodes/add/">Add node</a>
	<div class="clear"></div>
</div>

<table class="vpslist">
	<tr>
		<th>{%!list-column-hostname}</th>
		<th>{%!list-column-location}</th>
	</tr>
	{%foreach node in nodes}
		<tr class="clickable" data-url="/admin/node/{%?node[id]}/">
			<td>
				<a href="/admin/node/{%?node[id]}/">
					{%?node[hostname]}
				</a>
			</td>
			<td>
				{%?node[location]}
			</td>
		</tr>
	{%/foreach}
</table>
