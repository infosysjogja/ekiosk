var applicationName = "NPCT1";
(function(document,window,$){
  'use strict';
  var Site = window.Site;
  $(document).ready(function(){
	Site.run();
  });
})(document, window, jQuery);

function button_menu(formid,id){
	var checked = false;
	var url = "";
	var jml = "";
	var met = "";
	var w = "";
	var popup = "";
	var idget = "";
	var getid = "";
	chk = $(".tb_chk"+formid+":checked").length;
	url = $("#"+id).attr('url');
	jml = $("#"+id).attr('jml');
	met = $("#"+id).attr('met');
	div = $("#"+id).attr('div');
	w = $("#"+id).attr('w');
	status = $("#"+id).attr('status');
	popup = $("#"+id).attr('popup');
	idget = $("#"+id).attr('get');
	if(idget!="") getid = $('#'+idget).val();
	if(w=="") w = 60;
	if(url=="") return false;
	if(chk==0 && jml!=0){
		swalert('error','Maaf, Data belum dipilih');
		return false;
	}
	if(jml=='1' && chk > 1){
		swalert('error','Pilih salah satu data');
		$("#tb_menu"+formid).val(0);
		return false;
	}
	if(status!=""){
		var valid = $(".tb_chk"+formid+":checked").attr('validasi');
		if(status=="NOT-NULL"){
			if(valid!=""){
				swalert('error','Maaf, Data tidak dapat diproses');
				return false;
			}
		}else{
			if(valid!=status){
				swalert('error','Maaf, Data tidak dapat diproses');
				return false;
			}
		}
	}
	if(met=="GET"){
		var val = $(".tb_chk"+formid+":checked").val();
		$("#div"+met).remove();
		c_div('#div'+met,'<form name="frm'+formid+'" id="frm'+formid+'"></form>');
		var myform    = document.forms['frm'+formid];
		myform.method = 'POST';
		myform.action = url;
		add_hidden(myform, 'action', 'update');
		add_hidden(myform, 'generate', 'formjs');
		add_hidden(myform, 'arrpost', val);
		add_hidden(myform, 'id', val);
		myform.submit();
		if(popup!=""){
			close_popup(popup);
		}
		return false;
	}else if(met=="PREVIEW"){
		jConfirm('Do you want to process data ?', applicationName, 
		function(r){if(r==true){
			if(jml=='0')
				location.href = url;
			else
				location.href = url + '/' + $(".tb_chk"+formid+":checked").val();
		}else{return false;}});						
	}else if(met=="POST"){
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					var val = $("#"+formid+" input:checkbox").serialize()
					$.ajax({
						type: 'POST',
						url: site_url+'/'+url,
						data:val,
						beforeSend: function(){Loading(true)},
						complete: function(){Loading(false)},
						success: function(data){
							if(data.search("MSG")>=0){
								arrdata = data.split('#');
								if(arrdata[1]=="OK"){
									notify(arrdata[2],'success');
									$('#'+div).load(arrdata[3]);
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
	}else if(met=="POST_POPUP"){
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					var val = $("#"+formid+" input:checkbox").serialize()
					$.ajax({
						type: 'POST',
						url:url,
						data:val,
						beforeSend: function(){Loading(true)},
						complete: function(){Loading(false)},
						success: function(data){
							if(data.search("MSG")>=0){
								arrdata = data.split('#');
								if(arrdata[1]=="OK"){
									if(pop_type!=""){
										if(pop_type=="2"){
											jpopup_closetwo();
										}else{
											jpopup_close();
										}	
									}
									notify('success',arrdata[2]);
									var div = arrdata[3].split('~');
									$('#'+div[0]).load(div[1]);
								}else{
									notify('error',arrdata[2]);	
									return false;
								}
							}
						}
					});	
			 }else{
				return false
			 }
		});
	}else if(met=="ADD"){
		location.href = url;
	}else if(met=="MODAL"){
		if(jml==1){
			var val = $(".tb_chk"+formid+":checked").val();
			popup_search(url,'id='+val,w,600);
		}else{
			popup_search(url,'',w,600);
		}
	}else if(met=="EDIT"){
		var val = $(".tb_chk"+formid+":checked").val().toLowerCase().split("~");
		if(typeof(val[1])=='undefined'){
			location.href = url + '/' + val[0];
		}
		else{
			location.href = url + '/' + val[0] + '/' + val[1];
		}
	}else if(met=="EDIT_MODAL_AJAX"){
		$.ajax({
			type: 'POST',
			url: site_url+'/'+url,
			data: $("#"+formid+" input:checkbox").serialize(),
			success: function(data){
				if(data.search("MSG")>=0){
					arrdata = data.split('#');
					if(arrdata[1]=="OK"){
						popup_search(url,'id='+arrdata[2],w,600);
					}else{
						notify('error',arrdata[2]);
						return false;
					}
				}
			}
		});		
	}else if(met=="EDIT_AJAX"){
		jConfirm('Do you want to edit data ?', applicationName, 
		function(r){if(r==true){
			$.ajax({
				type: 'POST',
				url: url,
				data: $("#"+formid+" input:checkbox").serialize(),
				success: function(data){
					if(data.search("MSG")>=0){
						arrdata = data.split('#');
						if(arrdata[1]=="OK"){
							location.href = url + '/' + arrdata[2];
						}else{
							notify('error',arrdata[2]);
							return false;
						}
					}
				}
			});
		}else{return false;}});				
	}else if(met=="GET_POST"){
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					var val = $("#"+formid+" input:checkbox").serialize();
					$.ajax({
						type: 'POST',
						url: url,
						data: val,
						beforeSend: function(){Loading(true)},
						success: function(data){
							Loading(false);
							if(data.search("MSG")>=0){
								arrdata = data.split('#');
								if(arrdata[1]=="OK"){
									notify('success',arrdata[2]);
									setTimeout(function(){location.href = arrdata[3];}, 2000);
								}else{
									notify('error',arrdata[2]);	
								}
							}
							return false;
						}
					});	
			 }else{
				return false
			 }
		});
	}else if(met=="DELETE"){
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					var val = $("#"+formid+" input:checkbox").serialize();
					$.ajax({
						type: 'POST',
						url: site_url+'/'+url,
						data: val,
						beforeSend: function(){Loading(true)},
						success: function(data){
							Loading(false);
							if(data.search("MSG")>=0){
								arrdata = data.split('#');
								if(arrdata[1]=="OK"){
									$('#'+div).load(arrdata[3]);
									notify(arrdata[2],'success');
								}else{
									notify(arrdata[2],'error');
								}
							}
							return false;
						}
					});	
			 }else{
				return false
			 }
		});
	}else if(met=="SEND"){
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					var val = $("#"+formid+" input:checkbox").serialize();
					$.ajax({
						type: 'POST',
						url: site_url+'/'+url,
						data: val,
						beforeSend: function(){Loading(true)},
						success: function(data){
							Loading(false);
							if(data.search("MSG")>=0){
								arrdata = data.split('#');
								if(arrdata[1]=="OK"){
									notify('success',arrdata[2]);
									$('#'+div).load(arrdata[3]);
								}else{
									notify('error',arrdata[2]);	
								}
							}
							return false;
						}
					});	
			 }else{
				return false
			 }
		});
	}else if(met=="OPTION"){
		var val = $("#"+formid+" input:checkbox").serialize();
		$.ajax({
			type: 'POST',
			url: url,
			data: val,
			dataType: 'json',
			beforeSend: function(){},
			success: function(data){
				Loading(false);
				var jumdata = data.arrfield.length;
				if(jumdata>0){
					for(var a=0; a<jumdata; a++){
						$('#'+data.arrfield[a]).val(data.arrvalue[a]);
					}
					if(data.arrajax!=""){
						get_select(data.arrajax,data.arrvalue[0]);
					}
					if(popup!=""){
						close_popup(popup);
					}
				}
				else{
					jAlert('Maaf, Data gagal dipilih',applicationName);
					return false;
				}
			}
		});
	}else if(met=="PRINT"){
		var val = $(".tb_chk"+formid+":checked").val();
		$("#div"+met).remove();
		c_div('#div'+met,'<form name="'+formid+'" id="'+formid+'"></form>');
		var myform    = document.forms[formid];
		myform.method = 'POST';
		myform.action = site_url+'/'+url;
		myform.target = '_blank';
		add_hidden(myform, 'action', 'update');
		add_hidden(myform, 'generate', 'formjs');
		add_hidden(myform, 'arrpost', val);
		add_hidden(myform, 'id', val);
		myform.submit();
		location.reload(true);
		return false;
	}else if(met=="PRINTPREVIEW"){
		jConfirm('Apakah anda ingin proses print preview data ?', applicationName, 
		function(r){if(r==true){			
			var id = $(".tb_chk"+formid+":checked").val();
			if(id!=""){
				popup_search(url+'/'+id,id,w,600);
			}
		}else{return false;}});
	}else if(met=="POPUP"){
		var val = $(".tb_chk"+formid+":checked").val();
		popup_search(url+'/'+val[0],'id='+val[0],w,600);
	}else if(met=="VIEW"){
		jConfirm('Apakah anda ingin proses preview data ?', applicationName, 
		function(r){if(r==true){			
			var id = $(".tb_chk"+formid+":checked").val();
			popup_search(url+'/'+id,id,w,600);
		}else{return false;}});
	}else if(met=="GET_MODAL_AJAX"){//alert ('asaa');
		jConfirm('Do you want to process data ?', applicationName, 
		function(r){if(r==true){
			$.ajax({
				type: 'POST',
				url: site_url+'/'+url,
				data: $("#"+formid+" input:checkbox").serialize(),
				success: function(data){
					if(data.search("MSG")>=0){
						arrdata = data.split('#');
						if(arrdata[1]=="OK"){
							var val = $(".tb_chk"+formid+":checked").val().toLowerCase().split(".");
							popup_search(url+'/'+arrdata[2],'id='+val[0],w,600);
						}else{
							notify('error',arrdata[2]);
							return false;
						}
					}
				}
			});
		}else{return false;}});				
	}else if(met=="EXCEL"){
		var frm_act = $("#"+formid).attr('action');
		console.log(frm_act);
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					document.getElementById(formid).method = "post";
					document.getElementById(formid).action = url;
					document.getElementById(formid).target = "_blank";
					document.getElementById(formid).submit();
					location.reload(true);
					return false;
			 }else{
				return false
			 }
		});	
	}
	$("#tb_menu"+formid).val(0);
	return false;		
	
}

function button_list(formid,id){
	var checked = false;
	var url = "";
	var jml = "";
	var met = "";
	var w = "";
	var popup = "";
	var idget = "";
	var getid = "";
	var val= "";
	chk = $(".tb_chk"+formid+":checked").length;
	url = $("#"+id).attr('url');
	jml = $("#"+id).attr('jml');
	met = $("#"+id).attr('met');
	div = $("#"+id).attr('div');
	val = $("#"+id).attr('value');
	w = $("#"+id).attr('w');
	status = $("#"+id).attr('status');
	popup = $("#"+id).attr('popup');
	idget = $("#"+id).attr('get');
	if(idget!="") getid = $('#'+idget).val();
	if(w=="") w = 60;
	if(url=="") return false;
	if(met=="GET"){
		//var val = $(".tb_chk"+formid+":checked").val();
		$("#div"+met).remove();
		c_div('#div'+met,'<form name="frm'+formid+'" id="frm'+formid+'"></form>');
		var myform    = document.forms['frm'+formid];
		myform.method = 'POST';
		myform.action = url;
		add_hidden(myform, 'action', 'update');
		add_hidden(myform, 'generate', 'formjs');
		add_hidden(myform, 'arrpost', val);
		add_hidden(myform, 'id', val);
		myform.submit();
		if(popup!=""){
			close_popup(popup);
		}
		return false;
	}else if(met=="PREVIEW"){
		jConfirm('Do you want to process data ?', applicationName, 
		function(r){if(r==true){
			if(jml=='0')
				location.href = url;
			else
				location.href = url + '/' + $(".tb_chk"+formid+":checked").val();
		}else{return false;}});						
	}else if(met=="POST"){
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					//var val = $("#"+formid+" input:checkbox").serialize()	
					$.ajax({
						type: 'POST',
						url: site_url+'/'+url,
						data:val,
						beforeSend: function(){Loading(true)},
						complete: function(){Loading(false)},
						success: function(data){
							if(data.search("MSG")>=0){
								arrdata = data.split('#');
								if(arrdata[1]=="OK"){
									notify(arrdata[2],'success');
									$('#'+div).load(arrdata[3]);
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
	}else if(met=="POST_POPUP"){
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					var val = $("#"+formid+" input:checkbox").serialize()
					$.ajax({
						type: 'POST',
						url:url,
						data:val,
						beforeSend: function(){Loading(true)},
						complete: function(){Loading(false)},
						success: function(data){
							if(data.search("MSG")>=0){
								arrdata = data.split('#');
								if(arrdata[1]=="OK"){
									if(pop_type!=""){
										if(pop_type=="2"){
											jpopup_closetwo();
										}else{
											jpopup_close();
										}	
									}
									notify('success',arrdata[2]);
									var div = arrdata[3].split('~');
									$('#'+div[0]).load(div[1]);
								}else{
									notify('error',arrdata[2]);	
									return false;
								}
							}
						}
					});	
			 }else{
				return false
			 }
		});
	}else if(met=="ADD"){
		location.href = url;
	}else if(met=="MODAL"){
		if(jml==1){
			//var val = $(".tb_chk"+formid+":checked").val();
			popup_search(url,'id='+val,w,600);
		}else{
			popup_search(url,'',w,600);
		}
	}else if(met=="EDIT"){
		var val = $(".tb_chk"+formid+":checked").val().toLowerCase().split("~");
		if(typeof(val[1])=='undefined'){
			location.href = url + '/' + val[0];
		}
		else{
			location.href = url + '/' + val[0] + '/' + val[1];
		}
	}else if(met=="EDIT_MODAL_AJAX"){
		$.ajax({
			type: 'POST',
			url: site_url+'/'+url,
			data: $("#"+formid+" input:checkbox").serialize(),
			success: function(data){
				if(data.search("MSG")>=0){
					arrdata = data.split('#');
					if(arrdata[1]=="OK"){
						popup_search(url,'id='+arrdata[2],w,600);
					}else{
						notify('error',arrdata[2]);
						return false;
					}
				}
			}
		});		
	}else if(met=="EDIT_AJAX"){
		jConfirm('Do you want to edit data ?', applicationName, 
		function(r){if(r==true){
			$.ajax({
				type: 'POST',
				url: url,
				data: $("#"+formid+" input:checkbox").serialize(),
				success: function(data){
					if(data.search("MSG")>=0){
						arrdata = data.split('#');
						if(arrdata[1]=="OK"){
							location.href = url + '/' + arrdata[2];
						}else{
							notify('error',arrdata[2]);
							return false;
						}
					}
				}
			});
		}else{return false;}});				
	}else if(met=="GET_POST"){
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					var val = $("#"+formid+" input:checkbox").serialize();
					$.ajax({
						type: 'POST',
						url: url,
						data: val,
						beforeSend: function(){Loading(true)},
						success: function(data){
							Loading(false);
							if(data.search("MSG")>=0){
								arrdata = data.split('#');
								if(arrdata[1]=="OK"){
									notify('success',arrdata[2]);
									setTimeout(function(){location.href = arrdata[3];}, 2000);
								}else{
									notify('error',arrdata[2]);	
								}
							}
							return false;
						}
					});	
			 }else{
				return false
			 }
		});
	}else if(met=="DELETE"){
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					var val = $("#"+formid+" input:checkbox").serialize();
					$.ajax({
						type: 'POST',
						url: site_url+'/'+url,
						data: val,
						beforeSend: function(){Loading(true)},
						success: function(data){
							Loading(false);
							if(data.search("MSG")>=0){
								arrdata = data.split('#');
								if(arrdata[1]=="OK"){
									$('#'+div).load(arrdata[3]);
									notify(arrdata[2],'success');
								}else{
									notify(arrdata[2],'error');
								}
							}
							return false;
						}
					});	
			 }else{
				return false
			 }
		});
	}else if(met=="SEND"){
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					var val = $("#"+formid+" input:checkbox").serialize();
					$.ajax({
						type: 'POST',
						url: site_url+'/'+url,
						data: val,
						beforeSend: function(){Loading(true)},
						success: function(data){
							Loading(false);
							if(data.search("MSG")>=0){
								arrdata = data.split('#');
								if(arrdata[1]=="OK"){
									notify('success',arrdata[2]);
									$('#'+div).load(arrdata[3]);
								}else{
									notify('error',arrdata[2]);	
								}
							}
							return false;
						}
					});	
			 }else{
				return false
			 }
		});
	}else if(met=="OPTION"){
		var val = $("#"+formid+" input:checkbox").serialize();
		$.ajax({
			type: 'POST',
			url: url,
			data: val,
			dataType: 'json',
			beforeSend: function(){},
			success: function(data){
				Loading(false);
				var jumdata = data.arrfield.length;
				if(jumdata>0){
					for(var a=0; a<jumdata; a++){
						$('#'+data.arrfield[a]).val(data.arrvalue[a]);
					}
					if(data.arrajax!=""){
						get_select(data.arrajax,data.arrvalue[0]);
					}
					if(popup!=""){
						close_popup(popup);
					}
				}
				else{
					jAlert('Maaf, Data gagal dipilih',applicationName);
					return false;
				}
			}
		});
	}else if(met=="PRINT"){
		var val = $(".tb_chk"+formid+":checked").val();
		$("#div"+met).remove();
		c_div('#div'+met,'<form name="frm'+formid+'" id="frm'+formid+'"></form>');
		var myform    = document.forms['frm'+formid];
		myform.method = 'POST';
		myform.action = site_url+'/'+url;
		myform.target = '_blank';
		add_hidden(myform, 'action', 'update');
		add_hidden(myform, 'generate', 'formjs');
		add_hidden(myform, 'arrpost', val);
		add_hidden(myform, 'id', val);
		myform.submit();
		return false;
	}else if(met=="PRINTPREVIEW"){
		jConfirm('Apakah anda ingin proses print preview data ?', applicationName, 
		function(r){if(r==true){			
			var id = $(".tb_chk"+formid+":checked").val();
			if(id!=""){
				popup_search(url+'/'+id,id,w,600);
			}
		}else{return false;}});
	}else if(met=="POPUP"){
		var val = $(".tb_chk"+formid+":checked").val();
		popup_search(url+'/'+val[0],'id='+val[0],w,600);
	}else if(met=="VIEW"){
		jConfirm('Apakah anda ingin proses preview data ?', applicationName, 
		function(r){if(r==true){			
			var id = $(".tb_chk"+formid+":checked").val();
			popup_search(url+'/'+id,id,w,600);
		}else{return false;}});
	}else if(met=="GET_MODAL_AJAX"){//alert ('asaa');
		jConfirm('Do you want to process data ?', applicationName, 
		function(r){if(r==true){
			$.ajax({
				type: 'POST',
				url: site_url+'/'+url,
				data: $("#"+formid+" input:checkbox").serialize(),
				success: function(data){
					if(data.search("MSG")>=0){
						arrdata = data.split('#');
						if(arrdata[1]=="OK"){
							var val = $(".tb_chk"+formid+":checked").val().toLowerCase().split(".");
							popup_search(url+'/'+arrdata[2],'id='+val[0],w,600);
						}else{
							notify('error',arrdata[2]);
							return false;
						}
					}
				}
			});
		}else{return false;}});				
	}else if(met=="EXCEL"){
		var frm_act = $("#"+formid).attr('action');
		console.log(frm_act);
		swal({title:applicationName,
			  text:'Apakah ingin proses data ?',
			  type:'info',
			  showCancelButton:true,
			  closeOnConfirm:true,
			  showLoaderOnConfirm:true},
			  function(r){
				if(r){
					document.getElementById(formid).method = "post";
					document.getElementById(formid).action = url;
					document.getElementById(formid).target = "_blank";
					document.getElementById(formid).submit();
					location.reload(true);
					return false;
			 }else{
				return false
			 }
		});	
	}
	$("#tb_menu"+formid).val(0);
	return false;		
	
}	

function tb_chkall(formid,status){
	var valtemp = $('#tmpchk'+formid).val();
	$('#newtr').remove();
	if(status==false){
		$("#"+formid+" input:checkbox:not(#tb_chkall"+formid+")").parent().parent().removeClass("selected");
		$('input[id^="tb_chk'+formid+'"]').prop("checked",false);
	}else{
		$("#"+formid+" input:checkbox:not(#tb_chkall"+formid+")").parent().parent().addClass("selected");
		$('input[id^="tb_chk'+formid+'"]').prop("checked",true);
	}
	if(status == true){
		$('input[id^="tb_chk'+formid+'"]').each(function(i){
			if(strpos(valtemp,$(this).val()) === false){
		 		$('#tmpchk'+formid).val($(this).val()+"*"+$('#tmpchk'+formid).val());
			}
		});
	}else{
		$('input[id^="tb_chk'+formid+'"]').each(function(i){
		 	$('#tmpchk'+formid).val($('#tmpchk'+formid).val().replace($(this).val()+'*',''));
		});
	}
}

function strpos(haystack, needle, offset) {
  var i = (haystack + '').indexOf(needle, (offset || 0));
  return i === -1 ? false : i;
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

function tb_chk(formid,status,id){
 	$('input:not(:checked)').parent().parent().removeClass("selected");
 	$('input:checked').parent().parent().addClass("selected");
	tmp_chk(formid,status,id);
}

function tr_chk(formid,data){
   //$(':checkbox', data).trigger('click');
}

function tb_hals(formid,id){ 
	form = $("#tb_menu"+formid).attr('formid');
	newhal = $(id).val();
	newhal++;
	redirect_url(newhal,form);
	return false;
}
function td_click(id){
	$("#detils_bawah").html('<center><img src=\"'+base_url+'img/_indicator.gif\" alt=\"\" /><br> Loading ...</center>');	
	$.ajax({
		type: 'POST',
		url: $(".tabelajax #bawah").attr('urldetil')+"/"+id,
		data: 'ajax=1',
		success: function(html){
			$("#detils_bawah").html(html);
		}
	});					
}
function redirect_url(newhal,formid){
	newlocation = $("#"+formid).attr('action') + '/row/' + $("#tb_view").val() + '/page/' + newhal + '/order/' + $("#orderby").val() + '/' + $("#sortby").val();
	if($("#tb_cari").val()!="") newlocation +=  '/search/' + $("#tb_keycari").val() + '/' + $("#tb_cari").val().replace('/', '');
	location.href = newlocation;
}

function newtable_search(form,div,page,sortby,orderby,toggle){
	$.ajax({
		type: 'POST',
		url: $("#"+form).attr("action"),
		data: 'ajax=1&page='+page+'&orderby='+orderby+'&sortby='+sortby+'&'+$("#"+form).serialize(),
		beforeSend: function(){Loading(true)},
		complete: function(){Loading(false)},
		success: function(data){
			$('#'+div).html(data);
		}
	});
}

function td_pilih(id){
	var arr = id.split("|");
	var formName = arr[0];
	var fIndexEdit = arr[1];
	var inputField = arr[2];
	var input = inputField.split(";");
	var val = fIndexEdit.split(";");
	for(var c=0;c<(input.length)-1;c++){
		if(typeof($("#"+input[c]).get(0))=="undefined"){
			jAlert('<b>ERROR:\n</b>Ada Elemen Form ('+input[c]+') yang tak terdefinisi.\nMohon periksa script codenya.');
			return false;
			break;
		}		
		var tipe = $("#"+input[c]).get(0).tagName;
		if(tipe=='INPUT'){
			$("#"+formName).find("#"+input[c]).val(val[c]);
		}
		else if(tipe=='TEXTAREA'){
			$("#"+formName).find("#"+input[c]).attr("value",val[c])
		}
		else if(tipe=='SELECT'){
			//$("#"+formName).find('#'+input[c]+' option:contains("'+val[c]+'")').attr('selected', true);
			$("#"+formName).find('#'+input[c]).val(val[c]);
		}
		else{
			$("#"+formName).find("#"+input[c]).html(val[c]);
		}
	}
	$("#"+input[0]).focus();	
	closedialog('Dialog-dok');	
}

function popupid(url,id,width,height){
	if(id != ""){
		var val = '';
		var arrID = id.split("|");
		var banyak = arrID.length;
		for(var a=0; a<banyak; a++){
			val += $('#'+arrID[a]).val()+'|';
		}
		var lengthid = val.length;
		valdata = val.substr(0,lengthid-1);
	}
	jpopup(site_url+"/"+url+"/"+valdata,applicationName,id,width,height);
	return false;	
}

function formCari(div){
	$.ajax({
		type: 'POST',
		url: site_url+"/"+$("#formCari").attr("action"),
		data: 'ajax=1&'+$("#formCari").serialize(),
		beforeSend: function(){Loading(true)},
		complete: function(){Loading(false)},
		success: function(data){
			$('#'+div).html(data);
		}
	});	
}

function FormDiv(div,url,id){
	$.ajax({
		type: 'POST',
		url: site_url+"/"+url+'/'+id,
		data: 'ajax=1&id='+id+'&'+$("#FormDiv").serialize(),
		beforeSend: function(){Loading(true)},
		complete: function(){Loading(false)},
		success: function(data){
			$('#'+div).html(data);
		}
	});	
}

function td_detil_priview(id,thisid){
	var obj = $(thisid).next().attr("id");	
	if(obj=="newtr"){ 
		$('#newtr').remove();
	}else{
		if($(thisid).attr('urldetil')){
			if($(thisid).attr('urldetil')!=""){
				$('#newtr').remove();
				var jmltd = $('td', $(thisid)).length;
				var addtd = '';
				if($(".tabelajax input:checkbox").length > 0){
					addtd = '<td></td>';
					jmltd--;
				}
				$(thisid).after('<tr id="newtr">' + addtd + '<td id="filltd" colspan="' + jmltd + '"></td></tr>');
				$('#filltd').html('<img src=\"'+base_url+'img/loading.gif\" alt=\"\" />  Loading...');
				$('#filltd').load($(thisid).attr('urldetil'));
			}
		}
		return false;
	}
}

function add_hidden(formname, key, value) {
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = key;
    'name-as-seen-at-the-server';
    input.value = value;
    formname.appendChild(input);
}

function c_div(id, inner){
	div = document.createElement("div");	
	div.innerHTML = '<div id="'+id+'" style="display: none;">'+inner+'</div>';
	document.body.appendChild(div);
}

function get_detail(id){
	var met = $(id).attr('type');
	var url = $(id).attr('url');
	var formid = $(id).attr('formdata');
	var val	= $(id).attr('value');
	if(met=="GET"){
		$("#div"+met).remove();
		c_div('#div'+met,'<form name="frm'+formid+'" id="frm'+formid+'"></form>');
		var myform    = document.forms['frm'+formid];
		myform.method = 'POST';
		myform.action = url;
		add_hidden(myform, 'action', 'update');
		add_hidden(myform, 'generate', 'formjs');
		add_hidden(myform, 'arrpost', val);
		add_hidden(myform, 'id', val);
		myform.submit();
		return false;
	}else if(met=="POPUP"){
		popup_search($(id).attr('url'),'id='+val,80,600);
	}else if(met=="DRILLDOWN"){
        $('#new_tr').remove();
        var jml_td = $('td', $(id)).length;
        var add_td = '';
        if($(".tabelajax input:checkbox").length > 0){
            add_td = '<td></td>';
            jml_td--;
        }
        $(id).after('<tr id="new_tr">' + add_td + '<td id="filltd" colspan="' + jml_td + '"></td></tr>');
        $('#filltd').html('<img src=\"'+base_url+'assets/images/loading_tr.gif\" alt=\"\" />  Loading...');
        $('#filltd').load($(id).attr('url')+'/'+val);
    }
}

function get_select(param,id){
	if(param != ""){
		var arrdata = param.split("|");
		var div = arrdata[0];
		var url = arrdata[1];
		$.ajax({
		  type: "POST",
		  url: site_url+'/'+url,
		  data: 'ajax=1&id='+id,
		  beforeSend: function(){Loading(true)},
		  complete:function(){Loading(false)},
		  success: function(data){
			$("#"+div).html(data);
		  }
		});	
	}
	return false;
}
