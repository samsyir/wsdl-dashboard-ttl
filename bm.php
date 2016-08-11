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
	  	$jenis 			= array();
	  	$jenis_jasa     = array();
	  	$uom 			= array();
	  	$bch 			= array();
	  	$bch_gross 		= array();
	  	$bsh 			= array();
	  	$bsh_gross		= array();
		$i 				= 0;
		$k				= 0;
		$data 			= array();

			$query_get_bch = oci_parse($conn, "SELECT substr(b.DATE_WORK,0,4) AS tahun,
				        substr(b.DATE_WORK,5,2) AS bulan,
				        'INTERNATIONAL' AS jenis,
				        'CONTAINER' AS jenis_jasa,
				        'BOX' AS UOM,
				        avg(b.BCH) AS BCH,
				        avg(bg.BCH) AS BCH_GROSS
				    FROM VD_BCH b
				    left JOIN VD_BCH_GROSS bg
				    	on b.ves_id = bg.ves_id
				    where substr(b.ves_id,0,4) IN
				        (SELECT ves_code
				        FROM vessels
				        WHERE     ocean_interisland = 'I'
				        AND LINER = b.shipper)
					AND substr(b.DATE_WORK,0,4) = '".date('Y')."' AND substr(b.DATE_WORK,5,2) = '".date('m')."'
				GROUP BY substr(b.DATE_WORK,0,4),
				    substr(b.DATE_WORK,5,2)                    
				UNION ALL
				SELECT substr(b.DATE_WORK,0,4) AS tahun,
				    substr(b.DATE_WORK,5,2) AS bulan,
				    'DOMESTIC' AS jenis,
				    'CONTAINER' AS jenis_jasa,
				    'BOX' AS UOM,
				    avg(b.BCH) AS BCH,
				    avg(bg.BCH) AS BCH_GROSS
				FROM VD_BCH b
				left JOIN VD_BCH_GROSS bg
				on b.ves_id = bg.ves_id
				where substr(b.ves_id,0,4) IN
				    (SELECT ves_code
				        FROM vessels
				        WHERE     ocean_interisland = 'D'
				        AND LINER = b.shipper)
				AND substr(b.DATE_WORK,0,4) = '".date('Y')."' AND substr(b.DATE_WORK,5,2) = '".date('m')."'
				GROUP BY substr(b.DATE_WORK,0,4),
				    substr(b.DATE_WORK,5,2) 
				order by tahun, bulan");
			oci_execute($query_get_bch);

			while (($row = oci_fetch_array($query_get_bch, OCI_BOTH)) != false) 
			  {
			  	  
				  $tahun[$i] 		= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
				  $bulan[$i] 		= isset($row['BULAN']) ? $row['BULAN'] : '';
				  $jenis[$i] 		= isset($row['JENIS']) ? $row['JENIS'] : '';
				  $jenis_jasa[$i] 	= isset($row['JENIS_JASA'])		? $row['JENIS_JASA']	 : '';
				  $uom[$i]			= isset($row['UOM'])		? $row['UOM']	 : '';
				  $bch[$i] 			= $row['BCH'];
				  $bch_gross[$i]	= $row['BCH_GROSS'];			  

				  $i+=1;
			  }

			$query_get_bsh = oci_parse($conn, "SELECT to_char(bs.ACT_BERTH_TS,'yyyy') AS tahun,
			        to_char(bs.ACT_BERTH_TS,'mm') AS bulan,
			        'INTERNATIONAL' AS jenis,
			        'CONTAINER' AS jenis_jasa,
			        'BOX' AS UOM,
			        avg(bs.BSH) AS BSH,
			        avg(bv.BVWH) AS BSH_GROSS
			   FROM VD_BSH bs
			   left JOIN VD_BVWH bv
			   on trim(bs.ves_id) = trim(bv.ves_id)
			   where trim(bs.ves_code) IN
			               (SELECT trim(v.ves_code)
			                  FROM vessels v
			                 WHERE     v.ocean_interisland = 'I'
			                       AND v.agent = bs.agent)
			AND to_char(bs.ACT_BERTH_TS,'yyyy') = '".date('Y')."' AND to_char(bs.ACT_BERTH_TS,'mm') = '".date('m')."'
			GROUP BY to_char(bs.ACT_BERTH_TS,'yyyy'),
			        to_char(bs.ACT_BERTH_TS,'mm')
			UNION ALL
			SELECT to_char(bs.ACT_BERTH_TS,'yyyy') AS tahun,
			        to_char(bs.ACT_BERTH_TS,'mm') AS bulan,
			        'DOMESTIC' AS jenis,
			        'CONTAINER' AS jenis_jasa,
			        'BOX' AS UOM,
			        avg(bs.BSH) AS BSH,
			        avg(bv.BVWH) AS BSH_GROSS
			   FROM VD_BSH bs
			   left JOIN VD_BVWH bv
			   on trim(bs.ves_id) = trim(bv.ves_id)
			   where trim(bs.ves_code) IN
			               (SELECT trim(v.ves_code)
			                  FROM vessels v
			                 WHERE     v.ocean_interisland = 'D'
			                       AND v.agent = bs.agent)
			AND to_char(bs.ACT_BERTH_TS,'yyyy') = '".date('Y')."' AND to_char(bs.ACT_BERTH_TS,'mm') = '".date('m')."'
			GROUP BY to_char(bs.ACT_BERTH_TS,'yyyy'),
			        to_char(bs.ACT_BERTH_TS,'mm')
			order by tahun, bulan");
			oci_execute($query_get_bsh);

			while (($row = oci_fetch_array($query_get_bsh, OCI_BOTH)) != false) 
			  {
			  	  
				  $tahun[$k] 		= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
				  $bulan[$k] 		= isset($row['BULAN']) ? $row['BULAN'] : '';
				  $jenis[$k] 		= isset($row['JENIS']) ? $row['JENIS'] : '';
				  $jenis_jasa[$k] 	= isset($row['JENIS_JASA'])		? $row['JENIS_JASA']	 : '';
				  $uom[$k]			= isset($row['UOM'])		? $row['UOM']	 : '';
				  $bsh[$k] 			= $row['BSH'];
				  $bsh_gross[$k]	= $row['BSH_GROSS'];			  

				  $k+=1;
			  }

			$client = new SOAPClient("./BM.wsdl",array('login' => 'ttlsupport', 'password' => 'teluklamong15'));


			for ($j=0; $j < $i; $j++) { 
				$content = []; 
				$data = [];
			    $content = array(
						'ZTAHUN'		=> $tahun[$j],
						'ZBULAN'		=> $bulan[$j],
						'ZJENIS'		=> substr($jenis[$j],0,1),
						'ZJENIS_JASA'	=> $jenis_jasa[$j],
						'ZUOM'			=> $uom[$j],
						'ZBCH'			=> round($bch[$j],2),
						'ZBCH_GROSS'	=> round($bch_gross[$j],2),
						'ZBSH' 			=> round($bsh[$j],2),
						'ZBSH_GROSS' 	=> round($bsh_gross[$j],2)
				);

				$data['LT_TAB']['ITEM'][] = $content;

				try {				
					$client->SI_BM_WS2RFC_SRC($data);
					echo date('dmY').date('His').' Bongkar Muat sent'."\r\n";
				} catch (Exception $e) {
					echo $e;
				}
			}

			
	  
	?>