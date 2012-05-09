<h1><%!title-reinstall></h1>

<form method="post" action="../reinstall/">
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
		<label for="confirm_reinstall" class="confirm-text"><%!reinstall-warning></label>
	</div>

	<div class="centered">
		<button type="submit" name="submit" value="submit" class="padded spaced"><%!button-reinstall></button>
	</div>
</form>
