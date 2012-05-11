<table class="userinfo vertical">
	<tr>
		<th><%!admin-title-id></th>
		<td><%?id></td>
	</tr>
	<tr>
		<th><%!admin-title-username></th>
		<td><%?username></td>
	</tr>
	<tr>
		<th><%!admin-title-email></th>
		<td><%?email></td>
	</tr>
	<tr>
		<th><%!admin-title-accesslevel></th>
		<td>
			<%if accesslevel == 1>
				<%!admin-level-enduser>
			<%/if><%if accesslevel == 10>
				<%!admin-level-reseller>
			<%/if><%if accesslevel == 20>
				<%!admin-level-nodeadmin>
			<%/if><%if accesslevel == 30>
				<%!admin-level-masteradmin>
			<%/if>
		</td>
	</tr>
	<tr>
		<th><%!admin-title-containers></th>
		<td><%?containers></td>
	</tr>
</table>
