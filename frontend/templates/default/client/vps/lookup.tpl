<h1>{%!title-overview}</h1>

<div class="quota">
	<div class="quota-item">
		<h3>{%!overview-quota-title-disk}</h3>
		<div class="quota-bar">
			<div class="quota-bar-inner" style="width: {%?disk-percentage}%;"></div>
			<div class="quota-bar-label">{%?disk-used}/{%?disk-total}{%?disk-unit}</div>
		</div>
	</div>
	<div class="quota-item">
		<h3>{%!overview-quota-title-ram}</h3>
		<div class="quota-bar">
			<div class="quota-bar-inner" style="width: {%?ram-percentage}%;"></div>
			<div class="quota-bar-label">{%?ram-used}/{%?ram-total}{%?ram-unit}</div>
		</div>
	</div>
	<div class="quota-item last">
		<h3>{%!overview-quota-title-traffic}</h3>
		<div class="quota-bar">
			<div class="quota-bar-inner" style="width: {%?traffic-percentage}%;"></div>
			<div class="quota-bar-label">{%?traffic-used}/{%?traffic-total}{%?traffic-unit}</div>
		</div>
	</div>
	<div class="clear"></div>
</div>

<div class="controlbox">
	<a class="controlbutton button-loader" href="/{%?id}/start/">
		<img src="/templates/default/static/images/button/start.png" class="button-icon">
		{%!overview-button-start}
	</a>
	<a class="controlbutton button-loader" href="/{%?id}/restart/">
		<img src="/templates/default/static/images/button/restart.png" class="button-icon">
		{%!overview-button-restart}
	</a>
	<a class="controlbutton button-loader last" href="/{%?id}/stop/">
		<img src="/templates/default/static/images/button/stop.png" class="button-icon">
		{%!overview-button-stop}
	</a>
	<div class="clear"></div>
</div>

<h2>{%!overview-title-configuration}</h2>
<table class="vpsinfo vertical">
	<tr>
		<th>{%!overview-title-status}</th>
		<td>
			{%if status == running}
				<span class="online">{%!overview-status-running}</span>
			{%/if}{%if status == stopped}
				<span class="offline">{%!overview-status-stopped}</span>
			{%/if}{%if status == suspended}
				<span class="suspended">{%!overview-status-suspended}</span>
			{%/if}{%if status == unknown}
				<span class="unknown">{%!overview-status-unknown}</span>
			{%/if}
		</td>
		<th>{%!overview-title-os}</th>
		<td>{%?operating-system}</td>
	</tr>
	<tr>
		<th>{%!overview-title-guaranteed}</th>
		<td>{%?guaranteed-ram}</td>
		<th>{%!overview-title-burstable}</th>
		<td>{%?burstable-ram}</td>
	</tr>
	<tr>
		<th>{%!overview-title-disk}</th>
		<td>{%?disk-space}</td>
		<th>{%!overview-title-bandwidth}</th>
		<td>{%?bandwidth-limit}</td>
	</tr>
	<tr>
		{%if total-traffic-limit == "0B"}
			<th>{%!overview-title-traffic-incoming}</th>
			<td>{%?incoming-traffic-limit}</td>
			<th>{%!overview-title-traffic-outgoing}</th>
			<td>{%?outgoing-traffic-limit}</td>
		{%else}
			<th>{%!overview-title-traffic}</th>
			<td colspan="3">{%?total-traffic-limit}</td>
		{%/if}
	</tr>
	<tr>
		<th>{%!overview-title-location}</th>
		<td colspan="3">{%?server-location}</td>
	</tr>
	<tr>
		<th>{%!overview-title-ipv4}</th>
		<td colspan="3"></td>
	</tr>
	<tr>
		<th>{%!overview-title-ipv6}</th>
		<td colspan="3"></td>
	</tr>
</table>

{%if accesslevel = 20}
	<h3>{%!header-vps-admin}</h3>
	<div class="vps-admin">
		<a href="/admin/container/{%?id}/suspend/">{%!vps-admin-suspend}</a>
		<a href="/admin/container/{%?id}/transfer/">{%!vps-admin-transfer}</a>
		<a href="/admin/container/{%?id}/terminate/">{%!vps-admin-terminate}</a>
	</div>
	<div class="clear"></div>
{%/if}
