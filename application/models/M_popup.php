<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class M_popup extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
	function popup_search($act,$id,$popup,$ajax){
		$func = get_instance();
		$this->load->library('newtable');
		$KD_USER = $this->session->userdata('ID');
		$KD_ORG = $this->session->userdata('KD_ORGANISASI');
		$KD_TPS = $this->session->userdata('KD_TPS');
		$KD_GUDANG = $this->session->userdata('KD_GUDANG');
		$KD_GROUP = $this->session->userdata('KD_GROUP');
		$arract = explode("|",$act);
		$showchk = true;
		if($id!="")	$id = "/".$id;
		if($ajax!="") $ajax = "/".$ajax;
		if($arract[0]=="perusahaan"){
			$judul = "PERUSAHAAN";
			$SQL = "SELECT A.NPWP, CONCAT(A.KD_BENTUK_PERUSAHAAN,'. ',A.NAMA) AS NAMA, A.ALAMAT, A.TELP, A.FAX, A.ID
					FROM t_organisasi A 
					LEFT JOIN app_tipe_organisasi B ON B.ID=A.KD_TIPE_ORGANISASI WHERE 1=1";
			$proses = array('SELECT' => array('OPTION', site_url()."/popup/pilih".$id, '1','','icon md-check',$popup));
			$this->newtable->search(array(array('A.NAMA', 'NAMA PERUSAHAAN')));
			$this->newtable->action(site_url()."/popup/popup_search/".$arract[0]."|".$arract[1].$id."/".$popup);
			$this->newtable->hiddens(array('ID'));			
			$this->newtable->keys(array("ID","NAMA"));
			$this->newtable->orderby(1);
			$this->newtable->sortby("ASC");
			$showchk = true;
		}
		$this->newtable->multiple_search(false);
		$this->newtable->tipe_proses('button');
		$this->newtable->show_chk($showchk);
		$this->newtable->show_search(true);
		$this->newtable->cidb($this->db);
		$this->newtable->set_formid("tblsearch");
		$this->newtable->set_divid("divtblsearch");
		$this->newtable->rowcount(10);
		$this->newtable->clear(); 
		$this->newtable->menu($proses);
		$tabel .= $this->newtable->generate($SQL);			
		$arrdata = array("title" => $judul, "content" => $tabel);
		if($this->input->post("ajax")||$act=="post") return $tabel;				 
		else return $arrdata;
	}
	
	function pilih($id,$ajax){
		$arrayReturn = array();
		$arrfield = explode('|',$id);
		if(count($arrfield>0)){
			foreach($this->input->post('tb_chktblsearch') as $chkitem){
				$arrdata[]  = $chkitem;
			}
			$value = implode($arrdata,",");
			$arrvalue = explode("~",$value);
		}
		if($ajax!="") $ajax = str_replace("~","/",$ajax);
		$arrayReturn['arrajax'] = $ajax;
		$arrayReturn['arrvalue'] = $arrvalue;
		$arrayReturn['arrfield'] = $arrfield;
		echo json_encode($arrayReturn);
	}
	
	public function execute($type,$act){
		$post = $this->input->post('term');
		if($type=="mst_kapal"){
			if (!$post) return;
			$SQL = "SELECT ID, NAMA, CALL_SIGN 
					FROM reff_kapal 
					WHERE ID LIKE '%".$post."%' OR NAMA LIKE '%".$post."%' LIMIT 5";
			$result = $this->db->query($SQL);
			$banyakData = $result->num_rows();
			$arrayDataTemp = array();
			if($banyakData > 0){
				foreach($result->result() as $row){
					$KODE = strtoupper($row->ID);
					$NAMA = strtoupper($row->NAMA);
					if($act=="kode"){
						$arrayDataTemp[] = array("value"=>$KODE,"label"=>$NAMA,'NAMA'=>$NAMA);
					}else if($act=="nama"){
						$arrayDataTemp[] = array("value"=>$NAMA,"KD_KAPAL"=>$KODE,'NAMA'=>$NAMA);
					}
				}
			}
			echo json_encode($arrayDataTemp);
		}else if($type=="mst_port"){
			if (!$post) return;
			$SQL = "SELECT ID, CONCAT(NAMA,'[',ID,']') AS NAMA, NAMA AS GET_NAME
					FROM reff_pelabuhan WHERE ID LIKE '%".$post."%' OR NAMA LIKE '%".$post."%' LIMIT 5";
			$result = $this->db->query($SQL);
			$banyakData = $result->num_rows();
			$arrayDataTemp = array();
			if($banyakData > 0){
				foreach($result->result() as $row){
					$KODE = strtoupper($row->ID);
					$NAMA = strtoupper($row->NAMA);
					$GET = strtoupper($row->GET_NAME);
					if($act=="kode"){
						$arrayDataTemp[] = array("value"=>$KODE,"label"=>$NAMA,"NAMA"=>$GET);	
					}else if($act=="nama"){
						$arrayDataTemp[] = array("value"=>$GET,"label"=>$NAMA,"KODE"=>$KODE);
					}
				}
			}
			echo json_encode($arrayDataTemp);
		}else if($type=="mst_kemasan"){
			if (!$post) return;
			$SQL = "SELECT ID, CONCAT(NAMA,' [',ID,']') AS NAMA, NAMA AS GET_NAME
					FROM reff_kemasan WHERE ID LIKE '%".$post."%' OR NAMA LIKE '%".$post."%' LIMIT 5";
			$result = $this->db->query($SQL);
			$banyakData = $result->num_rows();
			$arrayDataTemp = array();
			if($banyakData > 0){
				foreach($result->result() as $row){
					$KODE = strtoupper($row->ID);
					$NAMA = strtoupper($row->NAMA);
					$GET = strtoupper($row->GET_NAME);
					if($act=="kode"){
						$arrayDataTemp[] = array("value"=>$KODE,"label"=>$NAMA,'NAMA'=>$GET);
					}else if($act=="nama"){
						$arrayDataTemp[] = array("value"=>$GET,"label"=>$NAMA,'KODE'=>$KODE);
					}
				}
			}
			echo json_encode($arrayDataTemp);
		}else if($type=="mst_dok_bc"){
			if (!$post) return;
			$SQL = "SELECT ID, NAMA FROM reff_kode_dok_bc 
					WHERE KD_PERMIT = ".$this->db->escape(strtoupper($act))."
					AND NAMA LIKE '%".$post."%' LIMIT 5";
			$result = $this->db->query($SQL);
			$banyakData = $result->num_rows();
			$arrayDataTemp = array();
			if($banyakData > 0){
				foreach($result->result() as $row){
					$KODE = strtoupper($row->ID);
					$NAMA = strtoupper($row->NAMA);
					$arrayDataTemp[] = array("value"=>$NAMA,"KODE"=>$KODE);
				}
			}
			echo json_encode($arrayDataTemp);
		}else if($type=="mst_isocode"){
			if (!$post) return;
			$SQL = "SELECT ID, NAMA FROM reff_cont_isocode
					WHERE ID LIKE '%".$post."%' OR NAMA LIKE '%".$post."%' LIMIT 5";
			$result = $this->db->query($SQL);
			$banyakData = $result->num_rows();
			$arrayDataTemp = array();
			if($banyakData > 0){
				foreach($result->result() as $row){
					$KODE = strtoupper($row->ID);
					$NAMA = strtoupper($row->NAMA);
					$arrayDataTemp[] = array("value"=>$NAMA,"KODE"=>$KODE);
				}
			}
			echo json_encode($arrayDataTemp);
		}else if($type=="mst_organisasi"){
			if (!$post) return;
			if($act!=""){
				$add_sql = " AND KD_TIPE_ORGANISASI = ".$this->db->escape($act);
			}
			$SQL = "SELECT ID, NAMA FROM t_organisasi 
					WHERE NAMA LIKE '%".$post."%'".$add_sql."
					LIMIT 5";
			$result = $this->db->query($SQL);
			$banyakData = $result->num_rows();
			$arrayDataTemp = array();
			if($banyakData > 0){
				foreach($result->result() as $row){
					$KODE = strtoupper($row->ID);
					$NAMA = strtoupper($row->NAMA);
					$arrayDataTemp[] = array("value"=>$NAMA,"KODE"=>$KODE);
				}
			}
			echo json_encode($arrayDataTemp);
		}else if($type=="mst_gudang"){
			if (!$post) return;
			$SQL = "SELECT KD_TPS, KD_GUDANG, NAMA_GUDANG FROM reff_gudang 
					WHERE KD_GUDANG LIKE '%".$post."%' OR NAMA_GUDANG LIKE '%".$post."%' LIMIT 5";
			$result = $this->db->query($SQL);
			$banyakData = $result->num_rows();
			$arrayDataTemp = array();
			if($banyakData > 0){
				foreach($result->result() as $row){
					$KD_TPS = strtoupper($row->KD_TPS);
					$KD_GUDANG = strtoupper($row->KD_GUDANG);
					$NM_GUDANG = strtoupper($row->NAMA_GUDANG);
					if($act=="kode"){
						$arrayDataTemp[] = array("value"=>$KD_GUDANG,"KD_TPS"=>$KD_TPS,"NM_GUDANG"=>$NM_GUDANG);
					}else if($act=="nama"){
						$arrayDataTemp[] = array("value"=>$NM_GUDANG,"KD_TPS"=>$KD_TPS,"NM_GUDANG"=>$NM_GUDANG);
					}
				}
			}
			echo json_encode($arrayDataTemp);
		}else if($type=="res_plp"){
			if (!$post) return;
			$SQL = "SELECT A.ID, A.KD_KPBC, A.KD_TPS_ASAL, A.KD_TPS_TUJUAN, A.KD_GUDANG_TUJUAN, A.NO_PLP, A.NO_SURAT, A.TGL_SURAT, A.NM_ANGKUT, 
					DATE_FORMAT(A.TGL_PLP,'%d-%m-%Y') AS TGL_PLP, A.NO_VOY_FLIGHT, A.CALL_SIGN, A.TGL_TIBA, A.NO_BC11, A.TGL_BC11
					FROM t_respon_plp_tujuan_v2_hdr A
					WHERE A.NO_PLP LIKE '%".$post."%' LIMIT 5";
			$result = $this->db->query($SQL);
			$banyakData = $result->num_rows();
			$arrayDataTemp = array();
			if($banyakData > 0){
				foreach($result->result() as $row){
					$PLP = strtoupper($row->NO_PLP);
					$TGL_PLP = $row->TGL_PLP;
					$arrayDataTemp[] = array("value"=>$PLP,"TGL_PLP"=>$TGL_PLP);
				}
			}
			echo json_encode($arrayDataTemp);	
		}else if($type=="notification"){
			$banyakData = 0;
			$SQL = "SELECT ID FROM t_reservasi 
					WHERE KD_STATUS = '400' 
					AND FL_FEEDBACK = 'N' 
					AND NPWP = ".$this->db->escape($this->session->userdata('NPWP'));
			$result = $this->db->query($SQL);
			$banyakData = $result->num_rows();
			return $banyakData;
		}
	}
	
	public function get_combobox($act,$id){
		$func = get_instance();
		$func->load->model("m_main", "main", true);
		if($act=='cont_ukuran'){
			$sql = "SELECT ID, NAMA FROM reff_cont_ukuran ORDER BY ID ASC";
			$arrdata = $func->main->get_combobox($sql, "ID", "NAMA", TRUE);
		}else if($act=='cont_jenis'){
			$sql = "SELECT ID, NAMA FROM reff_cont_jenis ORDER BY ID ASC";
			$arrdata = $func->main->get_combobox($sql, "ID", "NAMA", TRUE);
		}else if($act=='cont_tipe'){
			$sql = "SELECT ID, NAMA FROM reff_cont_tipe ORDER BY ID ASC";
			$arrdata = $func->main->get_combobox($sql, "ID", "NAMA", TRUE);
		}else if($act=='cont_isocode'){
			$sql = "SELECT ID, NAMA FROM reff_cont_isocode ORDER BY ID ASC";
			$arrdata = $func->main->get_combobox($sql, "ID", "NAMA", TRUE);
		}else if($act=='cont_status'){
			$sql = "SELECT ID, NAMA FROM reff_cont_status ORDER BY ID ASC";
			$arrdata = $func->main->get_combobox($sql, "ID", "NAMA", TRUE);
		}else if($act=='sarana_angkut'){
			$sql = "SELECT ID, NAMA FROM reff_sarana_angkut ORDER BY ID ASC";
			$arrdata = $func->main->get_combobox($sql, "ID", "NAMA", TRUE);
		}else if($act=='dok_bc'){
			$sql = "SELECT ID, NAMA FROM reff_kode_dok_bc WHERE KD_PERMIT = ".$this->db->escape($id)." ORDER BY ID ASC";
			$arrdata = $func->main->get_combobox($sql, "ID", "NAMA", TRUE);
		}
		return $arrdata;
	}
}
?>