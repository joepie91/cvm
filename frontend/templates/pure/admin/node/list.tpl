<h2>{%!title-admin-nodelist}</h2>

<div class="pure-menu pure-menu-open pure-menu-horizontal submenu">
	<ul>
		<li><a href="/admin/nodes/add/"><i class="fa fa-fw fa-plus-circle"></i> {%!toolbar-addnode}</a></li>
	</ul>
</div>

<table class="pure-table pure-table-bordered stretch">
	<thead>
		<tr>
			<th>{%!list-column-hostname}</th>
			<th>{%!list-column-location}</th>
		</tr>
	</thead>
	<tbody>
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
	</tbody>
</table>
