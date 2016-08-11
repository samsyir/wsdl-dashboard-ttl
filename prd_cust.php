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
	  	$ves_code		= array();
	  	$shipper		= array();
	  	$full_name		= array();
	  	$ocean_interisland = array();
	  	$ves_name		= array();
	  	$bch			= array();
	  	$bsh			= array();
		$i 				= 0;
		$data 			=array();

			$query_get_data = oci_parse($conn, "SELECT TRIM(TAHUN) TAHUN, TRIM(BULAN) BULAN,
				TRIM(VES_CODE) VES_CODE, TRIM(SHIPPER) SHIPPER, TRIM(FULL_NAME) FULL_NAME,
				TRIM(OCEAN_INTERISLAND) OCEAN_INTERISLAND, TRIM(VES_NAME) VES_NAME, BCH, BSH
				FROM VD_KINERJA_CUSTOMER WHERE TAHUN = '".date('Y')."' AND BULAN = '".date('m')."'");
			oci_execute($query_get_data);

			while (($row = oci_fetch_array($query_get_data, OCI_BOTH)) != false) 
			  {
			  	  
				  $tahun[$i] 		= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
				  $bulan[$i] 		= isset($row['BULAN']) ? $row['BULAN'] : '';
				  $ves_code[$i] 	= isset($row['VES_CODE']) ? $row['VES_CODE'] : '';
				  $shipper[$i]	 	= isset($row['SHIPPER']) ? $row['SHIPPER'] : '';
				  $full_name[$i]	= isset($row['FULL_NAME']) ? $row['FULL_NAME'] : '';
				  $ocean_interisland[$i]	 	= isset($row['OCEAN_INTERISLAND']) ? $row['OCEAN_INTERISLAND'] : '';
				  $ves_name[$i]	 	= isset($row['VES_NAME']) ? $row['VES_NAME'] : '';
				  $bch[$i] 			= $row['BCH'];			  
				  $bsh[$i] 			= $row['BSH'];			  

				  $i+=1;
			  }
		

			$client = new SOAPClient("./prd_cust.wsdl",array('login' => 'ttlsupport', 'password' => 'teluklamong15'));


			for ($j=0; $j < $i; $j++) { 
				$content = []; 
				$data = [];
			    $content = array(
						'ZTAHUN'		=> $tahun[$j],
						'ZBULAN'		=> $bulan[$j],
						'ZJENIS'		=> $ocean_interisland[$j],
						'ZKAPAL'		=> $ves_code[$j],
						'ZVESSEL'		=> $ves_name[$j],
						'ZCUSTOMER'		=> $shipper[$j],
						'ZSHIPPING_AGENT'		=> $full_name[$j],
						'ZBCH'			=> $bch[$j],
						'ZBSH'			=> $bsh[$j],
						'ZTANGGAL'		=> date('d.m.Y'),
						'ZJAM'			=> date('H:i:s')
				);

				$data['LT_TAB']['ITEM'][] = $content;

				try {				
					$client->SI_PRD_CUST_WS2RFC_SRC($data);
					echo date('dmY').date('His').' Produksi Customer sent'."\r\n";
				} catch (Exception $e) {
					echo $e;
				}
			}			

			
	  
	?>