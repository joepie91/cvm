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
	<form method="post" action="/admin/templates/add" class="pure-form pure-form-aligned">
		{%foreach template in templates}
			<section>
				<div class="pure-controls">
					<h3>{%?template}</h3>
					<input type="hidden" name="filename[]" value="{%?template}">
				</div>
				
				<div class="pure-control-group">
					<label>Template name</label>
					{%input type="text" group="addtemplate" name="name[]"}
				</div>
				
				<div class="pure-control-group">
					<label>Description</label>
					<textarea name="description[]"></textarea>
				</div>
			</section>
		{%/foreach}
		<div class="pure-controls">
			<button type="submit" name="submit" class="pure-button pure-button-primary">Add templates</button>
		</div>
	</form>
{%/if}
