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

		/* require the user as the parameter */
		if(isset($_GET['vd_bch']) ) {

			/* soak in the passed variable or set our own */
			//$number_of_posts = isset($_GET['num']) ? intval($_GET['num']) : 10; //10 is the default
			//$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml'; //xml is the default

			$query_get_data = oci_parse($conn, "SELECT TRIM(DATE_WORK) DATE_WORK, TRIM(SHIFT_WORK) SHIFT_WORK,
				TRIM(GROUP_WORK) GROUP_WORK, TRIM(VES_ID) VES_ID, TRIM(SHIPPER) SHIPPER, TRIM(CC_NO) CC_NO, BCH
				FROM VD_BCH");
			oci_execute($query_get_data);

			while (($row = oci_fetch_array($query_get_data, OCI_BOTH)) != false) 
			  {
			  	  
				  $date_work[$i] 	= isset($row['DATE_WORK'])  ? $row['DATE_WORK']  : '';
				  $shift_work[$i] 	= isset($row['SHIFT_WORK']) ? $row['SHIFT_WORK'] : '';
				  $group_work[$i] 	= isset($row['GROUP_WORK']) ? $row['GROUP_WORK'] : '';
				  $ves_id[$i] 		= isset($row['VES_ID'])		? $row['VES_ID']	 : '';
				  $shipper[$i] 		= isset($row['SHIPPER'])	? $row['SHIPPER']	 : '';
				  $cc_no[$i] 		= isset($row['CC_NO'])		? $row['CC_NO']		 : '';
				  $bch[$i] 			= $row['BCH'];			  

				  $i+=1;
			  }

			 $print = array();

			for ($j=0; $j < $i; $j++) { 
			    $data['items'][] = array(
						'date_work'		=> $date_work[$j],
						'shift_work'	=> $shift_work[$j],
						'group_work'	=> $group_work[$j],
						'ves_id'		=> $ves_id[$j],
						'shipper'		=> $shipper[$j],
						'cc_no'			=> $cc_no[$j],
						'bch'			=> $bch[$j]
				);
			}

			//header('Content-type: application/json');
			//echo "[".implode(",", $print)."]";


			header('Content-type: text/xml');
			echo xml_encode($data);
		}
		else if(isset($_GET['vd_bor']) ) {

			/* soak in the passed variable or set our own */
			//$number_of_posts = isset($_GET['num']) ? intval($_GET['num']) : 10; //10 is the default
			//$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml'; //xml is the default

			$query_get_data = oci_parse($conn, "SELECT TRIM(TAHUN) TAHUN, TRIM(BULAN) BULAN,
				TRIM(BERTH_TYPE) BERTH_TYPE, BOR
				FROM VD_BOR WHERE TAHUN = '2016' AND BULAN = '07' ");
			oci_execute($query_get_data);

			while (($row = oci_fetch_array($query_get_data, OCI_BOTH)) != false) 
			  {
			  	  
				  $tahun[$i] 		= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
				  $bulan[$i] 		= isset($row['BULAN']) ? $row['BULAN'] : '';
				  $berth_type[$i] 	= isset($row['BERTH_TYPE']) ? $row['BERTH_TYPE'] : '';
				  $bor[$i] 			= $row['BOR'];			  

				  $i+=1;
			  }

			 $print = array();

			for ($j=0; $j < $i; $j++) { 
			    $data['LT_TAB']['item'][] = array(
						'ZTAHUN'			=> $tahun[$j],
						'ZBULAN'			=> $bulan[$j],
						'ZTANGGAL'			=> date('dmY'),
						'ZJAM'				=> date('His'),
						'ZBOR'				=> $bor[$j]
				);
			}

			class SOAPStruct
			{
			    function __construct($user, $pass) 
			    {
			        $this->username = $user;
			        $this->password = $pass;
			    }
			}

			$client = new SOAPClient("./bor.wsdl",array('login' => 'ttlsupport', 'password' => 'teluklamong15'));

			//$header = new SoapHeader("http://teluklamong.co.id/bo/to/bor","Authorization",$auth,false); 

			//$client->__setSoapHeaders($header);


			//header('Content-type: application/json');
			//echo "[".implode(",", $print)."]";


			//header('Content-type: text/xml');
			//$xml = xml_encode($data);
			try {				
				$client->SI_BOR_WS2RFC_SRC($data);
				echo 'bor sent';
			} catch (Exception $e) {
				echo $e;
			}		
		}
		else if(isset($_GET['vd_produksi_alat']) ) {

			/* soak in the passed variable or set our own */
			//$number_of_posts = isset($_GET['num']) ? intval($_GET['num']) : 10; //10 is the default
			//$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml'; //xml is the default

			$query_get_data = oci_parse($conn, "SELECT TRIM(TAHUN) TAHUN, TRIM(BULAN) BULAN,
				TRIM(JENIS_ALAT) JENIS_ALAT, TRIM(UNIT_ALAT) UNIT_ALAT, QTY
				FROM VD_PRODUKSI_ALAT");
			oci_execute($query_get_data);

			while (($row = oci_fetch_array($query_get_data, OCI_BOTH)) != false) 
			  {
			  	  
				  $tahun[$i] 		= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
				  $bulan[$i] 		= isset($row['BULAN']) ? $row['BULAN'] : '';
				  $jenis_alat[$i] 	= isset($row['JENIS_ALAT']) ? $row['JENIS_ALAT'] : '';
				  $unit_alat[$i] 	= isset($row['UNIT_ALAT']) ? $row['UNIT_ALAT'] : '';
				  $qty[$i] 			= $row['QTY'];			  

				  $i+=1;
			  }

			 $print = array();

			for ($j=0; $j < $i; $j++) { 
			    $data['items'][] = array(
						'tahun'			=> $tahun[$j],
						'bulan'			=> $bulan[$j],
						'jenis_alat'	=> $jenis_alat[$j],
						'unit_alat'		=> $unit_alat[$j],
						'qty'			=> $qty[$j]
				);
			}

			//header('Content-type: application/json');
			//echo "[".implode(",", $print)."]";


			header('Content-type: text/xml');
			echo xml_encode($data);
		}
		else if(isset($_GET['vd_throughput']) ) {

			/* soak in the passed variable or set our own */
			//$number_of_posts = isset($_GET['num']) ? intval($_GET['num']) : 10; //10 is the default
			//$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml'; //xml is the default

			$query_get_data = oci_parse($conn, "SELECT TRIM(TAHUN) TAHUN, TRIM(BULAN) BULAN,
				TRIM(CUSTOMER) CUSTOMER, TRIM(JENIS) JENIS, VALUE
				FROM VD_THROUGHPUT");
			oci_execute($query_get_data);

			while (($row = oci_fetch_array($query_get_data, OCI_BOTH)) != false) 
			  {
			  	  
				  $tahun[$i] 		= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
				  $bulan[$i] 		= isset($row['BULAN']) ? $row['BULAN'] : '';
				  $customer[$i] 	= isset($row['CUSTOMER']) ? $row['CUSTOMER'] : '';
				  $jenis[$i] 		= isset($row['JENIS']) ? $row['JENIS'] : '';
				  $value[$i] 		= $row['VALUE'];			  

				  $i+=1;
			  }

			 $print = array();

			for ($j=0; $j < $i; $j++) { 
			    $data['items'][] = array(
						'tahun'			=> $tahun[$j],
						'bulan'			=> $bulan[$j],
						'customer'		=> $customer[$j],
						'jenis'			=> $jenis[$j],
						'value'			=> $value[$j]
				);
			}

			//header('Content-type: application/json');
			//echo "[".implode(",", $print)."]";


			header('Content-type: text/xml');
			echo xml_encode($data);
		}
		else if(isset($_GET['vd_trt']) ) {

			/* soak in the passed variable or set our own */
			//$number_of_posts = isset($_GET['num']) ? intval($_GET['num']) : 10; //10 is the default
			//$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml'; //xml is the default

			$query_get_data = oci_parse($conn, "SELECT TRIM(TAHUN) TAHUN, TRIM(BULAN) BULAN,
				KURANG_30, LEBIH_30
				FROM VD_TRT");
			oci_execute($query_get_data);

			while (($row = oci_fetch_array($query_get_data, OCI_BOTH)) != false) 
			  {
			  	  
				  $tahun[$i] 		= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
				  $bulan[$i] 		= isset($row['BULAN']) ? $row['BULAN'] : '';
				  $kurang_30[$i] 	= isset($row['KURANG_30']) ? $row['KURANG_30'] : '';
				  $lebih_30[$i]		= isset($row['LEBIH_30']) ? $row['LEBIH_30'] : '';			  

				  $i+=1;
			  }

			 $print = array();

			for ($j=0; $j < $i; $j++) { 
			    $data['items'][] = array(
						'tahun'			=> $tahun[$j],
						'bulan'			=> $bulan[$j],
						'kurang_30'		=> $kurang_30[$j],
						'lebih_30'		=> $lebih_30[$j]
				);
			}

			//header('Content-type: application/json');
			//echo "[".implode(",", $print)."]";


			header('Content-type: text/xml');
			echo xml_encode($data);
		}
		else if(isset($_GET['vd_vessel_calls']) ) {

			/* soak in the passed variable or set our own */
			//$number_of_posts = isset($_GET['num']) ? intval($_GET['num']) : 10; //10 is the default
			//$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml'; //xml is the default

			$query_get_data = oci_parse($conn, "SELECT TRIM(TAHUN) TAHUN, TRIM(BULAN) BULAN,
				TRIM(JENIS) JENIS, UNIT
				FROM VD_VESSEL_CALLS");
			oci_execute($query_get_data);

			while (($row = oci_fetch_array($query_get_data, OCI_BOTH)) != false) 
			  {
			  	  
				  $tahun[$i] 		= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
				  $bulan[$i] 		= isset($row['BULAN']) ? $row['BULAN'] : '';
				  $jenis[$i] 		= isset($row['JENIS']) ? $row['JENIS'] : '';
				  $unit[$i] 		= $row['UNIT'];			  

				  $i+=1;
			  }

			 $print = array();

			for ($j=0; $j < $i; $j++) { 
			    $data['items'][] = array(
						'tahun'			=> $tahun[$j],
						'bulan'			=> $bulan[$j],
						'jenis'			=> $jenis[$j],
						'unit'			=> $unit[$j]
				);
			}

			//header('Content-type: application/json');
			//echo "[".implode(",", $print)."]";


			header('Content-type: text/xml');
			echo xml_encode($data);
		}
		else if(isset($_GET['vd_yor']) ) {

			/* soak in the passed variable or set our own */
			//$number_of_posts = isset($_GET['num']) ? intval($_GET['num']) : 10; //10 is the default
			//$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml'; //xml is the default

			$query_get_data = oci_parse($conn, "SELECT TRIM(TAHUN) TAHUN, TRIM(BULAN) BULAN,
				YOR
				FROM VD_YOR");
			oci_execute($query_get_data);

			while (($row = oci_fetch_array($query_get_data, OCI_BOTH)) != false) 
			  {
			  	  
				  $tahun[$i] 		= isset($row['TAHUN'])  ? $row['TAHUN']  : '';
				  $bulan[$i] 		= isset($row['BULAN']) ? $row['BULAN'] : '';
				  $yor[$i] 			= $row['YOR'];			  

				  $i+=1;
			  }

			 $print = array();

			for ($j=0; $j < $i; $j++) { 
			    $data['items'][] = array(
						'tahun'			=> $tahun[$j],
						'bulan'			=> $bulan[$j],
						'yor'			=> $yor[$j]
				);
			}

			//header('Content-type: application/json');
			//echo "[".implode(",", $print)."]";


			header('Content-type: text/xml');
			echo xml_encode($data);
		}


	function xml_encode($mixed, $domElement=null, $DOMDocument=null) {
	    if (is_null($DOMDocument)) {
	        $DOMDocument =new DOMDocument;
	        $DOMDocument->formatOutput = true;
	        xml_encode($mixed, $DOMDocument, $DOMDocument);
	        echo $DOMDocument->saveXML();
	    }
	    else {
	        if (is_array($mixed)) {
	            foreach ($mixed as $index => $mixedElement) {
	                if (is_int($index)) {
	                    if ($index === 0) {
	                        $node = $domElement;
	                    }
	                    else {
	                        $node = $DOMDocument->createElement($domElement->tagName);
	                        $domElement->parentNode->appendChild($node);
	                    }
	                }
	                else {
	                    $plural = $DOMDocument->createElement($index);
	                    $domElement->appendChild($plural);
	                    $node = $plural;
	                    if (rtrim($index,'s')!==$index && count($mixedElement)>1) {
	                        $singular = $DOMDocument->createElement(rtrim($index, 's'));
	                        $plural->appendChild($singular);
	                        $node = $singular;
	                    }
	                }
	 
	                xml_encode($mixedElement, $node, $DOMDocument);
	            }
	        }
	        else {
	            $domElement->appendChild($DOMDocument->createTextNode($mixed));
	        }
	    }
	}	  

	  
	?>