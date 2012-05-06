<table>
	<tr>
		<th>Hostname</th>
		<th>Platform</th>
		<th>Node</th>
		<th>Disk space</th>
		<th>RAM</th>
		<th>Template</th>
		<th>Status</th>
	</tr>
	<%foreach container in containers>
		<tr>
			<td><%?container[hostname]></td>
			<td>
				<%if container[virtualization-type] == 1>
					OpenVZ
				<%/if><%if container[virtualization-type] == 2>
					Xen PV
				<%/if><%if container[virtualization-type] == 3>
					Xen HVM
				<%/if><%if container[virtualization-type] == 4>
					KVM
				<%/if>
			</td>
			<td><%?container[node]> (<%?container[node-hostname]>)</td>
			<td><%?container[diskspace]></td>
			<td><%?container[guaranteed-ram]></td>
			<td><%?container[template]></td>
			<td>
				<%if container[status] == running>
					<span class="online">Running</span>
				<%/if><%if container[status] == stopped>
					<span class="offline">Stopped</span>
				<%/if><%if container[status] == suspended>
					<span class="suspended">Suspended</span>
				<%/if><%if container[status] == unknown>
					<span class="unknown">Unknown</span>
				<%/if>
			</td>
		</tr>
	<%/foreach>
</table>
