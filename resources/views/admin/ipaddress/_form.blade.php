<div class="row">						
	<div class="col-md-3">
		<div class="">
			@include('helpers.form_control', ['type' => 'text', 'placeholder' => trans('messages.ip_placeholder'),'name' => 'ip_address','value' => $ipaddress->ip_address, 'help_class' => 'ipaddress', 'rules' => $ipaddress->rules()])
		</div>
	</div>
	<div class="col-md-3">
		<div class="">		
			{{-- <label for="user_name" class="mt-2">User</label><br>
			<select name="user_id" id="user_name" class="form-control js-example-basic-single">
				<option value="">Please select users</option>
				@if (!empty($customer))
					@foreach ($customer as $item)
						<option value="{{!empty($item->user) ? $item->user->id : $item->id}}" {{!empty($item->user) ? ($item->user->id == $ipaddress->user_id ? 'selected' : '') : $item->id}}>{{!empty($item->user) ? $item->user->displayName() : $item->id}}</option>
					@endforeach
				@endif
			</select> --}}
			<label for="sending_server_id" class="mt-2">SMTP Server</label><span class="text-danger"> *</span><br>
			<select name="sending_server_id" id="sending_server_id" class="form-control js-example-basic-single">
				<option value="" disabled selected>Select SMTP Server</option>
					@foreach ($sendingserver as $item)
						<option value="{{$item->id}}" {{$item->id == $ipaddress->sending_server_id ? 'selected' : '' }} {{$item->id == old('sending_server_id') ? 'selected' : '' }} {{$item->status == 'inactive' ? 'disabled' : '' }}>{{$item->name}}{{$item->status == 'inactive' ? '(Inactive)' : '' }}</option>
					@endforeach
			</select>
		</div>
	</div>

	{{-- <div class="col-md-3">
		<div class="">		
			<label for="status" class="mt-2">Status</label><br>
			<select name="status" id="status" class="form-control js-example-basic-single">
				<option value="">Please select Select</option>
				<option {{$ipaddress->status == 'open' ? 'select' : ''}} value="open">Open</option>
				<option {{$ipaddress->status == 'assign' ? 'select' : ''}} value="assign">Assign</option>
				<option {{$ipaddress->status == 'inactive' ? 'select' : ''}} value="inactive">Inactive</option>
				<option {{$ipaddress->status == 'closed' ? 'select' : ''}} value="closed">Closed</option>
			</select>
		</div>
	</div> --}}
	<div class="col-md-6">
		<label for="" class="my-1">{{trans('messages.ip_label.price')}}</label>
		<div class="row">
			<div class="col-md-6">
				@include('helpers.form_control', ['label' => "", 'type' => 'text', 'placeholder' => trans('messages.monthly'),'name' => 'price_monthly','value' => $ipaddress->price_monthly, 'help_class' => 'ipaddress', 'rules' => $ipaddress->rules()])
			</div>
			<div class="col-md-6">
				@include('helpers.form_control', ['label' => "", 'type' => 'text', 'placeholder' => trans('messages.yearly'),'name' => 'price_yearly','value' => $ipaddress->price_yearly, 'help_class' => 'ipaddress', 'rules' => $ipaddress->rules()])
			</div>							
		</div>
	</div>
	<div class="col-md-12 text-end mt-4">
		<button class="btn btn-secondary mt-2"><i class="icon-check"></i> {{ trans('messages.save') }}</button>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('.js-example-basic-single').select2();
	});
</script>