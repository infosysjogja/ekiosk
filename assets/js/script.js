var applicationName = "KIOSK";
$(document).ready(function(){
	date('date');
	datedefault('datedefault');
	dateminutes('dateminutes');
	keyboard('key-num');
	keyboard('key-full');
	$('#scan').focus();
	$('#rms-wizard').stepWizard({
		stepTheme: 'steptheme1',/*defaultTheme,steptheme1,steptheme2*/
		allstepClickable: false,
		compeletedStepClickable: false,
		stepCounter: true,
		StepImage: true, 
		animation: true,
		animationClass: "fadeIn",
		stepValidation:true,
		validation : true
	});
});

function check(val,id){
	$('#'+id).val(val);
}
/*
function keyboard(id){
	var anims = ['show', 'fadeIn', 'slideDown', 'blind', 'blind', 'bounce', 'clip', 'clip', 'drop', 'drop', 'fold', 'fold', 'slide', 'slide', '']; 
	var animOpts = [null, null, null, {}, {direction: 'horizontal'}, {}, {}, {direction: 'horizontal'}, {}, {direction: 'up'}, {},  {horizFirst: true}, {}, {direction: 'up'}, {}]; 
	if(id=='key-full'){
		$('.'+id).keypad('option',{
			keypadOnly: false, 
			layout: $.keypad.qwertyLayout,
			showAnim: anims[8], 
			showOptions: animOpts[9]
		});
	}else if(id=='key-num'){
		$('.'+id).keypad({
			keypadOnly: false,
			showAnim: anims[8], 
			showOptions: animOpts[9]
		});
	}
}
*/

function keyboard(id){
	if(id=='key-full'){
		$('.'+id).keypad({
			keypadOnly: false, 
			layout: $.keypad.qwertyLayout
		});
	}else if(id=='key-num'){
		$('.'+id).keypad({
			keypadOnly: false
		});
	}
}

function get_data(id,url,act){
	if(act == "profile"){
		var length = 4;
		if(url == 'home/get_data/customer/id'){
			var length = 6;
		}
		if(id.length >= length){
			$.post(site_url+'/'+url,{key:id},
				function(data){
					$('.v_npwp').html(id.toUpperCase());
					if(data.result == 1){
						$('#npwp').val(data.npwp);
						$('#customer_id').val(data.cust_id);
						$('#customer_name').val(data.cust_name);
						$('#customer_address').val(data.cust_address);
						$('.v_npwp').html(data.npwp);
						$('.v_customer_id').html(data.cust_id);
						$('.v_customer_name').html(data.cust_name);
						$('.v_customer_address').html(data.cust_address);
					}else{
						//$('#npwp').val("");
						//$('#customer_id').val("");
						$('#customer_name').val("");
						$('#customer_address').val("");
						//$('.v_npwp').html("");
						//$('.v_customer_id').html("");
						$('.v_customer_name').html("");
						$('.v_customer_address').html("");
					}
			}, "json");
		}
	}else if(act == "isocode"){
		if(id.length == 4){
			$.post(site_url+'/'+url,{key:id},
			function(data){
				if(data.size == ""){
					swalert('info',"ISO CODE tidak ditemukan");
					$('#CONT_SIZE').val("");
					$('#CONT_TYPE').val("");
					$('#CONT_HEIGHT').val("");
				}else{
					$('#CONT_SIZE').val(data.size);
					$('#CONT_TYPE').val(data.type);
					$('#CONT_HEIGHT').val(data.height);
				}
			}, "json");
		}
	}	
}

					

function set_data(id,div,act){
	var url = site_url+'/'+act+'/'+Math.random();
	var arrdiv = div.split('|');
	$.post(url,{id:id},
		function(data){
			$.each(data, function(a, b){
				$('#'+a).val(b);
			});
	}, "json");
}

function get_change_header(id,val){
	$('#'+id).val(val);
	return false;
}

function get_change(id,act,name){
	var url = site_url+'/'+act+'/'+Math.random();
	$.post(url,{id:id},
		function(data){
			$('#div_'+name).html(data.result);
	}, "json");
}

function Loading(boolean){
	if(boolean){
		LoadingOpen();
	}
	else{
		LoadingClose();
	}	
}

function signout(){
	var url = base_url+'index.php/home/signout/'+Math.random();
	$.post(url,{'signout':'signout'},
		function(data){
			window.location.href = data.url;
	},'json');
}

function popup(url,id,width,height){
	jpopup(site_url+"/"+url,applicationName,id,width,height);
	return false;
}

/*
function tb_chk(formid,status,id){
 	$('input:not(:checked)').parent().parent().removeClass("selected");
 	$('input:checked').parent().parent().addClass("selected");
}*/

function autocomplete(divid,url,source){
	$("#"+divid).autocomplete({ 
		minLength:1,
		delay:0,
		autofocus:true,
		source: function (request, response){
			$.ajax({
			  type: "POST",
			  url: site_url + url,
			  data: request,
			  success: response,
			  dataType: 'json'
			});
		  },
		 select:source
	});
}

function validasi(form,div){
	var notvalid = 0;
	var notnumber = 0;
	var notemail = 0;
	var regNumber =/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/;
	var regEmail = /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
		$.each($('#'+form+" #"+div+" input, #"+form+" #"+div+" textarea, #"+form+" #"+div+" select"), function(n,element){;
			if($(this).attr('mandatory')=="yes"){
				$(this).addClass('mandatory');
				if($(element).val()==""){
					$("#"+element.id).css({
						'background-size':'100% 2px, 100% 1px',
						'border':'1px solid red'
					});
					notvalid++;
				}else{
					$("#"+element.id).removeAttr('style');
				}
			}
			if($(this).attr('format')=="number" && (!regNumber.test($(this).val()) && $(this).val()!="")){
				$(this).addClass('format');
				notnumber++;
			}
			if($(this).attr('format')=="email"){
				if(regEmail.test($(this).val()) == false){
					$("#"+element.id).css({
						'background-size':'100% 2px, 100% 1px',
						'background-image':'linear-gradient(#f44336,#f44336),linear-gradient(#e0e0e0,#e0e0e0)'
					});
					notemail++;
				}
			}
		});
	if(notvalid > 0 || notnumber > 0 || notemail > 0){
		var errorString = "";
		if(notvalid > 0){
		 	errorString += 'Terdapat data yang harus diisi<br>';
		}
		if(notnumber > 0){
			errorString += '- Format number tidak sesuai';
		}
		if(notemail > 0){
			errorString += '- Format email tidak sesuai\n';
		}
		swalert('error',errorString);
		//notify(errorString,'error');
		//swalert('error',errorString);
		return false;
	}else{
		return true;	
	}
	return false;
}


function required(form){
	var notvalid = 0;
	var notnumber = 0;
	var notemail = 0;
	var regNumber =/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/;
	var regEmail = /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
		$.each($('#'+form+" input, #"+form+" textarea, #"+form+" select"), function(n,element){;
			if($(this).attr('mandatory')=="yes"){
				$(this).addClass('mandatory');
				if($(element).val()==""){
					$("#"+element.id).css({
						'background-size':'100% 2px, 100% 1px',
						'border':'1px solid red'
					});
					notvalid++;
				}else{
					$("#"+element.id).removeAttr('style');
				}
			}
			if($(this).attr('format')=="number" && (!regNumber.test($(this).val()) && $(this).val()!="")){
				$(this).addClass('format');
				notnumber++;
			}
			if($(this).attr('format')=="email"){
				if(regEmail.test($(this).val()) == false){
					$("#"+element.id).css({
						'background-size':'100% 2px, 100% 1px',
						'background-image':'linear-gradient(#f44336,#f44336),linear-gradient(#e0e0e0,#e0e0e0)'
					});
					notemail++;
				}
			}
		});
	if(notvalid > 0 || notnumber > 0 || notemail > 0){
		var errorString = "";
		if(notvalid > 0){
		 	errorString += 'Terdapat data yang harus diisi<br>';
		}
		if(notnumber > 0){
			errorString += '- Format number tidak sesuai';
		}
		if(notemail > 0){
			errorString += '- Format email tidak sesuai\n';
		}
		swalert('error',errorString);
		//notify(errorString,'error');
		//swalert('error',errorString);
		return false;
	}else{
		return true;	
	}
	return false;
}

function strpos(haystack, needle, offset){
    var i = (haystack + '').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}

function date(className){
	$('.'+className).datetimepicker({
	 timepicker:false,
	 format:'d-m-Y', 
	 //mask:'39-19-9999' //'9999/19/39 29:59'
	});
}

function datedefault(className){
	$('.'+className).datetimepicker({
	 timepicker:false,
	 format:'d-m-Y 23:59', 
	 //mask:'39-19-9999' //'9999/19/39 29:59'
	});
}

function datetime(className){
	$('.'+className).datetimepicker({
	 timepicker:true,
	 format:'d-m-Y H:i:s', 
	 //mask:'39-19-9999 29:59:59' //'9999/19/39 29:59'
	});
}

function dateminutes(className){
	$('.'+className).datetimepicker({
	 timepicker:true,
	 format:'d-m-Y H:i', 
	// mask:'39-19-9999 29:59' //'9999/19/39 29:59'
	});
}

function swalert(type,message,time){
	if(time!=undefined) time = time;
	else time = 2000;
	if(type=="success"){
		swal({title:applicationName,
			  text:message,
			  timer:time,
			  type:'success',
			  showConfirmButton: false,
			  html: true
		});
	}else if(type=="error"){
		swal({title:applicationName,
			  text:message,
			  timer:time,
			  type:'error',
			  showConfirmButton: false,
			  html: true
		});	
	}else if(type=="info"){
		swal({title:applicationName,
			  text:message,
			  type:'info',
			  showConfirmButton:true,
			  html: true
		});
	}
	
}

function tb_chkall(formid,status){
	var valtemp = $('#tmpchk'+formid).val();
	if(status==false){
		$("#"+formid+" input:checkbox:not(#chkall"+formid+")").parent().parent().removeClass("selected");
		$('input[id^="chk'+formid+'"]').prop("checked",false);
	}else{
		$("#"+formid+" input:checkbox:not(#chkall"+formid+")").parent().parent().addClass("selected");
		$('input[id^="chk'+formid+'"]').prop("checked",true);
	}
	if(status == true){
		$('input[id^="chk'+formid+'"]').each(function(i){
			if(strpos(valtemp,$(this).val()) === false){
		 		$('#tmpchk'+formid).val($(this).val()+"*"+$('#tmpchk'+formid).val());
			}
		});
	}else{
		$('input[id^="chk'+formid+'"]').each(function(i){
		 	$('#tmpchk'+formid).val($('#tmpchk'+formid).val().replace($(this).val()+'*',''));
		});
	}
}

function tb_chk(formid,status,id){
 	$('input:not(:checked)').parent().parent().removeClass("selected");
 	$('input:checked').parent().parent().addClass("selected");
	tmp_chk(formid,status,id);
}

function tmp_chk(formid,status,id){
	var valtemp = $('#tmpchk'+formid).val();
	if(status==true){
		if(strpos(valtemp,id)===false){
			$('#tmpchk'+formid).val(id+"*"+$('#tmpchk'+formid).val());
		}
	}else{
		$('#tmpchk'+formid).val($('#tmpchk'+formid).val().replace(id+'*',''));
	}
}

function formatDate(valdate){
	var arrdate = valdate.split(' ');
	var arrday = arrdate[0].split('-');
	return arrday[2]+'-'+arrday[1]+'-'+arrday[0];
}

function formatDatetime(valdate){
	var arrdate = valdate.split(' ');
	var arrday = arrdate[0].split('-');
	return arrday[2]+'/'+arrday[1]+'/'+arrday[0];
}

function save_post(form){
	if(required(form)){
		swal({title:'Confirm',
		  text:'Apakah ingin proses data ?',
		  type:'info',
		  showCancelButton:true,
		  closeOnConfirm:true,
		  showLoaderOnConfirm:true,
		 },function(r){
			 if(r){
				$.ajax({
				type: 'POST',
				url: $('[name="'+form+'"]').attr('action'),
				data: $('[name="'+form+'"]').serialize(),
				beforeSend: function(){Loading(true)},
				complete:function(){Loading(false)},
				success: function(data){
					if(data.search("MSG")>=0){
						arrdata = data.split('#');
						if(arrdata[1]=="OK"){
							swalert('success',arrdata[2]);
							setTimeout(function(){
								var popup = $('[name="'+form+'"]').attr('popup');
								close_popup(popup);
							}, 1500);
							return false;
						}else{
							swalert('error',arrdata[2]);
						}
					}
				}
				});
			 }else{
				return false
			 }
		});
	}
}

function save_data(form,div){
	if(required(form)){
		swal({title:'Confirm',
		  text:'Apakah ingin proses data ?',
		  type:'info',
		  showCancelButton:true,
		  closeOnConfirm:true,
		  showLoaderOnConfirm:true,
		 },function(r){
			 if(r){
				$.ajax({
					type: 'POST',
					url: $('[name="'+form+'"]').attr('action'),
					data: $('[name="'+form+'"]').serialize(),
					beforeSend: function(){Loading(true)},
					complete:function(){Loading(false)},
					success: function(data){
						if(data.search("MSG")>=0){
							arrdata = data.split('#');
							if(arrdata[1]=="OK"){
								$('#'+div).html('<span style="color:green">'+arrdata[2]+'</span>');
							}else{
								$('#'+div).html('<span style="color:red">'+arrdata[2]+'<span>');
							}
						}
					}
				});
			 }else{
				return false
			 }
		});
	}
}

function close_popup(type){
	switch(type){
		case '1' : popup = jpopup_close(); break
		case '2' : popup = jpopup_closetwo(); break;
	}
	return popup;
}
