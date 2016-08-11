<?php
	set_time_limit(1000);
	$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 10.110.0.3)(PORT = 1521)))(CONNECT_DATA=(SERVICE_NAME=tos)))"; 
	$conn = ocilogon("cbslam","cbslam1",$db); 
	date_default_timezone_set("Asia/Bangkok");
	
	if (!$conn){
		$e = oci_error();
    	trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}

	  	$tahun			= array();
	  	$bulan 			= array();
		$date_work 		= array();
		$shift_work 	= array();
		$group_work 	= array();
		$ves_id 		= array();
		$shipper 		= array();
		$cc_no	 		= array();
		$bch 			= array();
		$berth_type		= array();
		$bor		 	= array();
		$jenis_alat		= array();
		$unit_alat		= array();
		$qty			= array();
		$customer		= array();
		$jenis 			= array();
		$value			= array();
		$kurang_30		= array();
		$lebih_30		= array();
		$unit 			= array();
		$yor			= array();
		$i 				= 0;
		$data 			=array();

			$query_get_data = oci_parse($conn, "SELECT TRIM(TAHUN) TAHUN, TRIM(BULAN) BULAN, TRIM(JENIS) JENIS,
				YOR
				FROM VD_YOR WHERE TAHUN = '".date('Y')."' AND BULAN = '".date('m')."' ");
			oci_execute($query_get_data);

			while (($row = oci_fetch_array($query_get_data, OCI_BOTH)) != false) 
			  {
			  	  
				  $tahun[$i] 		= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
				  $bulan[$i] 		= isset($row['BULAN']) ? $row['BULAN'] : '';
				  $jenis[$i] 		= isset($row['JENIS']) ? $row['JENIS'] : '';
				  $yor[$i] 			= $row['YOR'];			  

				  $i+=1;
			  }

			$client = new SOAPClient("./yor.wsdl",array('login' => 'ttlsupport', 'password' => 'teluklamong15'));

			for ($j=0; $j < $i; $j++) { 
				$content = []; 
				$data = [];
			    $content = array(
						'ZTAHUN'			=> $tahun[$j],
						'ZBULAN'			=> $bulan[$j],
						'ZJENIS'			=> $jenis[$j],
						'ZTANGGAL'			=> date('d.m.Y'),
						'ZJAM'				=> date('H:i:s'),
						'ZYOR'				=> $yor[$j]
				);

				$data['LT_TAB']['ITEM'][] = $content;	  

			try {				
				$client->SI_YOR_WS2RFC_SRC($data);
				echo date('dmY').date('His').' YOR sent'."\r\n";
			} catch (Exception $e) {
				echo $e;
			}

				
			}		

	  
	?>