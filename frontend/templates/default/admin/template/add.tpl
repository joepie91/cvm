<h2>Add templates</h2>

{%if isempty|templates == true}
	<p>
		To add new templates, add the corresponding tar.gz files to /etc/cvm/templates on the master node, and reload this page.
		New files will be automatically detected, and you will be able to add them as templates from this page. 
	</p>
	
	<p>
		All templates will be automatically synchronized to slave nodes.
	</p>
{%else}
	<form method="post" action="/admin/template/add">
		{%foreach template in templates}
			<div class="darkform">
				<h3>{%?template}</h3>
				
				<div class="field">
					<label>Name</label>
					{%input type="text" group="addtemplate" name="name[]"}
					<div class="clear"></div>
				</div>
			</div>
		{%/foreach}
	</form>
{%/if}
