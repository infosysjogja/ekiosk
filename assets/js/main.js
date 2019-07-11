var applicationName = "SIMPEL";
function signin(form){
	if(validasi(form)){
		$.ajax({
			type: 'POST',
			url: $('[name="'+form+'"]').attr('action')+ '/ajax',
			data: $('[name="'+form+'"]').serialize(),
			dataType: 'json',
			success: function(data){
				if(typeof(data) != 'undefined'){
					var arrayDataTemp = data.returnData.split("|");
					if(arrayDataTemp[0]>0){
						swalert('success',arrayDataTemp[1]);
						setTimeout(function(){window.location.href=arrayDataTemp[2]}, 1500);
					}else{
						swalert('error',arrayDataTemp[1]);
					}
				}
			}
		});
	}else{
		return false
	}
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
	}
	else if(type=="error"){
		swal({title:applicationName,
			  text:message,
			  timer:time,
			  type:'error',
			  showConfirmButton: false,
			  html: true
		});	
	}
}

function notify(message,type){
	if(type=="success") classIcon = 'toast-success';
	else if(type=="error") classIcon = 'toast-danger';
	else if(type=="warning") classIcon = 'toast-warning';
	else classIcon = 'toast-info';
	options = {tapToDismiss: true,
			   toastClass: 'toast',
			   containerId: 'toast-container',
			   showMethod:'slideDown',
			   debug: false,
			   fadeIn: 300,
			   fadeOut: 1000,
			   extendedTimeOut: 3000,
			   positionClass: 'toast-top-right',
			   timeOut: 3000,
			   titleClass: 'toast-title',
			   messageClass: 'toast-message',
			   progressBar:true,
			   preventDuplicates: true,
			   iconClass:classIcon
            }
	switch(type){
		case 'success' :
			toastr.success(message,'SUCCESS',options);
		break;
		case 'error' :
			toastr.error(message,'ERROR',options);
		break;
		case 'warning' :
			toastr.warning(message,'WARNING',options);
		break;
		deafault :
			toastr.info(message,'Information',options);
		break;
	}
}

function required(form,div){
	var notvalid = 0;
	var notnumber = 0;
	var regNumber =/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/;
		$.each($('#'+form+" #"+div+" input, #"+form+" #"+div+" textarea, #"+form+" #"+div+" select"), function(n,element){;
			if($(this).attr('mandatory')=="yes"){
				$(this).addClass('mandatory');
				if($(element).val()==""){
					$("#"+element.id).css({
						'background-size':'100% 2px, 100% 1px',
						'background-image':'linear-gradient(#f44336,#f44336),linear-gradient(#e0e0e0,#e0e0e0)'
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
		});
	if(notvalid>0 || notnumber >0){
		var errorString = "";
		if(notvalid > 0){
		 	errorString += 'Terdapat data yang harus diisi';
		}
		if(notnumber >0){
			errorString += 'There are ' + notvalid + ' data is required number';
		}
		notify(errorString,'error');
		
		return false;
	}else{
		return true;	
	}
	return false;
}

function validasi(form){
	var notvalid = 0;
	var notnumber = 0;
	var notemail = 0;
	var regNumber =/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/;
	var regEmail = /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
		$.each($('#'+form+" input, #"+form+" textarea, #"+form+" select"), function(n,element){
			if($(this).attr('mandatory')=="yes"){
				$(this).addClass('mandatory');
				if($(element).val()==""){
					$("#"+element.id).css({
						'background-size':'100% 2px, 100% 1px',
						'background-image':'linear-gradient(#f44336,#f44336),linear-gradient(#e0e0e0,#e0e0e0)'
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
		 	errorString += '- Terdapat data yang harus diisi<br>';
		}
		if(notnumber > 0){
			errorString += '- Format number tidak sesuai';
		}
		if(notemail > 0){
			errorString += '- Format email tidak sesuai\n';
		}
		notify(errorString,'error');
		//swalert('error',errorString);
		return false;
	}else{
		return true;	
	}
	return false;
}

function validasi_duplicate(field,divid){
	if(divid==""||typeof(divid)=="undefined"){
		var divid = "msg_";	
	}else{		
		var divid = divid;		
	}
	var notduplicate = 0;
	$.each($("input:hidden"), function(n,element){
		if($(this).attr('duplicate')=="no" && $('#'+field).val()==$(this).val()){
			$(this).addClass('duplicate');
				notduplicate++;
		}
	});
	if(notduplicate>0){
		var errorString = "Notifikasi!";
		errorString += '<br>Terdapat data yang sama';
		$("."+divid).css('color', 'red');
		noty({text:errorString, type: 'error'});
		return false;
	}else{
		return true;	
	}		
	return false;
}

function close_popup(type){
	switch(type){
		case '1' : popup = jpopup_close(); break
		case '2' : popup = jpopup_closetwo(); break;
	}
	return popup;
}

function save_post(form,div){
	if(validasi(form)){
		if(validasi(form)){
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
								if(div!=undefined){
									var popup = $('[name="'+form+'"]').attr('popup');
									$('#'+div).load(arrdata[3]);
									close_popup(popup);
								}else{
									setTimeout(function(){location.href = arrdata[3];}, 1500);
								}
								notify(arrdata[2],'success');
								return false;
							}else{
								notify(arrdata[2],'error');
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
}

function save_wizard(form,div){
	if(validasi(form)){
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
						if(div!=undefined){
							var popup = $('[name="'+form+'"]').attr('popup');
							$('#'+div).load(arrdata[3]);
							close_popup(popup);
						}else{
							setTimeout(function(){location.href = arrdata[3];}, 1500);
						}
						notify(arrdata[2],'success');
						return false;
					}else{
						notify(arrdata[2],'error');
					}
				}
			}
		});
	}
}

function save_popup(form,div){
	if(validasi(form)){
		swal({title:'Confirm',
		  text:'Apakah ingin process data ?',
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
				complete: function(){Loading(false)},
				success: function(data){
					if(data.search("MSG")>=0){
						arrdata = data.split('#');
						if(arrdata[1]=="OK"){
							notify(arrdata[2],'success');
							if(div==undefined)
								setTimeout(function(){location.href = arrdata[3];}, 1500);
							else
								$('#'+div).load(arrdata[3]);
							jpopup_close();
						}else{
							notify(arrdata[2],'error');	
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

function save_data(form,id,param){
	if(validasi(form)){
		jConfirm('Do you want process this data ? ', applicationName, 
		function(r){if(r==true){
			$.ajax({
			type: 'POST',
			url: $('[name="'+form+'"]').attr('action'),
			data: $('[name="'+form+'"]').serialize(),
			beforeSend: function(){/*Loading(true);*/},
			success: function(data){
					Loading(false);
					if(data.search("MSG")>=0){
						arrdata = data.split('#');
						if(arrdata[1]=="OK"){
							notify(arrdata[2],'success');
							if(id!="") $('#'+id).val(arrdata[3]);
							if(param!="") $('.'+param).css('display','');
						}else{
							notify(arrdata[2],'error');	
						}
					}
				}
			});
		}else{return false;}});	
	}
}

function save_ajax(form,div){
	if(validasi(form)){
		swal({title:'Confirm',
		  text:'Apakah ingin process data ?',
		  type:'info',
		  showCancelButton:true,
		  closeOnConfirm:true,
		  showLoaderOnConfirm:true,
		 },function(r){
			 if(r){
				var arrform = new FormData(document.getElementById(form));
				$.ajax({
				type: 'POST',
				url: site_url+'/'+$('[name="'+form+'"]').attr('action'),
				data: arrform,
				enctype: 'multipart/form-data',
				processData: false,
				contentType: false,
				cache: false,
				beforeSend: function(){Loading(true)},
				success: function(data){
						Loading(false);
						if(data.search("MSG")>=0){
							arrdata = data.split('#');
							if(arrdata[1]=="OK"){
								notify(arrdata[2],'success');
								if(div!="undefined"){
									var popup = $('[name="'+form+'"]').attr('popup');
									close_popup(popup);
									setTimeout(function(){location.href = arrdata[3];}, 1500);
								}else{
									var popup = $('[name="'+form+'"]').attr('popup');
									$('#'+div).load(arrdata[3]);
									close_popup(popup);
								}
								return false;
							}else{
								notify(arrdata[2],'error');
								return false;
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

function save_multiple_post(form){
	var formserial = "";
	var arrform = form.split('|');
	for(var f=0; f<arrform.length; f++){
		formserial += '[name="'+arrform[f]+'"],';
	}
	var form = formserial.slice(0,-1);
	if(validasi(arrform[0]) && validasi(arrform[1])){
		swal({title:'Confirm',
		  text:'Apakah ingin prosess data ini ?',
		  type:'info',
		  showCancelButton:true,
		  closeOnConfirm:true,
		  showLoaderOnConfirm:true,
		 },function(r){
			 if(r){
				$.ajax({
				type: 'POST',
				url: $('[name="'+arrform[0]+'"]').attr('action'),
				data: $(form).serialize(),
				beforeSend: function(){Loading(true)},
				complete:function(){Loading(false)},
				success: function(data){
					if(data.search("MSG")>=0){
						arrdata = data.split('#');
						if(arrdata[1]=="OK"){
							notify(arrdata[2],'success');
							setTimeout(function(){location.href = arrdata[3];}, 1500);
							return false;
						}else{
							notify(arrdata[2],'error');
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

function save_popup_ajax(form,div) {
    if(validasi(form)){
        var arrform = new FormData(document.getElementById(form));
		jConfirm('Do you want process this data ? ', applicationName, 
		function(r){if(r==true){
			$.ajax({
			type: 'POST',
			url: $('[name="'+form+'"]').attr('action'),
			data: arrform,
			enctype: 'multipart/form-data',
			processData: false,
			contentType: false,
			cache: false,
			beforeSend: function(){Loading(true)},
			complete: function(){Loading(false)},
			success: function(data){
					if(data.search("MSG")>=0){
						arrdata = data.split('#');
						if(arrdata[1]=="OK"){
							notify('success',arrdata[2]);
							$('#'+div).load(arrdata[3]);
							jpopup_close();
						}else{
							notify('error',arrdata[2]);	
						}
					}
				}
			});
		}else{return false;}});	
	}
}    


function cancel(formid){
	document.getElementById(formid).reset();
	return false;
};

function Loading_Table(boolean){
	if(boolean){
		$('#Loading').show();
	}
	else{
		$('#Loading').hide();
	}	
}

function save_dialog(formid,msg){
	if(validasi(msg)){		
		$.ajax({
			type: 'POST',
			url: $(formid).attr('action'),
			data: $(formid).serialize(),
			success: function(data){
				if(data.search("MSG")>=0){
					arrdata = data.split('#');
					if(arrdata[1]=="OK"){
						$("."+msg).css('color', 'green');
						$("."+msg).html(arrdata[2]);						
						$("#divtblmohon").load(arrdata[3]);
						closedialog('dialog-tbl');
					}else{
						$("."+msg).css('color', 'red');
						$("."+msg).html(arrdata[2]);
					}
				}else{
					$("."+msg).css('color', 'red');
					$("."+msg).html('Proses Gagal.');
				}
			}
		});	
	}return false;	
}

function list_tbl(id,divid){
	jloadings();
	var dok=$("#"+id).val();
	page=$("#"+id).attr("url")+"/"+dok;
	$('#'+divid).load(page,function(){
		Clearjloadings();	
		$("#"+id).val(0);
	});
};

function multiReplace(str, match, repl) {
    do {
        str = str.replace(match, repl);
    } while(str.indexOf(match) !== -1);
    return str;
}

function FormatHS(varnohs){
	if (varnohs!=""){
		varnohs = multiReplace(varnohs,'.','');
		var varresult = '';
		var varresult = varnohs.substr(0,4)+"."+varnohs.substr(4,2)+"."+varnohs.substr(6,2)+"."+varnohs.substr(8,2);
		return varresult;
	}
}

function getDataCombo(form,val,get){
	var getVal=$("#"+form+" #"+val).val();
	$("#"+form+" #"+get).val(getVal);
}

function limitChars(textid, limit, infodiv){
	var text = $('#'+textid).val(); 
	var textlength = text.length;
	if(textlength > limit)
	{
		$('#' + infodiv).html('<font color="red">Tidak bisa lebih dari '+limit+' karakter!</font>');
		$('#'+textid).val(text.substr(0,limit));
		return false;
	}
	else
	{
		$('#' + infodiv).html('<font color="green">'+(limit - textlength) +' karakter yang tersisa.</font>');
		return true;
	}
}

function intInput(event, keyRE) {
	if ( String.fromCharCode(((navigator.appVersion.indexOf('MSIE') != (-1)) ? event.keyCode : event.charCode)).search(keyRE) != (-1)
		|| ( navigator.appVersion.indexOf('MSIE') == (-1)
			&& ( event.keyCode.toString().search(/^(8|9|13|45|46|35|36|37|39)$/) != (-1) 
				|| event.ctrlKey || event.metaKey ) ) ) {
		return true;
	} else {
		return false;
	}
}

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

function strpos(haystack, needle, offset){
    var i = (haystack + '').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}

function check(id,input){
	var check = new Array();
	$.each($("input[id='"+id+"']:checked"),function(){
	  check.push($(this).val());
	});	
	$('#'+input).val(check);
}

function send_id(form,id,met){
	if(validasi(form)){
		var getid = $('[name="'+id+'"]').val();
		if(getid==""){
			notify('error','There are data is required');
			return false;
		}
		jConfirm('Do you want process this data ? ', applicationName,
		function(r){if(r==true){
			$.ajax({
			type: 'POST',
			url: $('#'+met).attr('action'),
			data: 'sendid='+getid+'&'+$('[name="'+form+'"]').serialize(),
			beforeSend: function(){Loading(true)},
			success: function(data){
					Loading(false);
					if(data.search("MSG")>=0){
						arrdata = data.split('#');
						if(arrdata[1]=="OK"){
							notify('success',arrdata[2]);
							setTimeout(function(){location.href = arrdata[3];}, 1500);
							return false;
						}else{
							notify('error',arrdata[2]);
						}
					}
				}
			});
		}else{return false;}});
	}
}

function send_multiple_id(form,id,met){
	var formserial = "";
	var arrform = form.split('|');
	for(var f=0; f<arrform.length; f++){
		formserial += '[name="'+arrform[f]+'"],';
	}
	var form = formserial.slice(0,-1);
	if(validasi(arrform[0])){
		var getid = $('[name="'+id+'"]').val();
		if(getid==""){
			notify('error','There are data is required');
			return false;
		}
		if(validasi(arrform[1])){
			jConfirm('Do you want process this data ? ', applicationName,
			function(r){if(r==true){
				$.ajax({
				type: 'POST',
				url: $('#'+met).attr('action'),
				data: 'sendid='+getid+'&'+$(form).serialize(),
				beforeSend: function(){Loading(true)},
				success: function(data){
						Loading(false);
						if(data.search("MSG")>=0){
							arrdata = data.split('#');
							if(arrdata[1]=="OK"){
								notify('success',arrdata[2]);
								setTimeout(function(){location.href = arrdata[3];}, 1500);
								return false;
							}else{
								notify('error',arrdata[2]);
							}
						}
					}
				});
			}else{return false;}});
		}
	}
}

function date(className){
	$("."+className).datetimepicker({
		format: "dd-mm-yyyy",
		todayBtn: false,
		todayHighlight: true,
		autoclose: true,
		minView: '2'
	});
}

function datetime(className){
	$('.'+className).datetimepicker({
		format: "dd-mm-yyyy hh:ii:ss",
		todayBtn: false,
		todayHighlight: true,
		autoclose: true
	});
}

function ajax_lokasi(id,div,act){
	var arrdiv = div.split('|');
	var arract = act.split('/');
	var url = site_url+'/'+act+'/'+Math.random();
	$.post(url,{id:id,name:arrdiv},
		function(data){
			if(arract[2]=='kabupaten'){
				$('#DIV_'+arrdiv[0]).html(data);
				for(var a=1; a<arrdiv.length; a++){
					$('#DIV_'+arrdiv[a]).html('<select class="form-control" name="DATA['+arrdiv[a]+']" id="'+arrdiv[a]+'" mandatory="yes" style="width:100%"><option></option></select>');
				}
			}else if(arract[2]=='kecamatan'){
				$('#DIV_'+arrdiv[1]).html(data);
				for(var a=2; a<arrdiv.length; a++){
					$('#DIV_'+arrdiv[a]).html('<select class="form-control" name="DATA['+arrdiv[a]+']" id="'+arrdiv[a]+'" mandatory="yes"><option></option></select>');
				}
			}else if(arract[2]=='kelurahan'){
				$('#DIV_'+arrdiv[2]).html(data);
			}
	}, "html");
}

function on_detail(id,div,act){
	var url = site_url+'/'+act+'/'+Math.random();
	$.post(url,{id:id},
		function(data){
			$('#'+div).html(data);
	}, "html");
}

function Loading(boolean){
	if(boolean){
		LoadingOpen();
	}
	else{
		LoadingClose();
	}	
}

function popup(url,id,width,height){
	jpopup(site_url+"/"+url,applicationName,id,width,height);
	return false;
}

function popup_checked(url,id,width,height,check){
	if(status == false){
		//jpopup(site_url+"/"+url,applicationName,id,width,height);
	}	
}

function popup_search(url,id,width,height){
	jpopup(site_url+"/"+url,applicationName,id,width,height);
	return false;
}

function popup_searchtwo(url,id,width,height){
	jpopuptwo(site_url+"/"+url,applicationName,id,width,height);
	return false;	
}

function check_user(){
	var username = $('#USERNAME').val();
	if(username == ""){
		notify('Username belum diinput','error');
	}else{
		$.ajax({
		type: 'POST',
		url: site_url + '/home/check_user/select/username/'+Math.random(),
		data: 'username='+username,
		dataType:'json',
		beforeSend: function(){Loading(true)},
		success: function(data){
				Loading(false);
				if(data.returnData > 0) notify(data.message,'success');
				else notify(data.message,'error');
				return false;	
			}
		});
	}
}

function formatter(div,pattern){
	$('#'+div).formatter({
	  'pattern': pattern,
	  'persistent': true
	});
}

