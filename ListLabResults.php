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
    $conn = oci_pconnect('rcaremagnumfo', 'rcaremagnumfo', $DBstr);
//getting the patient id , passed as parameter, via GET method
    $PatientID = $_GET['pntID'];
    $query="SELECT DISTINCT	CH.DOCNUM LABNO,
       					PAT.DOCNUM MRNNO,
				        FN_GET_PATIENT_NAME(PAT.DOCNUM) PATIENTNAME,
       					CASE
         				WHEN pat.patientgendercode = 'M' THEN
          				'MALE'
         				WHEN pat.patientgendercode = 'F' THEN
          				'FEMALE'
       					END SEX,
       					VT.VISITTYPENAME VISITTYPE,
       					CD.SAMPLEID SAMPLENO,
       					SPT.SPECIALITYNAME,
       					SP.SPECIMENNAME,
       					SER.SERVICENAME PROFILE,
       					SERV.SERVICENAME INVESTICATION,
       					lrd.resultnumeric RESULT,
       					lrd.normalminvalue || '-' || lrd.normalmaxvalue NORMALVALUES,
       					lrd.resulttemplatetext,
       					OH.DOCNUM ORDERNO,
       					trunc(OH.ORDERDATE) ORDERDATE,
       					CH.DOCDATE COLLECTIONDATE,
       					FN_GET_EMPLOYEE_NAME(EMP.EMPLOYEECODE) DOCTOR,
       					DEP.DEPARTMENTNAME DEPARTMENT,
       					ST.STATUSNAME STATUS,
       					prt.priorityname PRIORITY,
       					lrd.organismremarks,
       					lrd.microbiologyremarks
  				FROM 	COLLECTIONHEADER        CH,
       					COLLECTIONDETAILS       CD,
       					COLLECTIONSTATUSDETAILS CSD,
       					LABRESULTDETAILS        LRD,
       					STATUS                  ST,
       					SPECIMENS               SP,
       					PATIENTS                PAT,
       					SERVICES                SER,
       					SERVICES                SERV,
       					INVESTIGATIONPARAMETERS IVP,
       					SPECIALITIES            SPT,
       					LABORDERS               LO,
       					ORDERHEADER             OH,
       					VISITTYPES              VT,
       					EMPLOYEES               EMP,
       					DEPARTMENT              DEP,
       					ORGSTRUCTURE            ORG,
       					priorities              prt
 				WHERE 	CH.DOCID = CD.DOCIDCOLLECTIONHEADER
				   AND CD.DOCDETAILID = CSD.DOCDETAILIDCOLLDETAILS
				   AND CSD.DOCDETAILID = LRD.DOCDETAILIDCSD(+)
				   AND (CD.SAMPLESTATUS = ST.STATUSNUMBER AND ST.STATUSTYPECODE = 'LABSTS')
				   AND CD.SAMPLETYPE = SP.SPECIMENCODE
				   AND CH.DOCIDPATIENTS = PAT.DOCID
				   AND CSD.PROFILE = SER.SERVICECODE(+)
				   AND CSD.INVESTIGATION = SERV.SERVICECODE
				   AND LRD.PARAMETERCODE = IVP.CODE(+)
				   AND CD.SAMPLESENTOLOCATION = SPT.SPECIALITYCODE
				   AND CSD.DOCDETAILIDLABORDERS = LO.DOCDETAILID
				   AND LO.DOCIDORDERHEADER = OH.DOCID
				   AND OH.VISITTYPECODE = VT.VISITTYPECODE
				   AND OH.ORDERPLACERCODE = EMP.EMPLOYEECODE
				   AND OH.DEPARTMENTCODE = DEP.DEPARTMENTCODE
				   AND OH.ORGSTRUCTCODE = ORG.ORGSTRUCTCODE
				   AND lo.prioritycode = prt.prioritycode
				   AND   PAT.DOCNUM='".$PatientID."' AND ROWNUM<5    
				ORDER BY  CH.DOCNUM,CH.DOCDATE DESC";



    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS);
    
    if($row != false) {
       $err = array("status"=>"true");
	$row=array_merge($err,$row);
       print_r(json_encode(array("LabResultList"=>$row)));
    }else{
      //echo "Please Enter Valid Patient ID";
	//$row = array("state"=>"error");
       $err = array("status"=>"false");
	//$row=array_merge($err,$row);
       print_r(json_encode(array("LabResultList"=>$err)));
    }

}else{
//  echo "Please Enter Valid Patient ID";
//	$row = array("state"=>"error");
      $err = array("status"=>"false");
	//$row=array_merge($err,$row);
       print_r(json_encode(array("LabResultList"=>$err)));
}
?>
