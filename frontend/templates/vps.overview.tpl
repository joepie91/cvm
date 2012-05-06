<h1>Overview</h1>

<!-- <h2>Consolidated traffic example</h2> -->
<div class="quota">
	<div class="quota-item">
		<h3>Disk space</h3>
		<div class="quota-bar">
			<div class="quota-bar-inner" style="width: 55%;"></div>
			<div class="quota-bar-label">55/100GB</div>
		</div>
	</div>
	<div class="quota-item">
		<h3>RAM</h3>
		<div class="quota-bar">
			<div class="quota-bar-inner" style="width: 24%;"></div>
			<div class="quota-bar-label">241/1024MB</div>
		</div>
	</div>
	<div class="quota-item last">
		<h3>Traffic</h3>
		<div class="quota-bar">
			<div class="quota-bar-inner" style="width: 8%;"></div>
			<div class="quota-bar-label">80/1000GB</div>
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

<div class="infobox">
	<h2>VPS configuration</h2>
	<table class="vpsinfo">
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
			<td>98.142.213.226, 204.12.235.84</td>
		</tr>
		<tr>
			<th>IPv6 Addresses</th>
			<td>2607:f7a0:1:1::24:6</td>
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
</div>
