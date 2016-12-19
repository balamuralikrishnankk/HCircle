<?php
/* API Details
 * Parameter name	: pntID
 * Method 	 	: GET
 * URL Access Pattern	:?pntID=value_of_PatientID
*/
error_reporting(E_ALL);
ini_set('display_errors', 'On');
if(true){
    $DBstr = "(DESCRIPTION =
        (ADDRESS_LIST =
          (ADDRESS = (PROTOCOL = TCP)(HOST = 202.191.203.21)(PORT = 1521))
        )
        (CONNECT_DATA =
          (SERVICE_NAME = ABMHT2DB)
        )
      )";
    $conn = oci_connect('rcaremagnumfo', 'rcaremagnumfo', $DBstr);
//getting the patient id , passed as parameter, via GET method
    $PatientID = $_GET['pntID'];
    $query="SELECT  OH.DOCNUM ORDERNO,
		    RI.ITEMDESC,
		    MO.QUANTITY,
		    (TO_CHAR(OH.ORDERDATE,'DD/MM/YYYY')) ORDERDATE,
        	    PV.DOCNUM VISITNO,
	            PA.DOCNUM MRN,
		    FN_GET_PATIENT_NAME(PA.DOCNUM) PATIENT,
		    VT.VISITTYPENAME,
               	    FN_GET_EMPLOYEE_NAME(OH.ORDERPLACERCODE) DOCTOR,
		    RU.UOMNAME
	    FROM    MEDICATIONORDERS MO
	    INNER JOIN ORDERHEADER OH ON (MO.DOCIDORDERHEADER=OH.DOCID)
	    INNER JOIN STATUS OS ON (MO.ORDERSTATUSCODE=OS.STATUSNUMBER AND OS.STATUSTYPECODE = 'MEDSTS')
	    INNER JOIN ITEM RI ON (MO.ITEMCODE=RI.ITEMCODE)
	    INNER JOIN UOM RU ON (RI.SALESUOMCODE=RU.UOMCODE)
	    INNER JOIN PATIENTS PA ON (OH.DOCIDPATIENTS=PA.DOCID)
	    INNER JOIN PATIENTVISITS PV ON (OH.DOCIDPATIENTVISITS=PV.DOCID)
	    INNER JOIN VISITTYPES VT ON (PV.VISITTYPECODE=VT.VISITTYPECODE)
	    WHERE PA.DOCNUM='".$PatientID."' AND ROWNUM<5 ORDER BY OH.DOCDATE DESC";



    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
    
    if($row != false) {

       //print_r(json_encode($row));
	print_r(json_encode(array("state"=>true,"PharmacyOrderList"=>$row)));
    }else{
      echo "Please Enter Valid Patient ID";
    }

}else{
  echo "Please Enter Valid Patient ID";
}
?>
