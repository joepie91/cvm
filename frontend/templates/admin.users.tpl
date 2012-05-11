<h2><%!title-admin-userlist></h2>

<table class="userlist">
	<tr>
		<th><%!admin-title-username></th>
		<th><%!admin-title-email></th>
		<th><%!admin-title-accesslevel></th>
	</tr>
	<%foreach user in users>
		<tr class="clickable" data-url="/admin/user/<%?user[id]>/">
			<td><%?user[username]></td>
			<td><%?user[email]></td>
			<td>
				<%if user[accesslevel] == 1>
					<%!admin-level-enduser>
				<%/if><%if user[accesslevel] == 10>
					<%!admin-level-reseller>
				<%/if><%if user[accesslevel] == 20>
					<%!admin-level-nodeadmin>
				<%/if><%if user[accesslevel] == 30>
					<%!admin-level-masteradmin>
				<%/if>
			</td>
		</tr>
	<%/foreach>
</table>
