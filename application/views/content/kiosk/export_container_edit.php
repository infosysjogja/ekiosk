<div class="panel">
  <div class="ribbon ribbon-clip ribbon-primary">
  	<span class="ribbon-inner">
		<i class="fa fa-server" aria-hidden="true"></i> Edit Kontainer <?print_r($arrdata); ?>
    </span>
  </div>
  <div>&nbsp;</div>
  <small class="help-block" style="text-align:center"><span id="div-message">&nbsp;</span></small>
  <div class="panel-body container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <form name="form_cont" id="form_cont" class="form-horizontal" role="form" method="post" autocomplete="off" popup="1">
          <div class="panel-body container-fluid" id="f-cont">
            <div class="row">
              <div class="form-group">
                <label class="col-sm-3 control-label">NO. KONTAINER</label>
                <div class="col-sm-4">
				  <input name="CONT_NO" id="CONT_NO" mandatory="yes" class="form-control" placeholder="CONTAINER NO" onkeyup="check_iso('CONT_NO',this.value);" maxlength="11" type="text" value="<?php echo $arrdata['cont']; ?>">
				  <small class="help-block" style="color:orange"><span id="verify_cont"></span></small>
                </div>
				<div class="col-sm-2">
					<div class="radio-custom radio-primary margin-top-10">
					  <input id="F" name="FE" value="F" type="radio" mandatory="yes" <?php echo ($arrdata['fe']=="F")?"checked":""; ?>>
					  <label for="F">FULL</label>
					</div>
                </div>
				<div class="col-sm-2">
					<div class="radio-custom radio-primary margin-top-10">
					  <input id="E" name="FE" value="E" type="radio" mandatory="yes" <?php echo ($arrdata['fe']=="E")?"checked":""; ?>>
					  <label for="E">EMPTY</label>
					</div>
                </div>
              </div>
			  <div class="form-group">
                <label class="col-sm-3 control-label">ISO CODE</label>
				<div class="col-sm-2">
                  <input name="CODE_ISO" id="CODE_ISO" mandatory="yes" class="form-control" placeholder="ISO CODE" type="text" maxlength="4" onkeyUp="get_data(this.value,'kiosk/execute_export/isocode','isocode');" value="<?php echo $arrdata['isocode']; ?>">
                </div>
				<div class="col-sm-2">
				  <input name="CONT_SIZE" id="CONT_SIZE" class="form-control focus" placeholder="UKURAN" value="<?php echo get_isocode($arrdata['isocode'],'size'); ?>" type="text" readonly>
                </div>
				<div class="col-sm-2">
				  <input name="CONT_TYPE" id="CONT_TYPE" class="form-control focus" placeholder="TIPE" type="text" value="<?php echo get_isocode($arrdata['isocode'],'type'); ?>" readonly>
                </div>
				<div class="col-sm-2">
				  <input name="CONT_HEIGHT" id="CONT_HEIGHT" class="form-control focus" placeholder="TINGGI" type="text" value="<?php echo get_isocode($arrdata['isocode'],'height'); ?>" readonly>
                </div>
              </div>
			  <div class="form-group">
                <label class="col-sm-3 control-label">IMO DG</label>
				<div class="col-sm-2">
				  <span class="checkbox-custom checkbox-primary margin-top-10">
					<input name="FL_IMDG" id="FL_IMDG" value="Y" onclick="check(this,'DG_CLASS|DG_UN');" type="checkbox" <?php echo ($arrdata['dgclass']!="")?"checked":""; ?>>
					<label for="FL_IMDG">&nbsp;</label>
				  </span>
                </div>
				<div class="col-sm-2">
				  <input name="DG_CLASS" id="DG_CLASS" class="form-control" placeholder="CLASS" type="text" value="<?php echo $arrdata['dgclass']; ?>" <?php echo ($arrdata['dgclass']=="")?"disabled":""; ?>>
                </div>
				<div class="col-sm-2">
				  <input name="DG_UN" id="DG_UN" class="form-control" placeholder="UN" type="text" value="<?php echo $arrdata['dgun']; ?>" <?php echo ($arrdata['dgun']=="")?"disabled":""; ?>>
                </div>
              </div>
			  <div class="form-group">
                <label class="col-sm-3 control-label">REEFER PLUG</label>
                <div class="col-sm-2">
				  <span class="checkbox-custom checkbox-primary margin-top-10">
					<input name="REEFER_TYPE" id="REEFER_TYPE" value="Y" onclick="check(this,'TEMPERATURE');" type="checkbox" <?php echo ($arrdata['temp']!="")?"checked":""; ?>>
					<label for="REEFER_TYPE">&nbsp;</label>
				  </span>
                </div>
				<div class="col-sm-2">
				  <input name="TEMPERATURE" id="TEMPERATURE" class="form-control" placeholder="°C" type="text" value="<?php echo $arrdata['temp']; ?>" <?php echo ($arrdata['temp']=="")?"disabled":""; ?>>
                </div>
              </div>
			  <div class="form-group">
                <label class="col-sm-3 control-label">OOG</label>
				<div class="col-sm-2">
				  <span class="checkbox-custom checkbox-primary margin-top-10">
					<input name="FL_OOG" id="FL_OOG" value="Y" onclick="check(this,'OR|OH|OL');" type="checkbox" <?php echo ($arrdata['or']!="" || $arrdata['oh'] != "" || $arrdata['ol'] != "")?"checked":""; ?>>
					<label for="FL_OOG">&nbsp;</label>
				  </span>
                </div>
				<div class="col-sm-2">
				  <input name="OR" id="OR" class="form-control" placeholder="OR" type="text" <?php echo ($arrdata['or']=="")?"disabled":""; ?> value="<?php echo $arrdata['or']; ?>">
                </div>
				<div class="col-sm-2">
				  <input name="OH" id="OH" class="form-control" placeholder="OH" type="text" <?php echo ($arrdata['oh']=="")?"disabled":""; ?> value="<?php echo $arrdata['oh']; ?>">
                </div>
				<div class="col-sm-2">
				  <input name="OL" id="OL" class="form-control" placeholder="OL" type="text" <?php echo ($arrdata['ol']=="")?"disabled":""; ?> value="<?php echo $arrdata['ol']; ?>">
                </div>
              </div>
			  <div class="form-group">
                <label class="col-sm-3 control-label">SEAL</label>
				<div class="col-sm-4">
                  <input name="SEAL" id="SEAL" class="form-control" placeholder="SEAL" type="text" value="<?php echo $arrdata['seal']; ?>">
                </div>
              </div>
			  <div class="form-group">
                <label class="col-sm-3 control-label">&nbsp;</label>
				<div class="col-sm-4">
                  <button type="button" onclick="save_cont('<?php echo $arrdata['index']; ?>');" class="btn btn-sm btn-primary">
						<i class="fa fa-floppy-o" aria-hidden="true"></i> Simpan
				  </button>
				  <button type="reset" class="btn btn-sm btn-danger">
						<i class="fa fa-refresh" aria-hidden="true"></i> Reset
				  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
function check_iso(div,con){
    if (!con || con == "") { return false;}
    con = con.toUpperCase();
    var re = /^[A-Z]{4}\d{7}/;
    if (re.test(con)){
        var sum = 0;
        for (i = 0; i < 10; i++) {
            var n = con.substr(i, 1);
            if (i < 4) {
                n = "0123456789A?BCDEFGHIJK?LMNOPQRSTU?VWXYZ".indexOf(con.substr(i, 1));
            }
            n *= Math.pow(2, i);
            sum += n;
        }
		/*
        if (con.substr(0, 4) == "HLCU") {
            sum -= 2;
        }*/
        sum %= 11;
        sum %= 10;
        sum == con.substr(10);
    }
	if(!sum){
		$('#div-message').html('<p style="color:orange;">Kontainer tidak sesuai format</p>');
	}else{
		$("#"+div).removeAttr('style');
		$('#div-message').html('');
	}

}

function check(obj,object){
	var arrobject = object.split("|");
	var cb = $('#'+obj.id).is(':checked');
	if(cb){
		$.each(arrobject, function(a, b){
			$('#'+arrobject[a]).removeAttr('disabled');
		});
	}else{
		$.each(arrobject, function(a, b){
			$('#'+arrobject[a]).attr('disabled','disabled');
		})
	}
}

function save_cont(index_cont){
	if(validasi('form_cont','f-cont')){
		if (strpos($('#index_cont').val(),",") === false){
			$('#tablecont tbody tr').remove();
		}
		var html  = '<tr id="cont_'+index_cont+'" ondblclick="popup(\'kiosk/execute_export/kontainerdetail/edit\',\'cont='+$('#CONT_NO').val()+'&isocode='+$('#CODE_ISO').val()+'&size='+$('#CONT_SIZE').val()+'&type='+$('#CONT_TYPE').val()+'&fe='+$('input[name="FE"]:checked').val()+'&dgclass='+$('#DG_CLASS').val()+'&dgun='+$('#DG_UN').val()+'&temp='+$('#TEMPERATURE').val()+'&or='+$('#OR').val()+'&oh='+$('#OH').val()+'&ol='+$('#OL').val()+'&seal='+$('#SEAL').val()+'&index='+index_cont+'\'); ">';
			html += "<td><center>";
			html += "<span class='checkbox-custom checkbox-primary'>";
			html += "<input name=\"chkcontainer[]\" id=\"chkform-rms-wizard\" class=\"chkform-rms-wizard\" type=\"checkbox\" value=\""+index_cont+"\" onclick=\"tb_chk('form-rms-wizard',this.checked,this.value);\"><label for=\"chkform-rms-wizard\"></label>";
			html += "</span>";
			html += "</center></td>";
			
			html += "<td>";
				html += $('#CONT_NO').val().toUpperCase();
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][CONTAINER]\" id=\"CONTAINER_"+index_cont+"\" value=\""+$('#CONT_NO').val()+"\"/>";
			html += "</td>";
			
			html += "<td>";
				html += $('#CODE_ISO').val().toUpperCase();
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][ISOCODE]\" id=\"ISOCODE_"+index_cont+"\" value=\""+$('#CODE_ISO').val()+"\"/>";
			html += "</td>";
			
			html += "<td>";
				html += $('#CONT_SIZE').val().toUpperCase();
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][CONT_SIZE]\" id=\"CONT_SIZE_"+index_cont+"\" value=\""+$('#CONT_SIZE').val()+"\"/>";
			html += "</td>";
			
			html += "<td>";
				html += $('#CONT_TYPE').val().toUpperCase();
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][CONT_TYPE]\" id=\"CONT_TYPE_"+index_cont+"\" value=\""+$('#CONT_TYPE').val()+"\"/>";
			html += "</td>";
			
			html += "<td>";
				html += $('input[name="FE"]:checked').val();
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][FE]\" id=\"FE_"+index_cont+"\" value=\""+$('input[name="FE"]:checked').val()+"\"/>";
			html += "</td>";
			
			html += "<td>";
				html += $('#DG_CLASS').val().toUpperCase();
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][DG_CLASS]\" id=\"DG_CLASS_"+index_cont+"\" value=\""+$('#DG_CLASS').val()+"\"/>";
			html += "</td>";
			
			html += "<td>";
				html += $('#DG_UN').val().toUpperCase();
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][DG_UN]\" id=\"DG_UN_"+index_cont+"\" value=\""+$('#DG_UN').val()+"\"/>";
			html += "</td>";
			
			html += "<td>";
				html += $('#TEMPERATURE').val().toUpperCase();
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][TEMPERATURE]\" id=\"TEMPERATURE_"+index_cont+"\" value=\""+$('#TEMPERATURE').val()+"\"/>";
			html += "</td>";
			
			html += "<td>";
				html += $('#OR').val().toUpperCase();
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][OR]\" id=\"OR_"+index_cont+"\" value=\""+$('#OR').val()+"\"/>";
			html += "</td>";
			
			html += "<td>";
				html += $('#OH').val().toUpperCase();
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][OH]\" id=\"OH_"+index_cont+"\" value=\""+$('#OH').val()+"\"/>";
			html += "</td>";
			
			html += "<td>";
				html += $('#OL').val().toUpperCase();
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][OL]\" id=\"OL_"+index_cont+"\" value=\""+$('#OL').val()+"\"/>";
			html += "</td>";
			
			html += "<td>";
				html += "";
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][SHFT_RFR]\" id=\"SHFT_RFR_"+index_cont+"\"/>";
				html +=	"<input type=\"hidden\" name=\"containers[CONT_"+index_cont+"][SEAL]\" id=\"SEAL_"+index_cont+"\" value=\""+$('#SEAL').val()+"\"/>";
			html += "</td>";
			/*
			html += "<td><center>";
			html +=	'<button class=\'btn btn-icon btn-xs btn-danger waves-effect waves-light waves-round waves-effect waves-light\' type=\'button\' title=\'DELETE\' onclick="delete_cont(\''+index_cont+'\')">';
			html += '<i class="icon md-delete"></i>';
			html += '</button>';
			html += "</center></td>";
			*/
			html += '</tr>';
		$('#tablecont tbody tr#cont_'+index_cont).remove();
		$('#tablecont tbody').append(html);
		//$('#index_cont').val($('#index_cont').val()+','+index_cont);
		jpopup_close();
	}
}
</script>