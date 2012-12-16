<h2>{%!title-admin-vpslist}</h2>

<table class="vpslist">
	<tr>
		<th></th>
		<th>{%!list-column-hostname}</th>
		<th>{%!list-column-platform}</th>
		<th>{%!list-column-disk}</th>
		<th>{%!list-column-ram}</th>
		<th>{%!list-column-template}</th>
	</tr>
	{%foreach vps in vpses}
		<tr class="clickable" data-url="/{%?vps[id]}/">
			<td class="container-status">
				{%if vps[status] == running}
					<img src="/images/icon_online.png" alt="{%!list-status-running}">
				{%/if}{%if vps[status] == stopped}
					<img src="/images/icon_offline.png" alt="{%!list-status-stopped}">
				{%/if}{%if vps[status] == suspended}
					<img src="/images/icon_suspended.png" alt="{%!list-status-suspended}">
				{%/if}
			</td>
			<td>
				<a href="/{%?vps[id]}/">
					{%?vps[hostname]}
				</a>
			</td>
			<td>
				<a href="/{%?vps[id]}/">
					{%if vps[virtualization-type] == 1}
						OpenVZ
					{%/if}{%if vps[virtualization-type] == 2}
						Xen PV
					{%/if}{%if vps[virtualization-type] == 3}
						Xen HVM
					{%/if}{%if vps[virtualization-type] == 4}
						KVM
					{%/if}
				</a>
			</td>
			<td>
				{%?vps[diskspace]}
				<span class="unit">{%?vps[diskspace-unit]}</span>
			</td>
			<td>
				{%?vps[guaranteed-ram]}
				<span class="unit">{%?vps[guaranteed-ram-unit]}</span>
			</td>
			<td>{%?vps[template]}</td>
		</tr>
	{%/foreach}
</table>
