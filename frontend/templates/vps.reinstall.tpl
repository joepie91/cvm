<h1>Reinstall your VPS</h1>

<%foreach template in templates>
	<div class="template-option">
		<input type="radio" id="tpl_<%?template[id]>" name="template" value="<%?template[id]>"><label class="template-name" for="tpl_<%?template[id]>"><%?template[name]></label>
		<div class="template-description"><%?template[description]></div>
	</div>
<%/foreach>
