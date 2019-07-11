<style>
.radio-custom input[type="radio"]{
    width: 150px;
    height: 150px;
    opacity: 0;
    z-index: 1;
}
.radio-custom label::before {
    width: 150px;
    height: 150px;
    border: 3px solid #818F93;
}
.radio-custom label::after {
    width: 120px;
    height: 120px;
    left: 15px;
    top: 15px;
    background-color: #FCB348;
    border-radius: 0%;
}
</style>
<div class="col-md-6">
	<div class="rms-content-body" style="height:175px;">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-4">
					<div class="radio-custom radio-primary margin-top-5">
					  <input id="export" name="document_type" value="EXPORT" type="radio" onclick="check(this.value,'document_order');">
					  <label for="export">&nbsp;</label>
					</div>
				</div>
				<div class="col-md-8">
					<label for="export" style="font-size:40px;margin-top:55px">EXPORT</label>
				</div>
			</div>
		</div>
		<div>&nbsp;</div>
	</div>
</div>
<div class="col-md-6">
	<div class="rms-content-body" style="height:175px;">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-4">
					<div class="radio-custom radio-primary margin-top-5">
					  <input id="export" name="document_type" value="IMPORT" type="radio" onclick="check(this.value,'document_order');">
					  <label for="import">&nbsp;</label>
					</div>
				</div>
				<div class="col-md-8">
					<label for="import" style="font-size:40px;margin-top:55px">IMPORT</label>
				</div>
			</div>
		</div>
		<div>&nbsp;</div>
	</div>
</div>
<div class="col-md-6">
	<div class="rms-content-body" style="height:175px;">
		<div class="col-md-12">
			<div class="row">
				&nbsp;
			</div>
		</div>
		<div>&nbsp;</div>
	</div>
</div>
<div class="col-md-6">
	<div class="rms-content-body" style="height:175px;">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-4">
					<div class="radio-custom radio-primary margin-top-5">
					  <input id="import_p" name="document_type" value="PERPANJANGAN IMPORT" type="radio" onclick="check(this.value,'document_order');">
					  <label for="import_p">&nbsp;</label>
					</div>
				</div>
				<div class="col-md-8">
					<label for="import_p" style="font-size:40px;margin-top:25px">PERPANJANGAN IMPORT</label>
				</div>
			</div>
		</div>
		<div>&nbsp;</div>
	</div>
</div>
<input type="hidden" name="document_order" id="document_order" mandatory="yes" readonly>