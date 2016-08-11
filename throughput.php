<?php
	set_time_limit(3000);
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
		$jenis_jasa		= array();
		$uom 			= array();
		$value			= array();
		$kurang_30		= array();
		$lebih_30		= array();
		$unit 			= array();
		$yor			= array();
		$agent			= array();
		$i 				= 0;

			$query_get_data = oci_parse($conn, "SELECT TRIM(TAHUN) TAHUN, TRIM(BULAN) BULAN,
				TRIM(CUSTOMER) CUSTOMER, TRIM(AGENT) AGENT, TRIM(JENIS) JENIS, TRIM(JENIS_JASA) JENIS_JASA, TRIM(UOM) UOM, VALUE
				FROM VD_THROUGHPUT ");
			oci_execute($query_get_data);

			while (($row = oci_fetch_array($query_get_data, OCI_BOTH)) != false) 
			  {
			  	  
				  $tahun[$i] 		= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
				  $bulan[$i] 		= isset($row['BULAN']) ? $row['BULAN'] : '';
				  $customer[$i] 	= isset($row['CUSTOMER']) ? $row['CUSTOMER'] : '';
				  $agent[$i]		= isset($row['AGENT']) ? $row['AGENT'] : '';
				  $jenis[$i] 		= isset($row['JENIS']) ? $row['JENIS'] : '';
				  $jenis_jasa[$i] 	= isset($row['JENIS_JASA']) ? $row['JENIS_JASA'] : '';
				  $uom[$i]		 	= isset($row['UOM']) ? $row['UOM'] : '';
				  $value[$i] 		= $row['VALUE'];			  

				  $i+=1;
			  }

			$client = new SOAPClient("./Throughput.wsdl",array('login' => 'ttlsupport', 'password' => 'teluklamong15'));


			for ($j=0; $j < $i; $j++) {
				$content = []; 
				$data = [];
			    $content = array(
						'ZTAHUN'		=> $tahun[$j],
						'ZBULAN'		=> $bulan[$j],
						'ZCUSTOMER'		=> $customer[$j],
						'ZCUST_CODE'	=> $agent[$j],
						'ZJENIS'		=> $jenis[$j],
						'ZJENIS_JASA'	=> $jenis_jasa[$j],
						'ZUOM'			=> $uom[$j],
						'ZVALUE'		=> $value[$j]
				);

				$data['LT_TAB']['ITEM'][] = $content;

				try {				
					$client->SI_THTP_WS2RFC_SRC($data);
					echo date('dmY').date('His').' Throughput sent'."\r\n";
				} catch (Exception $e) {
					echo $e;
				}
			}	
	  
	?>