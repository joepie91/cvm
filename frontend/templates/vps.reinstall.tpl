<h1>Reinstall your VPS</h1>

<%foreach template in templates>
	<div class="template-option">
		<div class="template-name">
			<input type="radio" id="tpl_<%?template[id]>" name="template" value="<%?template[id]>">
			<label for="tpl_<%?template[id]>"><%?template[name]></label>
		</div>
		<div class="template-description"><%?template[description]></div>
	</div>
<%/foreach>

<div class="confirm">
	<input type="checkbox" name="confirm" value="true" id="confirm_reinstall">
	<label for="confirm_reinstall" class="confirm-text">I understand that by reinstalling my VPS, <strong>all data on the VPS is permanently lost</strong> and cannot be recovered. There will be no further confirmations, <strong>after clicking the Reinstall button the reinstallation process cannot be aborted.</strong></label>
</div>

<div class="centered">
	<button type="submit" name="submit" value="submit" class="padded spaced">Reinstall</button>
</div>
