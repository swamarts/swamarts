<div class="row">						
	<div class="col-md-3">
		<div class="">							
			@include('helpers.form_control', ['type' => 'text', 'name' => 'name', 'value' => $currency->name, 'help_class' => 'currency', 'rules' => $currency->rules()])
		</div>
	</div>
	<div class="col-md-2">
		<div class="">
			@include('helpers.form_control', ['type' => 'text', 'name' => 'code', 'value' => $currency->code, 'help_class' => 'currency', 'rules' => $currency->rules()])
		</div>
	</div>
	<div class="col-md-2">
		<div class="">
			@include('helpers.form_control', [
				'type' => 'text',
				'name' => 'format',
				'label' => trans('messages.currency_format'),
				'value' => $currency->format ? $currency->format : '{PRICE}',
				'help_class' => 'currency',
				'rules' => $currency->rules()
			])
		</div>
	</div>
	<div class="col-md-3">
		<div class="">
			@include('helpers.form_control', ['type' => 'text', 'name' => 'localisation_factor', 'label' => trans('messages.currency.localisation_factor'),'value' => $currency->localisation_factor ? $currency->localisation_factor : '0.00', 'help_class' => 'currency', 'rules' => $currency->rules()])
		</div>
	</div>
	<div class="col-md-2">
		<div class="">
			@include('helpers.form_control', ['type' => 'text', 'name' => 'usd_value', 'label' => trans('messages.currency.usdvalue'),'value' => $currency->usd_value, 'help_class' => 'currency', 'rules' => $currency->rules()])
		</div>
	</div>
</div>
<hr />
<div class="text-end">
	<button class="btn btn-secondary"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
</div>