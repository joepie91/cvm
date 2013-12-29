<h2>Template overview</h2>

<div class="pure-menu pure-menu-open pure-menu-horizontal submenu">
	<ul>
		<li><a href="/admin/templates/add/"><i class="fa fa-fw fa-plus-circle"></i> Add template(s)</a></li>
	</ul>
</div>

<table class="pure-table pure-table-bordered stretch">
	<thead>
		<tr>
			<th>Filename</th>
			<th>Template name</th>
			<th>Supported</th>
			<th>Up-to-date</th>
			<th>Available</th>
		</tr>
	</thead>
	<tbody>
		{%foreach template in templates}
			<tr class="clickable" data-url="/admin/template/{%?template[id]}/">
				<td>
					<a href="/admin/template/{%?template[id]}/">
						{%?template[filename]}
					</a>
				</td>
				<td>
					<a href="/admin/template/{%?template[id]}/">
						{%?template[name]}
					</a>
				</td>
				<td class="icon">
					{%if template[supported] == true}
						<i class="fa fa-check-circle ok"></i>
					{%else}
						<i class="fa fa-times-circle not-ok"></i>
					{%/if}
				</td>
				<td class="icon">
					{%if template[outdated] == true}
						<i class="fa fa-times-circle not-ok"></i>
					{%else}
						<i class="fa fa-check-circle ok"></i>
					{%/if}
				</td>
				<td class="icon">
					{%if template[available] == true}
						<i class="fa fa-check-circle ok"></i>
					{%else}
						<i class="fa fa-times-circle not-ok"></i>
					{%/if}
				</td>
			</tr>
		{%/foreach}
	</tbody>
</table>
