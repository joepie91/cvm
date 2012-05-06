<table class="vpslist">
	<tr>
		<th></th>
		<th>Hostname</th>
		<th>Platform</th>
		<th>Node</th>
		<th>Disk space</th>
		<th>RAM</th>
		<th>Template</th>
	</tr>
	<%foreach container in containers>
		<tr class="clickable" data-url="/<%?container[id]>/">
			<td class="container-status">
				<%if container[status] == running>
					<img src="/images/icon_online.png" alt="Running">
				<%/if><%if container[status] == stopped>
					<img src="/images/icon_offline.png" alt="Stopped">
				<%/if><%if container[status] == suspended>
					<img src="/images/icon_suspended.png" alt="Suspended">
				<%/if>
			</td>
			<td>
				<a href="/<%?container[id]>/">
					<%?container[hostname]>
				</a>
			</td>
			<td>
				<a href="/<%?container[id]>/">
					<%if container[virtualization-type] == 1>
						OpenVZ
					<%/if><%if container[virtualization-type] == 2>
						Xen PV
					<%/if><%if container[virtualization-type] == 3>
						Xen HVM
					<%/if><%if container[virtualization-type] == 4>
						KVM
					<%/if>
				</a>
			</td>
			<td>
				<a href="/<%?container[id]>/">
					<span class="nodename"><%?container[node]></span>
					<span class="hostname">(<%?container[node-hostname]>)</span>
				</a>
			</td>
			<td>
				<%?container[diskspace]>
				<span class="unit"><%?container[diskspace-unit]></span>
			</td>
			<td>
				<%?container[guaranteed-ram]>
				<span class="unit"><%?container[guaranteed-ram-unit]></span>
			</td>
			<td><%?container[template]></td>
		</tr>
	<%/foreach>
</table>
