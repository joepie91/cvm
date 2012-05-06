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
			<td><%?container[virtualization-type]></td>
			<td><%?container[node]> (<%?container[node-hostname]>)</td>
			<td><%?container[diskspace]></td>
			<td><%?container[guaranteed-ram]></td>
			<td><%?container[template]></td>
			<td><%?container[hostname]></td>
		</tr>
	<%/foreach>
</table>
