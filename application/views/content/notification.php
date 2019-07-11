<div class="panel" id="notification_feedback" popup="open">
  <div class="ribbon ribbon-clip ribbon-primary">
    <span class="ribbon-inner">
        <i class="icon md-check-square margin-0" aria-hidden="true"></i> <?php echo $title; ?>
    </span>
  </div>
  <button type="button" class="btn btn-sm btn-primary navbar-right navbar-btn waves-effect waves-light" onclick="save_post('form_reservasi_finish','divtblreservasi'); return false;">
  	<i class="icon md-badge-check"></i> FINISH
  </button>
  <div class="padding-top-30">&nbsp;</div>
  <div class="panel-body"> 
    <form name="form_reservasi_finish" id="form_reservasi_finish" class="form-horizontal" method="post" action="<?php echo site_url('execute/process/update/reservasi_finish/'.$arrdata[0]['KD_RESERVASI']); ?>" popup="1">
        <div class="row">
        	<?php foreach($arrdata as $data) : ?>
                <blockquote class="blockquote blockquote-danger">
                    <p><?php echo $data['KETERANGAN']; ?></p>
                </blockquote> 
            <?php endforeach; ?>
            <div class="form-group form-material">
                <div class="col-sm-12">
                  <textarea name="KETERANGAN_COMMENT" id="KETERANGAN_COMMENT" mandatory="yes" class="form-control focus" placeholder="KETERANGAN"><?php echo $arrdata['KETERANGAN']; ?></textarea>
                  <div class="hint">KETERANGAN</div>
                </div>
            </div>
        </div>
    </form>
  </div>
</div>