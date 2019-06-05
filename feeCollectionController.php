<?php
namespace App\Http\Controllers;
//first change
class feeCollectionController extends Controller {

	var $data = array();
	var $panelInit ;
	var $layout = 'dashboard';

	public function __construct(){
		if(app('request')->header('Authorization') != ""){
			$this->middleware('jwt.auth');
		}else{
			$this->middleware('authApplication');
		}
		$this->panelInit = new \DashboardInit();
		$this->data['panelInit'] = $this->panelInit;
		$this->data['breadcrumb']['Settings'] = \URL::to('/dashboard/languages');
		$this->data['users'] = $this->panelInit->getAuthUser();
		if(!isset($this->data['users']->id)){
			return \Redirect::to('/');
		}

		if(!$this->panelInit->hasThePerm('accounting')){
			exit;
		}
	}
	public function permission_denied(){
		return $this->panelInit->apiOutput(false,"Restricted","You don't have permissions to access this module.");
	}
	// public function listAll($page = 1){
	// 	$toReturn = array();
	// 	if($this->data['users']->role == "admin" || $this->data['users']->role == "account"){
	// 		$toReturn['invoices'] = \DB::table('fee_collection')
	// 					->where('fee_collection.school_id',$this->panelInit->authUser['school_id'])
	// 					->leftJoin('users', 'users.id', '=', 'fee_collection.paymentStudent')
	// 					->select('fee_collection.id as id',
	// 					'fee_collection.paymentTitle as paymentTitle',
	// 					'fee_collection.paymentDescription as paymentDescription',
	// 					'fee_collection.paymentAmount as paymentAmount',
	// 					'fee_collection.paidAmount as paidAmount',
	// 					'fee_collection.paymentStatus as paymentStatus',
	// 					'fee_collection.paymentDate as paymentDate',
	// 					'fee_collection.dueDate as dueDate',
	// 					'fee_collection.paymentStudent as studentId',
	// 					'users.fullName as fullName');
	// 	}elseif($this->data['users']->role == "student" || $this->data['users']->role == "teacher"){
	// 		$toReturn['invoices'] = \DB::table('fee_collection')
	// 					->where('fee_collection.school_id',$this->panelInit->authUser['school_id'])
	// 					->where('paymentStudent',$this->data['users']->id)
	// 					->leftJoin('users', 'users.id', '=', 'fee_collection.paymentStudent')
	// 					->select('fee_collection.id as id',
	// 					'fee_collection.paymentTitle as paymentTitle',
	// 					'fee_collection.paymentDescription as paymentDescription',
	// 					'fee_collection.paymentAmount as paymentAmount',
	// 					'fee_collection.paidAmount as paidAmount',
	// 					'fee_collection.paymentStatus as paymentStatus',
	// 					'fee_collection.paymentDate as paymentDate',
	// 					'fee_collection.dueDate as dueDate',
	// 					'fee_collection.paymentStudent as studentId',
	// 					'users.fullName as fullName');

	// 	}elseif($this->data['users']->role == "parent"){

	// 		$studentId = array();
	// 		$parentOf = json_decode($this->data['users']->parentOf,true);
	// 		if(is_array($parentOf)){
	// 			while (list($key, $value) = each($parentOf)) {
	// 				$studentId[] = $value['id'];
	// 			}
	// 		}
	// 		$toReturn['invoices'] = \DB::table('fee_collection')
	// 					->where('fee_collection.school_id',$this->panelInit->authUser['school_id'])
	// 					->whereIn('paymentStudent',$studentId)
	// 					->leftJoin('users', 'users.id', '=', 'fee_collection.paymentStudent')
	// 					->select('fee_collection.id as id',
	// 					'fee_collection.paymentTitle as paymentTitle',
	// 					'fee_collection.paymentDescription as paymentDescription',
	// 					'fee_collection.paymentAmount as paymentAmount',
	// 					'fee_collection.paidAmount as paidAmount',
	// 					'fee_collection.paymentStatus as paymentStatus',
	// 					'fee_collection.paymentDate as paymentDate',
	// 					'fee_collection.dueDate as dueDate',
	// 					'fee_collection.paymentStudent as studentId',
	// 					'users.fullName as fullName');
	// 	}

	// 	if(\Input::has('searchInput')){
	// 		$searchInput = \Input::get('searchInput');
	// 		if(is_array($searchInput)){

	// 			if(isset($searchInput['dueInv']) AND $searchInput['dueInv'] == true){
	// 				$toReturn['invoices'] = $toReturn['invoices']->where('dueDate','<',time())->where('paymentStatus','!=','1');
	// 			}

	// 			if(isset($searchInput['text']) AND strlen($searchInput['text']) > 0 ){
	// 				$keyword = $searchInput['text'];
	// 				$toReturn['invoices'] = $toReturn['invoices']->where(function($query) use ($keyword){
	// 					$query->where('fee_collection.paymentTitle','LIKE','%'.$keyword.'%');
	// 					$query->orWhere('fee_collection.paymentDescription','LIKE','%'.$keyword.'%');
	// 					$query->orWhere('fullName','LIKE','%'.$keyword.'%');
	// 				});
	// 			}

	// 			if(isset($searchInput['paymentStatus']) AND $searchInput['paymentStatus'] != ""){
	// 				$toReturn['invoices'] = $toReturn['invoices']->where('paymentStatus',$searchInput['paymentStatus']);
	// 			}

	// 			if(isset($searchInput['fromDate']) AND $searchInput['fromDate'] != ""){
	// 				$searchInput['fromDate'] = $this->panelInit->date_to_unix($searchInput['fromDate']);
	// 				$toReturn['invoices'] = $toReturn['invoices']->where('paymentDate','>=',$searchInput['fromDate']);
	// 			}

	// 			if(isset($searchInput['toDate']) AND $searchInput['toDate'] != ""){
	// 				$searchInput['toDate'] = $this->panelInit->date_to_unix($searchInput['toDate']);
	// 				$toReturn['invoices'] = $toReturn['invoices']->where('paymentDate','<=',$searchInput['toDate']);
	// 			}

	// 		}
	// 	}

	// 	$toReturn['totalItems'] = $toReturn['invoices']->count();
	// 	$toReturn['invoices'] = $toReturn['invoices']->orderBy('id','DESC')->take('20')->skip(20* ($page - 1) )->get();

	// 	foreach ($toReturn['invoices'] as $key => $value) {
	// 		$toReturn['invoices'][$key]->paymentDate = $this->panelInit->unix_to_date($toReturn['invoices'][$key]->paymentDate);
	// 		$toReturn['invoices'][$key]->dueDate = $this->panelInit->unix_to_date($toReturn['invoices'][$key]->dueDate);
	// 		/*$toReturn['invoices'][$key]->paymentAmount = $toReturn['invoices'][$key]->paymentAmount + ($this->panelInit->settingsArray['paymentTax']*$toReturn['invoices'][$key]->paymentAmount) /100;*/
	// 		$toReturn['invoices'][$key]->paymentAmount = $toReturn['invoices'][$key]->paymentAmount;
	// 	}

	// 	$toReturn['currency_symbol'] = $this->panelInit->settingsArray['currency_symbol'];

	// 	$classes = \classes::where('school_id',$this->panelInit->authUser['school_id'])->get();
	// 	$toReturn['classes'] = array();
	// 	foreach ($classes as $key => $value) {
	// 		$toReturn['classes'][$key]['className'] = $classes[$key]->className ;
	// 		$toReturn['classes'][$key]['id'] = $classes[$key]->id;
	// 	}

	// 	return $toReturn;
	// }

	// public function delete($id){
	// 	if($this->data['users']->role != "admin" && $this->data['users']->role != "account") exit;
	// 	if ( $postDelete = \fee_collection::where('id', $id)->first() )
    //     {
    //         $postDelete->delete();
    //         return $this->panelInit->apiOutput(true,$this->panelInit->language['delPayment'],$this->panelInit->language['paymentDel']);
    //     }else{
    //         return $this->panelInit->apiOutput(false,$this->panelInit->language['delPayment'],$this->panelInit->language['paymentNotExist']);
    //     }
	// }





	// public function create(){
	// 	if($this->data['users']->role != "admin" && $this->data['users']->role != "account") exit;
	// 	$craetedPayments = array();

	// 	$school_id = $this->panelInit->authUser['school_id'];

	// 	if(\Input::get('userType') == "students"){
	// 		$classId = \Input::get('classId');
	// 		$userslistArray = \User::where(array('school_id'=>$school_id,'role'=>'student','studentClass'=>$classId))->select('id')->get()->toArray();
	// 		$studentClass = \User::whereIn('id',$userslistArray)->select('id')->get()->toArray();
	// 	}else{
	// 		$studentClass = \Input::get('paymentStudent');
	// 	}
	



	// 	if(!is_array($studentClass)){
	// 		return $this->panelInit->apiOutput(false,$this->panelInit->language['addPayment'],"No students are selected");
	// 	}
	// 	while (list($key, $value) = each($studentClass)) {
	// 		if($value['id'] == "" || $value['id'] == "0"){
	// 			continue;
	// 		}
	// 		$payments = new \fee_collection();
	// 		$payments->paymentTitle = \Input::get('paymentTitle');
	// 		if(\Input::has('paymentDescription')){
	// 			$payments->paymentDescription = \Input::get('paymentDescription');
	// 		}
	// 		$payments->paymentStudent = $value['id'];

	// 		if(\Input::has('paymentRows')){
	// 			$payments->paymentRows = json_encode(\Input::get('paymentRows'));

	// 			$paymentAmount = 0;
	// 			$paymentRows = \Input::get('paymentRows');
	// 			while (list($key, $value) = each($paymentRows)) {
	// 				$paymentAmount += $value['amount'];
	// 			}
	// 		}else{
	// 			$paymentRows = array();
	// 			$payments->paymentRows = json_encode($paymentRows);
	// 			$paymentAmount = 0;
	// 		}

	// 		$payments->paymentAmount = $paymentAmount;
	// 		if(\Input::get('paymentStatus') == 0 || \Input::get('paymentStatus') == 2){
	// 			$payments->paymentDate = $this->panelInit->date_to_unix(\Input::get('paymentDate'));
	// 			$payments->dueDate = $this->panelInit->date_to_unix(\Input::get('dueDate'));
	// 			$payments->payment_date = date('Y-m-d',strtotime(str_replace('/','-',\Input::get('paymentDate'))));
	// 			$payments->due_date = date('Y-m-d',strtotime(str_replace('/','-',\Input::get('dueDate'))));
	// 		}	
	// 		$payments->paymentUniqid = uniqid();
	// 		$payments->school_id = $this->panelInit->authUser['school_id'];
	// 		$payments->paymentStatus = \Input::get('paymentStatus');
	// 		if(\Input::get('paymentStatus') == 1 ){
	// 			$payments->paidAmount = $paymentAmount;
	// 			if(\Input::has('paidMethod')){
	// 				$payments->paidMethod = \Input::get('paidMethod');
	// 			}
	// 			if(\Input::has('paidTime')){
	// 				$payments->paidTime = \Input::get('paidTime');
	// 			}
	// 		}
	// 		if(\Input::get('paymentStatus') == 2 ){
	// 			$payments->paidAmount = \Input::get('partial_amount');
	// 			if(\Input::has('paidMethod')){
	// 				$payments->paidMethod = \Input::get('paidMethod');
	// 			}
	// 			// if(\Input::has('paidTime')){
	// 			// 	$payments->paidTime = \Input::get('paidTime');
	// 			// }
	// 		}



			
	// 		$payments->save();

	// 		if(\Input::get('paymentStatus') == 2){
	// 			//if(\Input::has('paidTime')){
	// 				$paymentsCollection = new \paymentsCollection();
	// 				$paymentsCollection->invoiceId = $payments->id;
	// 				$paymentsCollection->collectionAmount = \Input::get('partial_amount');
	// 				$paymentsCollection->collectionDate = $this->panelInit->date_to_unix(\Input::get('paymentDate'));
	// 				$paymentsCollection->collection_date = date('Y-m-d',strtotime(str_replace('/','-',\Input::get('paymentDate'))));
	// 				$paymentsCollection->collectionMethod = \Input::get('paidMethod');
	// 				if(\Input::has('partialcollectionNote')){
	// 					$paymentsCollection->collectionNote = \Input::get('partialcollectionNote');
	// 				}
	// 				$paymentsCollection->collectedBy = $this->data['users']->id;
	// 				$paymentsCollection->save();
	// 			//}
	// 		}

	// 		$this->panelInit->mobNotifyUser('users',$value['id'], $this->panelInit->language['newPaymentNotif']);

	// 		$payments->paymentDate = \Input::get('paymentDate');
	// 		$payments->dueDate = \Input::get('dueDate');

	// 		$craetedPayments[] = $payments->toArray();
	// 	}

	// 	return $this->panelInit->apiOutput(true,$this->panelInit->language['addPayment'],$this->panelInit->language['paymentCreated'],$craetedPayments );
	// }

/* ----------------------------------------------------- Old---------------------------------------------------- */






	// public function create(){
	// 	if($this->data['users']->role != "admin" && $this->data['users']->role != "account") exit;
	// 	$craetedPayments = array();
	// 	$studentClass = \Input::get('paymentStudent');
	// 	if(!is_array($studentClass)){
	// 		return $this->panelInit->apiOutput(false,$this->panelInit->language['addPayment'],"No students are selected");
	// 	}
	// 	while (list($key, $value) = each($studentClass)) {
	// 		if($value['id'] == "" || $value['id'] == "0"){
	// 			continue;
	// 		}
	// 		$payments = new \fee_collection();
	// 		$payments->paymentTitle = \Input::get('paymentTitle');
	// 		if(\Input::has('paymentDescription')){
	// 			$payments->paymentDescription = \Input::get('paymentDescription');
	// 		}
	// 		$payments->paymentStudent = $value['id'];

	// 		if(\Input::has('paymentRows')){
	// 			$payments->paymentRows = json_encode(\Input::get('paymentRows'));

	// 			$paymentAmount = 0;
	// 			$paymentRows = \Input::get('paymentRows');
	// 			while (list($key, $value) = each($paymentRows)) {
	// 				$paymentAmount += $value['amount'];
	// 			}
	// 		}else{
	// 			$paymentRows = array();
	// 			$payments->paymentRows = json_encode($paymentRows);
	// 			$paymentAmount = 0;
	// 		}

	// 		$payments->paymentAmount = $paymentAmount;
	// 		$payments->paymentDate = $this->panelInit->date_to_unix(\Input::get('paymentDate'));
	// 		$payments->dueDate = $this->panelInit->date_to_unix(\Input::get('dueDate'));

	// 		$payments->paymentUniqid = uniqid();
	// 		$payments->school_id = $this->panelInit->authUser['school_id'];
	// 		$payments->paymentStatus = \Input::get('paymentStatus');
	// 		if(\Input::get('paymentStatus') == 1){
	// 			$payments->paidAmount = $paymentAmount;
	// 			if(\Input::has('paidMethod')){
	// 				$payments->paidMethod = \Input::get('paidMethod');
	// 			}
	// 			if(\Input::has('paidTime')){
	// 				$payments->paidTime = \Input::get('paidTime');
	// 			}
	// 		}
	// 		$payments->save();

	// 		$this->panelInit->mobNotifyUser('users',$value['id'], $this->panelInit->language['newPaymentNotif']);

	// 		$payments->paymentDate = \Input::get('paymentDate');
	// 		$payments->dueDate = \Input::get('dueDate');

	// 		$craetedPayments[] = $payments->toArray();
	// 	}

	// 	return $this->panelInit->apiOutput(true,$this->panelInit->language['addPayment'],$this->panelInit->language['paymentCreated'],$craetedPayments );
	// }

	// function invoice($id = ""){
	// 	$return = array();
	// 	$return['payment'] = \fee_collection::where('id',$id)->first()->toArray();
	// 	$return['payment']['paymentDate'] = $this->panelInit->unix_to_date($return['payment']['paymentDate']);
	// 	if($return['payment']['dueDate'] < time()){
	// 		$return['payment']['isDueDate'] = true;
	// 	}
	// 	$return['payment']['dueDate'] = $this->panelInit->unix_to_date($return['payment']['dueDate']);
	// 	if($return['payment']['paymentStatus'] == "1"){
	// 		$return['payment']['paidTime'] = $this->panelInit->unix_to_date($return['payment']['paidTime']);
	// 	}
	// 	$return['payment']['paymentRows'] = json_decode($return['payment']['paymentRows'],true);
	// 	$return['siteTitle'] = $this->panelInit->settingsArray['siteTitle'];
	// 	$return['baseUrl'] = \URL::to('/');
	// 	$return['address'] = $this->panelInit->settingsArray['address'];
	// 	$return['address2'] = $this->panelInit->settingsArray['address2'];
	// 	$return['systemEmail'] = $this->panelInit->settingsArray['systemEmail'];
	// 	$return['phoneNo'] = $this->panelInit->settingsArray['phoneNo'];
	// 	$return['paypalPayment'] = $this->panelInit->settingsArray['paypalPayment'];
	// 	$return['currency_code'] = $this->panelInit->settingsArray['currency_code'];
	// 	$return['currency_symbol'] = $this->panelInit->settingsArray['currency_symbol'];
	// 	$return['paymentTax'] = $this->panelInit->settingsArray['paymentTax'];
	// 	$return['amountTax'] = $return['payment']['paymentAmount'];
	// 	$return['totalWithTax'] = $return['payment']['paymentAmount'];
	// 	$return['pendingAmount'] = $return['totalWithTax'] - $return['payment']['paidAmount'];
	// 	$return['user'] = \User::where('users.id',$return['payment']['paymentStudent'])->leftJoin('classes','users.studentClass','=','classes.id')->leftJoin('sections','users.studentSection','=','sections.id')->select('users.*','classes.className','sections.sectionName','sections.sectionTitle')->first()->toArray();

	// 	$return['paypalEnabled'] = $this->panelInit->settingsArray['paypalEnabled'];
	// 	$return['2coEnabled'] = $this->panelInit->settingsArray['2coEnabled'];
	// 	$return['payumoneyEnabled'] = $this->panelInit->settingsArray['payumoneyEnabled'];

	// 	$return['collection'] = \paymentsCollection::where('invoiceId',$id)->get()->toArray();
	// 	while (list($key, $value) = each($return['collection'])) {
	// 		$return['collection'][$key]['collectionDate'] = $this->panelInit->unix_to_date($return['collection'][$key]['collectionDate']);
	// 	}

	// 	return $return;
	// }

	function fetch($id){
		$payments = \fee_collection::where('id',$id)->first()->toArray();
		$payments['paymentDate'] = $this->panelInit->unix_to_date($payments['paymentDate']);
		$payments['dueDate'] = $this->panelInit->unix_to_date($payments['dueDate']);
		$payments['paymentRows'] = json_decode($payments['paymentRows'],true);
		if(!is_array($payments['paymentRows'])){
			$payments['paymentRows'] = array();
			$payments['paymentRows'][] = array('title'=>$payments['paymentDescription'],'amount'=>$payments['paymentAmount']);
		}
		return $payments;
	}

	// function fetch_fee_detail($id){
	// 	$payments = \fee_collection::where('id',$id)->first()->toArray();
	// 	$payments['paymentDate'] = $this->panelInit->unix_to_date($payments['paymentDate']);
	// 	$payments['dueDate'] = $this->panelInit->unix_to_date($payments['dueDate']);
		

	// 	$studentDetails = \DB::table('fee_collection')
	// 		->where('fee_collection.id',$id)
	// 		->leftJoin('users', 'users.id', '=', 'fee_collection.paymentStudent')
	// 		->leftJoin('classes', 'classes.id', '=', 'users.studentClass')
	// 		->leftJoin('sections', 'sections.id', '=', 'users.studentSection')
	// 		->leftJoin('schools', 'schools.id', '=', 'users.school_id')
	// 		->select('users.fullName as student',
	// 		'users.studentRollId as studentRollId',
	// 		'classes.className as className',
	// 		'sections.sectionTitle as section',
	// 		'schools.school_name as school',
	// 		'schools.address as address'
	// 		)->get();
	// 	$payments['student'] = "";
	// 	$payments['school'] = "";
	// 	if(!empty($studentDetails)){
	// 		$payments['student'] = $studentDetails[0]->student;
	// 		$payments['school'] = $studentDetails[0]->school;
	// 		$payments['className'] = $studentDetails[0]->className;
	// 		$payments['section'] = $studentDetails[0]->section;
	// 		$payments['address'] = $studentDetails[0]->address;
	// 	}
		

	// 	$payments['paymentRows'] = json_decode($payments['paymentRows'],true);
	// 	if(!is_array($payments['paymentRows'])){
	// 		$payments['paymentRows'] = array();
	// 		$payments['paymentRows'][] = array('title'=>$payments['paymentDescription'],'amount'=>$payments['paymentAmount']);
	// 	}
	// 	return $payments;
	// }

	function edit($id){
		if($this->data['users']->role != "admin" && $this->data['users']->role != "account") exit;
		$payments = \fee_collection::find($id);
		$payments->paymentTitle = \Input::get('paymentTitle');
		if(\Input::has('paymentDescription')){
			$payments->paymentDescription = \Input::get('paymentDescription');
		}

		if(\Input::has('paymentRows')){
			$payments->paymentRows = json_encode(\Input::get('paymentRows'));

			$paymentAmount = 0;
			$paymentRows = \Input::get('paymentRows');
			while (list($key, $value) = each($paymentRows)) {
				$paymentAmount += $value['amount'];
			}
		}else{
			$paymentRows = array();
			$payments->paymentRows = json_encode($paymentRows);
			$paymentAmount = 0;
		}

		$payments->paymentAmount = $paymentAmount;
		$payments->paymentDate = $this->panelInit->date_to_unix(\Input::get('paymentDate'));
		$payments->dueDate = $this->panelInit->date_to_unix(\Input::get('dueDate'));
		$payments->save();

		$payments->paymentDate = \Input::get('paymentDate');
		$payments->dueDate = \Input::get('dueDate');

		return $this->panelInit->apiOutput(true,$this->panelInit->language['editPayment'],$this->panelInit->language['paymentModified'],$payments->toArray() );
	}

	// function collect($id){
	// 	if($this->data['users']->role != "admin" && $this->data['users']->role != "account") exit;
	// 	$payments = \fee_collection::where('id',$id);
	// 	if($payments->count() == 0){
	// 		return;
	// 	}
	// 	$payments = $payments->first();
	// 	$amountTax = $payments->paymentAmount;
	// 	$totalWithTax = $payments->paymentAmount;
	// 	$pendingAmount = $totalWithTax - $payments->paidAmount;

	// 	if(bccomp(\Input::get('collectionAmount'), $pendingAmount,10) == 1){
	// 		return $this->panelInit->apiOutput(false,"Invoice Collection","Collection amount is greater that invoice pending amount");
	// 	}

	// 	$paymentsCollection = new \paymentsCollection();
	// 	$paymentsCollection->invoiceId = $id;
	// 	$paymentsCollection->collectionAmount = \Input::get('collectionAmount');
	// 	$paymentsCollection->collectionDate = $this->panelInit->date_to_unix(\Input::get('collectionDate'));
	// 	$paymentsCollection->collectionMethod = \Input::get('collectionMethod');
	// 	if(\Input::has('collectionNote')){
	// 		$paymentsCollection->collectionNote = \Input::get('collectionNote');
	// 	}
	// 	$paymentsCollection->collectedBy = $this->data['users']->id;
	// 	$paymentsCollection->save();

	// 	$payments->paidAmount = $payments->paidAmount+$paymentsCollection->collectionAmount;
	// 	if($payments->paidAmount >= $totalWithTax){
	// 		$payments->paymentStatus = 1;
	// 	}else{
	// 		$payments->paymentStatus = 2;
	// 	}
	// 	$payments->paidMethod = \Input::get('collectionMethod');
	// 	$payments->paidTime = $this->panelInit->date_to_unix(\Input::get('collectionDate'));
	// 	$payments->save();

	// 	$payments->paymentAmount = $totalWithTax;

	// 	return $this->panelInit->apiOutput(true,"Invoice Collection","Collection completed successfully",$payments->toArray());
	// }

	function revert($id){
		if($this->data['users']->role != "admin" && $this->data['users']->role != "account") exit;
		$paymentsCollection = \paymentsCollection::where('id',$id);
		if($paymentsCollection->count() == 0){
			return;
		}
		$paymentsGet = $paymentsCollection->first();
		$invoice = $paymentsGet->invoiceId;
		$paymentsCollection = $paymentsCollection->delete();

		//recalculate
		$totalPaid = 0;
		$paymentsCollection = \paymentsCollection::where('invoiceId',$invoice)->get();
		foreach ($paymentsCollection as $key => $value) {
			$totalPaid += $value['collectionAmount'];
		}

		$payments = \fee_collection::where('id',$invoice);
		if($payments->count() == 0){
			return;
		}
		$payments = $payments->first();

		$amountTax = ($this->panelInit->settingsArray['paymentTax']*$payments->paymentAmount) /100;
		$totalWithTax = $payments->paymentAmount + $amountTax;

		if($totalPaid >= $totalWithTax){
			$payments->paymentStatus = 1;
		}elseif ($totalPaid == 0) {
			$payments->paymentStatus = 0;
		}else{
			$payments->paymentStatus = 2;
		}
		$payments->paidAmount = $totalPaid;
		$payments->save();

		return $this->panelInit->apiOutput(true,"Revert Invoice Collection","Collection reverted successfully",$payments->toArray());
	}

	function paymentSuccess($uniqid){
		$payments = \fee_collection::where('paymentUniqid',$uniqid)->first();
		if(\Input::get('verify_sign')){
			$payments->paymentStatus = 1;
			$payments->paymentSuccessDetails = json_encode(\Input::all());
			$payments->save();
		}
		return \Redirect::to('/#/feeCollection');
	}

	function PaymentData($id){
		if($this->data['users']->role != "admin" && $this->data['users']->role != "account") exit;
		$payments = \fee_collection::where('id',$id)->first();
		if($payments->paymentSuccessDetails == ""){
			return $this->panelInit->apiOutput(false,$this->panelInit->language['paymentDetails'],$this->panelInit->language['noPaymentDetails'] );
		}else{
			return $this->panelInit->apiOutput(true,null,null,json_decode($payments->paymentSuccessDetails,true) );
		}
	}

	function paymentFailed(){
		return \Redirect::to('/#/feeCollection');
	}

	

	function export($type){
		if($this->data['users']->role != "admin" && $this->data['users']->role != "account") exit;
		if($type == "excel"){

			$return['currency_symbol'] = $this->panelInit->settingsArray['currency_symbol'];

			$data = array(1 => array ('Invoice ID','Title','Student','Amount','Paid Amount','Date','Due Date','Status'));

			$toReturn['invoices'] = \DB::table('fee_collection')
						->leftJoin('users', 'users.id', '=', 'fee_collection.paymentStudent')
						->select('fee_collection.id as id',
						'fee_collection.paymentTitle as paymentTitle',
						'fee_collection.paymentDescription as paymentDescription',
						'fee_collection.paymentAmount as paymentAmount',
						'fee_collection.paidAmount as paidAmount',
						'fee_collection.paymentStatus as paymentStatus',
						'fee_collection.paymentDate as paymentDate',
						'fee_collection.dueDate as dueDate',
						'fee_collection.paymentStudent as studentId',
						'users.fullName as fullName');
			$toReturn['totalItems'] = $toReturn['invoices']->count();
			$toReturn['invoices'] = $toReturn['invoices']->orderBy('id','DESC')->get();

			foreach ($toReturn['invoices'] as $key => $value) {
				$value->paymentDate = $this->panelInit->unix_to_date($toReturn['invoices'][$key]->paymentDate);
				$value->dueDate = $this->panelInit->unix_to_date($toReturn['invoices'][$key]->dueDate);
				$value->paymentAmount = $toReturn['invoices'][$key]->paymentAmount + ($this->panelInit->settingsArray['paymentTax']*$toReturn['invoices'][$key]->paymentAmount) /100;
				if($value->paymentStatus == 1){
					$paymentStatus = "PAID";
				}elseif($value->paymentStatus == 2){
					$paymentStatus = "PARTIALLY PAID";
				}else{
					$paymentStatus = "UNPAID";
				}
				$data[] = array($value->paymentTitle,$value->paymentDescription,$value->fullName,$return['currency_symbol']." ".$value->paymentAmount,$return['currency_symbol']." ".$value->paidAmount,$value->paymentDate,$value->dueDate,$paymentStatus);
			}

			\Excel::create('Payments-Sheet', function($excel) use($data) {

			    // Set the title
			    $excel->setTitle('Payments Sheet');

			    // Chain the setters
			    $excel->setCreator('Schoex')->setCompany('SolutionsBricks');

				$excel->sheet('Payments', function($sheet) use($data) {
					$sheet->freezeFirstRow();
					$sheet->fromArray($data, null, 'A1', true,false);
				});

			})->download('xls');

		}elseif ($type == "pdf") {
			$return['currency_symbol'] = $this->panelInit->settingsArray['currency_symbol'];

			$header = array ('Invoice ID','Title','Student','Amount','Paid Amount','Date','Due Date','Status');
			$data = array();

			$toReturn['invoices'] = \DB::table('fee_collection')
						->leftJoin('users', 'users.id', '=', 'fee_collection.paymentStudent')
						->select('fee_collection.id as id',
						'fee_collection.paymentTitle as paymentTitle',
						'fee_collection.paymentDescription as paymentDescription',
						'fee_collection.paymentAmount as paymentAmount',
						'fee_collection.paidAmount as paidAmount',
						'fee_collection.paymentStatus as paymentStatus',
						'fee_collection.paymentDate as paymentDate',
						'fee_collection.dueDate as dueDate',
						'fee_collection.paymentStudent as studentId',
						'users.fullName as fullName');
			$toReturn['totalItems'] = $toReturn['invoices']->count();
			$toReturn['invoices'] = $toReturn['invoices']->orderBy('id','DESC')->limit('100')->get();

			foreach ($toReturn['invoices'] as $key => $value) {
				$value->paymentDate = $this->panelInit->unix_to_date($toReturn['invoices'][$key]->paymentDate);
				$value->dueDate = $this->panelInit->unix_to_date($toReturn['invoices'][$key]->dueDate);
				$value->paymentAmount = $toReturn['invoices'][$key]->paymentAmount + ($this->panelInit->settingsArray['paymentTax']*$toReturn['invoices'][$key]->paymentAmount) /100;
				if($value->paymentStatus == 1){
					$paymentStatus = "PAID";
				}elseif($value->paymentStatus == 2){
					$paymentStatus = "PARTIALLY PAID";
				}else{
					$paymentStatus = "UNPAID";
				}
				$data[] = array($value->paymentTitle,$value->paymentDescription,$value->fullName,$return['currency_symbol']." ".$value->paymentAmount,$return['currency_symbol']." ".$value->paidAmount,$value->paymentDate,$value->dueDate,$paymentStatus);
			}

			$doc_details = array(
								"title" => "Payments",
								"author" => $this->data['panelInit']->settingsArray['siteTitle'],
								"topMarginValue" => 10
								);

			$pdfbuilder = new \PdfBuilder($doc_details);

			$content = "<table cellspacing=\"0\" cellpadding=\"4\" border=\"1\">
		        <thead><tr>";
				foreach ($header as $value) {
					$content .="<th style='width:15%;border: solid 1px #000000; padding:2px;'>".$value."</th>";
				}
			$content .="</tr></thead><tbody>";

			foreach($data as $row)
			{
				$content .= "<tr>";
				foreach($row as $col){
					$content .="<td>".$col."</td>";
				}
				$content .= "</tr>";
			}

	        $content .= "</tbody></table>";

			$pdfbuilder->table($content, array('border' => '0','align'=>'') );
			$pdfbuilder->output('Payments.pdf');

		}
	}

	public static function generateInvoice($user,$case){
		if($user->studentClass == "" || $user->studentClass == "0"){
			return;
		}

		$feeAllocationUser = \fee_allocation::where('allocationType','student')->where('allocationWhen',$case)->where('allocationId',$user->id)->get()->toArray();
		$feeAllocationClass = \fee_allocation::where('allocationType','class')->where('allocationWhen',$case)->where('allocationId',$user->studentClass)->get()->toArray();

		$feeTypesArray = array();
		$feeTypes = \fee_type::get();
		foreach($feeTypes as $type){
			$feeTypesArray[$type->id] = $type->feeTitle;
		}

		if(count($feeAllocationUser) > 0){
			foreach ($feeAllocationUser as $allocatedUser) {

				$paymentDescription = array();
				$paymentAmount = 0;
				$allocationValues = json_decode($allocatedUser->allocationValues,true);
				while (list($key, $value) = each($allocationValues)) {
					if(isset($feeTypesArray[$key])){
						$paymentDescription[] = $feeTypesArray[$key];
						$paymentAmount += $value;
					}
				}

				$payments = new \fee_collection();
				$payments->paymentTitle = $allocatedUser->allocationTitle;
				$payments->paymentDescription = implode(", ",$paymentDescription);
				$payments->paymentStudent = $user->id;
				$payments->paymentAmount = $paymentAmount;
				$payments->paymentStatus = "0";
				$payments->paymentDate = time();
				$payments->paymentUniqid = uniqid();
				$payments->save();

			}
		}

		if(count($feeAllocationClass) > 0){
			foreach ($feeAllocationClass as $allocatedUser) {

				$paymentDescription = array();
				$paymentAmount = 0;
				$allocationValues = json_decode($allocatedUser['allocationValues'],true);
				while (list($key, $value) = each($allocationValues)) {
					if(isset($feeTypesArray[$key])){
						$paymentDescription[] = $feeTypesArray[$key];
						$paymentAmount += $value;
					}
				}

				$payments = new \fee_collection();
				$payments->paymentTitle = $allocatedUser['allocationTitle'];
				$payments->paymentDescription = implode(", ",$paymentDescription);
				$payments->paymentStudent = $user->id;
				$payments->paymentAmount = $paymentAmount;
				$payments->paymentStatus = "0";
				$payments->paymentDate = time();
				$payments->paymentUniqid = uniqid();
				$payments->save();

			}
		}

	}










	function getFeeGroups($frequency = "",$classId = ""){
		$toReturn = array();
		$toReturn['fee_groups'] = \fee_group_master::where(array('collection_frequency'=>$frequency,'class_id'=>$classId))->get();
		return $toReturn;
	}

	function getFeeParticulars($feegroupId = ""){
		$toReturn = array();
		$toReturn['fee_particulars'] = \feeheads::where(array('fee_group_id'=>$feegroupId,'status'=>1))->get();
		$totalAmount = 0;
		if(!empty($toReturn['fee_particulars'])){
			foreach($toReturn['fee_particulars'] as $key => $value){
				$totalAmount += $toReturn['fee_particulars'][$key]->amount;
			}
			$toReturn['grandTotal'] = $totalAmount;
		}
		return $toReturn;
	}
	function getLastInvoicesDetails($studentId){
		$school_id = $this->panelInit->authUser['school_id'];
		$toReturn = array();
		$query = "SELECT DISTINCT
		fee_collection.id as invoiceId,
		fee_collection.created_date as paymentDate,
		users.fullName as fullName,
		fee_collection.paymentStatus as paymentStatus,
		users.id as studentId,
		fee_collection.dueDate as dueDate,
		fee_collection.paymentStatus as paymentStatus,

		fee_collection.fee_invoice_date as fee_invoice_date,
		fee_collection.fee_invoice_end_date as fee_invoice_end_date,
		
		(
		SELECT sum(fee_collection_particulars.amount) + fee_collection.fineAmount - fee_collection.discountAmount FROM fee_collection_particulars
			LEFT JOIN fee_collection ON fee_collection.id = fee_collection_particulars.fee_collection_id 
			WHERE fee_collection_particulars.fee_collection_id = invoiceId) AS  paymentAmount,
		
		(
		SELECT sum(fee_payment_collection_status.collectionAmount)  FROM fee_payment_collection_status
		WHERE fee_payment_collection_status.fee_collection_id = invoiceId AND fee_payment_collection_status.status = 1
		) AS paidAmount
		
		FROM fee_collection

		LEFT JOIN users
		ON users.id = fee_collection.paymentStudent			
		
		LEFT JOIN fee_collection_particulars
		ON fee_collection_particulars.fee_collection_id = fee_collection.id
		
		LEFT JOIN fee_payment_collection_status
		ON fee_payment_collection_status.fee_collection_id = fee_collection.id
		WHERE fee_collection.school_id = $school_id
		AND fee_collection.paymentStudent = $studentId
		AND fee_collection.fee_group_id != 0
		GROUP BY fee_collection.id
		ORDER BY fee_collection.created_date DESC LIMIT 0, 5";

		$query_count = $query;

		$toReturn['getLastInvoicesDeatails'] = \DB::select(\DB::raw($query));


		foreach ($toReturn['getLastInvoicesDeatails'] as $key => $value) {
			$toReturn['getLastInvoicesDeatails'][$key]->pendingCheques = 0;
			$toReturn['getLastInvoicesDeatails'][$key]->paymentDate = (strtotime($toReturn['getLastInvoicesDeatails'][$key]->paymentDate)>0) ? date('Y-m-d',strtotime($toReturn['getLastInvoicesDeatails'][$key]->paymentDate)) : 'N/A';
			$toReturn['getLastInvoicesDeatails'][$key]->invoiceId = $toReturn['getLastInvoicesDeatails'][$key]->invoiceId;
			$toReturn['getLastInvoicesDeatails'][$key]->fee_invoice_date = (strtotime($toReturn['getLastInvoicesDeatails'][$key]->fee_invoice_date)>0) ? date('d-m-Y',strtotime($toReturn['getLastInvoicesDeatails'][$key]->fee_invoice_date)): 'N/A';
			$toReturn['getLastInvoicesDeatails'][$key]->fee_invoice_end_date = (strtotime($toReturn['getLastInvoicesDeatails'][$key]->fee_invoice_end_date)>0) ? date('d-m-Y',strtotime($toReturn['getLastInvoicesDeatails'][$key]->fee_invoice_end_date)): 'N/A';
			$toReturn['getLastInvoicesDeatails'][$key]->dueDate = (strtotime($toReturn['getLastInvoicesDeatails'][$key]->dueDate) > 0) ? date('d-m-Y',strtotime($toReturn['getLastInvoicesDeatails'][$key]->dueDate)): 'N/A';
			$toReturn['getLastInvoicesDeatails'][$key]->paymentAmount = $toReturn['getLastInvoicesDeatails'][$key]->paymentAmount;
			$toReturn['getLastInvoicesDeatails'][$key]->paidAmount = $toReturn['getLastInvoicesDeatails'][$key]->paidAmount;




			// $paymentStatus = 0;
			// if($toReturn['getLastInvoicesDeatails'][$key]->paymentAmount == $toReturn['getLastInvoicesDeatails'][$key]->paidAmount){
			// 	$paymentStatus = 1;
			// }else if($toReturn['getLastInvoicesDeatails'][$key]->paidAmount != '' && $toReturn['getLastInvoicesDeatails'][$key]->paymentAmount > $toReturn['getLastInvoicesDeatails'][$key]->paidAmount){
			// 	$paymentStatus = 2;
			// }else{
			// 	$paymentStatus = 0;
			// }

			// $toReturn['getLastInvoicesDeatails'][$key]->paymentStatus = $paymentStatus;
			// $update_fee_collection = \fee_collection::find($toReturn['getLastInvoicesDeatails'][$key]->invoiceId);
			// $update_fee_collection->paymentStatus = $paymentStatus;
			// $update_fee_collection->save();

			// $pendingCheques = 0;
			// $pendingCheques = count(\fee_payment_collection_status::where(
			// array('fee_collection_id'=>$toReturn['getLastInvoicesDeatails'][$key]->invoiceId, 'status'=> 0))->get()->toArray());

			// $toReturn['getLastInvoicesDeatails'][$key]->pendingCheques = $pendingCheques;

		}

		return $toReturn;
	}

	function getStudentPaidAndDueFeeDetail(){
		$toReturn = array();
		// $collectFrequecncy = \Input::get('collectFrequecncy');
		// if($collectFrequecncy == 'Monthly'){
		// 	$frequencyVal = \Input::get('frequencyVal');
		// 	$studentId = \Input::get('studentId');


		// }else if($collectFrequecncy == 'Quarterly'){
		// 	$frequencyVal = \Input::get('frequencyVal');
		// }else if($collectFrequecncy == 'Biannually'){
		// 	$frequencyVal = \Input::get('frequencyVal');
		// }else if($collectFrequecncy == 'Annually'){
		// 	$frequencyVal = \Input::get('frequencyVal');
		// }
		
		//$toReturn['studentId'] = $studentId;
		return $toReturn;
	}



	public function listAll($page = 1){
		$school_id = $this->panelInit->authUser['school_id'];
		$toReturn = array();
		if(empty(\Input::has('searchInput'))  && ($this->data['users']->role == "admin" || $this->data['users']->role == "account")){
			$offset = (20* ($page - 1));
			$query = "SELECT DISTINCT
								fee_collection.id as invoiceId,
								fee_collection.created_date as paymentDate,
								users.fullName as fullName,
								fee_collection.paymentStatus as paymentStatus,
								users.id as studentId,
								fee_collection.dueDate as dueDate,
								fee_collection.paymentStatus as paymentStatus,

								fee_collection.fee_invoice_date as fee_invoice_date,
								fee_collection.fee_invoice_end_date as fee_invoice_end_date,
								
								(
								SELECT sum(fee_collection_particulars.amount) + fee_collection.fineAmount - fee_collection.discountAmount FROM fee_collection_particulars
								LEFT JOIN fee_collection ON fee_collection.id = fee_collection_particulars.fee_collection_id 
								WHERE fee_collection_particulars.fee_collection_id = invoiceId) AS  paymentAmount,
								
								(
								SELECT sum(fee_payment_collection_status.collectionAmount)  FROM fee_payment_collection_status
								WHERE fee_payment_collection_status.fee_collection_id = invoiceId AND fee_payment_collection_status.status = 1
								) AS paidAmount,

								(
								SELECT sum(fee_installments.installment_amount)  FROM fee_installments
								WHERE fee_installments.fee_invoice_id = invoiceId AND fee_installments.paid_status = 1
								) AS paidInstllments

								FROM fee_collection

								LEFT JOIN users
								ON users.id = fee_collection.paymentStudent			
								
								LEFT JOIN fee_collection_particulars
								ON fee_collection_particulars.fee_collection_id = fee_collection.id
								
								LEFT JOIN fee_payment_collection_status
								ON fee_payment_collection_status.fee_collection_id = fee_collection.id
								WHERE fee_collection.school_id = $school_id
								AND fee_collection.fee_group_id != 0
								GROUP BY fee_collection.id
								ORDER BY fee_collection.created_date DESC";


								$query_count = $query;

								$query .= " LIMIT $offset , 20";


								$toReturn['fee_invoices'] = \DB::select(\DB::raw($query));
								$count_fee_invoices = \DB::select(\DB::raw($query_count));
			
		
		}elseif($this->data['users']->role == "student"){
			$offset = (20* ($page - 1));
				$student_id = $this->data['users']->id;
				$query = "SELECT DISTINCT
				fee_collection.id as invoiceId,
				fee_collection.created_date as paymentDate,
				users.fullName as fullName,
				fee_collection.paymentStatus as paymentStatus,
				users.id as studentId,
				fee_collection.dueDate as dueDate,
				fee_collection.paymentStatus as paymentStatus,

				fee_collection.fee_invoice_date as fee_invoice_date,
				fee_collection.fee_invoice_end_date as fee_invoice_end_date,
				
				(
				SELECT sum(fee_collection_particulars.amount) + fee_collection.fineAmount - fee_collection.discountAmount FROM fee_collection_particulars
					LEFT JOIN fee_collection ON fee_collection.id = fee_collection_particulars.fee_collection_id 
					WHERE fee_collection_particulars.fee_collection_id = invoiceId) AS  paymentAmount,
				
				(
				SELECT sum(fee_payment_collection_status.collectionAmount)  FROM fee_payment_collection_status
				WHERE fee_payment_collection_status.fee_collection_id = invoiceId AND fee_payment_collection_status.status = 1) AS paidAmount
				
				FROM fee_collection

				LEFT JOIN users
				ON users.id = fee_collection.paymentStudent			
				
				LEFT JOIN fee_collection_particulars
				ON fee_collection_particulars.fee_collection_id = fee_collection.id
				
				LEFT JOIN fee_payment_collection_status
				ON fee_payment_collection_status.fee_collection_id = fee_collection.id
				WHERE fee_collection.paymentStudent = $student_id
				AND fee_collection.fee_group_id != 0
				GROUP BY fee_collection.id
				ORDER BY fee_collection.created_date DESC";
				

				$query_count = $query;

				$query .= " LIMIT $offset , 20";

				$toReturn['fee_invoices'] = \DB::select(\DB::raw($query));
				$count_fee_invoices = \DB::select(\DB::raw($query_count));	



		}elseif($this->data['users']->role == "parent"){

			$offset = (20* ($page - 1));
			$studentId = array();
			$parentOf = json_decode($this->data['users']->parentOf,true);
			if(is_array($parentOf)){
				while (list($key, $value) = each($parentOf)) {
					$studentId[] = $value['id'];
				}
			}
			


			$query = "SELECT DISTINCT
				fee_collection.id as invoiceId,
				fee_collection.created_date as paymentDate,
				users.fullName as fullName,
				fee_collection.paymentStatus as paymentStatus,
				users.id as studentId,
				fee_collection.dueDate as dueDate,
				fee_collection.paymentStatus as paymentStatus,

				fee_collection.fee_invoice_date as fee_invoice_date,
				fee_collection.fee_invoice_end_date as fee_invoice_end_date,
				
				(
				SELECT sum(fee_collection_particulars.amount) + fee_collection.fineAmount - fee_collection.discountAmount FROM fee_collection_particulars
					LEFT JOIN fee_collection ON fee_collection.id = fee_collection_particulars.fee_collection_id 
					WHERE fee_collection_particulars.fee_collection_id = invoiceId) AS  paymentAmount,
				
				(
				SELECT sum(fee_payment_collection_status.collectionAmount)  FROM fee_payment_collection_status
				WHERE fee_payment_collection_status.fee_collection_id = invoiceId AND fee_payment_collection_status.status = 1) AS paidAmount
				
				FROM fee_collection

				LEFT JOIN users
				ON users.id = fee_collection.paymentStudent			
				
				LEFT JOIN fee_collection_particulars
				ON fee_collection_particulars.fee_collection_id = fee_collection.id
				
				LEFT JOIN fee_payment_collection_status
				ON fee_payment_collection_status.fee_collection_id = fee_collection.id
				WHERE fee_collection.paymentStudent IN ('".implode(',',$studentId)."')

				AND fee_collection.fee_group_id != 0
				GROUP BY fee_collection.id
				ORDER BY fee_collection.created_date DESC";
			
				$query_count = $query;

				$query .= " LIMIT $offset , 20";


				$toReturn['fee_invoices'] = \DB::select(\DB::raw($query));
				$count_fee_invoices = \DB::select(\DB::raw($query_count));
		}



		if(\Input::has('searchInput')){

			$offset = (20* ($page - 1));



			$searchInput = \Input::get('searchInput');
			if(!empty($searchInput['fromDate'])){
				$fromDate = date('Y-m-d',strtotime(str_replace('/','-',$searchInput['fromDate'])));
			}if(!empty($searchInput['toDate'])){
				$toDate = date('Y-m-d',strtotime(str_replace('/','-',$searchInput['toDate'])));
			}
			if(!empty($searchInput['paymentStatus']) || $searchInput['paymentStatus'] == 0 ){
				$paymentStatus = $searchInput['paymentStatus'];
			}if(!empty($searchInput['classId'])){
				$class_id = $searchInput['classId'];
			}if(!empty($searchInput['searchStudent'])){
				$searchStudent = $searchInput['searchStudent'];
			}
			



			$query = "SELECT DISTINCT
			fee_collection.id as invoiceId,
			fee_collection.created_date as paymentDate,
			users.fullName as fullName,
			fee_collection.paymentStatus as paymentStatus,
			users.id as studentId,
			fee_collection.dueDate as dueDate,
			fee_collection.paymentStatus as paymentStatus,

			fee_collection.fee_invoice_date as fee_invoice_date,
			fee_collection.fee_invoice_end_date as fee_invoice_end_date,

			(
			SELECT sum(fee_collection_particulars.amount) + fee_collection.fineAmount - fee_collection.discountAmount FROM fee_collection_particulars
			LEFT JOIN fee_collection ON fee_collection.id = fee_collection_particulars.fee_collection_id 
			WHERE fee_collection_particulars.fee_collection_id = invoiceId) AS  paymentAmount,

			(
			SELECT sum(fee_payment_collection_status.collectionAmount)  FROM fee_payment_collection_status
			WHERE fee_payment_collection_status.fee_collection_id = invoiceId AND fee_payment_collection_status.status = 1
			) AS paidAmount
			
			FROM fee_collection

			LEFT JOIN users
			ON users.id = fee_collection.paymentStudent			
			
			LEFT JOIN fee_collection_particulars
			ON fee_collection_particulars.fee_collection_id = fee_collection.id
			
			LEFT JOIN fee_payment_collection_status
			ON fee_payment_collection_status.fee_collection_id = fee_collection.id";
				
			
		
	
			$query .= " WHERE fee_collection.school_id = $school_id";
			
			if(isset($fromDate)){
				$query .= " AND DATE(fee_collection.created_date) >=  '".$fromDate."'";
			}
			if(isset($toDate)){
				$query .= " AND DATE(fee_collection.created_date) <= '".$toDate."'";
			}
			if(isset($paymentStatus)){
				$query .= " AND fee_collection.paymentStatus = 0";
			}
		
			if(isset($class_id)){
				$query .= " AND fee_collection.class_id = $class_id";
			}
			
			if(isset($searchStudent)){
				$query .= " AND users.fullName LIKE '%$searchStudent%'";
			}

			$query .= " AND fee_collection.fee_group_id != 0
			GROUP BY fee_collection.id
			ORDER BY fee_collection.created_date DESC";

			$query_count = $query;

			$query .= " LIMIT $offset , 20";
			

			$toReturn['fee_invoices'] = \DB::select(\DB::raw($query));
			$count_fee_invoices = \DB::select(\DB::raw($query_count));

		}


		$totalPaymentAmount = 0;
		$totalPaidAmount = 0;
		$toReturn['balance_amount'] = 0;
		foreach ($toReturn['fee_invoices'] as $key => $value) {
			$toReturn['fee_invoices'][$key]->pendingCheques = 0;
			$toReturn['fee_invoices'][$key]->paymentDate = (strtotime($toReturn['fee_invoices'][$key]->paymentDate)>0) ? date('Y-m-d',strtotime($toReturn['fee_invoices'][$key]->paymentDate)) : 'N/A';
			$toReturn['fee_invoices'][$key]->invoiceId = $toReturn['fee_invoices'][$key]->invoiceId;

			// $toReturn['fee_invoices'][$key]->fee_invoice_date = (strtotime($toReturn['fee_invoices'][$key]->fee_invoice_date)>0) ? date('d-M-Y',strtotime($toReturn['fee_invoices'][$key]->fee_invoice_date)): 'N/A';
			// $toReturn['fee_invoices'][$key]->fee_invoice_end_date = (strtotime($toReturn['fee_invoices'][$key]->fee_invoice_end_date)>0) ? date('d-M-Y',strtotime($toReturn['fee_invoices'][$key]->fee_invoice_end_date)): 'N/A';


			$start_month = date('F', strtotime($toReturn['fee_invoices'][$key]->fee_invoice_date));
			$end_month = date('F', strtotime($toReturn['fee_invoices'][$key]->fee_invoice_end_date));
			if($start_month == $end_month){
				$toReturn['fee_invoices'][$key]->fee_duration = date('M-Y', strtotime($toReturn['fee_invoices'][$key]->fee_invoice_date));
			}else{
				$toReturn['fee_invoices'][$key]->fee_duration = date('M Y', strtotime($toReturn['fee_invoices'][$key]->fee_invoice_date))." - ".date('M Y', strtotime($toReturn['fee_invoices'][$key]->fee_invoice_end_date));
			}

			



			$toReturn['fee_invoices'][$key]->dueDate = (strtotime($toReturn['fee_invoices'][$key]->dueDate) > 0) ? date('d-m-Y',strtotime($toReturn['fee_invoices'][$key]->dueDate)): 'N/A';
			/*$toReturn['fee_invoices'][$key]->paymentAmount = $toReturn['fee_invoices'][$key]->paymentAmount + ($this->panelInit->settingsArray['paymentTax']*$toReturn['invoices'][$key]->paymentAmount) /100;*/
			$toReturn['fee_invoices'][$key]->paymentAmount = $toReturn['fee_invoices'][$key]->paymentAmount;
			$toReturn['fee_invoices'][$key]->paidAmount = $toReturn['fee_invoices'][$key]->paidAmount;


			//$toReturn['fee_invoices'][$key]->allInstallments
			


			$paymentStatus = 0;
			if($toReturn['fee_invoices'][$key]->paymentAmount == $toReturn['fee_invoices'][$key]->paidAmount){
				$paymentStatus = 1;
			}else if($toReturn['fee_invoices'][$key]->paidAmount != '' && $toReturn['fee_invoices'][$key]->paymentAmount > $toReturn['fee_invoices'][$key]->paidAmount){
				$paymentStatus = 2;
			}else{
				$paymentStatus = 0;
			}

			/* check installments */
			$allinstallments = \fee_installments::where(
				array('fee_invoice_id'=>$toReturn['fee_invoices'][$key]->invoiceId))->get()->toArray();
			// $totalInstallmentAmount = 0;
			// if(!empty($allinstallments)){
			// 	foreach($allinstallments as $key4 => $value4){
			// 		if($allinstallments[$key4]['paid_status'] == 0){
			// 			$totalInstallmentAmount += $allinstallments[$key4]['installment_amount'];
			// 		}
			// 	}
	
			// 	if($totalInstallmentAmount ==  $toReturn['fee_invoices'][$key]->paidAmount){
			// 		$paymentStatus = 1;
			// 	}else if($totalInstallmentAmount <  $toReturn['fee_invoices'][$key]->paidAmount){
			// 		$paymentStatus = 2;
			// 	}else{
			// 		$paymentStatus = 0;
			// 	}
			// }
			

			$toReturn['fee_invoices'][$key]->paymentStatus = $paymentStatus;
			$update_fee_collection = \fee_collection::find($toReturn['fee_invoices'][$key]->invoiceId);
			$update_fee_collection->paymentStatus = $paymentStatus;
			$update_fee_collection->save();


			$pendingCheques = 0;
			$pendingCheques = count(\fee_payment_collection_status::where(
				array('fee_collection_id'=>$toReturn['fee_invoices'][$key]->invoiceId, 'status'=> 0))->get()->toArray());
			$toReturn['fee_invoices'][$key]->pendingCheques = $pendingCheques;

			/* installments details */
			$installMents = 0;
			$toReturn['fee_invoices'][$key]->intallments = '';
			

			$toReturn['fee_invoices'][$key]->countInstallments = 0;
			$paidInstallmentAmount = 0;
			$installmentStatus = "";
			if(!empty($allinstallments)){
				$toReturn['fee_invoices'][$key]->countInstallments = count($allinstallments); 
				$index = 1;
				foreach($allinstallments as $key3 => $value3){
					$allinstallments[$key3]['intallmentId'] = $allinstallments[$key3]['id']; 
					$allinstallments[$key3]['installment_amount'] = $allinstallments[$key3]['installment_amount']; 
					
					$allinstallments[$key3]['paid_status'] = ($allinstallments[$key3]['paid_status'] == 1) ? 'Paid' : 'Due';
					$allinstallments[$key3]['due_date'] = ($allinstallments[$key3]['paid_status'] == 'Due') ? date('d-m-Y',strtotime($allinstallments[$key3]['due_date'])) : "";
					$installmentStatus .= $index.". ";
					if($allinstallments[$key3]['paid_status'] == 'Due'){

						$installmentStatus .= "<span style='color:red;'>";
					}else{
						$installmentStatus .= "<span style='color:green;'>";
						$paidInstallmentAmount += $allinstallments[$key3]['installment_amount']; 
						
					}
					$installmentStatus .= $allinstallments[$key3]['installment_amount'];
					if($allinstallments[$key3]['paid_status'] == 'Due'){
						$installmentStatus .= " ".$allinstallments[$key3]['paid_status']." On ";
						$toReturn['fee_invoices'][$key]->paymentStatus = '4';
					}else{
						$installmentStatus .= " ".$allinstallments[$key3]['paid_status'];
					}
					
					$installmentStatus .= $allinstallments[$key3]['due_date']."<br>";
					$installmentStatus .= "</span>";

					$index++;
				}
				$toReturn['fee_invoices'][$key]->intallments = $installmentStatus;
				
			}	
			$installMents = count($allinstallments);
			$toReturn['fee_invoices'][$key]->intallments = $installmentStatus;	


			//$toReturn['fee_invoices'][$key]->paidAmount = $paidInstallmentAmount + $toReturn['fee_invoices'][$key]->paidAmount;
			//$totalPaidAmount += $paidInstallmentAmount;
		}
		
		foreach ($count_fee_invoices as $key1 => $value1) {
			$totalPaymentAmount += $count_fee_invoices[$key1]->paymentAmount;
			$totalPaidAmount += $count_fee_invoices[$key1]->paidAmount;
		}

		$toReturn['total_paid_amount'] = (!empty($totalPaidAmount))?$totalPaidAmount:0;
		$toReturn['total_payment_amount'] = (!empty($totalPaymentAmount))?$totalPaymentAmount:0;
		$toReturn['balance_amount'] = $totalPaymentAmount - $totalPaidAmount;
		$toReturn['totalItems'] = count($count_fee_invoices);
		$toReturn['currency_symbol'] = $this->panelInit->settingsArray['currency_symbol'];

		$classes = \classes::where('school_id',$this->panelInit->authUser['school_id'])->get();
		$toReturn['classes'] = array();
		foreach ($classes as $key => $value) {
			$toReturn['classes'][$key]['className'] = $classes[$key]->className ;
			$toReturn['classes'][$key]['id'] = $classes[$key]->id;
		}
		// echo "<pre>";
		// print_r($toReturn);
		// exit;

		return $toReturn;
	}


	public function viewPendingCheques($invoiceId){
		$toReturn = array();
		$pendingChequeDetails = \fee_payment_collection_status::where(
			array('fee_collection_id'=>$invoiceId, 'status'=> 0))->get();
			foreach($pendingChequeDetails as $key => $value){

				if($pendingChequeDetails[$key]->collectionType != 0){
					$groupPayCheque = \fee_cheque_collection_for_multiple_invoice::where(
							array('id'=>$pendingChequeDetails[$key]->collectionType))->first()->toArray();
					$toReturn['pendingCheques'][$key]['id'] = $pendingChequeDetails[$key]->id;
					$toReturn['pendingCheques'][$key]['cheque_number'] = $groupPayCheque['cheque_number'];
					$toReturn['pendingCheques'][$key]['collectionAmount'] = $groupPayCheque['collection_amount'];
					$toReturn['pendingCheques'][$key]['bank_name'] = $groupPayCheque['bank_name'];
					$toReturn['pendingCheques'][$key]['branch_name'] = $groupPayCheque['bank_branch'];
					$toReturn['pendingCheques'][$key]['cheque_validity'] = date('d/m/Y',strtotime($groupPayCheque['cheque_validity']));	
				}else{
					$toReturn['pendingCheques'][$key]['id'] = $pendingChequeDetails[$key]->id;
					$toReturn['pendingCheques'][$key]['cheque_number'] = $pendingChequeDetails[$key]->cheque_number;
					$toReturn['pendingCheques'][$key]['collectionAmount'] = $pendingChequeDetails[$key]->collectionAmount;
					$toReturn['pendingCheques'][$key]['bank_name'] = $pendingChequeDetails[$key]->bank_name;
					$toReturn['pendingCheques'][$key]['branch_name'] = $pendingChequeDetails[$key]->branch_name;
					$toReturn['pendingCheques'][$key]['cheque_validity'] = date('d/m/Y',strtotime($pendingChequeDetails[$key]->cheque_validity));
				}
	
			}
		return $toReturn;
	}
	public function changeChequeStatus($id ="",$status = ""){
		$toReturn = array();
		if($id != '' && $status != ''){
			$fee_payment_collection_status = \fee_payment_collection_status::find($id);
			if($status == 'bounced'){
				$fee_payment_collection_status->status = 2;
			}else{
				$fee_payment_collection_status->status = 1;
			}
			$fee_payment_collection_status->save();
		}

		$isGroupPayment = \fee_payment_collection_status::where(array('id'=>$id))->where('collectionType','!=',0)->value('collectionType');
		if(!empty($isGroupPayment)){
			$fee_cheque_collection_for_multiple_invoice = \fee_cheque_collection_for_multiple_invoice::find($isGroupPayment);
			if($status == 'bounced'){
				$fee_cheque_collection_for_multiple_invoice->status = 2;
			}else{
				$fee_cheque_collection_for_multiple_invoice->status = 1;
			}
			$fee_cheque_collection_for_multiple_invoice->save();
		}
	
		return $this->panelInit->apiOutput(true,"Success","Cheque status changed sucessfully",$toReturn);
	}


	public function old_invoices($page = 1){

		$toReturn = array();

		/*------------------------------- Old Invoices -------------------------------*/
		$toReturn['old_invoices'] = \DB::table('fee_collection')
						->where('fee_collection.school_id',$this->panelInit->authUser['school_id'])
						->where('fee_collection.fee_group_id','=',0)
						->leftJoin('users', 'users.id', '=', 'fee_collection.paymentStudent')
						->select('fee_collection.id as invoiceId',
						'fee_collection.paymentRows as paymentRows',
						'fee_collection.paymentTitle as paymentTitle',
						'fee_collection.paymentDescription as paymentDescription',
						'fee_collection.paymentAmount as paymentAmount',
						'fee_collection.paidAmount as paidAmount',
						'fee_collection.paymentStatus as paymentStatus',
						'fee_collection.paymentDate as paymentDate',
						'fee_collection.dueDate as dueDate',
						'fee_collection.paymentStudent as studentId',
						'users.fullName as fullName');

		

		$toReturn['totalItems'] = $toReturn['old_invoices']->count();
		$toReturn['old_invoices'] = $toReturn['old_invoices']->orderBy('invoiceId','DESC')->get();

		foreach ($toReturn['old_invoices'] as $key => $value) {
			$toReturn['old_invoices'][$key]->paymentDate = $this->panelInit->unix_to_date($toReturn['old_invoices'][$key]->paymentDate);
			$toReturn['old_invoices'][$key]->dueDate = $this->panelInit->unix_to_date($toReturn['old_invoices'][$key]->dueDate);
			// $toReturn['invoices'][$key]->paymentAmount = $toReturn['invoices'][$key]->paymentAmount + ($this->panelInit->settingsArray['paymentTax']*$toReturn['invoices'][$key]->paymentAmount) /100;
			$toReturn['old_invoices'][$key]->paymentAmount = $toReturn['old_invoices'][$key]->paymentAmount;
			$toReturn['old_invoices'][$key]->paidAmount = $toReturn['old_invoices'][$key]->paidAmount;


			$feeheads = "";
			$allParticulars = array();
			if(!empty($toReturn['old_invoices'][$key]->paymentRows)){
				$allParticulars = json_decode($toReturn['old_invoices'][$key]->paymentRows);
				foreach($allParticulars as $key1 => $value1){
					$feeheads .="".$allParticulars[$key1]->title.": ".$allParticulars[$key1]->amount."\n";
				}
			}
			$toReturn['old_invoices'][$key]->feeHeads = $feeheads;
		}
		

		return $toReturn;


	}



	public function create(){
		$toReturn = array();
		
		$studentType = \Input::get('selectUser');
		if($studentType == 'student'){
			$studentId = \Input::get('studentId');
		}else{
			$allStudentId = \Input::get('finalselectedUsers');
			//$invoiceType = \Input::get('invoiceType');
		}
		$feeCreation = array();
		if(!empty($allStudentId)){
			foreach($allStudentId as $key1 => $value1){
				$feeCreation = $this->createFee($allStudentId[$key1]['id']);
			}
		}else{
			$feeCreation = $this->createFee($studentId);
		}
		//if($fee_collection == true){
			return $this->panelInit->apiOutput(true,"Invoice Generated","Invoice generated successfully",$toReturn);
		//}
		
	}

	


	function createFee($studentId = ""){
		$toReturn = array();
		$toReturn['studentId'] = $studentId; 
		$school_id = $this->panelInit->authUser['school_id'];
		$fee_group = \Input::get('fee_group');
		$yearofcollection = \Input::get('yearofcollection');


		$fee_installment = \Input::get('fee_installment');
		//$paymentStatus = \Input::get('paymentStatus');
		if(!empty($fee_installment)){
			$paymentStatus = 0;
		}else{
			$paymentStatus = \Input::get('paymentStatus');
		}
		


		$class_id = \Input::get('classId');
		$DiscountAmount = \Input::get('DiscountAmount');
		$fineAmount = \Input::get('fineAmount');
		

		$collectFrequecncy = \Input::get('collectFrequecncy');
		if(!empty($collectFrequecncy) && $collectFrequecncy == "Monthly"){
			
			$frequencyVal = \Input::get('frequencyVal');
			$frequencyVal = ($frequencyVal < 10) ? "0".$frequencyVal : $frequencyVal;
			$start_date= date($yearofcollection.'-'.$frequencyVal.'-01');
			$days_in_month = cal_days_in_month(CAL_GREGORIAN, $frequencyVal, $yearofcollection); 
			$end_date= date($yearofcollection.'-'.$frequencyVal.'-'.$days_in_month);
		
		}else if(!empty($collectFrequecncy) && ($collectFrequecncy == "Quarterly" or $collectFrequecncy == "Biannually" )){
			$frequencyVal = \Input::get('frequencyVal');
			$monthVal = array();
			$monthVal = str_split($frequencyVal);
			$start_month = ($monthVal[0] < 10) ? "0".$monthVal[0] : $monthVal[0];
			$end_month = ($monthVal[2] < 10) ? "0".$monthVal[2] : $monthVal[2];
			$start_date= date($yearofcollection.'-'.$start_month.'-01');
			$days_in_month = cal_days_in_month(CAL_GREGORIAN, $end_month, $yearofcollection); 
			$end_date= date($yearofcollection.'-'.$end_month.'-'.$days_in_month);
		}else{
			$frequencyVal = \Input::get('frequencyVal');
			if(!empty($frequencyVal) && $frequencyVal > 1){
				$start_month = ($frequencyVal < 10) ? "0".$frequencyVal : $frequencyVal;
				$end_month = $frequencyVal - 1;
				$end_month = ($end_month < 10) ? "0".$end_month : $end_month;
				$days_in_month = cal_days_in_month(CAL_GREGORIAN, $end_month, $yearofcollection);
				$start_date= date($yearofcollection.'-'.$start_month.'-01');
				$end_date= date(++$yearofcollection.'-'.$end_month.'-'.$days_in_month);
			}else{
				$start_date= date($yearofcollection.'-01-01');
				$end_date= date($yearofcollection.'-12-31');
			}
		}

		
		// $checkAlreadyPayment = \DB::table('fee_collection')
		// 				->where(array('school_id'=>$school_id,'paymentStudent'=>$studentId,'fee_group_id'=>$fee_group))	
		// 				->whereDate('fee_invoice_date', '>=', $start_date)
		// 				->whereDate('fee_invoice_end_date', '<=', $end_date)
		// 				->where('status',1)
		// 				->count();

		
		$checkAlreadyPayment = "";
		$fee_particulars = \Input::get('fee_particulars');
		if(!empty($fee_particulars)){
			$totalParticularAmount = 0;
			foreach($fee_particulars as $key => $value){
				if(!empty($fee_particulars[$key]['selectfeepart'])){
					$totalParticularAmount += $fee_particulars[$key]['amount'];
				}
			}
		}

		$totalInstallmentAmount = 0;
		
		if(!empty($fee_installment)){
			$fee_invoice_installments = \Input::get('allInstallments');
			foreach($fee_invoice_installments as $key1 => $value1){
				if(!empty($fee_invoice_installments[$key1]['installmentAmount'])){
					$totalInstallmentAmount += $fee_invoice_installments[$key1]['installmentAmount'];
				}
			}
		}

		



		$fineAmount = \Input::get('fineAmount');
		$amountPaid = \Input::get('payAmount');
		$DiscountAmount = \Input::get('DiscountAmount');
		$due_date = \Input::get('paidTime');
		$netPayble = ($totalParticularAmount + $fineAmount) - $DiscountAmount;
		
		if(!empty($checkAlreadyPayment)){
			return $this->panelInit->apiOutput(false,"Payment done","Payment has already done for this student of this duration" );
		}else if( $amountPaid > $netPayble){
			return $this->panelInit->apiOutput(false,"Invalid amount","Payment amount cannot be higher than payble amount" );
		}else if($totalInstallmentAmount > $netPayble){
			return $this->panelInit->apiOutput(false,"Invalid amount","Installment total amount cannot be higher than payble amount" );
		}else{
			$fee_collection = new \fee_collection();
			$fee_collection->fee_group_id = $fee_group;
			$fee_collection->school_id = $school_id;
			$fee_collection->paymentStudent = $studentId;
			$fee_collection->class_id = $class_id;
				if(!empty($due_date)){
					$fee_collection->due_date = date('Y-m-d',strtotime(str_replace('/','-',$due_date)));
				}else{
					$fee_collection->due_date = date('Y-m-d');	
				}	
				if(!empty($amountPaid) && $amountPaid < $netPayble){
					$fee_collection->paymentStatus = 2;
				}else if($amountPaid == $netPayble){
					$fee_collection->paymentStatus = 1;
				}else{
					$fee_collection->paymentStatus = 0;
				}
				
			$fee_collection->created_by = $this->data['users']->id;
			$fee_collection->created_date = date('Y-m-d H:i:s');
			$fee_collection->payment_date = date('Y-m-d');
			$fee_collection->fee_invoice_date = $start_date;
			$fee_collection->fee_invoice_end_date = $end_date;
			$fee_collection->status = 1;
			$fee_collection->save();

	

			$fee_particulars = \Input::get('fee_particulars');
			if(!empty($fee_particulars)){
				foreach($fee_particulars as $key => $value){
					if(!empty($fee_particulars[$key]['selectfeepart'])){
						$fee_collection_particulars = new \fee_collection_particulars();
						$fee_collection_particulars->fee_collection_id = $fee_collection->id;
						$fee_collection_particulars->fee_particular = $fee_particulars[$key]['feehead'];
						
						$fee_collection_particulars->amount = $fee_particulars[$key]['amount'];
						$fee_collection_particulars->status = 1;
						$fee_collection_particulars->created_by = $this->data['users']->id;
						$fee_collection_particulars->created_date = date('Y-m-d H:i:s');
						$fee_collection_particulars->save();
					}
				}
			}

			if(!empty($paymentStatus)){
				if($paymentStatus == 1){
					$fee_payment_collection_status = new \fee_payment_collection_status();
					$fee_payment_collection_status->fee_collection_id = $fee_collection->id;
					$fee_payment_collection_status->collectionAmount = \Input::get('payAmount');
					$fee_payment_collection_status->collectionDate = date('Y-m-d H:i:s');
					$fee_payment_collection_status->collectionMethod = \Input::get('paidMethod');

					$fee_payment_collection_status->status = 1;	
					$chequeNmbr = \Input::get('chequeNmbr');
					if(!empty($chequeNmbr)){
						$fee_payment_collection_status->cheque_number = $chequeNmbr;
						$fee_payment_collection_status->cheque_validity = date('Y-m-d',strtotime(str_replace('/','-',\Input::get('chequeValidityDate'))));
						$fee_payment_collection_status->bank_name = \Input::get('bank_name');
						$fee_payment_collection_status->branch_name = \Input::get('branch_name');
						$fee_payment_collection_status->status = 0;	
					}
					
					$fee_payment_collection_status->collectionNote = (!empty(\Input::get('collectionNote'))) ? \Input::get('collectionNote') : '';
					$fee_payment_collection_status->collectedBy = $this->data['users']->id;
					$fee_payment_collection_status->save();
				}
			}

			/*Save installments details*/
			if(!empty($fee_invoice_installments)){
				foreach($fee_invoice_installments as $key2 => $value2){
					$fee_installments = new \fee_installments();
					$fee_installments->fee_invoice_id = $fee_collection->id;
					$fee_installments->installment_amount = $fee_invoice_installments[$key2]['installmentAmount'];
					$fee_installments->due_date = date('Y-m-d',strtotime(str_replace('/','-',$fee_invoice_installments[$key2]['installmentDate'])));
					$fee_installments->paid_status = (!empty($fee_invoice_installments[$key2]['installmentPaid'])) ? 1 : 0;
					$fee_installments->save();
					if($fee_installments->paid_status == 1){
						$fee_payment_collection_status = new \fee_payment_collection_status();
						$fee_payment_collection_status->fee_collection_id = $fee_collection->id;
						$fee_payment_collection_status->collectionAmount =  $fee_invoice_installments[$key2]['installmentAmount'];
						$fee_payment_collection_status->collectionDate = date('Y-m-d H:i:s');
						$fee_payment_collection_status->collectionMethod = '';
						$fee_payment_collection_status->status = 1;	
						$fee_payment_collection_status->collectionNote = '';
						$fee_payment_collection_status->collectedBy = $this->data['users']->id;
						$fee_payment_collection_status->save();	
					}

				}
			}

	
			$fee_collection_update = \fee_collection::find($fee_collection->id); 
			if(!empty($fineAmount)){
				$fee_collection_update->fineAmount = $fineAmount;
			}
			if(!empty($DiscountAmount)){
				$fee_collection_update->discountAmount = $DiscountAmount;
			}
			$fee_collection_update->save();


			/* Notify to student */
			date_default_timezone_set('Asia/Kolkata'); 
			$notificationData = new \notification();
			$notificationData->school_id = $this->panelInit->authUser['school_id'];
			$notificationData->notification_type = 'payment';
			$notificationData->exam_id = 0;
			$notificationData->student_id = $studentId;
			$notificationData->grade = 0;

			if($paymentStatus == 1){
				$notificationData->display_msg = 'Fees paid from '.$start_date.' to '.$end_date.' with Receipt Id '.$fee_collection->id;
			}else{
				if($paymentStatus == 0){
					$notificationData->display_msg = 'Invoice generated from '.$start_date.' to '.$end_date.' with Invoice Id '.$fee_collection->id;
				}
			}
			$notificationData->status = 'active' ;
			$notificationData->subject_id = 0 ;
			$notificationData->read_status = 1 ;
			$notificationData->date = date("Y-m-d H:i:s") ;
			$notificationData->save();
		}
		return true;
	}




	function fetch_fee_detail($id = ""){

		$school_id = $this->panelInit->authUser['school_id'];
		$toReturn = array();
		if($this->data['users']->role == "admin" || $this->data['users']->role == "account"){
			
			$fee_details = \DB::select(\DB::raw("SELECT DISTINCT 
								
								users.fullName as student,
								users.id as studentId,
								users.studentRollId as studentRollId,

								fee_collection.id as invoiceId,
								fee_collection.created_date as paymentDate,
								fee_collection.dueDate as dueDate,
								fee_collection.paymentStatus as paymentStatus,
								fee_collection.fineAmount as fineAmount,
                                fee_collection.discountAmount as discountAmount,
								fee_collection.fee_invoice_date as fee_invoice_date,
								fee_collection.fee_invoice_end_date as fee_invoice_end_date,



                                fee_collection_particulars.fee_particular,
                                fee_collection_particulars.amount,

								schools.school_name as school,
								schools.address as address,
								schools.id as schoolId,

								
								schools.email as email,
								schools.contact_no as contact_no,


                                classes.className as className,
                                sections.sectionName as section,
								
								(
								SELECT sum(fee_collection_particulars.amount) + fee_collection.fineAmount - fee_collection.discountAmount FROM fee_collection_particulars
                                LEFT JOIN fee_collection ON fee_collection.id = fee_collection_particulars.fee_collection_id 
								WHERE fee_collection_particulars.fee_collection_id = invoiceId) AS  paymentAmount,
								(
								SELECT sum(fee_collection_particulars.amount) FROM fee_collection_particulars 
								WHERE fee_collection_particulars.fee_collection_id = invoiceId) AS  grandTotal,
								(
								SELECT sum(fee_payment_collection_status.collectionAmount)  FROM fee_payment_collection_status
								WHERE fee_payment_collection_status.fee_collection_id = invoiceId AND fee_payment_collection_status.status != 2) AS paidAmount
								
								FROM fee_collection

								LEFT JOIN users
								ON users.id = fee_collection.paymentStudent

								LEFT JOIN schools
								ON schools.id = fee_collection.school_id
                                
                                LEFT JOIN classes
								ON users.studentClass = classes.id
                                
                                LEFT JOIN sections
								ON sections.classId = classes.id
								
								LEFT JOIN fee_collection_particulars
								ON fee_collection_particulars.fee_collection_id = fee_collection.id
								
								LEFT JOIN fee_payment_collection_status
								ON fee_payment_collection_status.fee_collection_id = fee_collection.id
								WHERE fee_collection.school_id = $school_id
								AND fee_collection.id = $id
							
								"));
		}
		
		$payments['student'] = "";
		$payments['school'] = "";
		if(!empty($fee_details)){

			$parentsArr = \User::where(array('role'=>'parent','school_id'=>$school_id))->get();
			if(!empty($parentsArr)){
				
				foreach($parentsArr as $key => $value){
					$thisParent = array();
					$thisParent = json_decode($parentsArr[$key]->parentOf,true);
					if(!empty($thisParent)){
						if($thisParent[0]['id'] == $fee_details[0]->studentId){
							$payments['fatherName'] = $parentsArr[$key]->fullName;
							break;
						}
					}
					
				}
			}

			// $studentId = array();
			// $parentOf = json_decode($this->data['users']->parentOf,true);
			// if(is_array($parentOf)){
			// 	while (list($key, $value) = each($parentOf)) {
			// 		$studentId[] = $value['id'];
			// 	}
			// }
			// if(count($studentId) > 0){
			// 	$students = \User::where('role','student')->where('school_id',$school_id)->where('activated','1')->whereIn('id', $studentId);
			// }


			
			$payments['fineAmount'] = (!empty($fee_details[0]->fineAmount)) ? $fee_details[0]->fineAmount : 0;
			$payments['discountAmount'] = (!empty($fee_details[0]->discountAmount)) ? $fee_details[0]->discountAmount : 0;
			$payments['paymentAmount'] = (!empty($fee_details[0]->paymentAmount)) ? $fee_details[0]->paymentAmount : 0;
			$payments['paidAmount'] = (!empty($fee_details[0]->paidAmount)) ? $fee_details[0]->paidAmount : 0;
			$payments['grandTotal'] = (!empty($fee_details[0]->grandTotal)) ? $fee_details[0]->grandTotal : 0;
			$payments['paymentStatus'] = $fee_details[0]->paymentStatus;
			$payments['paymentMonth'] = date('m',strtotime($fee_details[0]->paymentDate));
			$payments['currentDate'] = date('d-m-Y',strtotime($fee_details[0]->paymentDate));

			$payments['paymentAmntInWords'] = $this->convertToRupee($payments['paymentAmount']);
			$payments['paidAmntInWords'] = 	 $this->convertToRupee($payments['paidAmount']);

			$payments['fee_invoice_date'] = date('j-M',strtotime($fee_details[0]->fee_invoice_date));
			$payments['fee_invoice_end_date'] = date('j-M-Y',strtotime($fee_details[0]->fee_invoice_end_date));





			// $start_month = date('F', strtotime($fee_details[0]->fee_invoice_date));
			// $end_month = date('F', strtotime($fee_details[0]->fee_invoice_end_date));
			// if($start_month == $end_month){
			// 	$payments['fee_duration'] = date('M-Y', strtotime($fee_details[0]->fee_invoice_date));
			// }else{
			// 	$payments['fee_duration'] = date('M Y', strtotime($fee_details[0]->fee_invoice_date))." - ".date('M Y', strtotime($fee_details[0]->fee_invoice_end_date));
			// }





			
			$payments['student'] = $fee_details[0]->student;
			$payments['studentRollId'] = $fee_details[0]->studentRollId;
			$payments['school'] = $fee_details[0]->school;
			$payments['schoolId'] = $fee_details[0]->schoolId;

			$payments['invoiceId'] = $fee_details[0]->invoiceId;
			$payments['email'] = $fee_details[0]->email;
			$payments['contact_no'] = $fee_details[0]->contact_no;


			$payments['className'] = $fee_details[0]->className;
			$payments['section'] = $fee_details[0]->section;
			$payments['address'] = $fee_details[0]->address;

			$payments['bank_copy'] = \fee_setting::where('school_id',$school_id)->value('bank_copy');	
			$payments['student_copy'] = \fee_setting::where('school_id',$school_id)->value('student_copy');	



			$allinstallments = \fee_installments::where(
				array('fee_invoice_id'=>$fee_details[0]->invoiceId))->get()->toArray();	
			$installmentStatus = '';
			if(!empty($allinstallments)){
				$paidInstallmentAmount = 0;
				$index = 1;
				foreach($allinstallments as $key3 => $value3){
					$allinstallments[$key3]['intallmentId'] = $allinstallments[$key3]['id']; 
					$allinstallments[$key3]['installment_amount'] = $allinstallments[$key3]['installment_amount']; 
					
					$allinstallments[$key3]['paid_status'] = ($allinstallments[$key3]['paid_status'] == 1) ? 'Paid' : 'Due';
					$allinstallments[$key3]['due_date'] = ($allinstallments[$key3]['paid_status'] == 'Due') ? date('d-m-Y',strtotime($allinstallments[$key3]['due_date'])) : "";
					$installmentStatus .= $index.". ";
					if($allinstallments[$key3]['paid_status'] == 'Due'){
						$installmentStatus .= "<span style='color:red;'>";
					}else{
						$installmentStatus .= "<span style='color:green;'>";
						$paidInstallmentAmount += $allinstallments[$key3]['installment_amount']; 
					}
					$installmentStatus .= $allinstallments[$key3]['installment_amount'];
					if($allinstallments[$key3]['paid_status'] == 'Due'){
						$installmentStatus .= " ".$allinstallments[$key3]['paid_status']." On ";
						
					}else{
						$installmentStatus .= " ".$allinstallments[$key3]['paid_status'];
					}
					
					$installmentStatus .= $allinstallments[$key3]['due_date']."<br>";
					$installmentStatus .= "</span>";
					$index++;
				}
				$payments['installment_details'] = $installmentStatus;
			}



		}
		
		if(!empty($fee_details)){
			$fee_details_particulars = \fee_collection_particulars::where(array('fee_collection_id'=>$fee_details[0]->invoiceId,'status'=>1))->get();
			$payments['paymentRows'] = array();
			foreach($fee_details_particulars as $key => $value){
				//$payments['paymentRows'][] = array('title'=>$payments['paymentDescription'],'amount'=>$payments['paymentAmount']);
				$payments['paymentRows'][] = array('title'=>$fee_details_particulars[$key]->fee_particular,'amount'=>$fee_details_particulars[$key]->amount);
			}
			
		}
	
		return $payments;
	}



	/*----------------Convert Rupees in word -------------------------------*/

	function convertToRupee($number){
		$no = round($number);
		$whole = floor($number);      
		$point = $number - $whole; 

		$hundred = null;
		$digits_1 = strlen($no);
		$i = 0;
		$str = array();
		$words = array('0' => '', '1' => 'One', '2' => 'Two',
				'3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
				'7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
				'10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
				'13' => 'Thirteen', '14' => 'fourteen',
				'15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
				'18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
				'30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
				'60' => 'Sixty', '70' => 'Seventy',
				'80' => 'Eighty', '90' => 'Ninety');
				$digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
		while ($i < $digits_1) {
			$divider = ($i == 2) ? 10 : 100;
			$number = floor($no % $divider);
			$no = floor($no / $divider);
			$i += ($divider == 10) ? 1 : 2;
			if ($number) {
				$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
				$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
				$str [] = ($number < 21) ? $words[$number] .
					" " . $digits[$counter] . $plural . " " . $hundred
					:
					$words[floor($number / 10) * 10]
					. " " . $words[$number % 10] . " "
					. $digits[$counter] . $plural . " " . $hundred;
			} else $str[] = null;
		}
		$str = array_reverse($str);
		$result = implode('', $str);
		$points = $this->paiseValue($point);

		$finalStr = '';

		if(!empty($result)){
			$finalStr .= $result. " Rupees  ";
		}
		if(!empty($points)){
			$finalStr .= "and ".$points. " Paise";
		}
		if(!empty($finalStr)){
			$finalStr .= " Only.";
		}

		return $finalStr;
	}


	function paiseValue($number){
		$no = round( str_replace('.','',$number));
		$hundred = null;
		$digits_1 = strlen($no);
		$i = 0;
		$str = array();
		$words = array('0' => '', '1' => 'One', '2' => 'Two',
		'3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
		'7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
		'10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
		'13' => 'Thirteen', '14' => 'fourteen',
		'15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
		'18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
		'30' => 'Thirty', '40' => 'Fourty', '50' => 'Fifty',
		'60' => 'Sixty', '70' => 'Seventy',
		'80' => 'Eighty', '90' => 'Ninety');
		$digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
		while ($i < $digits_1) {
		$divider = ($i == 2) ? 10 : 100;
		$number = floor($no % $divider);
		$no = floor($no / $divider);
		$i += ($divider == 10) ? 1 : 2;
		if ($number) {
			$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			$str [] = ($number < 21) ? $words[$number] .
				" " . $digits[$counter] . $plural . " " . $hundred
				:
				$words[floor($number / 10) * 10]
				. " " . $words[$number % 10] . " "
				. $digits[$counter] . $plural . " " . $hundred;
		} else $str[] = null;
		}
		$str = array_reverse($str);
		$result = implode('', $str);

		return $result;

	
	}




	function invoice($id = ""){

		$school_id = $this->panelInit->authUser['school_id'];
		$return = array();

		if($this->data['users']->role == "admin" || $this->data['users']->role == "account"){
			
			$fee_details = \DB::select(\DB::raw("SELECT		
						users.fullName as student,
						users.id as studentId,
						users.studentRollId as studentRollId,

						fee_collection.id as invoiceId,
						fee_collection.created_date as paymentDate,
						fee_collection.dueDate as dueDate,
						fee_collection.paymentStatus as paymentStatus,
						fee_collection.fineAmount as fineAmount,
						fee_collection.discountAmount as discountAmount,
						fee_collection_particulars.fee_particular,
						fee_collection_particulars.amount,

						fee_payment_collection_status.collectionDate as paidDate,
                        fee_payment_collection_status.collectionAmount as collectionAmount,

						schools.school_name as school,
						schools.address as address,

						classes.className as className,
						sections.sectionName as section,
						
						(
						SELECT sum(fee_collection_particulars.amount) + fee_collection.fineAmount - fee_collection.discountAmount FROM fee_collection_particulars
						LEFT JOIN fee_collection ON fee_collection.id = fee_collection_particulars.fee_collection_id 
						WHERE fee_collection_particulars.fee_collection_id = invoiceId) AS  paymentAmount,
						(
						SELECT sum(fee_collection_particulars.amount) FROM fee_collection_particulars 
						WHERE fee_collection_particulars.fee_collection_id = invoiceId) AS  grandTotal,
						(
						SELECT sum(fee_payment_collection_status.collectionAmount)  FROM fee_payment_collection_status
						WHERE fee_payment_collection_status.fee_collection_id = invoiceId AND fee_payment_collection_status.status != '2') AS paidAmount

						
						FROM fee_collection

						LEFT JOIN users
						ON users.id = fee_collection.paymentStudent

						LEFT JOIN schools
						ON schools.id = fee_collection.school_id
						
						LEFT JOIN classes
						ON users.studentClass = classes.id
						
						LEFT JOIN sections
						ON sections.classId = classes.id
						
						LEFT JOIN fee_collection_particulars
						ON fee_collection_particulars.fee_collection_id = fee_collection.id
						
						LEFT JOIN fee_payment_collection_status
						ON fee_payment_collection_status.fee_collection_id = fee_collection.id
						WHERE fee_collection.school_id = $school_id
						AND fee_collection.id = $id
						"));
		}

		//return $return;

		$return['totalWithTax'] = $fee_details[0]->paymentAmount;
		$return['invoiceId'] = $id;

		


		$allinstallments = \fee_installments::where(
			array('fee_invoice_id'=>$id))->get()->toArray();
		$paidInstallmentAmount = 0;
		$installmentStatus = "";
		if(!empty($allinstallments)){
			foreach($allinstallments as $key3 => $value3){
				$return['intallments'][$key3]['intallmentId'] = $allinstallments[$key3]['id']; 
				$return['intallments'][$key3]['installment_amount'] = $allinstallments[$key3]['installment_amount']; 
				$return['intallments'][$key3]['paid_status'] = $allinstallments[$key3]['paid_status'];
				$return['intallments'][$key3]['installmentPaid'] = $allinstallments[$key3]['paid_status'];
				$return['intallments'][$key3]['due_date'] = ($allinstallments[$key3]['paid_status'] == 'Due') ? date('d-m-Y',strtotime($allinstallments[$key3]['due_date'])) : "";
			}
			
		}	
	

		$return['pendingAmount'] = $fee_details[0]->paymentAmount - $fee_details[0]->paidAmount;

		$return['paidAmount'] = $fee_details[0]->paidAmount;
		$fee_collection_details = \fee_payment_collection_status::where('fee_collection_id',$fee_details[0]->invoiceId)->get();
		$return['collection'] = array();
		if(!empty($fee_collection_details)){
			foreach($fee_collection_details as $key => $value){
				$return['collection'][$key]['id'] = $fee_collection_details[$key]->id;
				$return['collection'][$key]['paidDate'] = date('d-m-Y',strtotime($fee_collection_details[$key]->collectionDate));
				$return['collection'][$key]['collectionAmount'] = $fee_collection_details[$key]->collectionAmount;
				$return['collection'][$key]['collectionMethod'] = $fee_collection_details[$key]->collectionMethod;
				$paidStatus = '';
				if($fee_collection_details[$key]->status == 2){
					$paidStatus = 'Bounce';
					$backColor = 'background-color: #de9191';
				}else if($fee_collection_details[$key]->status == 0){
					$paidStatus = 'Pending';
					$backColor = 'background-color: #f3ecbf';
				}else{
					$backColor = '';
				}
				$return['collection'][$key]['backColor'] = $backColor;
				if($fee_collection_details[$key]->collectionMethod == 'cheque'){
					$return['collection'][$key]['collectionMethod'] = ($paidStatus == 'Bounce' || $paidStatus == 'Pending') ? $fee_collection_details[$key]->collectionMethod."(".$paidStatus.")" : $fee_collection_details[$key]->collectionMethod;
				}
			}
		}
		return $return;
	}

	function collect($id = ""){
		if($this->data['users']->role != "admin" && $this->data['users']->role != "account") exit;
		$toReturn = array();
		$pendingAmount = \Input::get('pendingAmount');
		$collectionNote = \Input::get('collectionNote');
		$installMents = \Input::get('allinstallments');
		$collectionAmount = \Input::get('collectionAmount');

		//return $installMents;
		if(!empty($collectionAmount)){
			if(bccomp($collectionAmount, $pendingAmount,10) == 1){
				return $this->panelInit->apiOutput(false,"Invoice Collection","Collection amount is greater than invoice pending amount");
			}
		}


		if(!empty($collectionAmount)){
			$fee_payment_collection_status = new \fee_payment_collection_status();
			$fee_payment_collection_status->fee_collection_id = $id;
			$fee_payment_collection_status->collectionAmount = \Input::get('collectionAmount');
			$fee_payment_collection_status->collectionDate = date('Y-m-d H:i:s');
			$fee_payment_collection_status->collectionMethod = \Input::get('paidMethod');
			$fee_payment_collection_status->status = 1;

			$paidMethod = \Input::get('paidMethod');
			if(!empty($paidMethod)){
				$chequeNmbr = \Input::get('chequeNmbr');
				if(!empty($chequeNmbr)){
					$fee_payment_collection_status->cheque_number = $chequeNmbr;
					$fee_payment_collection_status->cheque_validity = date('Y-m-d',strtotime(str_replace('/','-',\Input::get('chequeValidityDate'))));
					$fee_payment_collection_status->bank_name = \Input::get('bank_name');
					$fee_payment_collection_status->branch_name = \Input::get('branch_name');
					$fee_payment_collection_status->status = 0;	
				}
			}

			if(!empty($collectionNote)){
				$fee_payment_collection_status->collectionNote = \Input::get('collectionNote');
			}
			
			$fee_payment_collection_status->collectedBy = $this->data['users']->id;
			$fee_payment_collection_status->save();
		}else{
			if(!empty($installMents)){
				foreach($installMents as $key1 => $value1){
					if(!empty($installMents[$key1]['installmentPaid']) && $installMents[$key1]['paid_status'] == 0){
						$fee_payment_collection_status = new \fee_payment_collection_status();
						$fee_payment_collection_status->fee_collection_id = $id;
						$fee_payment_collection_status->collectionAmount = $installMents[$key1]['installment_amount'];
						$fee_payment_collection_status->collectionDate = date('Y-m-d H:i:s');
						$fee_payment_collection_status->collectionMethod = \Input::get('paidMethod');
						$fee_payment_collection_status->status = 1;
						$fee_payment_collection_status->save();
						$paidMethod = \Input::get('paidMethod');
						if(!empty($paidMethod)){
							$chequeNmbr = \Input::get('chequeNmbr');
							if(!empty($chequeNmbr)){
								$fee_payment_collection_status->cheque_number = $chequeNmbr;
								$fee_payment_collection_status->cheque_validity = date('Y-m-d',strtotime(str_replace('/','-',\Input::get('chequeValidityDate'))));
								$fee_payment_collection_status->bank_name = \Input::get('bank_name');
								$fee_payment_collection_status->branch_name = \Input::get('branch_name');
								$fee_payment_collection_status->status = 0;	
							}
						}
						if(!empty($collectionNote)){
							$fee_payment_collection_status->collectionNote = \Input::get('collectionNote');
						}	
						$fee_payment_collection_status->collectedBy = $this->data['users']->id;
						$fee_payment_collection_status->save();
		
						$fee_installments = \fee_installments::find($installMents[$key1]['intallmentId']);
						$fee_installments->paid_status = 1;
						$fee_installments->save();
					}
				}
			}			
		}
		

		



		if(!empty($collectionAmount)){
			$amountPaid = \Input::get('collectionAmount');
		}




		// $fee_collection_update = \fee_collection::find($id); 
		// if($amountPaid < $pendingAmount){
		// 	$fee_collection_update->paymentStatus = 2;
		// }else if($amountPaid == $pendingAmount){
		// 	$fee_collection_update->paymentStatus = 1;
		// }else{
		// 	$fee_collection_update->paymentStatus = 0;
		// }
		// $fee_collection_update->save();

		return $this->panelInit->apiOutput(true,"Invoice Collection","Collection completed successfully");

	}

	public function searchStudents(){
		//$students = \User::where('school_id',$this->panelInit->authUser['school_id'])->where('role','student')->where('fullName','like','%'.$student.'%')->orWhere('username','like','%'.$student.'%')->orWhere('email','like','%'.$student.'%')->get();
		$school_id = $this->panelInit->authUser['school_id'];
		$class_id = \Input::get('classId');
		$query = \Input::get('searchAbout');

		$students =	\DB::table('users')
		->select('users.id as id',
				'users.fullName as fullName',
				'users.studentRollId as studentRollId',
				'sections.sectionName as sectionName',
				'users.email as email',
				'classes.className as className')
		->where('users.school_id', $school_id)
		->where('users.studentClass', $class_id)
		->leftJoin('classes','classes.id','=','users.studentClass')
		->leftJoin('sections','sections.id','=','users.studentSection')
		->where(function($q) use ($query) {
			$q->where('users.username','like','%'.$query.'%')
			 ->orWhere('users.email','like','%'.$query.'%')
			 ->orWhere('users.fullName','like','%'.$query.'%')
			 ->orWhere('users.studentRollId','like','%'.$query.'%');
		})->get();

		$retArray = array();
		if(!empty($students)){
			foreach ($students as $key => $value) {
				$retArray[$students[$key]->id] = array("id"=>$students[$key]->id,"name"=>$students[$key]->fullName,"email"=>$students[$key]->email,"className"=>$students[$key]->className,"sectionName"=>$students[$key]->sectionName,"studentRollId"=>$students[$key]->studentRollId,"class_id"=>$class_id);
			}
		}
		return json_encode($retArray);
	}

	public function delete($id){
		if($this->data['users']->role != "admin" && $this->data['users']->role != "account") exit;
		if ( $postDelete = \fee_collection::where('id', $id)->first() )
        {
			if($fee_collection_particulars = \fee_collection_particulars::where('fee_collection_id',$id)){
				$fee_collection_particulars->delete();
			}
			if($fee_payment_collection_status = \fee_payment_collection_status::where('fee_collection_id',$id)){
				$fee_payment_collection_status->delete();
			}
			if($fee_installments = \fee_installments::where('fee_invoice_id',$id)){
					$fee_installments->delete();
			
			}

            $postDelete->delete();
            return $this->panelInit->apiOutput(true,$this->panelInit->language['delPayment'],$this->panelInit->language['paymentDel']);
        }else{
            return $this->panelInit->apiOutput(false,$this->panelInit->language['delPayment'],$this->panelInit->language['paymentNotExist']);
        }
	}







	function getAllInvoices($studentId){

		$toReturn = array();
		$school_id = $this->panelInit->authUser['school_id'];

		$query = "SELECT DISTINCT
			fee_collection.id as invoiceId,
			fee_collection.created_date as paymentDate,
			fee_collection.paymentStatus as paymentStatus,
			fee_collection.dueDate as dueDate,
			fee_collection.paymentStatus as paymentStatus,
			fee_collection.fee_invoice_date as fee_invoice_date,
			fee_collection.fee_invoice_end_date as fee_invoice_end_date,
			
			users.fullName as fullName,
			users.id as studentId,

			classes.className as className,
			

			(
			SELECT sum(fee_collection_particulars.amount) + fee_collection.fineAmount - fee_collection.discountAmount FROM fee_collection_particulars
				LEFT JOIN fee_collection ON fee_collection.id = fee_collection_particulars.fee_collection_id 
				WHERE fee_collection_particulars.fee_collection_id = invoiceId) AS  paymentAmount,
			
			(
			SELECT sum(fee_payment_collection_status.collectionAmount)  FROM fee_payment_collection_status
			WHERE fee_payment_collection_status.fee_collection_id = invoiceId AND fee_payment_collection_status.status = 1
			) AS paidAmount
			
			FROM fee_collection

			LEFT JOIN users
			ON users.id = fee_collection.paymentStudent			

			LEFT JOIN classes
			ON classes.id = fee_collection.class_id			
			
			LEFT JOIN fee_collection_particulars
			ON fee_collection_particulars.fee_collection_id = fee_collection.id	
			LEFT JOIN fee_payment_collection_status
			ON fee_payment_collection_status.fee_collection_id = fee_collection.id
			WHERE fee_collection.school_id = $school_id			
			AND fee_collection.fee_group_id != 0
			AND fee_collection.paymentStudent = $studentId
			GROUP BY fee_collection.id
			ORDER BY fee_collection.created_date DESC";


			$query_count = $query;

			//$query .= " LIMIT $offset , 20";

			$toReturn['fee_invoices'] = \DB::select(\DB::raw($query));
			$count_fee_invoices = \DB::select(\DB::raw($query_count));

			$totalPaymentAmount = 0;
		$totalPaidAmount = 0;
		$toReturn['balance_amount'] = 0;
		foreach ($toReturn['fee_invoices'] as $key => $value) {

			$fee_installments = \fee_installments::where('fee_invoice_id',$toReturn['fee_invoices'][$key]->invoiceId)->get()->toArray();
			if(empty($fee_installments)){
				$toReturn['fee_invoices'][$key]->pendingCheques = 0;
				$toReturn['fee_invoices'][$key]->paymentDate = (strtotime($toReturn['fee_invoices'][$key]->paymentDate)>0) ? date('Y-m-d',strtotime($toReturn['fee_invoices'][$key]->paymentDate)) : 'N/A';
				$toReturn['fee_invoices'][$key]->invoiceId = $toReturn['fee_invoices'][$key]->invoiceId;
				$toReturn['fee_invoices'][$key]->fee_invoice_date = (strtotime($toReturn['fee_invoices'][$key]->fee_invoice_date)>0) ? date('d-m-Y',strtotime($toReturn['fee_invoices'][$key]->fee_invoice_date)): 'N/A';
				$toReturn['fee_invoices'][$key]->fee_invoice_end_date = (strtotime($toReturn['fee_invoices'][$key]->fee_invoice_end_date)>0) ? date('d-m-Y',strtotime($toReturn['fee_invoices'][$key]->fee_invoice_end_date)): 'N/A';

				$toReturn['fee_invoices'][$key]->dueDate = (strtotime($toReturn['fee_invoices'][$key]->dueDate) > 0) ? date('d-m-Y',strtotime($toReturn['fee_invoices'][$key]->dueDate)): 'N/A';
				$toReturn['fee_invoices'][$key]->paymentAmount = $toReturn['fee_invoices'][$key]->paymentAmount;
				$toReturn['fee_invoices'][$key]->paidAmount = $toReturn['fee_invoices'][$key]->paidAmount;

				$paymentStatus = 0;
				if($toReturn['fee_invoices'][$key]->paymentAmount == $toReturn['fee_invoices'][$key]->paidAmount){
					$paymentStatus = 1;
				}else if($toReturn['fee_invoices'][$key]->paidAmount != '' && $toReturn['fee_invoices'][$key]->paymentAmount > $toReturn['fee_invoices'][$key]->paidAmount){
					$paymentStatus = 2;
				}else{
					$paymentStatus = 0;
				}

				$toReturn['fee_invoices'][$key]->paymentStatus = $paymentStatus;
				$update_fee_collection = \fee_collection::find($toReturn['fee_invoices'][$key]->invoiceId);
				$update_fee_collection->paymentStatus = $paymentStatus;
				$update_fee_collection->save();

				$pendingCheques = 0;
				$pendingCheques = count(\fee_payment_collection_status::where(
					array('fee_collection_id'=>$toReturn['fee_invoices'][$key]->invoiceId, 'status'=> 0))->get()->toArray());
				$toReturn['fee_invoices'][$key]->pendingCheques = $pendingCheques;
			}else{
				unset($toReturn['fee_invoices'][$key]);
			}
		}

		
        $array = array_values($toReturn['fee_invoices']);
		$toReturn['fee_invoices'] = $array;

		foreach ($count_fee_invoices as $key1 => $value1) {
			$fee_installments = \fee_installments::where('fee_invoice_id',$count_fee_invoices[$key1]->invoiceId)->get()->toArray();
			if(empty($fee_installments)){
				$totalPaymentAmount += $count_fee_invoices[$key1]->paymentAmount;
				$totalPaidAmount += $count_fee_invoices[$key1]->paidAmount;
			}
		}

		$toReturn['total_paid_amount'] = (!empty($totalPaidAmount))?$totalPaidAmount:0;
		$toReturn['total_payment_amount'] = (!empty($totalPaymentAmount))?$totalPaymentAmount:0;
		$toReturn['balance_amount'] = $totalPaymentAmount - $totalPaidAmount;

		$toReturn['totalItems'] = count($count_fee_invoices);
		
		return $toReturn;

	}

	function saveMultipleDueInvoices(){
		$toReturn = array();
		$school_id = $this->panelInit->authUser['school_id'];
		$Invoices = \Input::get('due_fee_invoices');

		$paidAmount = \Input::get('DuePaidAmount');
		$balance_amount = \Input::get('balance_amount');

		$collectionType= 0;

		if(empty($paidAmount)){
			return $this->panelInit->apiOutput(false,"Failed","Please enter some amount to paid.");
		}else if($paidAmount > $balance_amount){
			return $this->panelInit->apiOutput(false,"Failed","Paid amount should not higher than balance amount.");
		}

		$allInvoices = array_reverse($Invoices);
		
		foreach($allInvoices as $key => $value){
			$fee_installments = \fee_installments::where('fee_invoice_id',$allInvoices[$key]['invoiceId'])->get()->toArray();
			if(empty($fee_installments)){
			$invoiceBalanceAmount = $allInvoices[$key]['paymentAmount'] - $allInvoices[$key]['paidAmount'];
			if($invoiceBalanceAmount > 0 && $paidAmount > 0){
				if($paidAmount > $invoiceBalanceAmount){
					$fee_payment_collection_status = new \fee_payment_collection_status();
					$fee_payment_collection_status->fee_collection_id = $allInvoices[$key]['invoiceId'];
					$fee_payment_collection_status->collectionAmount = $invoiceBalanceAmount;
					$fee_payment_collection_status->collectionDate = date('Y-m-d H:i:s');
					$fee_payment_collection_status->collectionMethod = 'cash';
					$fee_payment_collection_status->status = 1;


					$paidMethod = \Input::get('paidMethod');
					if(!empty($paidMethod)){
						$chequeNmbr = \Input::get('chequeNmbr');
						if(!empty($chequeNmbr)){
							$fee_payment_collection_status->cheque_number = $chequeNmbr;
							$fee_payment_collection_status->cheque_validity = date('Y-m-d',strtotime(str_replace('/','-',\Input::get('chequeValidityDate'))));
							$fee_payment_collection_status->bank_name = \Input::get('bank_name');
							$fee_payment_collection_status->branch_name = \Input::get('branch_name');
							$fee_payment_collection_status->status = 0;	
						}
					}

					// if(!empty($collectionNote)){
					// 	$fee_payment_collection_status->collectionNote = \Input::get('collectionNote');
					// }

					$fee_payment_collection_status->collectedBy = $this->data['users']->id;
					$fee_payment_collection_status->save();
					$paidAmount -= $invoiceBalanceAmount;
				}else{

					$fee_payment_collection_status = new \fee_payment_collection_status();
					$fee_payment_collection_status->fee_collection_id = $allInvoices[$key]['invoiceId'];
					$fee_payment_collection_status->collectionAmount = $paidAmount;
					$fee_payment_collection_status->collectionDate = date('Y-m-d H:i:s');
					$fee_payment_collection_status->collectionMethod = \Input::get('paidMethod');
					$fee_payment_collection_status->status = 1;

					if(count($allInvoices) > 1){
						$paidMethod = \Input::get('paidMethod');
						if(!empty($paidMethod) && $paidMethod == "cheque" ){
							$chequeNmbr = \Input::get('chequeNmbr');
							if(!empty($chequeNmbr)){

								$fee_cheque_collection_for_multiple_invoice = new \fee_cheque_collection_for_multiple_invoice();
								$fee_cheque_collection_for_multiple_invoice->school_id = $school_id;
								$fee_cheque_collection_for_multiple_invoice->cheque_number = $chequeNmbr;
								$fee_cheque_collection_for_multiple_invoice->cheque_validity = date('Y-m-d',strtotime(str_replace('/','-',\Input::get('chequeValidityDate'))));
								$fee_cheque_collection_for_multiple_invoice->collection_amount = $paidAmount;
								$fee_cheque_collection_for_multiple_invoice->bank_name = \Input::get('bank_name');
								$fee_cheque_collection_for_multiple_invoice->bank_branch = \Input::get('branch_name');
								$fee_cheque_collection_for_multiple_invoice->status = 0;	
								$fee_cheque_collection_for_multiple_invoice->date = date('Y-m-d H:i:s');	
								$fee_cheque_collection_for_multiple_invoice->save();	


								$fee_payment_collection_status->collectionType = $fee_cheque_collection_for_multiple_invoice->id;
								
							}
						}
					}else{
						$paidMethod = \Input::get('paidMethod');
						if(!empty($paidMethod)){
							$chequeNmbr = \Input::get('chequeNmbr');
							if(!empty($chequeNmbr)){
								$fee_payment_collection_status->cheque_number = $chequeNmbr;
								$fee_payment_collection_status->cheque_validity = date('Y-m-d',strtotime(str_replace('/','-',\Input::get('chequeValidityDate'))));
								$fee_payment_collection_status->bank_name = \Input::get('bank_name');
								$fee_payment_collection_status->branch_name = \Input::get('branch_name');
								$fee_payment_collection_status->status = 0;	
							}
						}
					}
					

					// if(!empty($collectionNote)){
					// 	$fee_payment_collection_status->collectionNote = \Input::get('collectionNote');
					// }

					$fee_payment_collection_status->collectedBy = $this->data['users']->id;
					$fee_payment_collection_status->save();

					$paidAmount = 0;
					}

				}
			}
		}
		return $this->panelInit->apiOutput(true,"Invoice Collection","Due Invoice collected successfully");

	}












}
