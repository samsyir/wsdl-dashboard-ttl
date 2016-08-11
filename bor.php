<?php
	set_time_limit(1000);
	$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 10.110.0.3)(PORT = 1521)))(CONNECT_DATA=(SERVICE_NAME=tos)))"; 
	$conn = ocilogon("cbslam","cbslam1",$db); 
	date_default_timezone_set("Asia/Bangkok");
	
	if (!$conn){
		$e = oci_error();
    	trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}

	  	$tahun				= array();
	  	$bulan 				= array();
	  	$bor_international	= array();
	  	$bor_domestic		= array();
	  	$bor_dry_bulk		= array();
	  	$total_bor			= array();
		$i 					= 0;
		$data 				=array();



		$query_get_data = oci_parse($conn, "SELECT TRIM(TAHUN) TAHUN, TRIM(BULAN) BULAN,
		       NVL (SUM (DECODE (DESCR, 'INTERNATIONAL', BOR)), 0) BOR_INTERNATIONAL,
		       NVL (SUM (DECODE (DESCR, 'DOMESTIC', BOR)), 0) BOR_DOMESTIC,
		       NVL (SUM (DECODE (DESCR, 'DRY_BULK', BOR)), 0) BOR_DRY_BULK,
		       NVL (AVG (BOR), 0) TOTAL_BOR
		       FROM VD_BOR
		       WHERE TAHUN = '".date('Y')."' AND BULAN = '".date('m')."'
		   	   GROUP BY TRIM(TAHUN), TRIM(BULAN)
		       ORDER BY TAHUN");
		oci_execute($query_get_data);

		while (($row = oci_fetch_array($query_get_data, OCI_BOTH)) != false) 
		{
			  	  
			$tahun[$i] 				= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
			$bulan[$i]				= isset($row['BULAN']) ? $row['BULAN'] : '';
			$bor_international[$i]  = $row['BOR_INTERNATIONAL'];
			$bor_domestic[$i] 		= $row['BOR_DOMESTIC'];	
			$bor_dry_bulk[$i]  		= $row['BOR_DRY_BULK'];
			$total_bor[$i] 			= $row['TOTAL_BOR'];			  

			$i+=1;
		}

			$client = new SOAPClient("./bor.wsdl",array('login' => 'ttlsupport', 'password' => 'teluklamong15'));

			for ($j=0; $j < $i; $j++) {
				$content = []; 
				$data = [];
				$content =  array(
							'ZTAHUN'			=> $tahun[$j],
							'ZBULAN'			=> $bulan[$j],
							'ZTANGGAL'			=> date('d.m.Y'),
							'ZJAM'				=> date('H:i:s'),
							'ZBOR_INTER'		=> $bor_international[$j],
							'ZBOR_DOM'			=> $bor_domestic[$j],
							'ZBOR_DBULK'		=> $bor_dry_bulk[$j],
							'ZBOR_TOTAL'		=> $total_bor[$j],
						);

				$data['LT_TAB']['ITEM'][] = $content;	    

				try {				
					$client->SI_BOR_WS2RFC_SRC($data);
					echo date('dmY').date('His').' BOR sent'."\r\n";
				} catch (Exception $e) {
					echo $e;
				}

				
			}				

	?>