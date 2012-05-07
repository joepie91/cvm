<h1>Overview</h1>

<!-- <h2>Consolidated traffic example</h2> -->
<div class="quota">
	<div class="quota-item">
		<h3>Disk space</h3>
		<div class="quota-bar">
			<div class="quota-bar-inner" style="width: <%?disk-percentage>%;"></div>
			<div class="quota-bar-label"><%?disk-used>/<%?disk-total><%?disk-unit></div>
		</div>
	</div>
	<div class="quota-item">
		<h3>RAM</h3>
		<div class="quota-bar">
			<div class="quota-bar-inner" style="width: <%?ram-percentage>%;"></div>
			<div class="quota-bar-label"><%?ram-used>/<%?ram-total><%?ram-unit></div>
		</div>
	</div>
	<div class="quota-item last">
		<h3>Traffic</h3>
		<div class="quota-bar">
			<div class="quota-bar-inner" style="width: <%?traffic-percentage>%;"></div>
			<div class="quota-bar-label"><%?traffic-used>/<%?traffic-total><%?traffic-unit></div>
		</div>
	</div>
	<div class="clear"></div>
</div>

<div class="controlbox">
	<a class="controlbutton button-loader" href="/<%?id>/start/">
		<img src="/images/button_start.png" class="button-icon">
		Start VPS
	</a>
	<a class="controlbutton button-loader" href="/<%?id>/restart/">
		<img src="/images/button_restart.png" class="button-icon">
		Restart VPS
	</a>
	<a class="controlbutton button-loader last" href="/<%?id>/stop/">
		<img src="/images/button_stop.png" class="button-icon">
		Stop VPS
	</a>
	<div class="clear"></div>
</div>

<h2>VPS configuration</h2>
<table class="vpsinfo vertical">
	<tr>
		<th>Status</th>
		<td>
			<%if status == running>
				<span class="online">Running</span>
			<%/if><%if status == stopped>
				<span class="offline">Stopped</span>
			<%/if><%if status == suspended>
				<span class="suspended">Suspended</span>
			<%/if><%if status == unknown>
				<span class="unknown">Unknown</span>
			<%/if>
		</td>
	</tr>
	<tr>
		<th>Server location</th>
		<td><%?server-location></td>
	</tr>
	<tr>
		<th>Operating system</th>
		<td><%?operating-system></td>
	</tr>
	<tr>
		<th>IPv4 Addresses</th>
		<td></td>
	</tr>
	<tr>
		<th>IPv6 Addresses</th>
		<td></td>
	</tr>
	<tr>
		<th>Guaranteed RAM</th>
		<td><%?guaranteed-ram></td>
	</tr>
	<tr>
		<th>Burstable RAM</th>
		<td><%?burstable-ram></td>
	</tr>
	<tr>
		<th>Disk space</th>
		<td><%?disk-space></td>
	</tr>
	<tr>
		<th>Traffic</th>
		<td><%?total-traffic-limit></td>
	</tr>
	<tr>
		<th>Bandwidth</th>
		<td><%?bandwidth-limit></td>
	</tr>
</table>
