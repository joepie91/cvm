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
			<td><%?container[status]></td>
		</tr>
	<%/foreach>
</table>
